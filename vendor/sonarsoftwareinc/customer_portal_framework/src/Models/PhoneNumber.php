<?php

namespace SonarSoftware\CustomerPortalFramework\Models;

use InvalidArgumentException;

class PhoneNumber
{
    /** Types */
    const WORK = "work";
    const HOME = "home";
    const MOBILE = "mobile";
    const FAX = "fax";

    private $number;
    private $type;
    private $extension = null;

    /**
     * PhoneNumber constructor.
     * @param $values - Array of 'number', 'type', and optionally 'extension'
     */
    public function __construct($values)
    {
        $this->storeInput($values);
    }

    /**
     * Get the phone number.
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Get the type.
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the extension, may be null.
     * @return mixed
     */
    public function getExtension()
    {
        return $this->getExtension();
    }

    /**
     * Set the phone number.
     * @param $number
     * @return mixed
     */
    public function setNumber($number)
    {
        if (!is_numeric($number) && $number != null)
        {
            throw new InvalidArgumentException("Number is not numeric.");
        }
        $this->number = preg_replace('/\D/', '', $number);
    }

    /**
     * Set the type.
     * @param $type
     * @return mixed
     */
    public function setType($type)
    {
        if (!in_array($type,[$this::WORK, $this::HOME, $this::MOBILE, $this::FAX]))
        {
            throw new InvalidArgumentException("Type is not a valid type. You must use one of the constants in the PhoneNumber class as the type.");
        }
        $this->type = $type;
    }

    /**
     * Set the extension, may be null.
     * @param $extension
     * @return mixed
     */
    public function setExtension($extension)
    {
        $this->extension = trim($extension);
    }

    /**
     * @param $values
     */
    private function storeInput($values)
    {
        $requiredKeys = [
            'number',
            'type',
        ];

        foreach ($requiredKeys as $key)
        {
            if (!array_key_exists($key, $values))
            {
                throw new InvalidArgumentException("$key is a required key in the input array.");
            }
        }

        $this->number = trim($values['number']);
        $this->type = trim($values['type']);
        if (array_key_exists("extension",$values))
        {
            $this->extension = trim($values['extension']);
        }
    }
}