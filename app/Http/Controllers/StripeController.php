<?php

namespace App\Http\Controllers;

use App\Billing\PortalStripe;
use Illuminate\Http\Request;
use Stripe;

class StripeController extends Controller
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new PortalStripe();
    }

    /**
     * Return PaymentMethod associated with ID
     *
     * @return mixed
     */
    public function paymentMethod(Request $request, string $id)
    {
        try {
            return $this->stripe->paymentMethod($id);
        } catch (Stripe\Exception\ApiErrorException $e) {
            return redirect()->back()->withErrors(utrans('errors.stripePaymentMethodNotFound'));
        }
    }
}
