<?php

namespace SonarSoftware\CustomerPortalFramework\Models;

use InvalidArgumentException;

class BankAccount
{
    private $accountNumber;
    private $routingNumber;
    private $name;
    private $type;

    public function __construct($values)
    {
        $this->validateInput($values);
        $this->storeInput($values);
    }

    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    public function getRoutingNumber()
    {
        return $this->routingNumber;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $input
     */
    private function validateInput($input)
    {
        if (!is_array($input))
        {
            throw new InvalidArgumentException("Input must be an array.");
        }

        if (!isset($input['account_number']))
        {
            throw new InvalidArgumentException("account_number must be set.");
        }

        if (!isset($input['routing_number']))
        {
            throw new InvalidArgumentException("routing_number must be set.");
        }

        if (!isset($input['name']))
        {
            throw new InvalidArgumentException("name must be set.");
        }

        if (!isset($input['type']))
        {
            throw new InvalidArgumentException("type must be set.");
        }

        if (strlen($input['routing_number']) != 9)
        {
            throw new InvalidArgumentException("Routing number must be 9 digits.");
        }

        if (!in_array($input['type'],['checking','savings']))
        {
            throw new InvalidArgumentException("type must be one of 'checking' or 'savings'");
        }

        if (!is_numeric($input['account_number']))
        {
            throw new InvalidArgumentException("account_number must be numeric.");
        }

        if (!is_numeric($input['routing_number']))
        {
            throw new InvalidArgumentException("routing_number must be numeric.");
        }
    }

    /**
     * @param $input
     */
    private function storeInput($input)
    {
        $this->accountNumber = trim($input['account_number']);
        $this->routingNumber = trim($input['routing_number']);
        $this->name = trim($input['name']);
        $this->type = trim($input['type']);
    }
}