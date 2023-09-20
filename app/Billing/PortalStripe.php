<?php

namespace App\Billing;

use Stripe;

// This class is named to ensure there is never a clash between \Stripe.

class PortalStripe
{
    public function __construct()
    {
        Stripe\Stripe::setApiKey(config('customer_portal.stripe_private_api_key'));
    }

    /**
     * Return a new Stripe\SetupIntent client secret
     */
    public function setupIntent(): string
    {
        $customer = Stripe\Customer::create();

        $intent = Stripe\SetupIntent::create([
            'customer' => $customer->id,
        ]);

        return $intent->client_secret;
    }

    /**
     * Return a PaymentMethod by ID
     *
     * @throws Stripe\Exception\ApiErrorException if the request fails
     */
    public function paymentMethod(string $id): Stripe\PaymentMethod
    {
        return Stripe\PaymentMethod::retrieve($id);
    }
}
