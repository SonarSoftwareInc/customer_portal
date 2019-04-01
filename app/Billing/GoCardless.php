<?php

namespace App\Billing;

use App\GoCardlessToken;
use Illuminate\Support\Facades\Redirect;
use InvalidArgumentException;
use SonarSoftware\CustomerPortalFramework\Exceptions\ApiException;
use SonarSoftware\CustomerPortalFramework\Exceptions\AuthenticationException;
use SonarSoftware\CustomerPortalFramework\Helpers\HttpHelper;

class GoCardless
{
    private $client;
    public function __construct()
    {
        $this->client = new \GoCardlessPro\Client([
            'access_token' => config("customer_portal.gocardless_access_token"),
            'environment' => config("customer_portal.gocardless_environment"),
        ]);
    }

    /**
     * Setup the redirect flow
     */
    public function createRedirect()
    {
        $token = str_random(32);
        while (GoCardlessToken::where('token','=',$token)->count() > 0)
        {
            $token = str_random(32);
        }
        $gocardlessToken = new GoCardlessToken([
            'token' => $token,
            'account_id' => get_user()->account_id
        ]);

        $params = [
            'params' => [
                'description' => config("customer_portal.company_name"),
                'session_token' => $gocardlessToken->token,
                'success_redirect_url' => action("GoCardlessController@handleReturnRedirect"),
            ]
        ];

        $redirectFlow = $this->client->redirectFlows()->create($params);

        $gocardlessToken->redirect_flow_id = $redirectFlow->id;
        $gocardlessToken->save();

        return $redirectFlow->redirect_url;
    }

    /**
     * @param GoCardlessToken $goCardlessToken
     * @return mixed
     * @throws \GoCardlessPro\Core\Exception\InvalidStateException
     */
    public function completeRedirect(GoCardlessToken $goCardlessToken)
    {
        $completedFlow = $this->client->redirectFlows()->complete(
            $goCardlessToken->redirect_flow_id,
            [
                "params" => [
                    "session_token" => $goCardlessToken->token
                ]
            ]
        );

        try
        {

            $fullMandate = $this->client->mandates()->get($completedFlow->links->mandate);
            $bankAccount = $this->client->customerBankAccounts()->get($fullMandate->links->customer_bank_account);

            $httpHelper = new HttpHelper();
            $result = $httpHelper->post("accounts/" . get_user()->account_id . "/tokenized_payment_method", [
                'token' => $completedFlow->links->mandate,
                'name_on_account' => get_user()->contact_name,
                'type' => 'echeck',
                'identifier' => $bankAccount->account_number_ending,
                'auto' => true,
            ]);
        }
        catch (ApiException $e)
        {
            throw new InvalidArgumentException($e->getMessage());
        }

        return $completedFlow->confirmation_url;
    }
}