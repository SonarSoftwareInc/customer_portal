<?php

namespace SonarSoftware\CustomerPortalFramework\Models;

use Inacho\CreditCard as CreditCardValidator;
use InvalidArgumentException;

class CreditCard
{
    private $name;
    private $number;
    private $expiration_month;
    private $expiration_year;
    private $line1;
    private $city;
    private $state;
    private $zip;
    private $country;
    private $cvc;

    /**
     * When passing values into this function, the country must be a two character ISO country code. The state must be a subdivision returned from subdivisions($countryCode)
     *
     * CreditCardPayment constructor.
     * @param $values - An array of 'name', 'number', 'expiration_month', 'expiration_year', 'line1', 'city', 'state', 'zip', 'country'
     */
    public function __construct($values)
    {
        $this->validateInput($values);
        $this->storeInput($values);
    }

    /**
     * Get the name on the card.
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the credit card number.
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Get the expiration month.
     * @return mixed
     */
    public function getExpirationMonth()
    {
        return $this->expiration_month;
    }

    /**
     * Get the expiration year.
     * @return mixed
     */
    public function getExpirationYear()
    {
        return $this->expiration_year;
    }

    /**
     * Get line 1 of the address
     * @return mixed
     */
    public function getLine1()
    {
        return $this->line1;
    }

    /**
     * Get the city
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Get the state
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Get the ZIP
     * @return mixed
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Get the country
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    public function getCvc()
    {
        return $this->cvc;
    }

    /**
     * Validate the input to the constructor.
     * @param $values
     * @throws InvalidArgumentException
     */
    private function validateInput($values)
    {
        if (!array_key_exists("name",$values))
        {
            throw new InvalidArgumentException("You must supply a name.");
        }

        if (!array_key_exists("number",$values))
        {
            throw new InvalidArgumentException("You must supply a credit card number.");
        }

        if (!array_key_exists("expiration_month",$values))
        {
            throw new InvalidArgumentException("You must supply an expiration month");
        }

        if (!array_key_exists("expiration_year",$values))
        {
            throw new InvalidArgumentException("You must supply an expiration year.");
        }

        if (!array_key_exists("line1",$values))
        {
            throw new InvalidArgumentException("Line 1 of the address is missing.");
        }

        if (!array_key_exists("city",$values))
        {
            throw new InvalidArgumentException("The city of the address is missing.");
        }

        if (!array_key_exists("zip",$values))
        {
            throw new InvalidArgumentException("The ZIP/postal code of the address is missing.");
        }

        if (!array_key_exists("country",$values))
        {
            throw new InvalidArgumentException("The country of the address is missing.");
        }

        $card = CreditCardValidator::validCreditCard($values['number']);
        if ($card['valid'] !== true)
        {
            throw new InvalidArgumentException("The credit card number is not valid.");
        }

        if (CreditCardValidator::validCvc($values['cvc'], $card['type']) === false)
        {
            throw new InvalidArgumentException("The CVC is not valid.");
        }

        $month = sprintf("%02d", $values['expiration_month']);
        if (strlen($values['expiration_year']) !== 4)
        {
            throw new InvalidArgumentException("You must input a 4 digit year.");
        }

        if (!CreditCardValidator::validDate($values['expiration_year'], $month))
        {
            throw new InvalidArgumentException("Expiration date is not valid.");
        }

        if (!isset(countries()[$values['country']]))
        {
            throw new InvalidArgumentException($values['country'] . " is not a valid country.");
        }

        if (!in_array($values['country'],['US','CA']))
        {
            if (count(subdivisions($values['country'])) > 0 && !in_array($values['state'],subdivisions($values['country'])))
            {
                throw new InvalidArgumentException($values['state'] . " is not a valid state.");
            }
        }
        else
        {
            if (!isset(subdivisions($values['country'])[$values['state']]))
            {
                throw new InvalidArgumentException($values['state'] . " is not a valid state.");
            }
        }
    }

    /**
     * Store the input to private vars
     * @param $values
     */
    private function storeInput($values)
    {
        $this->name = trim($values['name']);
        $this->number = trim(str_replace(" ","",$values['number']));
        $this->expiration_month = sprintf("%02d", $values['expiration_month']);
        $this->expiration_year = trim($values['expiration_year']);
        $this->line1 = trim($values['line1']);
        $this->city = trim($values['city']);
        $this->state = isset($values['state']) ? trim($values['state']) : null;
        $this->zip = trim($values['zip']);
        $this->country = trim($values['country']);
        $this->cvc = trim($values['cvc']);
    }
}