<?php

namespace SonarSoftware\CustomerPortalFramework\Models;

use InvalidArgumentException;

class Contact
{
    private $contactID;
    private $accountID;
    private $name;
    private $role;
    private $emailAddress;
    private $phoneNumbers = [
        PhoneNumber::HOME => null,
        PhoneNumber::MOBILE => null,
        PhoneNumber::WORK => null,
        PhoneNumber::FAX => null
    ];

    /**
     * Contact constructor.
     * @param $values
     */
    public function __construct($values)
    {
        $this->phoneNumbers = [
            PhoneNumber::HOME => new PhoneNumber(['number' => null, 'type' => PhoneNumber::HOME]),
            PhoneNumber::MOBILE => new PhoneNumber(['number' => null, 'type' => PhoneNumber::MOBILE]),
            PhoneNumber::WORK => new PhoneNumber(['number' => null, 'type' => PhoneNumber::WORK]),
            PhoneNumber::FAX => new PhoneNumber(['number' => null, 'type' => PhoneNumber::FAX]),
        ];

        $this->storeInput($values);
    }

    /**
     * Get the contact ID
     * @return mixed
     */
    public function getContactID()
    {
        return $this->contactID;
    }

    /**
     * Get the account ID
     * @return mixed
     */
    public function getAccountID()
    {
        return $this->accountID;
    }

    /**
     * Get the name
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the role
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Get the email address
     * @return mixed
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * Returns an array of PhoneNumber objects
     * @return array
     */
    public function getPhoneNumbers()
    {
        return $this->phoneNumbers;
    }

    /**
     * Get a specific type of phone number
     * @param $type
     * @return mixed
     */
    public function getPhoneNumber($type)
    {
        if (!in_array($type, [PhoneNumber::WORK, PhoneNumber::HOME, PhoneNumber::FAX, PhoneNumber::MOBILE]))
        {
            throw new InvalidArgumentException($type . " is not a valid phone number type.");
        }

        return $this->phoneNumbers[$type];
    }

    /**
     * Format the phone numbers for the API.
     * @return array
     */
    public function getPhoneNumbersFormatted()
    {
        $formattedArray = [];
        foreach ($this->phoneNumbers as $key => $phoneNumber)
        {
            if($phoneNumber->getNumber() != null) {
                $formattedArray[$key] = [
                    'number' => $phoneNumber->getNumber(),
                    'extension' => null
                ];
            }
        }

        return $formattedArray;
    }

    /**
     * Set the contact ID
     * @param $contactID
     * @return mixed
     */
    public function setContactID($contactID)
    {
        if (trim($contactID) == null)
        {
            throw new InvalidArgumentException("You must supply a contact ID.");
        }

        if (!is_numeric($contactID))
        {
            throw new InvalidArgumentException("Contact ID must be numeric.");
        }
        $this->contactID = intval($contactID);
    }

    /**
     * Set the account ID
     * @param $accountID
     * @return mixed
     */
    public function setAccountID($accountID)
    {
        if (trim($accountID) == null)
        {
            throw new InvalidArgumentException("You must supply an account ID.");
        }

        if (!is_numeric($accountID))
        {
            throw new InvalidArgumentException("Account ID must be numeric.");
        }
        $this->accountID = intval($accountID);
    }

    /**
     * Set the name
     * @param $name
     * @return mixed
     */
    public function setName($name)
    {
        if (trim($name) == null)
        {
            throw new InvalidArgumentException("You must supply a name.");
        }
        $this->name = $name;
    }

    /**
     * Set the role
     * @param $role
     * @return mixed
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * Set the email address
     * @param $emailAddress
     * @return mixed
     */
    public function setEmailAddress($emailAddress)
    {
        if ($emailAddress != null)
        {
            if (filter_var(trim($emailAddress), FILTER_VALIDATE_EMAIL) === false)
            {
                throw new InvalidArgumentException($emailAddress . " is not a valid email address.");
            }
        }

        $this->emailAddress = $emailAddress;
    }

    /**
     * Set a phone number on the contact.
     * @param PhoneNumber $phoneNumber
     * @return array
     */
    public function setPhoneNumber(PhoneNumber $phoneNumber)
    {
        if (!is_a($phoneNumber, PhoneNumber::class, false))
        {
            throw new InvalidArgumentException("The phone number is not an instance of the PhoneNumber class.");
        }

        $this->phoneNumbers[$phoneNumber->getType()] = $phoneNumber;
    }

    /**
     * @param $values
     */
    public function storeInput($values)
    {
        $requiredKeys = [
            'contact_id',
            'account_id',
            'name',
            'role',
            'email_address',
            'phone_numbers'
        ];

        foreach ($requiredKeys as $key)
        {
            if (!array_key_exists($key, $values))
            {
                throw new InvalidArgumentException("$key is a required key in the input array.");
            }
        }

        if (!is_array($values['phone_numbers']))
        {
            throw new InvalidArgumentException("The phone_numbers value must be an array.");
        }

        $this->setContactID($values['contact_id']);
        $this->setAccountID($values['account_id']);
        $this->setName($values['name']);
        $this->setRole($values['role']);
        $this->setEmailAddress($values['email_address']);
        foreach ($values['phone_numbers'] as $phoneNumber)
        {
            $this->setPhoneNumber($phoneNumber);
        }

    }
}