<?php

namespace SonarSoftware\CustomerPortalFramework\Models;

use Exception;
use InvalidArgumentException;
use SonarSoftware\CustomerPortalFramework\Helpers\StringFormatter;

class Ticket
{
    private $subject;
    private $description;
    private $open = true;
    private $ticketID = null;
    private $accountID;
    private $ticketGroupID = null;
    private $userID = null;
    private $priority = 4;
    private $inboundEmailAccountID;
    private $emailAddress;
    private $lastReplyIncoming = false;

    public function __construct($values)
    {
        $this->storeInput($values);
    }

    /**
     * Get the current subject
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set the subject.
     * @param $value
     */
    public function setSubject($value)
    {
        if (trim($value) == null)
        {
            throw new InvalidArgumentException("Subject cannot be empty.");
        }
        $this->subject = trim($value);
    }

    /**
     * Get the current description (first reply)
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the description.
     * @param $value
     */
    public function setDescription($value)
    {
        $this->description = trim($value);
    }


    /**
     * Get the current open status
     * @return boolean
     */
    public function getOpen()
    {
        return $this->open;
    }

    /**
     * Set the open status, accepts a boolean
     * @param $value
     */
    public function setOpen($value)
    {
        if (!is_bool($value))
        {
            throw new InvalidArgumentException($value . " must be true or false.");
        }
        $this->open = $value;
    }

    /**
     * Get the ticket ID
     * @return integer
     */
    public function getTicketID()
    {
        return $this->ticketID;
    }

    /**
     * Set the ticket ID to an integer
     * @param $value
     */
    public function setTicketID($value)
    {
        if (!is_numeric($value))
        {
            throw new InvalidArgumentException("Ticket ID must be numeric.");
        }
        $this->ticketID = (int)$value;
    }

    /**
     * Get the account ID
     * @return integer
     */
    public function getAccountID()
    {
        return $this->accountID;
    }

    /**
     * Set the account ID to an integer
     * @param $value
     */
    public function setAccountID($value)
    {
        if (!is_numeric($value))
        {
            throw new InvalidArgumentException("Account ID must be numeric.");
        }
        $this->accountID = (int)$value;
    }

    /**
     * Get the ticket group ID
     * @return integer
     */
    public function getTicketGroupID()
    {
        return $this->ticketGroupID;
    }

    /**
     * Set the ticket group ID to an integer
     * @param $value
     */
    public function setTicketGroupID($value)
    {
        if (!is_numeric($value))
        {
            throw new InvalidArgumentException("Ticket group ID must be numeric.");
        }
        $this->ticketGroupID = (int)$value;
    }

    /**
     * Get the user ID
     * @return integer
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * Set the user ID to an integer
     * @param $value
     */
    public function setUserID($value)
    {
        if (!is_numeric($value))
        {
            throw new InvalidArgumentException("User ID must be numeric.");
        }
        $this->userID = (int)$value;
    }

    /**
     * Get the priority
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set the priority to an integer
     * @param $value
     */
    public function setPriority($value)
    {
        if (!in_array($value,[1,2,3,4]))
        {
            throw new InvalidArgumentException("Priority must be between 1 and 4");
        }
        $this->priority = (int)$value;
    }

    /**
     * Get the inbound email account ID
     * @return integer
     */
    public function getInboundEmailAccountID()
    {
        return $this->inboundEmailAccountID;
    }

    /**
     * Set the inbound email account ID to an integer
     * @param $value
     */
    public function setInboundEmailAccountID($value)
    {
        if (!is_numeric($value))
        {
            throw new InvalidArgumentException("Inbound email account ID must be numeric.");
        }
        $this->inboundEmailAccountID = (int)$value;
    }

    /**
     * Get the email address of the customer
     * @return integer
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * Set the email address
     * @param $value
     */
    public function setEmailAddress($value)
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false)
        {
            throw new InvalidArgumentException($value . " is not a valid email address.");
        }
        $this->emailAddress = trim($value);
    }

    /**
     * Get the last reply incoming value
     * @return integer
     */
    public function getLastReplyIncoming()
    {
        return $this->lastReplyIncoming;
    }


    /**
     * Set the last reply incoming value
     * @param $value
     */
    public function setLastReplyIncoming($value)
    {
        if (!is_bool($value))
        {
            throw new InvalidArgumentException($value . " must be true or false.");
        }
        $this->lastReplyIncoming = (boolean)$value;
    }

    /**
     * Store the input from the constructor
     * @param $values
     */
    private function storeInput($values)
    {
        $requiredKeys = [
            'subject',
            'account_id',
            'inbound_email_account_id',
            'email_address',
        ];

        foreach ($requiredKeys as $key)
        {
            if (!array_key_exists($key, $values))
            {
                throw new InvalidArgumentException("$key is a required key in the input array.");
            }
        }

        foreach ($values as $key => $value)
        {
            if ($value === null)
            {
                continue;
            }
            $function = ucwords(StringFormatter::camelCase($key));
            try {
                call_user_func([$this, "set$function"], $value);
            }
            catch (Exception $e)
            {
                continue;
            }
        }
    }
}