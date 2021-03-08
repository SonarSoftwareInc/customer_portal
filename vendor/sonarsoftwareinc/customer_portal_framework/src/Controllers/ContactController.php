<?php

namespace SonarSoftware\CustomerPortalFramework\Controllers;

use SonarSoftware\CustomerPortalFramework\Helpers\HttpHelper;
use SonarSoftware\CustomerPortalFramework\Models\Contact;
use SonarSoftware\CustomerPortalFramework\Models\PhoneNumber;

class ContactController
{
    private $httpHelper;
    /**
     * AccountAuthenticationController constructor.
     */
    public function __construct()
    {
        $this->httpHelper = new HttpHelper();
    }

    /*
     * GET functions
     */

    /**
     * Get details on a contact. See https://sonar.software/apidoc/#api-Account_Contacts-GetAccountContact
     * @param $contactID
     * @param $accountID
     * @return Contact
     * @throws \SonarSoftware\CustomerPortalFramework\Exceptions\ApiException
     */
    public function getContact($contactID, $accountID)
    {
        $result = $this->httpHelper->get("accounts/" . intval($accountID) . "/contacts/" . intval($contactID));

        $phoneNumbers = [];
        if (is_object($result->phone_numbers))
        {
            foreach ($result->phone_numbers as $type => $phoneNumberResult)
            {
                $phoneNumber = new PhoneNumber([
                    'number' => $phoneNumberResult->number,
                    'extension' => $phoneNumberResult->extension,
                    'type' => $type,
                ]);
                array_push($phoneNumbers, $phoneNumber);
            }
        }

        $contact = new Contact([
            'contact_id' => $result->id,
            'account_id' => intval($accountID),
            'name' => $result->name,
            'role' => $result->role,
            'email_address' => $result->email_address,
            'phone_numbers' => $phoneNumbers,
        ]);

        return $contact;
    }

    /*
     * POST/PATCH functions
     */

    /**
     * Update an existing contact
     * @param Contact $contact
     * @return mixed
     * @throws \SonarSoftware\CustomerPortalFramework\Exceptions\ApiException
     */
    public function updateContact(Contact $contact)
    {
        return $this->httpHelper->patch("accounts/" . $contact->getAccountID() . "/contacts/" . $contact->getContactID(), [
            'name' => $contact->getName(),
            'email_address' => $contact->getEmailAddress(),
            'phone_numbers' => $contact->getPhoneNumbersFormatted(),
        ]);
    }

    /**
     * Update the password for a contact
     * @param Contact $contact
     * @param $newPassword
     * @return mixed
     * @throws \SonarSoftware\CustomerPortalFramework\Exceptions\ApiException
     */
    public function updateContactPassword(Contact $contact, $newPassword)
    {
        return $this->httpHelper->patch("accounts/" . $contact->getAccountID() . "/contacts/" . $contact->getContactID(), [
            'password' => $newPassword,
        ]);
    }
}