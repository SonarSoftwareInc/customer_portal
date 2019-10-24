<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\SystemSetting;
use Exception;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use SonarSoftware\CustomerPortalFramework\Controllers\AccountAuthenticationController;
use SonarSoftware\CustomerPortalFramework\Controllers\ContactController;
use SonarSoftware\CustomerPortalFramework\Models\Contact;
use SonarSoftware\CustomerPortalFramework\Models\PhoneNumber;

class ProfileController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show()
    {
        $user = get_user();
        $contact = $this->getContact();
        //Format the phone numbers into something usable by the form
        $phoneNumbers = [
            PhoneNumber::WORK => null,
            PhoneNumber::MOBILE => null,
            PhoneNumber::HOME => null,
            PhoneNumber::FAX => null
        ];

        foreach ($contact->getPhoneNumbers() as $phoneNumber) {
            if ($phoneNumber != null) {
                $phoneNumbers[$phoneNumber->getType()] = $phoneNumber->getNumber();
            }
        }
        $country = SystemSetting::first()->country;

        return view("pages.profile.show", compact('user', 'contact', 'phoneNumbers', 'country'));
    }

    /**
     * Update the profile based on the form submission
     * @param ProfileUpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileUpdateRequest $request)
    {
        $contact = $this->getContact();

        $contact->setName($request->input('name'));
        $contact->setRole($request->input('role'));
        $contact->setEmailAddress($request->input('email_address'));

        try {
            $work = $contact->getPhoneNumber(PhoneNumber::WORK);
            $work->setNumber(preg_replace("/[^0-9]/", "", $request->input('work_phone')));
            $contact->setPhoneNumber($work);

            $mobile = $contact->getPhoneNumber(PhoneNumber::MOBILE);
            $mobile->setNumber(preg_replace("/[^0-9]/", "", $request->input('mobile_phone')));
            $contact->setPhoneNumber($mobile);

            $home = $contact->getPhoneNumber(PhoneNumber::HOME);
            $home->setNumber(preg_replace("/[^0-9]/", "", $request->input('home_phone')));
            $contact->setPhoneNumber($home);

            $fax = $contact->getPhoneNumber(PhoneNumber::FAX);
            $fax->setNumber(preg_replace("/[^0-9]/", "", $request->input('fax')));
            $contact->setPhoneNumber($fax);
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
        
        $contactController = new ContactController();
        try {
            $contactController->updateContact($contact);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(utrans("errors.failedToUpdateProfile"));
        }

        $this->clearProfileCache();
        return redirect()->action("ProfileController@show")->with('success', utrans("profile.profileUpdated"));
    }

    /**
     * Update the user password
     * @param UpdatePasswordRequest $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        //Validate that the current password is correct before allowing an update
        $accountAuthenticationController = new AccountAuthenticationController();
        try {
            $accountAuthenticationController->authenticateUser(get_user()->username, $request->input('current_password'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(utrans("errors.currentPasswordInvalid"));
        }

        $contact = $this->getContact();
        $contactController = new ContactController();
        try {
            $contactController->updateContactPassword($contact, $request->input('new_password'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }

        return redirect()->action("ProfileController@show")->with('success', utrans("profile.passwordUpdated"));
    }

    /**
     * Clear the profile cache.
     */
    private function clearProfileCache()
    {
        Cache::tags("profile.details")->forget(get_user()->contact_id);
        return;
    }

    /**
     * Get info on the current user via the Sonar API.
     * @return Contact
     * @throws \SonarSoftware\CustomerPortalFramework\Exceptions\ApiException
     */
    private function getContact()
    {
        if (!Cache::tags("profile.details")->has(get_user()->contact_id)) {
            $contactController = new ContactController();
            $contact = $contactController->getContact(get_user()->contact_id, get_user()->account_id);
            Cache::tags("profile.details")->put(get_user()->contact_id, $contact, 10);
        }
        return Cache::tags("profile.details")->get(get_user()->contact_id);
    }
}
