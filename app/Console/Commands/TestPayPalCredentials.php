<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use PayPal\Api\OpenIdUserinfo;
use PayPal\Api\Webhook;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class TestPayPalCredentials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sonar:test:paypal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the PayPal credentials.';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (config("customer_portal.paypal_enabled") !== true) {
            $this->error("PayPal is not enabled in the customer portal configuration.");
            return;
        }

        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                config("customer_portal.paypal_api_client_id"),
                config("customer_portal.paypal_api_client_secret")
            )
        );

        $apiContext->setConfig([
            //You can set this to 'sandbox' if you want to test with a sandbox account
            'mode' => 'live',
            'log.LogEnabled' => true,
            'log.FileName' => storage_path("logs/paypal.log"),
            'log.LogLevel' => 'ERROR',
        ]);

        try {
            Webhook::getAll($apiContext);
        } catch (Exception $e) {
            $this->error("Credentials failed! Please make sure this is a LIVE account and not a SANDBOX account and try again.");
            $this->error("Specific error was: {$e->getMessage()}");
            return;
        }
        $this->info("Credentials tested successfully.");
    }
}
