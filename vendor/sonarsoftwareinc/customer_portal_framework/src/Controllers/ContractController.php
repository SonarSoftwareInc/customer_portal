<?php

namespace SonarSoftware\CustomerPortalFramework\Controllers;

use SonarSoftware\CustomerPortalFramework\Helpers\HttpHelper;
use SonarSoftware\CustomerPortalFramework\Models\Contract;

class ContractController
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
     * This returns an array of 'Contract' objects
     * @param int $accountID
     * @param int $page
     * @return array
     */
    public function getContracts(int $accountID, int $page = 1)
    {
        $result = $this->httpHelper->get("/accounts/" . intval($accountID) . "/contracts", $page);
        $contracts = [];

        foreach ($result as $datum)
        {
            $contract = new Contract((array)$datum);
            array_push($contracts,$contract);
        }

        return $contracts;
    }

    /**
     * @param int $accountID
     * @param int $contractID
     * @return Contract
     */
    public function getContract(int $accountID, int $contractID)
    {
        $result = $this->httpHelper->get("/accounts/" . intval($accountID) . "/contracts/" . intval($contractID));
        return new Contract((array)$result);
    }

    /**
     * @param int $accountID
     * @param int $contractID
     * @return mixed
     */
    public function getSignedContractAsBase64(int $accountID, int $contractID)
    {
        $result = $this->httpHelper->get("/accounts/" . intval($accountID) . "/contracts/" . intval($contractID) . "/base64");
        return $result->base64;
    }
}