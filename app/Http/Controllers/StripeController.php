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
     * @param string $id
     * @return mixed
     */
    public function paymentMethod(Request $request, $id)
    {
        try {
            return $this->stripe->paymentMethod($id);
        } catch (Stripe\Exception\ApiErrorException $e)
        {
            return redirect()->back()->withErrors(utrans("errors.stripePaymentMethodNotFound"));
        }
    }

}
