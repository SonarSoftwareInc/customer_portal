<?php

namespace SonarSoftware\CustomerPortalFramework\Models;

use Carbon\Carbon;
use Dotenv\Dotenv;
use InvalidArgumentException;
use SonarSoftware\CustomerPortalFramework\Helpers\StringFormatter;

class Contract
{
    /**
     * Contract constructor.
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (file_exists(__DIR__ . '/../../../../../.env')) {
            $dotenv = new Dotenv(__DIR__ . '/../../../../../');
            $this->loadDotEnv($dotenv);
        } else if (file_exists(__DIR__ . '/../.env')) {
            $dotenv = new Dotenv(__DIR__ . '/../');
            $this->loadDotEnv($dotenv);
        }

        $this->storeInput($values);
    }

    private function loadDotEnv(Dotenv $dotenv)
    {
        $dotenv->load();
        $dotenv->required([
            'SONAR_URL',
        ])->notEmpty();
    }

    private $id = null;
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $value
     */
    private function setId($value)
    {
        if (!is_int($value))
        {
            throw new InvalidArgumentException("ID must be an integer.");
        }
        if ($value < 1)
        {
            throw new InvalidArgumentException("ID must be 1 or greater.");
        }
        $this->id = $value;
    }

    private $contractName = null;
    public function getContractName()
    {
        return $this->contractName;
    }

    /**
     * @param $value
     */
    private function setContractName($value)
    {
        $this->contractName = $value;
    }

    private $contractText = null;
    public function getContractText()
    {
        return $this->contractText;
    }

    private function setContractText($value)
    {
        $this->contractText = $value;
    }

    private $contactID = null;
    public function getContactId()
    {
        return $this->contactID;
    }

    private function setContactId($value)
    {
        if (!is_int($value))
        {
            throw new InvalidArgumentException("Contact ID must be an integer.");
        }
        if ($value < 1)
        {
            throw new InvalidArgumentException("Contact ID must be 1 or greater.");
        }
        $this->contactID = $value;
    }

    private $termInMonths = null;
    public function getTermInMonths()
    {
        return $this->termInMonths;
    }

    private function setTermInMonths($value)
    {
        if (!$value)
        {
            return;
        }

        if (!is_int($value))
        {
            throw new InvalidArgumentException("Term in months be an integer.");
        }
        if ($value < 1)
        {
            throw new InvalidArgumentException("Term in months must be 1 or greater.");
        }
        $this->termInMonths = $value;
    }

    private $acceptanceDatetime = null;
    public function getAcceptanceDatetime()
    {
        return $this->acceptanceDatetime;
    }

    private function setAcceptanceDatetime($value)
    {
        if (!$value)
        {
            return;
        }

        $carbon = new Carbon($value);
        $this->acceptanceDatetime = $carbon->toDateTimeString();
    }

    private $expirationDate = null;
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    private function setExpirationDate($value)
    {
        if (!$value)
        {
            return;
        }

        $carbon = new Carbon($value);
        $this->expirationDate = $carbon->toDateTimeString();
    }

    private $customMessage = null;
    public function getCustomMessage()
    {
        return $this->customMessage;
    }

    private function setCustomMessage($value)
    {
        $this->customMessage = trim($value);
    }

    private $signerName = null;
    public function getSignerName()
    {
        return $this->signerName;
    }

    private function setSignerName($value)
    {
        $this->signerName = trim($value);
    }

    private $signerIp = null;
    public function getSignerIp()
    {
        return $this->signerIp;
    }

    private function setSignerIp($value)
    {
        if (!$value)
        {
            return;
        }

        if (!filter_var($value,FILTER_VALIDATE_IP))
        {
            throw new InvalidArgumentException($value . " is not a valid IP address.");
        }
        $this->signerIp = $value;
    }

    private $uniqueLinkKey = null;
    public function getUniqueLinkKey($value)
    {
        return $this->uniqueLinkKey;
    }

    private function setUniqueLinkKey($value)
    {
        $this->uniqueLinkKey = $value;
    }


    public function generateSignatureLink()
    {
        return getenv('SONAR_URL') . "/contract_signing/" . $this->uniqueLinkKey;
    }

    /**
     * @param array $values
     */
    private function storeInput(array $values)
    {
        foreach ($values as $key => $value)
        {
            if ($value === null)
            {
                continue;
            }
            $function = ucwords(StringFormatter::camelCase($key));
            call_user_func([$this, "set$function"], $value);
        }
    }
}