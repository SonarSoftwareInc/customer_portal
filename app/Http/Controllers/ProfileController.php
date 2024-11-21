<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\PasswordPolicy;
use App\SystemSetting;
use App\Services\ContactService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\View\Factory;
use Illuminate\View\View;
use SonarSoftware\CustomerPortalFramework\Controllers\AccountAuthenticationController;
use SonarSoftware\CustomerPortalFramework\Controllers\ContactController;
use SonarSoftware\CustomerPortalFramework\Models\PhoneNumber;
use SonarSoftware\CustomerPortalFramework\Models\Contact;

class ProfileController extends Controller
{
    private $passwordPolicy;
    protected $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->passwordPolicy = new PasswordPolicy();
        $this->contactService = $contactService;
    }

    public function show(): Factory|View
    {
        $user = get_user();
        $contact = $this->getContact();
        //Format the phone numbers into something usable by the form
        $phoneNumbers = [
            PhoneNumber::WORK => null,
            PhoneNumber::MOBILE => null,
            PhoneNumber::HOME => null,
            PhoneNumber::FAX => null,
        ];

        foreach ($contact->getPhoneNumbers() as $phoneNumber) {
            if ($phoneNumber != null) {
                $phoneNumbers[$phoneNumber->getType()] = $phoneNumber->getNumber();
            }
        }
        $country = SystemSetting::first()->country;

        return view('pages.profile.show', compact('user', 'contact', 'phoneNumbers', 'country'));
    }

    /**
     * Update the profile based on the form submission
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $contact = $this->getContact();

        $contact->setName($request->input('name'));
        $contact->setEmailAddress($request->input('email_address'));

        try {
            $work = $contact->getPhoneNumber(PhoneNumber::WORK);
            $work->setNumber(preg_replace('/[^0-9]/', '', $request->input('work_phone')));
            $contact->setPhoneNumber($work);

            $mobile = $contact->getPhoneNumber(PhoneNumber::MOBILE);
            $mobile->setNumber(preg_replace('/[^0-9]/', '', $request->input('mobile_phone')));
            $contact->setPhoneNumber($mobile);

            $home = $contact->getPhoneNumber(PhoneNumber::HOME);
            $home->setNumber(preg_replace('/[^0-9]/', '', $request->input('home_phone')));
            $contact->setPhoneNumber($home);

            $fax = $contact->getPhoneNumber(PhoneNumber::FAX);
            $fax->setNumber(preg_replace('/[^0-9]/', '', $request->input('fax')));
            $contact->setPhoneNumber($fax);
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->getMessage());
        }

        $contactController = new ContactController();
        try {
            $contactController->updateContact($contact);
        } catch (Exception $e) {
            Log::error($e);
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($this->convertErrorMessage($e->getMessage()));
        }

        $this->clearProfileCache();

        return redirect()
            ->action([\App\Http\Controllers\ProfileController::class, 'show'])
            ->with('success', utrans('profile.profileUpdated'));
    }

    /**
     * Update the user password
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        //Validate that the current password is correct before allowing an update
        $accountAuthenticationController = new AccountAuthenticationController();
        try {
            $accountAuthenticationController->authenticateUser(
                get_user()->username,
                $request->input('current_password')
            );
        } catch (Exception $e) {
            return redirect()->back()->withErrors(utrans('errors.currentPasswordInvalid'));
        }

        if (!$this->passwordPolicy->isPasswordValid($request->input('new_password'))) {
            return redirect()->back()->withErrors(utrans("errors.passwordIsTooWeak"))->withInput();
        }

        $contact = $this->getContact();
        $contactController = new ContactController();
        try {
            $contactController->updateContactPassword($contact, $request->input('new_password'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }

        return redirect()
            ->action([\App\Http\Controllers\ProfileController::class, 'show'])
            ->with('success', utrans('profile.passwordUpdated'));
    }

    /**
     * Clear the profile cache.
     */
    private function clearProfileCache(): void
    {
        Cache::tags('profile.details')->forget(get_user()->contact_id);
    }

    private function getContact(): Contact
    {
        return $this->contactService->getContact();
    }

    private function convertErrorMessage(string $message)
    {
        //Trying to save a phone number type that doesn't exist in Sonar will result in this message. The caching above
        //in getContact() compounds the problem sending old phone number types after they've been edited.
        if (strpos($message, 'The Phone Number Type is invalid: ') === 0) {
            return utrans("errors.phoneNumberNotValid");
        }

        return utrans("errors.failedToUpdateProfile");
    }
}
