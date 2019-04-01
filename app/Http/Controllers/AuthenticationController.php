<?php

namespace App\Http\Controllers;

use App\CreationToken;
use App\Http\Requests\AccountCreationRequest;
use App\Http\Requests\AuthenticationRequest;
use App\Http\Requests\LookupEmailRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\SendPasswordResetRequest;
use App\PasswordReset;
use App\Services\LanguageService;
use App\SystemSetting;
use App\Traits\Throttles;
use App\UsernameLanguage;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use SonarSoftware\CustomerPortalFramework\Controllers\AccountAuthenticationController;
use SonarSoftware\CustomerPortalFramework\Controllers\ContactController;
use SonarSoftware\CustomerPortalFramework\Exceptions\AuthenticationException;

class AuthenticationController extends Controller
{
    use Throttles;

    /**
     * Show the main login page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $systemSetting = SystemSetting::firstOrNew([
            'id' => 1
        ]);
        return view("pages.root.index", compact('systemSetting'));
    }

    /**
     * Authenticate against the Sonar API
     * @param AuthenticationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authenticate(AuthenticationRequest $request)
    {
        if ($this->getThrottleValue("login", $this->generateLoginThrottleHash($request)) > 10) {
            return redirect()->back()->withErrors(utrans("errors.tooManyFailedAuthenticationAttempts",[],$request));
        }
        $accountAuthenticationController = new AccountAuthenticationController();
        try {
            $result = $accountAuthenticationController->authenticateUser($request->input('username'), $request->input('password'));
            $request->session()->put('authenticated', true);
            $request->session()->put('user', $result);
        } catch (AuthenticationException $e) {
            $this->incrementThrottleValue("login", $this->generateLoginThrottleHash($request));
            $request->session()->forget('authenticated');
            $request->session()->forget('user');
            return redirect()->back()->withErrors(utrans("errors.loginFailed",[],$request));
        }

        $this->resetThrottleValue("login", $this->generateLoginThrottleHash($request));

        $usernameLanguage = UsernameLanguage::firstOrNew(['username' => $request->input('username')]);
        $usernameLanguage->language = $request->input('language');
        $usernameLanguage->save();

        return redirect()->action("BillingController@index");
    }

    /**
     * Show the registration form
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view("pages.root.register");
    }

    /**
     * Look up an email address to see if it can be used to create a new account.
     * @param LookupEmailRequest $request
     * @return $this
     */
    public function lookupEmail(LookupEmailRequest $request)
    {
        if ($this->getThrottleValue("email_lookup", md5($request->getClientIp())) > 10) {
            return redirect()->back()->withErrors(utrans("errors.tooManyFailedLookupAttempts",[],$request));
        }
        
        $accountAuthenticationController = new AccountAuthenticationController();
        try {
            $result = $accountAuthenticationController->lookupEmail($request->input('email'));
        } catch (Exception $e) {
            $this->incrementThrottleValue("email_lookup", md5($request->getClientIp()));
            Log::info($e->getMessage());
            return redirect()->back()->withErrors(utrans("errors.emailLookupFailed",[],$request));
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
                'greeting' => trans("emails.greeting",[],$language),
                'body' => trans("emails.accountCreateBody", [
                    'portal_url' => config("app.url"),
                    'creation_link' => config("app.url") . "/create/" . $creationToken->token,
                ],$language),
                'deleteIfNotYou' => trans("emails.deleteIfNotYou",[],$language),
            ], function ($m) use ($result, $request) {
                $m->from(config("customer_portal.from_address"), config("customer_portal.from_name"));
                $m->to($result->email_address, $result->email_address)
                    ->subject(utrans("emails.createAccount", ['companyName' => config("customer_portal.company_name")],$request));
            });
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(utrans("errors.emailSendFailed",[],$request));
        }

        $this->resetThrottleValue("email_lookup", md5($request->getClientIp()));
        return redirect()->action("AuthenticationController@index")->with('success', utrans("root.emailFound",[],$request));
    }

    /**
     * Show the account creation form
     * @param $token
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showCreationForm($token, Request $request)
    {
        $creationToken = CreationToken::where('token', '=', trim($token))
            ->where('updated_at', '>=', Carbon::now("UTC")->subHours(24)->toDateTimeString())
            ->first();
        if ($creationToken === null) {
            return redirect()->action("AuthenticationController@showRegistrationForm")->withErrors(utrans("errors.invalidToken",[],$request));
        }
        return view("pages.root.create", compact('creationToken'));
    }

    /**
     * Create a new account
     * @param AccountCreationRequest $request
     * @param $token
     * @return $this
     */
    public function createAccount(AccountCreationRequest $request, $token)
    {
        if ($this->getThrottleValue("create_account", md5($token . $request->getClientIp())) > 10) {
            return redirect()->back()->withErrors(utrans("errors.tooManyFailedCreationAttempts",[],$request));
        }

        $creationToken = CreationToken::where('token', '=', trim($token))
            ->where('updated_at', '>=', Carbon::now("UTC")->subHours(24)->toDateTimeString())
            ->first();
        if ($creationToken === null) {
            $this->incrementThrottleValue("email_lookup", md5($token . $request->getClientIp()));
            return redirect()->action("AuthenticationController@showRegistrationForm")->withErrors(utrans("errors.invalidToken",[],$request));
        }
        
        if (strtolower(trim($creationToken->email)) != strtolower(trim($request->input('email')))) {
            $this->incrementThrottleValue("email_lookup", md5($token . $request->getClientIp()));
            return redirect()->back()->withErrors(utrans("errors.invalidEmailAddress",[],$request))->withInput();
        }
        
        $accountAuthenticationController = new AccountAuthenticationController();
        try {
            $accountAuthenticationController->createUser($creationToken->account_id, $creationToken->contact_id, $request->input('username'), $request->input('password'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }

        $creationToken->delete();

        $this->resetThrottleValue("email_lookup", md5($token . $request->getClientIp()));
        return redirect()->action("AuthenticationController@index")->with('success', utrans("register.accountCreated",[],$request));
    }

    /**
     * Show the reset password form
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetPasswordForm()
    {
        return view("pages.root.reset");
    }

    /**
     * Check for, and email a password reset if email is valid.
     * @param SendPasswordResetRequest $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function sendResetEmail(SendPasswordResetRequest $request)
    {
        if ($this->getThrottleValue("password_reset", md5($request->getClientIp())) > 5) {
            return redirect()->back()->withErrors(utrans("errors.tooManyPasswordResetRequests",[],$request));
        }

        $accountAuthenticationController = new AccountAuthenticationController();
        try {
            $result = $accountAuthenticationController->lookupEmail($request->input('email'), false);
        } catch (Exception $e) {
            $this->incrementThrottleValue("password_reset", md5($request->getClientIp()));
            return redirect()->back()->withErrors(utrans("errors.resetLookupFailed",[],$request));
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
                'greeting' => trans("emails.greeting",[],$language),
                'body' => trans("emails.passwordResetBody", [
                    'portal_url' => config("app.url"),
                    'reset_link' => config("app.url") . "/reset/" . $passwordReset->token,
                    'username' => $result->username,
                ],$language),
                'deleteIfNotYou' => trans("emails.deleteIfNotYou",[],$language),
            ], function ($m) use ($result, $request) {
                $m->from(config("customer_portal.from_address"), config("customer_portal.from_name"));
                $m->to($result->email_address, $result->email_address);
                $m->subject(utrans("emails.passwordReset", ['companyName' => config("customer_portal.company_name")],$request));
            });
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(utrans("errors.emailSendFailed",[],$request));
        }

        return redirect()->action("AuthenticationController@index")->with('success', utrans("root.resetSent",[],$request));
    }

    /**
     * Show the password reset form, if valid.
     * @param $token
     * @param Request $request
     * @return $this
     */
    public function showNewPasswordForm($token, Request $request)
    {
        $passwordReset = PasswordReset::where('token', '=', $token)
            ->where('updated_at', '>=', Carbon::now("UTC")->subHours(24)->toDateTimeString())
            ->first();
        if ($passwordReset === null) {
            return redirect()->action("AuthenticationController@index")->withErrors(utrans("errors.resetTokenNotValid",[],$request));
        }

        return view("pages.root.new_password", compact('passwordReset'));
    }

    /**
     * Attempt to reset the password to a new value
     * @param PasswordUpdateRequest $request
     * @param $token
     * @return $this
     */
    public function updateContactWithNewPassword(PasswordUpdateRequest $request, $token)
    {
        if ($this->getThrottleValue("password_update", md5($request->getClientIp())) > 5) {
            return redirect()->back()->withErrors(utrans("errors.tooManyFailedPasswordResets",[],$request));
        }

        $passwordReset = PasswordReset::where('token', '=', trim($token))
            ->where('updated_at', '>=', Carbon::now("UTC")->subHours(24)->toDateTimeString())
            ->first();
        if ($passwordReset === null) {
            $this->incrementThrottleValue("password_update", md5($token . $request->getClientIp()));
            return redirect()->action("AuthenticationController@showResetPasswordForm")->withErrors(utrans("errors.invalidToken",[],$request));
        }

        if ($passwordReset->email != $request->input('email')) {
            $this->incrementThrottleValue("password_update", md5($token . $request->getClientIp()));
            return redirect()->back()->withErrors(utrans("errors.invalidEmailAddress",[],$request));
        }
        
        $contactController = new ContactController();
        try {
            $contact = $contactController->getContact($passwordReset->contact_id, $passwordReset->account_id);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(utrans("errors.couldNotFindAccount",[],$request));
        }
        try {
            $contactController->updateContactPassword($contact, $request->input('password'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(utrans("errors.failedToResetPassword",[],$request));
        }

        $passwordReset->delete();

        $this->resetThrottleValue("password_update", md5($token . $request->getClientIp()));
        return redirect()->action("AuthenticationController@index")->with('success', utrans("register.passwordReset",[],$request));
    }
    
    /**
     * Log out the current session
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $request->session()->forget('authenticated');
        $request->session()->forget('user');

        return redirect()->action("AuthenticationController@index");
    }


    /*
     * Login throttling functions
     */

    /**
     * @param AuthenticationRequest $request
     * @return string
     */
    private function generateLoginThrottleHash(AuthenticationRequest $request)
    {
        return md5($request->input('username') . "_" . $request->getClientIp());
    }
}
