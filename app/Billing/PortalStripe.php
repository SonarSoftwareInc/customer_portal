<?php

namespace App\Billing;

use Stripe;

// This class is named to ensure there is never a clash between \Stripe.

class PortalStripe
{
    public function __construct()
    {
        Stripe\Stripe::setApiKey(config("customer_portal.stripe_private_api_key"));
    }

    /**
     * Return a new Stripe\SetupIntent client secret
     *
     * @return string
     */
    public function setupIntent()
    {
        $customer = Stripe\Customer::create();

        $intent = Stripe\SetupIntent::create([
            "customer" => $customer->id
        ]);

        return $intent->client_secret;
    }

    /**
     * Return a PaymentMethod by ID
     *
     * @param string $id
     *
     * @throws Stripe\Exception\ApiErrorException if the request fails
     *
     * @return static
     */
    public function paymentMethod(string $id)
    {
        return Stripe\PaymentMethod::retrieve($id);
    }
}
