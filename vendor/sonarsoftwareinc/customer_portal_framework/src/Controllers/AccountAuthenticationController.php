<?php

namespace SonarSoftware\CustomerPortalFramework\Controllers;

use Exception;
use InvalidArgumentException;
use SonarSoftware\CustomerPortalFramework\Exceptions\ApiException;
use SonarSoftware\CustomerPortalFramework\Exceptions\AuthenticationException;
use SonarSoftware\CustomerPortalFramework\Helpers\HttpHelper;

class AccountAuthenticationController
{
    private $httpHelper;
    /**
     * AccountAuthenticationController constructor.
     */
    public function __construct()
    {
        $this->httpHelper = new HttpHelper();
    }

    /**
     * Test a username and password against the Sonar customer portal API (see https://sonar.software/apidoc/index.html#api-Customer_Portal-PostCustomerPortalCredentials)
     * @param $username
     * @param $password
     * @return mixed
     * @throws AuthenticationException
     */
    public function authenticateUser($username, $password)
    {
        try
        {
            return $this->httpHelper->post("customer_portal/auth", ['username' => $username, 'password' => $password]);
        }
        catch (ApiException $e)
        {
            throw new AuthenticationException($e->getMessage());
        }
    }

    /**
     * Look up an email address and see if it is valid to create a new account, or can be used for a password reset (see https://sonar.software/apidoc/index.html#api-Customer_Portal-PostCustomerPortalEmailLookup)
     * @param $emailAddress
     * @param bool $checkIfAvailable - If you want to create a new account with this email, set this to true. If you're looking up for a password reset, set this to false
     * @return mixed
     */
    public function lookupEmail($emailAddress, $checkIfAvailable = true)
    {
        try
        {
            return $this->httpHelper->post("customer_portal/email_lookup", ['email_address' => trim($emailAddress), 'check_if_available' => (boolean)$checkIfAvailable]);
        }
        catch (ApiException $e)
        {
            throw new InvalidArgumentException($e->getMessage());
        }
    }

    /**
     * Create a username and password for a contact
     * @param $accountID
     * @param $contactID
     * @param $username
     * @param $password
     * @return mixed
     */
    public function createUser($accountID, $contactID, $username, $password)
    {
        try
        {
            return $this->httpHelper->patch("accounts/" . intval($accountID) . "/contacts/" . intval($contactID),[
                'username' => $username,
                'password' => $password
            ]);
        }
        catch (ApiException $e)
        {
            throw new InvalidArgumentException($e->getMessage());
        }
    }
}