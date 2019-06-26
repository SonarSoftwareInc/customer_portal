<?php

namespace App\Http\Controllers;

use App\PaypalTemporaryToken;
use Exception;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use RuntimeException;
use SonarSoftware\CustomerPortalFramework\Controllers\AccountBillingController;

class PayPalController extends Controller
{
    private $currency;
    private $apiContext;

    /**
     * PayPalController constructor.
     */
    public function __construct()
    {
        $this->currency = config("customer_portal.paypal_currency_code");

        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                config("customer_portal.paypal_api_client_id"),
                config("customer_portal.paypal_api_client_secret")
            )
        );

        $this->apiContext->setConfig([
            //You can set this to 'sandbox' if you want to test with a sandbox account
            'mode' => 'live',
            'log.LogEnabled' => true,
            'log.FileName' => storage_path("logs/paypal.log"),
            'log.LogLevel' => 'ERROR',
        ]);
    }

    /**
     * Generate the approval link for a PayPal payment
     * @param $amountToPay
     * @return mixed
     */
    public function generateApprovalLink($amountToPay)
    {
        if (config("customer_portal.paypal_enabled") !== true) {
            throw new RuntimeException("PayPal is not enabled in the customer portal configuration.");
        }

        $amountToPay = number_format($amountToPay, 2, ".", "");

        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        $amount = new Amount();
        $amount->setCurrency($this->currency)
            ->setTotal($amountToPay);
        
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setDescription(utrans("billing.paymentToCompany", ['company_name' => config("customer_portal.company_name")]))
            ->setInvoiceNumber(uniqid(true)); //This is not a payment on a specific invoice, so we'll just generate a unique string, which is what PayPal requires

        $tempToken = new PaypalTemporaryToken(['account_id' => get_user()->account_id, 'token' => uniqid(true)]);
        $tempToken->save();

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(action("PayPalController@completePayment", ['temporary_token' => $tempToken->token]))
            ->setCancelUrl(action("PayPalController@cancelPayment", ['temporary_token' => $tempToken->token]));

        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions([$transaction]);

        $payment->create($this->apiContext);

        return $payment->getApprovalLink();
    }

    /**
     * Complete the PayPal payment
     * @param Request $request
     * @param $temporaryToken
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function completePayment(Request $request, $temporaryToken)
    {
        $token = PaypalTemporaryToken::where('token', '=', $temporaryToken)->where('account_id', '=', get_user()->account_id)->first();
        if ($token === null) {
            $error = utrans("errors.paypalTokenInvalid");
            return view("pages.paypal.error", compact('error'));
        }
        $token->delete();

        if ($request->input('paymentId') === null || $request->input('PayerID') === null) {
            $error = utrans("errors.missingPaypalInformation");
            return view("pages.paypal.error", compact('error'));
        }

        $payment = Payment::get($request->input('paymentId'), $this->apiContext);
        $execution = new PaymentExecution();
        $execution->setPayerId($request->input('PayerID'));

        try {
            $payment->execute($execution, $this->apiContext);
            $payment = Payment::get($request->input('paymentId'), $this->apiContext);
        } catch (Exception $e) {
            $error = utrans("errors.paypalGenericError");
            return view("pages.paypal.error", compact('error'));
        }
        
        if (strtolower($payment->getState() != 'approved')) {
            $error = utrans("errors.paymentNotApproved");
            return view("pages.paypal.error", compact('error'));
        }

        //POST the payment back into Sonar for storage
        try {
            $accountBillingController = new AccountBillingController();
            $transaction = $payment->getTransactions()[0];
            $accountBillingController->storePayPalPayment(get_user()->account_id, $transaction->related_resources[0]->sale->amount->total, $transaction->related_resources[0]->sale->id);
        } catch (Exception $e) {
            $error = utrans("errors.failedToApplyPaypalPayment");
            return view("pages.paypal.error", compact('error'));
        }

        $billingController = new BillingController;
        $billingController->clearBillingCache();
        return view("pages.paypal.success");
    }

    /**
     * This route is hit if the user cancels the payment from the PayPal site.
     * @param $temporaryToken
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cancelPayment($temporaryToken)
    {
        $token = PaypalTemporaryToken::where('token', '=', $temporaryToken)->where('account_id', '=', get_user()->account_id)->first();
        if ($token !== null) {
            $token->delete();
        }

        return view("pages.paypal.cancel");
    }
}
