<?php

namespace SonarSoftware\CustomerPortalFramework\Controllers;

use Carbon\Carbon;
use SonarSoftware\CustomerPortalFramework\Helpers\HttpHelper;

class DataUsageController
{
    private $httpHelper;

    /**
     * DataUsageController constructor.
     */
    public function __construct()
    {
        $this->httpHelper = new HttpHelper();
    }

    /*
     * GET functions
     */

    /**
     * Return granular data usage across the last $days days - See https://sonar.software/apidoc/index.html#api-Account_Data_Usage-GetAccountDataUsages
     * @param $accountID
     * @param $days
     * @return mixed
     * @throws \SonarSoftware\CustomerPortalFramework\Exceptions\ApiException
     */
    public function getGranularUsage($accountID, $days)
    {
        $endTime = Carbon::now("UTC");
        $startTime = Carbon::now("UTC")->subDays(intval($days));
        return $this->httpHelper->get("accounts/" . intval($accountID) . "/granular_data_usage/" . $startTime->timestamp . "/" . $endTime->timestamp);
    }

    /**
     * Get the last 100 aggregated data usage items - see https://sonar.software/apidoc/index.html#api-Account_Data_Usage-GetAccountDataUsageHistories
     * @param $accountID
     * @return mixed
     * @throws \SonarSoftware\CustomerPortalFramework\Exceptions\ApiException
     */
    public function getAggregatedDataUsage($accountID)
    {
        return $this->httpHelper->get("accounts/" . intval($accountID) . "/data_usage_histories");
    }

    /**
     * Get usage based billing policy details for an account - see https://sonar.software/apidoc/index.html#api-Account_Services-GetAccountUsageBasedBillingPolicyDetails
     * @param $accountID
     * @return mixed
     * @throws \SonarSoftware\CustomerPortalFramework\Exceptions\ApiException
     */
    public function getUsageBasedBillingPolicyDetails($accountID)
    {
        return $this->httpHelper->get("accounts/" . intval($accountID) . "/usage_based_billing_policy_details");
    }

    /*
     * POST functions
     */

    /**
     * Purchase top off data for an account. See https://sonar.software/apidoc/index.html#api-Account_Data_Usage-PostAccountDataUsageTopOff
     * This will throw an ApiException if the user does not have a usage based billing policy with top offs enabled.
     * @param $accountID
     * @param int $quantity
     * @return mixed
     * @throws \SonarSoftware\CustomerPortalFramework\Exceptions\ApiException
     */
    public function purchaseTopOff($accountID, $quantity = 1)
    {
        return $this->httpHelper->post("accounts/" . intval($accountID) . "/top_off", ['quantity' => intval($quantity)]);
    }
}