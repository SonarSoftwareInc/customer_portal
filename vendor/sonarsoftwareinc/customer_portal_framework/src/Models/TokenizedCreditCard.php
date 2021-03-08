<?php


namespace SonarSoftware\CustomerPortalFramework\Models;

use Inacho\CreditCard as CreditCardValidator;
use InvalidArgumentException;

class TokenizedCreditCard
{
    private $token;
    private $customer_id;
    private $identifier;
    private $expiration_year;
    private $expiration_month;
    private $card_type;
    private $line1;
    private $city;
    private $state;
    private $zip;
    private $country;
    private $name;

    /**
     * When passing values into this function, the country must be a two
     * character ISO country code. The state must be a subdivision returned
     * from subdivisions($countryCode)
     *
     * CreditCardPayment constructor.
     * @param $values - An array of 'token', 'customer_id', 'identifier',
     * 'expiration_date', 'expiration_month', 'card_type', 'line1', 'city',
     * 'state', 'zip', 'country', 'name'.
     */
    public function __construct($values)
    {
        $this->validateInput($values);
        $this->storeInput($values);
    }

	/**
	 * Get the cardholder name
	 * @return mixed
	 */
	public function getName()
	{
        return $this->name;
	}

    /**
     * Get the card token.
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Get the card customer ID.
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->customer_id;
    }

    /**
     * Get the card identifier (ex. last 4 numbers).
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
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
     * Get the expiration month.
     * @return mixed
     */
    public function getExpirationMonth()
    {
        return $this->expiration_month;
    }

    /**
     * Get the card type (amex, visa, etc.)
     * @return mixed
     */
    public function getCardType()
    {
        return $this->card_type;
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

    /**
     * Validate the input to the constructor.
     * @param $values
     * @throws InvalidArgumentException
     */
    private function validateInput($values)
    {

        if (!array_key_exists("token", $values)) {
            throw new InvalidArgumentException("You must supply a credit card token.");
        }

        if (!array_key_exists("card_type", $values)) {
            throw new InvalidArgumentException("You must supply a card type.");
        }

        if (!array_key_exists("identifier", $values)) {
            throw new InvalidArgumentException("You must supply a credit card identifier.");
        }

        if (!array_key_exists("expiration_year", $values)) {
            throw new InvalidArgumentException("You must supply an expiration year.");
        }

        if (!array_key_exists("expiration_month", $values)) {
            throw new InvalidArgumentException("You must supply an expiration month");
        }

        if (!array_key_exists("line1", $values)) {
            throw new InvalidArgumentException("Line 1 of the address is missing.");
        }

        if (!array_key_exists("city", $values)) {
            throw new InvalidArgumentException("The city of the address is missing.");
        }

        if (!array_key_exists("state", $values)) {
            throw new InvalidArgumentException("The state of the address is missing.");
        }

        if (!array_key_exists("zip", $values)) {
            throw new InvalidArgumentException("The ZIP/postal code of the address is missing.");
        }

        if (!array_key_exists("country", $values)) {
            throw new InvalidArgumentException("The country of the address is missing.");
        }

        if (!array_key_exists("name", $values)) {
            throw new InvalidArgumentException("You must supply a name.");
        }

        $month = sprintf("%02d", $values['expiration_month']);
        if (strlen($values['expiration_year']) !== 4) {
            throw new InvalidArgumentException("You must input a 4 digit year.");
        }

        if (!CreditCardValidator::validDate($values['expiration_year'], $month)) {
            throw new InvalidArgumentException("Expiration date is not valid.");
        }

        if (!isset(countries()[$values['country']])) {
            throw new InvalidArgumentException($values['country'] . " is not a valid country.");
        }

        if (!in_array($values['country'], ['US', 'CA'])) {
            if (count(subdivisions($values['country'])) > 0 && !in_array($values['state'], subdivisions($values['country']))) {
                throw new InvalidArgumentException($values['state'] . " is not a valid state.");
            }
        } else {
            if (!isset(subdivisions($values['country'])[$values['state']])) {
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
        $this->token = trim($values['token']);
        $this->customer_id = trim($values['customer_id']);
        $this->identifier = sprintf("%04d", $values['identifier']);
        $this->expiration_month = sprintf("%02d", $values['expiration_month']);
        $this->expiration_year = trim($values['expiration_year']);
        $this->line1 = trim($values['line1']);
        $this->city = trim($values['city']);
        $this->state = isset($values['state']) ? trim($values['state']) : null;
        $this->zip = trim($values['zip']);
        $this->country = trim($values['country']);
        $this->name = trim($values['name']);
        $this->card_type = trim($values['card_type']);
    }
}
