<?php

namespace App\Http\Controllers;

use App\CreationToken;
use App\Http\Requests\AccountCreationRequest;
use App\Http\Requests\AuthenticationRequest;
use App\Http\Requests\LookupEmailRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\SendPasswordResetRequest;
use App\PasswordPolicy;
use App\PasswordReset;
use App\Services\LanguageService;
use App\SystemSetting;
use App\Traits\Throttles;
use App\UsernameLanguage;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\Factory;
use Illuminate\View\View;
use SonarSoftware\CustomerPortalFramework\Controllers\AccountAuthenticationController;
use SonarSoftware\CustomerPortalFramework\Controllers\ContactController;
use SonarSoftware\CustomerPortalFramework\Exceptions\AuthenticationException;

class AuthenticationController extends Controller
{
    use Throttles;

    private $passwordPolicy;

    public function __construct()
    {
        $this->passwordPolicy = new PasswordPolicy();
    }

    /**
     * Show the main login page
     */
    public function index(): Factory|View
    {
        $systemSetting = SystemSetting::firstOrNew([
            'id' => 1,
        ]);

        return view('pages.root.index', compact('systemSetting'));
    }

    /**
     * Authenticate against the Sonar API
     */
    public function authenticate(AuthenticationRequest $request): RedirectResponse
    {
        if ($this->getThrottleValue('login', $this->generateLoginThrottleHash($request)) > 10) {
            return redirect()->back()->withErrors(utrans('errors.tooManyFailedAuthenticationAttempts', [], $request));
        }
        $accountAuthenticationController = new AccountAuthenticationController();
        try {
            $result = $accountAuthenticationController->authenticateUser(
                $request->input('username'),
                $request->input('password')
            );
            $request->session()->put('authenticated', true);
            $request->session()->put('user', $result);
        } catch (AuthenticationException $e) {
            $this->incrementThrottleValue('login', $this->generateLoginThrottleHash($request));
            $request->session()->forget('authenticated');
            $request->session()->forget('user');

            return redirect()->back()->withErrors(utrans('errors.loginFailed', [], $request));
        }

        $this->resetThrottleValue('login', $this->generateLoginThrottleHash($request));

        $usernameLanguage = UsernameLanguage::firstOrNew(['username' => $request->input('username')]);
        $usernameLanguage->language = $request->input('language');
        $usernameLanguage->save();

        return redirect()->action([\App\Http\Controllers\BillingController::class, 'index']);
    }

    /**
     * Show the registration form
     */
    public function showRegistrationForm(): Factory|View
    {
        return view('pages.root.register');
    }

    /**
     * Look up an email address to see if it can be used to create a new account.
     */
    public function lookupEmail(LookupEmailRequest $request): RedirectResponse
    {
        if ($this->getThrottleValue('email_lookup', hash('sha256', $request->getClientIp())) > 10) {
            return redirect()->back()->withErrors(utrans('errors.tooManyFailedLookupAttempts', [], $request));
        }

        $accountAuthenticationController = new AccountAuthenticationController();
        try {
            $result = $accountAuthenticationController->lookupEmail($request->input('email'));
        } catch (Exception $e) {
            $this->incrementThrottleValue('email_lookup', hash('sha256', $request->getClientIp()));
            Log::info($e->getMessage());

            return redirect()->back()->withErrors(utrans('errors.emailLookupFailed', [], $request));
        }

        $creationToken = CreationToken::where('account_id', '=', $result->account_id)
            ->where('contact_id', '=', $result->contact_id)
            ->first();

        if ($creationToken === null) {
            $creationToken = new CreationToken([
                'token' => uniqid(),
                'email' => strtolower($result->email_address),
                'account_id' => $result->account_id,
                'contact_id' => $result->contact_id,
            ]);
        } else {
            $creationToken->token = uniqid();
        }

        $creationToken->save();

        $languageService = App::make(LanguageService::class);
        $language = $languageService->getUserLanguage($request);
        try {
            Mail::send('emails.basic', [
                'greeting' => trans('emails.greeting', [], $language),
                'body' => trans('emails.accountCreateBody', [
                    'isp_name' => config('app.name'),
                    'portal_url' => config('app.url'),
                    'creation_link' => config('app.url').'/create/'.$creationToken->token,
                ], $language),
                'deleteIfNotYou' => trans('emails.deleteIfNotYou', [], $language),
            ], function ($m) use ($result, $request) {
                $m->from(config('customer_portal.from_address'), config('customer_portal.from_name'));
                $m->to($result->email_address, $result->email_address)
                    ->subject(
                        utrans(
                            'emails.createAccount',
                            ['companyName' => config('customer_portal.company_name')],
                            $request
                        )
                    );
            });
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->withErrors(utrans('errors.emailSendFailed', [], $request));
        }

        $this->resetThrottleValue('email_lookup', hash('sha256', $request->getClientIp()));

        return redirect()
            ->action([\App\Http\Controllers\AuthenticationController::class, 'index'])
            ->with('success', utrans('root.emailFound', [], $request));
    }

    /**
     * Show the account creation form
     */
    public function showCreationForm($token, Request $request): Factory|View|RedirectResponse
    {
        $creationToken = CreationToken::where('token', '=', trim($token))
            ->where('updated_at', '>=', Carbon::now('UTC')->subHours(24)->toDateTimeString())
            ->first();
        if ($creationToken === null) {
            return redirect()
                ->action([\App\Http\Controllers\AuthenticationController::class, 'showRegistrationForm'])
                ->withErrors(utrans('errors.invalidToken', [], $request));
        }

        return view('pages.root.create', compact('creationToken'));
    }

    /**
     * Create a new account
     */
    public function createAccount(AccountCreationRequest $request, $token): RedirectResponse
    {
        if ($this->getThrottleValue('create_account', hash('sha256', $token.$request->getClientIp())) > 10) {
            return redirect()->back()->withErrors(utrans('errors.tooManyFailedCreationAttempts', [], $request));
        }

        $creationToken = CreationToken::where('token', '=', trim($token))
            ->where('updated_at', '>=', Carbon::now('UTC')->subHours(24)->toDateTimeString())
            ->first();
        if ($creationToken === null) {
            $this->incrementThrottleValue('email_lookup', hash('sha256', $token.$request->getClientIp()));

            return redirect()
                ->action([\App\Http\Controllers\AuthenticationController::class, 'showRegistrationForm'])
                ->withErrors(utrans('errors.invalidToken', [], $request));
        }

        if (strtolower(trim($creationToken->email)) != strtolower(trim($request->input('email')))) {
            $this->incrementThrottleValue('email_lookup', hash('sha256', $token.$request->getClientIp()));

            return redirect()->back()->withErrors(utrans('errors.invalidEmailAddress', [], $request))->withInput();
        }

        if (!$this->passwordPolicy->isPasswordValid($request->input('password'))) {
            return redirect()->back()->withErrors(utrans("errors.passwordIsTooWeak"))->withInput();
        }

        $accountAuthenticationController = new AccountAuthenticationController();
        try {
            $accountAuthenticationController->createUser(
                $creationToken->account_id,
                $creationToken->contact_id,
                $request->input('username'),
                $request->input('password')
            );
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }

        $creationToken->delete();

        $this->resetThrottleValue('email_lookup', hash('sha256', $token.$request->getClientIp()));

        return redirect()
            ->action([\App\Http\Controllers\AuthenticationController::class, 'index'])
            ->with('success', utrans('register.accountCreated', [], $request));
    }

    /**
     * Show the reset password form
     */
    public function showResetPasswordForm(): Factory|View
    {
        return view('pages.root.reset');
    }

    /**
     * Check for, and email a password reset if email is valid.
     */
    public function sendResetEmail(SendPasswordResetRequest $request): RedirectResponse
    {
        if ($this->getThrottleValue('password_reset', hash('sha256', $request->getClientIp())) > 5) {
            return redirect()->back()->withErrors(utrans('errors.tooManyPasswordResetRequests', [], $request));
        }

        $accountAuthenticationController = new AccountAuthenticationController();
        try {
            $result = $accountAuthenticationController->lookupEmail($request->input('email'), false);
        } catch (Exception $e) {
            $this->incrementThrottleValue('password_reset', hash('sha256', $request->getClientIp()));

            return redirect()->back()->withErrors(utrans('errors.resetLookupFailed', [], $request));
        }

        $passwordReset = PasswordReset::where('account_id', '=', $result->account_id)
            ->where('contact_id', '=', $result->contact_id)
            ->first();

        if ($passwordReset === null) {
            $passwordReset = new PasswordReset([
                'token' => uniqid(),
                'email' => $result->email_address,
                'contact_id' => $result->contact_id,
                'account_id' => $result->account_id,
            ]);
        } else {
            $passwordReset->token = uniqid();
        }

        $passwordReset->save();

        $languageService = App::make(LanguageService::class);
        $language = $languageService->getUserLanguage($request);

        try {
            Mail::send('emails.basic', [
                'greeting' => trans('emails.greeting', [], $language),
                'body' => trans('emails.passwordResetBody', [
                    'isp_name' => config('app.name'),
                    'portal_url' => config('app.url'),
                    'reset_link' => config('app.url').'/reset/'.$passwordReset->token,
                    'username' => $result->username,
                ], $language),
                'deleteIfNotYou' => trans('emails.deleteIfNotYou', [], $language),
            ], function ($m) use ($result, $request) {
                $m->from(config('customer_portal.from_address'), config('customer_portal.from_name'));
                $m->to($result->email_address, $result->email_address);
                $m->subject(
                    utrans('emails.passwordReset', ['companyName' => config('customer_portal.company_name')], $request)
                );
            });
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->withErrors(utrans('errors.emailSendFailed', [], $request));
        }

        return redirect()
            ->action([\App\Http\Controllers\AuthenticationController::class, 'index'])
            ->with('success', utrans('root.resetSent', [], $request));
    }

    /**
     * Show the password reset form, if valid.
     */
    public function showNewPasswordForm($token, Request $request): Factory|View|RedirectResponse
    {
        $passwordReset = PasswordReset::where('token', '=', $token)
            ->where('updated_at', '>=', Carbon::now('UTC')->subHours(24)->toDateTimeString())
            ->first();
        if ($passwordReset === null) {
            return redirect()
                ->action([\App\Http\Controllers\AuthenticationController::class, 'index'])
                ->withErrors(utrans('errors.resetTokenNotValid', [], $request));
        }

        return view('pages.root.new_password', compact('passwordReset'));
    }

    /**
     * Attempt to reset the password to a new value
     */
    public function updateContactWithNewPassword(PasswordUpdateRequest $request, $token): RedirectResponse
    {
        if ($this->getThrottleValue('password_update', hash('sha256', $request->getClientIp())) > 5) {
            return redirect()->back()->withErrors(utrans('errors.tooManyFailedPasswordResets', [], $request));
        }

        $passwordReset = PasswordReset::where('token', '=', trim($token))
            ->where('updated_at', '>=', Carbon::now('UTC')->subHours(24)->toDateTimeString())
            ->first();
        if ($passwordReset === null) {
            $this->incrementThrottleValue('password_update', hash('sha256', $token.$request->getClientIp()));

            return redirect()
                ->action([\App\Http\Controllers\AuthenticationController::class, 'showResetPasswordForm'])
                ->withErrors(utrans('errors.invalidToken', [], $request));
        }

        if ($passwordReset->email != $request->input('email')) {
            $this->incrementThrottleValue('password_update', hash('sha256', $token.$request->getClientIp()));

            return redirect()->back()->withErrors(utrans('errors.invalidEmailAddress', [], $request));
        }

        if (!$this->passwordPolicy->isPasswordValid($request->input('password'))) {
            return redirect()->back()->withErrors(utrans("errors.passwordIsTooWeak"))->withInput();
        }

        $contactController = new ContactController();
        try {
            $contact = $contactController->getContact($passwordReset->contact_id, $passwordReset->account_id);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(utrans('errors.couldNotFindAccount', [], $request));
        }
        try {
            $contactController->updateContactPassword($contact, $request->input('password'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(utrans('errors.failedToResetPassword', [], $request));
        }

        $passwordReset->delete();

        $this->resetThrottleValue('password_update', hash('sha256', $token.$request->getClientIp()));

        return redirect()
            ->action([\App\Http\Controllers\AuthenticationController::class, 'index'])
            ->with('success', utrans('register.passwordReset', [], $request));
    }

    /**
     * Log out the current session
     */
    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('authenticated');
        $request->session()->forget('user');

        return redirect()->action([\App\Http\Controllers\AuthenticationController::class, 'index']);
    }

    /*
     * Login throttling functions
     */

    private function generateLoginThrottleHash(AuthenticationRequest $request): string
    {
        return hash('sha256', $request->input('username').'_'.$request->getClientIp());
    }
}
