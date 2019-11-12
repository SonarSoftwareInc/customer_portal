<?php

namespace App\Http\Controllers;

use App\Billing\GoCardless;
use App\Http\Requests\CreateBankAccountRequest;
use App\Http\Requests\CreateCreditCardRequest;
use App\Http\Requests\CreditCardPaymentRequest;
use App\Http\Requests\PaymentMethodDeleteRequest;
use App\Services\LanguageService;
use App\SystemSetting;
use App\Traits\ListsPaymentMethods;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Inacho\CreditCard as CreditCardValidator;
use InvalidArgumentException;
use SonarSoftware\CustomerPortalFramework\Controllers\AccountBillingController;
use SonarSoftware\CustomerPortalFramework\Models\BankAccount;
use SonarSoftware\CustomerPortalFramework\Models\CreditCard;

class BillingController extends Controller
{
    use ListsPaymentMethods;
    private $frameworkDataUsageController;
    private $dataUsageController;
    private $accountBillingController;
    public function __construct()
    {
        $this->accountBillingController = new AccountBillingController();
	$this->frameworkDataUsageController = new \SonarSoftware\CustomerPortalFramework\Controllers\DataUsageController();
    }
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $billingDetails = $this->getAccountBillingDetails();
        $invoices = $this->getInvoices();
        $invoices = $this->paginate($invoices, 5, false, ['path' => '/portal/billing/invoices']);
        $transactions = $this->getTransactions();
        $transactions = $this->paginate($transactions, 5, false, ['path' => '/portal/billing/transaction']);
        $paymentMethods = $this->getPaymentMethods();

	    $historicalUsage = $this->getHistoricalUsage();
        $policyDetails = $this->getPolicyDetails();
        $currentUsage = $historicalUsage[0];
        $calculatedCap = $policyDetails->policy_cap_in_gigabytes + round($policyDetails->rollover_available_in_bytes/1000**3, 2) + round($policyDetails->purchased_top_off_total_in_bytes/1000**3, 2);

        $values = [
            'amount_due' => $billingDetails->balance_due,
            'next_bill_date' => $billingDetails->next_bill_date,
            'next_bill_amount' => $billingDetails->next_recurring_charge_amount,
            'total_balance' => $billingDetails->total_balance,
            'available_funds' => $billingDetails->available_funds,
            'payment_past_due' => $this->isPaymentPastDue(),
            'balance_minus_funds' => bcsub($billingDetails->total_balance, $billingDetails->available_funds, 2),
            'currentUsage' => $currentUsage
        ];

        $systemSetting = SystemSetting::firstOrNew(['id' => 1]);

        return view("pages.billing.index", compact('values', 'invoices', 'transactions', 'paymentMethods', 'systemSetting'));
    }

    /**
     * Get an invoice PDF as base64, and decode it
     * @param $invoiceID
     * @return $this|\Illuminate\Http\Response
     */
    public function getInvoicePdf($invoiceID)
    {
        try {
            $data = $this->accountBillingController->getInvoicePdf(get_user()->account_id, $invoiceID);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(utrans("errors.failedToDownloadInvoice"));
        }

        return response()->make(base64_decode($data->base64), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=Invoice $invoiceID.pdf",
        ]);
    }
    
    /**
     * Make payment page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function makePayment()
    {
        $billingDetails = $this->getAccountBillingDetails();
        $paymentMethods = $this->generatePaymentMethodListForPaymentPage();
        if (count($paymentMethods) == 0)
        {
            return redirect()->back()->withErrors(utrans("errors.addAPaymentMethod"));
        }

        return view('pages.billing.make_payment', compact('billingDetails', 'paymentMethods'));
    }

    /**
     * Process a submitted payment
     * @param CreditCardPaymentRequest $request
     * @return $this
     */
    public function submitPayment(CreditCardPaymentRequest $request)
    {
        switch ($request->input('payment_method')) {
            case "new_card":
                try {
                    $result = $this->payWithNewCreditCard($request);
                } catch (Exception $e) {
                    Log::error($e->getMessage());
                    return redirect()->back()->withErrors($e->getMessage())->withInput();
                }
                break;
            case "paypal":
                $paypalController = new PayPalController();
                try {
                    $redirectLink = $paypalController->generateApprovalLink($request->input('amount'));
                } catch (Exception $e) {
                    Log::error($e->getMessage());
                    return redirect()->back()->withErrors(utrans("errors.paypalFailed"));
                }
                return redirect()->to($redirectLink);
                break;
            default:
                //If we've made it here, this is an existing payment method
                try {
                    $result = $this->payWithExistingPaymentMethod($request);
                } catch (Exception $e) {
                    Log::error($e->getMessage());
                    return redirect()->back()->withErrors($e->getMessage())->withInput();
                }
                break;
        }

        $this->clearBillingCache();
        if ($result->success == true)
        {
            return redirect()->action("BillingController@index")->with('success', utrans("billing.paymentWasSuccessful"));
        }
        else
        {
            return redirect()->back()->withErrors(utrans("errors.paymentFailed"));
        }
    }

    /**
     * Delete an existing payment method from a customer account.
     * @param $id
     * @return $this
     */
    public function deletePaymentMethod($id)
    {
        $paymentMethods = $this->getPaymentMethods();
        foreach ($paymentMethods as $paymentMethod) {
            if ((int)$paymentMethod->id === (int)$id) {
                try {
                    $this->accountBillingController->deletePaymentMethodByID(get_user()->account_id, $id);
                    $this->clearBillingCache();
                    return redirect()->action("BillingController@index")->with('success', utrans("billing.creditCardDeleted"));
                } catch (Exception $e) {
                    //
                }
            }
        }
        
        return redirect()->back()->withErrors(utrans("errors.paymentMethodNotFound"));
    }

    /**
     * Toggle the auto pay setting on a payment method
     * @param $id
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function toggleAutoPay($id)
    {
        $paymentMethods = $this->getPaymentMethods();
        foreach ($paymentMethods as $paymentMethod) {
            if ((int)$paymentMethod->id === (int)$id) {
                try {
                    $existingAutoSetting = (boolean)$paymentMethod->auto;
                    $this->accountBillingController->setAutoOnPaymentMethod(get_user()->account_id, $paymentMethod->id, !$existingAutoSetting);
                    $this->clearBillingCache();
                    if ($existingAutoSetting == true) {
                        return redirect()->action("BillingController@index")->with('success', utrans("billing.autoPayDisabled"));
                    }
                    return redirect()->action("BillingController@index")->with('success', utrans("billing.autoPayEnabled"));
                } catch (Exception $e) {
                    //
                }
            }
        }

        return redirect()->back()->withErrors(utrans("errors.paymentMethodNotFound"));
    }

    /**
     * Show page to create a new payment method
     * @param $type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createPaymentMethod($type)
    {
        switch ($type)
        {
            case "credit_card":
                return view("pages.billing.add_card");
                break;
            case "bank":
                if (config("customer_portal.enable_gocardless") == 1)
                {
                    $gocardless = new GoCardless();
                    return Redirect::away($gocardless->createRedirect());
                }
                else
                {
                    return view("pages.billing.add_bank");
                }
                break;
            default:
                return redirect()->back()->withErrors(utrans("errors.invalidPaymentMethodType"));
        }
    }

    /**
     * Store a new credit card
     * @param CreateCreditCardRequest $request
     * @return $this|mixed
     */
    public function storeCard(CreateCreditCardRequest $request)
    {
        if (config("customer_portal.enable_credit_card_payments") == false)
        {
            throw new InvalidArgumentException(utrans("errors.creditCardPaymentsDisabled"));
        }

        try {
            $card = $this->createCreditCardObjectFromRequest($request);
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }

        try {
            $this->accountBillingController->createCreditCard(get_user()->account_id, $card, (bool)$request->input('auto'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(utrans("errors.failedToCreateCard"))->withInput();
        }
        
        unset($creditCard);
        unset($request);

        $this->clearBillingCache();
        return redirect()->action("BillingController@index")->with('success', utrans("billing.cardAdded"));
    }

    /**
     * Store a new credit card
     * @param CreateBankAccountRequest $request
     * @return $this|mixed
     */
    public function storeBank(CreateBankAccountRequest $request)
    {
        if (config("customer_portal.enable_bank_payments") != true)
        {
            return redirect()->back()->withErrors(utrans("errors.failedToCreateBankAccount"))->withInput();
        }

        try {
            $bankAccount = $this->createBankAccountObjectFromRequest($request);
        }
        catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }

        try {
            $this->accountBillingController->createBankAccount(get_user()->account_id, $bankAccount, (bool)$request->input('auto'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(utrans("errors.failedToCreateBankAccount"))->withInput();
        }

        unset($bankAccount);
        unset($request);

        $this->clearBillingCache();
        return redirect()->action("BillingController@index")->with('success', utrans("billing.bankAccountAdded"));
    }

    /**
     * Make a payment with an existing payment method
     * @param $request
     * @return mixed
     * @throws Exception
     */
    private function payWithExistingPaymentMethod($request)
    {
        
        try {
            $result = $this->accountBillingController->makePaymentUsingExistingPaymentMethod(get_user()->account_id, intval($request->input('payment_method')), trim($request->input('amount')));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new Exception(utrans("billing.paymentFailedTryAnother"));
        }

        if ($result->success !== true) {
            throw new Exception(utrans("billing.paymentFailedTryAnother"));
        }

        Log::info(json_encode($result));

        return $result;
    }

    /**
     * Make a payment with a new credit card
     * @param CreditCardPaymentRequest $request
     * @return mixed
     */
    private function payWithNewCreditCard(CreditCardPaymentRequest $request)
    {
        if (config("customer_portal.enable_credit_card_payments") == false)
        {
            throw new InvalidArgumentException(utrans("errors.creditCardPaymentsDisabled"));
        }

        $creditCard = $this->createCreditCardObjectFromRequest($request);

        try {
            $result = $this->accountBillingController->makeCreditCardPayment(get_user()->account_id, $creditCard, $request->input('amount'), (boolean)$request->input('makeAuto'));
        } catch (Exception $e) {
            throw new InvalidArgumentException(utrans("billing.errorSubmittingPayment"));
        }

        if ($result->success !== true) {
            throw new InvalidArgumentException(utrans("errors.paymentFailed"));
        }

        unset($creditCard);
        unset($request);

        return $result;
    }


    /**
     * Get account billing details
     * @return mixed
     */
    private function getAccountBillingDetails()
    {
        if (!Cache::tags("billing.details")->has(get_user()->account_id)) {
            $billingDetails = $this->accountBillingController->getAccountBillingDetails(get_user()->account_id);
            Cache::tags("billing.details")->put(get_user()->account_id, $billingDetails, 10);
        }

        return Cache::tags("billing.details")->get(get_user()->account_id);
    }

    /**
     * Get the invoice list for a user. This will only retrieve the last 100.
     * @return mixed
     */
    private function getInvoices()
    {
        if (!Cache::tags("billing.invoices")->has(get_user()->account_id)) {
            $invoicesToReturn = [];
            $invoices = $this->accountBillingController->getInvoices(get_user()->account_id);
            foreach ($invoices as $invoice) {
                //This check is here because this property did not exist prior to Sonar 0.6.6
                if (property_exists($invoice, "void")) {
                    if ($invoice->void != 1) {
                        array_push($invoicesToReturn, $invoice);
                    }
                } else {
                    array_push($invoicesToReturn, $invoice);
                }
            }
            Cache::tags("billing.invoices")->put(get_user()->account_id, $invoicesToReturn, 10);
        }

        return Cache::tags("billing.invoices")->get(get_user()->account_id);
    }

    /**
     * Get the transaction list for a user. This will only display the last 100 currently.
     * @return mixed
     */
    private function getTransactions()
    {
        
        if (!Cache::tags("billing.transactions")->has(get_user()->account_id)) {
            $transactions = [];
            $debits = $this->accountBillingController->getDebits(get_user()->account_id);
            foreach ($debits as $debit) {
                array_push($transactions, [
                    'type' => 'debit',
                    'amount' => $debit->amount,
                    'date' => $debit->date,
                    'taxes' => $debit->taxes,
                    'description' => $debit->description,
                ]);
            }

            $discounts = $this->accountBillingController->getDiscounts(get_user()->account_id);
            foreach ($discounts as $discount) {
                array_push($transactions, [
                    'type' => 'discount',
                    'amount' => $discount->amount,
                    'date' => $discount->date,
                    'taxes' => $discount->taxes,
                    'description' => $discount->description,
                ]);
            }

            $payments = $this->accountBillingController->getPayments(get_user()->account_id);
            foreach ($payments as $payment) {
                if ($payment->success === true && $payment->reversed === false) {
                    array_push($transactions, [
                        'type' => 'payment',
                        'amount' => $payment->amount,
                        'payment_type' => $payment->type,
                        'date' => $payment->date,
                    ]);
                }
            }

            /**
             * After sorting, limit transactions to 100. You can change this number if you want to show more,
             * but bear in mind that the queries above are limited to 100 items each, so this will never be
             * more than 300 at the most.
             */
            usort($transactions, function ($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
            $transactions = array_slice($transactions, 0, 100);

            Cache::tags("billing.transactions")->put(get_user()->account_id, $transactions, 10);
        }

        return Cache::tags("billing.transactions")->get(get_user()->account_id);
    }

    /**
     * Check if the payment is past due
     * @return bool
     */
    private function isPaymentPastDue()
    {
        $invoices = $this->getInvoices();
        $now = Carbon::now(config("app.timezone"));

        foreach ($invoices as $invoice) {
            if ($invoice->remaining_due <= 0) {
                continue;
            }
            $invoiceDueDate = new Carbon($invoice->due_date);
            if ($invoiceDueDate->lte($now)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate a formatted list of payment methods for the make payment page
     * @return array
     */
    private function generatePaymentMethodListForPaymentPage()
    {
        $paymentMethods = [];
        $validAccountMethods = $this->getPaymentMethods();
        foreach ($validAccountMethods as $validAccountMethod)
        {
            if ($validAccountMethod->type == "credit card" && config("customer_portal.enable_credit_card_payments") == 1)
            {
                $paymentMethods[$validAccountMethod->id] = utrans("billing.payUsingExistingCard", ['card' => "****" . $validAccountMethod->identifier . " (" . sprintf("%02d", $validAccountMethod->expiration_month) . " / " . $validAccountMethod->expiration_year . ")"]);
            }
            elseif ((config("customer_portal.enable_bank_payments") == 1 || config("customer_portal.enable_gocardless") == 1) && $validAccountMethod->type != "credit card")
            {
                $paymentMethods[$validAccountMethod->id] = utrans("billing.payUsingExistingBankAccount", ['accountNumber' => "**" . $validAccountMethod->identifier]);
            }
        }

        if (config("customer_portal.paypal_enabled") == 1) {
            $paymentMethods['paypal'] = utrans("billing.payWithPaypal");
        }
        if (config("customer_portal.enable_credit_card_payments") == 1)
        {
            $paymentMethods['new_card'] = utrans("billing.payWithNewCard");
        }

        $paymentMethods = array_reverse($paymentMethods, true);

        return $paymentMethods;
    }

    /**
     * Clear all the cached billing items.
     */
    public function clearBillingCache()
    {
        Cache::tags("billing.details")->forget(get_user()->account_id);
        Cache::tags("billing.invoices")->forget(get_user()->account_id);
        Cache::tags("billing.transactions")->forget(get_user()->account_id);
        Cache::tags("billing.payment_methods")->forget(get_user()->account_id);
    }

    /**
     * Create a credit card object from a request containing cc-number, name, and expirationDate
     * @param $request
     * @return CreditCard
     */
    private function createCreditCardObjectFromRequest($request)
    {
        $card = CreditCardValidator::validCreditCard(trim(str_replace(" ", "", $request->input('cc-number'))));
        if ($card['valid'] !== true) {
            throw new InvalidArgumentException(utrans("errors.invalidCreditCardNumber"));
        }

        $expiration = $request->input('expirationDate');
        $boom = explode(" / ", $expiration);
        $month = ltrim(trim($boom[0]), 0);
        $year = trim($boom[1]);
        if (strlen($year) == 2) {
            $now = Carbon::now(config("app.timezone"));
            $year = substr($now->year, 0, 2) . $year;
        }

        if (CreditCardValidator::validDate($year, $month) !== true) {
            throw new InvalidArgumentException(utrans("errors.invalidExpirationDate"));
        }

        $creditCard = new CreditCard([
            'name' => $request->input('name'),
            'number' => $card['number'],
            'expiration_month' => intval($month),
            'expiration_year' => $year,
            'line1' => $request->input('line1'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'zip' => $request->input('zip'),
            'country' => $request->input('country'),
            'cvc' => $request->input('cvc'),
        ]);

        return $creditCard;
    }

    /**
     * @param CreateBankAccountRequest $request
     * @return BankAccount
     */
    private function createBankAccountObjectFromRequest(CreateBankAccountRequest $request)
    {
        $bankAccount = new BankAccount([
            'name' => trim($request->input('name')),
            'type' => trim($request->input('account_type')),
            'account_number' => trim($request->input('account_number')),
            'routing_number' => trim($request->input('routing_number')),
        ]);

        return $bankAccount;
    }

   /**
     * Get cached usage based billing policy details. Cache is lower on this one in case service is changed.
     * @return mixed
     */
    private function getPolicyDetails()
    {
        if (!Cache::tags("usage_based_billing_policy_details")->has(get_user()->account_id)) {
            $policyDetails = $this->frameworkDataUsageController->getUsageBasedBillingPolicyDetails(get_user()->account_id, 3);
            Cache::tags("usage_based_billing_policy_details")->put(get_user()->account_id, $policyDetails, 10);
        }
        return Cache::tags("usage_based_billing_policy_details")->get(get_user()->account_id);
    }

    /**
     * Get cached historical data usage
     * @return mixed
     */
    private function getHistoricalUsage()
    {
        if (!Cache::tags("historical_data_usage")->has(get_user()->account_id)) {
            $dataUsage = $this->formatHistoricalUsageData(array_slice($this->frameworkDataUsageController->getAggregatedDataUsage(get_user()->account_id, 3), 0, 12));
            Cache::tags("historical_data_usage")->put(get_user()->account_id, $dataUsage, 60);
        }
        return Cache::tags("historical_data_usage")->get(get_user()->account_id);
    }

    /**
     * Format the data returned from the customer portal framework data usage controller into a usable format
     * @param $dataUsage
     * @return array
     */
    private function formatGranularDataUsage($dataUsage)
    {
        $graphData = [];
        $largestValue = 0;
        $dataToFormat = $dataUsage->granular->series;
        foreach ($dataToFormat as $datumToFormat) {
            foreach ($datumToFormat->in as $timestamp => $value) {
                if (!array_key_exists($timestamp, $graphData)) {
                    $graphData[$timestamp] = [
                        "in" => $value,
                        "out" => 0
                    ];
                } else {
                    $graphData[$timestamp]['in'] += $value;
                }

                if ($graphData[$timestamp]['in'] > $largestValue) {
                    $largestValue = $graphData[$timestamp]['in'];
                }
            }
            foreach ($datumToFormat->out as $timestamp => $value) {
                $graphData[$timestamp]['out'] += $value;
                if ($graphData[$timestamp]['out'] > $largestValue) {
                    $largestValue = $graphData[$timestamp]['out'];
                }
            }
        }

        //We need to convert all the values to some SI-suffixed value based on the largest value so that the graph is not shown in bytes.
        $suffixAndPower = $this->returnSiSuffixAndPower($largestValue);
        foreach ($graphData as $timestamp => $values) {
            $graphData[$timestamp] = [
                'in' => round($values['in'] / 1000**$suffixAndPower['power'], 2),
                'out' => round($values['out'] / 1000**$suffixAndPower['power'], 2),
            ];
        }

        return json_encode([
            'graphData' => $graphData,
            'suffix' => $suffixAndPower['suffix'],
        ]);
    }

    /**
     * Convert all historical usage to gigabytes
     * @param $historicalUsageData
     * @return array
     */
    private function formatHistoricalUsageData($historicalUsageData)
    {
        $formattedData = [];
        foreach ($historicalUsageData as $datum) {
            $timestamp = new Carbon($datum->start_time, "UTC");
            array_push($formattedData, [
                'timestamp' => $timestamp->toRfc3339String(),
                'billable' => round(($datum->billable_in_bytes+$datum->billable_out_bytes)/1000**3, 2),
                'free' => round(($datum->free_in_bytes+$datum->free_out_bytes)/1000**3, 2),
            ]);
        }
        return $formattedData;
    }

    private function paginate($items, $perPage, $setDefaultOption = true, $options = [])
    {
        if($setDefaultOption){
            $options = ['path' => request()->url(), 'query' => request()->query()];
        }

        $requestUrl = $this->cleanUrl(request()->url());

        if (isset($options['path']) && $_SERVER['HTTP_HOST'] . $options['path'] == $requestUrl) {
            $page = Input::get('page', 1); // Get the current page or default to 1
        } else {
            $page = 1; // Get the current page or default to 1
        }


        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    private function cleanUrl($url)
    {
        return str_replace('https://', '', str_replace('http://', '', $url));
    }

}
