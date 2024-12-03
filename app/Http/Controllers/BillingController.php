<?php

namespace App\Http\Controllers;

use App\Billing\GoCardless;
use App\Billing\PortalStripe;
use App\Http\Requests\CreateBankAccountRequest;
use App\Http\Requests\WifiRequest;
use App\Http\Requests\CreateCreditCardRequest;
use App\Http\Requests\CreateTokenizedCreditCardRequest;
use App\Http\Requests\CreditCardPaymentRequest;
use App\Http\Requests\TokenizedCreditCardPaymentRequest;
use App\SystemSetting;
use App\Traits\ListsPaymentMethods;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;
use SonarSoftware\CustomerPortalFramework\Controllers\AccountBillingController;
use SonarSoftware\CustomerPortalFramework\Controllers\AccountController;
use SonarSoftware\CustomerPortalFramework\Controllers\DataUsageController as FrameworkDataUsageController;
use SonarSoftware\CustomerPortalFramework\Controllers\SystemController;
use SonarSoftware\CustomerPortalFramework\Helpers\CreditCardValidator;
use SonarSoftware\CustomerPortalFramework\Models\BankAccount;
use SonarSoftware\CustomerPortalFramework\Models\CreditCard;
use SonarSoftware\CustomerPortalFramework\Models\TokenizedCreditCard;

class BillingController extends Controller
{
    use ListsPaymentMethods;

    private FrameworkDataUsageController $frameworkDataUsageController;

    private AccountBillingController $accountBillingController;
    private SystemController $systemController;
    private AccountController $accountController;

    public function __construct()
    {
        $this->accountBillingController = new AccountBillingController();
        $this->systemController = new SystemController();
        $this->accountController = new AccountController();
        $this->frameworkDataUsageController = new FrameworkDataUsageController();
    }

    public function index(): Factory|View
    {
        $accountDetails = $this->accountController->getAccountDetails(get_user()->account_id);
        $billingDetails = $this->getAccountBillingDetails();
        $invoices = $this->getInvoices();
        $invoices = $this->paginate($invoices, 5, false, ['path' => '/portal/billing/invoices']);
        $transactions = $this->getTransactions();
        $transactions = $this->paginate($transactions, 5, false, ['path' => '/portal/billing/transaction']);
        $paymentMethods = $this->getPaymentMethods();

        $historicalUsage = $this->getHistoricalUsage();
        $policyDetails = $this->getPolicyDetails();
        $currentUsage = $historicalUsage ? $historicalUsage[0] : [];
        $calculatedCap = $policyDetails->policy_cap_in_gigabytes
            + round($policyDetails->rollover_available_in_bytes / 1000 ** 3, 2)
            + round($policyDetails->purchased_top_off_total_in_bytes / 1000 ** 3, 2);

        $values = [
            'amount_due' => $billingDetails->balance_due,
            'next_bill_date' => $billingDetails->next_bill_date,
            'next_bill_amount' => $billingDetails->next_recurring_charge_amount,
            'total_balance' => $billingDetails->total_balance,
            'available_funds' => $billingDetails->available_funds,
            'payment_past_due' => $this->isPaymentPastDue(),
            'balance_minus_funds' => bcsub($billingDetails->total_balance, $billingDetails->available_funds, 2),
            'currentUsage' => $currentUsage,
        ];

        $systemSetting = SystemSetting::firstOrNew(['id' => 1]);
        
        $services = $this->accountBillingController->getServices(get_user()->account_id);
        $dataServiceId = 0;
        if ($accountDetails->company_id) {
            foreach ($services as $service) {
                //save a call back to sonar if no label is here to find anyway
                $trySvgPath = "public/assets/fcclabels/label_" . $service->id . "_" . $accountDetails->company_id . ".svg";
                if (file_exists(base_path("{$trySvgPath}"))) {
                    $serviceDef = $this->systemController->getService($service->id);
                    if ($serviceDef->data_service) {
                        $dataServiceId = $service->id;
                    }
                }
            }

            $svgPath = "/assets/fcclabels/label_" . $dataServiceId . "_" . $accountDetails->company_id . ".svg";

            if (file_exists(base_path("public{$svgPath}"))) {
                $svgDisplay = "initial";
                $svg = file_get_contents(base_path("public{$svgPath}"));
            } else {
                $svgDisplay = "none";
                $svg = "";
            }
        } else {//must be using v1
            $svgDisplay = "none";
            $svg = "";
        }
        $wifiData = [];

        $qcore_username = config('services.qcore.username');
        $qcore_password = config('services.qcore.password');
        $qcore_uri = config('services.qcore.qcore_uri');
        
        $qcore_data = [
            'username' => $qcore_username,
            'password' => $qcore_password,
        ];

        try {
            $qcore_response = Http::timeout(10)->post($qcore_uri.'/api/v1/api-token-auth/', $qcore_data);
    
            if ($qcore_response->successful()) {
                
                $response_data = $qcore_response->json();
                $token = $response_data['token'];
    
                $response = Http::withHeaders([
                    'Authorization' => 'Token ' . $token,
                    'Accept' => 'application/json',
                ])->timeout(10)->get($qcore_uri.'/api/v1/qportal/wifi-info/'.get_user()->account_id.'/');
    
                if ($response->successful()) {
                    $data = $response->json();
                    $wifiData = $data['data'] ?? [];
                }
            }

        } catch (Exception $e) {

            $wifiData = [];
        }

        try {
            $qcore_response = Http::timeout(10)->post($qcore_uri.'/api/v1/api-token-auth/', $qcore_data);
        
            if ($qcore_response->successful()) {
                $response_data = $qcore_response->json();
                $token = $response_data['token'];
        
                $response = Http::withHeaders([
                    'Authorization' => 'Token ' . $token,
                    'Accept' => 'application/json',
                ])->timeout(10)->get($qcore_uri.'/api/v1/qportal/sonar-account-info/'.get_user()->account_id.'/');
                
                if ($response->successful()) {
                    $acc_data = $response->json();
                    $serviceId = $acc_data['service'] ?? null;
                    $account_service_id = $acc_data['account_service_id'] ?? '';
                    $account_status_name = $acc_data['account_status_name'] ?? '';
                    
                    if ($serviceId !== null) {
                        $response = Http::withHeaders([
                            'Authorization' => 'Token ' . $token,
                            'Accept' => 'application/json',
                        ])->timeout(10)->get($qcore_uri.'/api/v1/qportal/services/list/');
        
                        if ($response->successful()) {
                            $services = $response->json();
                            $serviceArray = [];
        
                            foreach ($services as $service) {
                                if ($service['id'] == $serviceId) {
                                    $serviceArray[] = $service;
                                }
                            }
                            $service = $serviceArray;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $service = [];
            $account_service_id = '';
            $account_status_name = '';
        }
        
        return view(
            'pages.billing.index',
            compact('values', 'invoices', 'transactions', 'paymentMethods', 'systemSetting', 'svg', 'svgDisplay', 'wifiData', 'service', 'account_service_id', 'account_status_name')
        );
    }

    /**
     * Get an invoice PDF as base64, and decode it
     */
    public function getInvoicePdf($invoiceID): mixed
    {
        try {
            $data = $this->accountBillingController->getInvoicePdf(get_user()->account_id, $invoiceID);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->withErrors(utrans('errors.failedToDownloadInvoice'));
        }

        return response()->make(base64_decode($data->base64), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=Invoice $invoiceID.pdf",
        ]);
    }

    /**
     * Make payment page
     */
    public function makePayment(): Factory|View|RedirectResponse
    {
        $billingDetails = $this->getAccountBillingDetails();
        $paymentMethods = $this->generatePaymentMethodListForPaymentPage();
        if (count($paymentMethods) == 0) {
            return redirect()->back()->withErrors(utrans('errors.addAPaymentMethod'));
        }

        if (config('customer_portal.stripe_enabled') == 1) {
            $stripe = new PortalStripe();
            $secret = $stripe->setupIntent();
            $systemSettings = SystemSetting::first();
            $key = $systemSettings->stripe_public_api_key;

            return view(
                'pages.billing.make_payment_stripe',
                compact('billingDetails', 'paymentMethods', 'secret', 'key')
            );
        }

        return view('pages.billing.make_payment', compact('billingDetails', 'paymentMethods'));
    }

    /**
     * Process a tokenized submitted payment
     */
    public function submitTokenizedPayment(TokenizedCreditCardPaymentRequest $request): RedirectResponse
    {
        switch ($request->input('payment_method')) {
            case 'new_card':
                try {
                    $result = $this->payWithNewTokenizedCreditCard($request);
                } catch (Exception $e) {
                    Log::error($e->getMessage());

                    return redirect()->back()->withErrors($e->getMessage())->withInput();
                }
                break;
            default:
                try {
                    $result = $this->payWithExistingPaymentMethod($request);
                } catch (Exception $e) {
                    Log::error($e->getMessage());

                    return redirect()->back()->withErrors($e->getMessage())->withInput();
                }
                break;
        }

        $this->clearBillingCache();
        if ($result->success == true) {
            return redirect()
                ->action([BillingController::class, 'index'])
                ->with('success', utrans('billing.paymentWasSuccessful'));
        } else {
            return redirect()->back()->withErrors(utrans('errors.paymentFailed'));
        }
    }

    /**
     * Process a submitted payment
     */
    public function submitPayment(CreditCardPaymentRequest $request): RedirectResponse
    {
        switch ($request->input('payment_method')) {
            case 'new_card':
                try {
                    $result = $this->payWithNewCreditCard($request);
                } catch (Exception $e) {
                    Log::error($e->getMessage());

                    return redirect()->back()->withErrors($e->getMessage())->withInput();
                }
                break;
            case 'paypal':
                $paypalController = new PayPalController();
                try {
                    $redirectLink = $paypalController->generateApprovalLink($request->input('amount'));
                } catch (Exception $e) {
                    Log::error($e->getMessage());

                    return redirect()->back()->withErrors(utrans('errors.paypalFailed'));
                }

                return redirect()->to($redirectLink);

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
        if ($result->success == true) {
            return redirect()
                ->action([BillingController::class, 'index'])
                ->with('success', utrans('billing.paymentWasSuccessful'));
        } else {
            return redirect()->back()->withErrors(utrans('errors.paymentFailed'));
        }
    }

    /**
     * Delete an existing payment method from a customer account.
     */
    public function deletePaymentMethod($id): RedirectResponse
    {
        $paymentMethods = $this->getPaymentMethods();
        foreach ($paymentMethods as $paymentMethod) {
            if ((int) $paymentMethod->id === (int) $id) {
                try {
                    $this->accountBillingController->deletePaymentMethodByID(get_user()->account_id, $id);
                    $this->clearBillingCache();

                    return redirect()
                        ->action([BillingController::class, 'index'])
                        ->with('success', utrans('billing.creditCardDeleted'));
                } catch (Exception $e) {
                    //
                }
            }
        }

        return redirect()->back()->withErrors(utrans('errors.paymentMethodNotFound'));
    }

    /**
     * Toggle the auto pay setting on a payment method
     */
    public function toggleAutoPay($id): RedirectResponse
    {
        $paymentMethods = $this->getPaymentMethods();
        foreach ($paymentMethods as $paymentMethod) {
            if ((int) $paymentMethod->id === (int) $id) {
                try {
                    $existingAutoSetting = (bool) $paymentMethod->auto;
                    $this->accountBillingController->setAutoOnPaymentMethod(
                        get_user()->account_id,
                        $paymentMethod->id,
                        ! $existingAutoSetting
                    );
                    $this->clearBillingCache();
                    if ($existingAutoSetting === true) {
                        return redirect()
                            ->action([BillingController::class, 'index'])
                            ->with('success', utrans('billing.autoPayDisabled'));
                    }

                    return redirect()
                        ->action([BillingController::class, 'index'])
                        ->with('success', utrans('billing.autoPayEnabled'));
                } catch (Exception $e) {
                    //
                }
            }
        }

        return redirect()->back()->withErrors(utrans('errors.paymentMethodNotFound'));
    }

    /**
     * Show page to create a new payment method
     */
    public function createPaymentMethod($type): Factory|View|RedirectResponse
    {
        switch ($type) {
            case 'credit_card':
                if (config('customer_portal.stripe_enabled') == 1) {
                    $stripe = new PortalStripe();
                    $systemSettings = SystemSetting::first();

                    return view('pages.billing.add_card_stripe', [
                        'secret' => $stripe->setupIntent(),
                        'key' => $systemSettings->stripe_public_api_key,
                    ]);
                } else {
                    return view('pages.billing.add_card');
                }

            case 'bank':
                if (config('customer_portal.enable_gocardless') == 1) {
                    $gocardless = new GoCardless();

                    return Redirect::away($gocardless->createRedirect());
                } else {
                    return view('pages.billing.add_bank');
                }

            default:
                return redirect()->back()->withErrors(utrans('errors.invalidPaymentMethodType'));
        }
    }

    /**
     * Store a new tokenized credit card
     */
    public function storeTokenizedCard(CreateTokenizedCreditCardRequest $request): RedirectResponse
    {
        if (config('customer_portal.enable_credit_card_payments') == false) {
            throw new InvalidArgumentException(utrans('errors.creditCardPaymentsDisabled'));
        }

        try {
            $card = $this->createTokenizedCreditCardObjectFromRequest($request);
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }

        try {
            $this->accountBillingController->createTokenizedCreditCard(
                get_user()->account_id,
                $card,
                (bool) $request->input('auto')
            );
        } catch (Exception $e) {
            return redirect()->back()->withErrors(utrans('errors.failedToCreateCard'))->withInput();
        }

        $this->clearBillingCache();

        return redirect()->action([BillingController::class, 'index'])->with('success', utrans('billing.cardAdded'));
    }

    /**
     * Store a new credit card
     */
    public function storeCard(CreateCreditCardRequest $request): RedirectResponse
    {
        if (config('customer_portal.enable_credit_card_payments') == false) {
            throw new InvalidArgumentException(utrans('errors.creditCardPaymentsDisabled'));
        }

        try {
            $card = $this->createCreditCardObjectFromRequest($request);
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }

        try {
            $this->accountBillingController->createCreditCard(
                get_user()->account_id,
                $card,
                (bool) $request->input('auto')
            );
        } catch (Exception $e) {
            return redirect()->back()->withErrors(utrans('errors.failedToCreateCard'))->withInput();
        }

        unset($card);
        unset($request);

        $this->clearBillingCache();

        return redirect()->action([BillingController::class, 'index'])->with('success', utrans('billing.cardAdded'));
    }

    /**
     * Store a new credit card
     */
    public function storeBank(CreateBankAccountRequest $request): RedirectResponse
    {
        if (config('customer_portal.enable_bank_payments') != true) {
            return redirect()->back()->withErrors(utrans('errors.failedToCreateBankAccount'))->withInput();
        }

        try {
            $bankAccount = $this->createBankAccountObjectFromRequest($request);
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }

        try {
            $address = [
                'line1' => $request->input('line1'),
                'city' => $request->input('city'),
                'state' => $request->input('state'),
                'zip' => $request->input('zip'),
                'country' => $request->input('country'),
            ];
            $this->accountBillingController->createBankAccount(
                get_user()->account_id,
                $bankAccount,
                (bool) $request->input('auto'),
                $address
            );
        } catch (Exception $e) {
            Log::error($e);

            return redirect()->back()->withErrors(utrans('errors.failedToCreateBankAccount'))->withInput();
        }

        unset($bankAccount);
        unset($request);

        $this->clearBillingCache();

        return redirect()
            ->action([BillingController::class, 'index'])
            ->with('success', utrans('billing.bankAccountAdded'));
    }

    /**
     * Make a payment with an existing payment method
     *
     * @throws Exception
     */
    private function payWithExistingPaymentMethod($request): mixed
    {
        try {
            $result = $this->accountBillingController->makePaymentUsingExistingPaymentMethod(
                get_user()->account_id,
                intval($request->input('payment_method')),
                trim($request->input('amount')),
                $request->input('payment_tracker_id')
            );
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new Exception(utrans('billing.paymentFailedTryAnother'));
        }

        if ($result->success !== true) {
            throw new Exception(utrans('billing.paymentFailedTryAnother'));
        }

        Log::info(json_encode($result));

        return $result;
    }

    /**
     * Make a payment with a new credit card
     */
    private function payWithNewCreditCard(CreditCardPaymentRequest $request): mixed
    {
        if (config('customer_portal.enable_credit_card_payments') == false) {
            throw new InvalidArgumentException(utrans('errors.creditCardPaymentsDisabled'));
        }

        $creditCard = $this->createCreditCardObjectFromRequest($request);

        try {
            $result = $this->accountBillingController->makeCreditCardPayment(
                get_user()->account_id,
                $creditCard,
                $request->input('amount'),
                (bool) $request->input('makeAuto'),
                $request->input('payment_tracker_id')
            );
        } catch (Exception $e) {
            throw new InvalidArgumentException(utrans('billing.errorSubmittingPayment'));
        }

        if ($result->success !== true) {
            throw new InvalidArgumentException(utrans('errors.paymentFailed'));
        }

        unset($creditCard);
        unset($request);

        return $result;
    }

    /**
     * Make a payment with a new tokenized credit card
     */
    public function payWithNewTokenizedCreditCard(TokenizedCreditCardPaymentRequest $request): mixed
    {
        if (config('customer_portal.enable_credit_card_payments') == false) {
            throw new InvalidArgumentException(utrans('errors.creditCardPaymentsDisabled'));
        }

        $creditCard = $this->createTokenizedCreditCardObjectFromRequest($request);

        try {
            $result = $this->accountBillingController->makeTokenizedCreditCardPayment(
                get_user()->account_id,
                $creditCard,
                $request->input('amount'),
                (bool) $request->input('makeAuto'),
                $request->input('payment_tracker_id')
            );
        } catch (Exception $e) {
            throw new InvalidArgumentException(utrans('billing.errorSubmittingPayment'));
        }

        if ($result->success !== true) {
            throw new InvalidArgumentException(utrans('errors.paymentFailed'));
        }

        unset($creditCard);
        unset($request);

        return $result;
    }

    /**
     * Get account billing details
     */
    private function getAccountBillingDetails(): mixed
    {
        if (! Cache::tags('billing.details')->has(get_user()->account_id)) {
            $billingDetails = $this->accountBillingController->getAccountBillingDetails(get_user()->account_id);
            Cache::tags('billing.details')->put(
                get_user()->account_id,
                $billingDetails,
                Carbon::now()->addMinutes(10)
            );
        }

        return Cache::tags('billing.details')->get(get_user()->account_id);
    }

    /**
     * Get the invoice list for a user. This will only retrieve the last 100.
     */
    private function getInvoices(): mixed
    {
        if (! Cache::tags('billing.invoices')->has(get_user()->account_id)) {
            $invoicesToReturn = [];
            $invoices = $this->accountBillingController->getInvoices(get_user()->account_id);
            foreach ($invoices as $invoice) {
                //This check is here because this property did not exist prior to Sonar 0.6.6
                if (property_exists($invoice, 'void')) {
                    if ($invoice->void != 1) {
                        array_push($invoicesToReturn, $invoice);
                    }
                } else {
                    array_push($invoicesToReturn, $invoice);
                }
            }
            Cache::tags('billing.invoices')->put(
                get_user()->account_id,
                $invoicesToReturn,
                Carbon::now()->addMinutes(10)
            );
        }

        return Cache::tags('billing.invoices')->get(get_user()->account_id);
    }

    /**
     * Get the transaction list for a user. This will only display the last 100 currently.
     */
    private function getTransactions(): mixed
    {
        if (! Cache::tags('billing.transactions')->has(get_user()->account_id)) {
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

            Cache::tags('billing.transactions')->put(
                get_user()->account_id,
                $transactions,
                Carbon::now()->addMinutes(10)
            );
        }

        return Cache::tags('billing.transactions')->get(get_user()->account_id);
    }

    /**
     * Check if the payment is past due
     */
    private function isPaymentPastDue(): bool
    {
        $invoices = $this->getInvoices();
        $now = Carbon::now(config('app.timezone'));

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
     */
    private function generatePaymentMethodListForPaymentPage(): array
    {
        $paymentMethods = [];
        $validAccountMethods = $this->getPaymentMethods();
        foreach ($validAccountMethods as $validAccountMethod) {
            if (
                $validAccountMethod->type == 'credit card'
                && config('customer_portal.enable_credit_card_payments') == 1
            ) {
                $paymentMethods[$validAccountMethod->id] = utrans(
                    'billing.payUsingExistingCard',
                    [
                        'card' => '****'.$validAccountMethod->identifier.' ('
                            .sprintf('%02d', $validAccountMethod->expiration_month).' / '
                            .$validAccountMethod->expiration_year.')'
                    ]
                );
            } elseif (
                (
                    config('customer_portal.enable_bank_payments') == 1
                    || config('customer_portal.enable_gocardless') == 1
                )
                && $validAccountMethod->type != 'credit card'
            ) {
                $paymentMethods[$validAccountMethod->id] = utrans(
                    'billing.payUsingExistingBankAccount',
                    ['accountNumber' => '**'.$validAccountMethod->identifier]
                );
            }
        }

        if (config('customer_portal.paypal_enabled') == 1) {
            $paymentMethods['paypal'] = utrans('billing.payWithPaypal');
        }
        if (config('customer_portal.enable_credit_card_payments') == 1) {
            $paymentMethods['new_card'] = utrans('billing.payWithNewCard');
        }

        $paymentMethods = array_reverse($paymentMethods, true);

        return $paymentMethods;
    }

    /**
     * Clear all the cached billing items.
     */
    public function clearBillingCache(): void
    {
        Cache::tags('billing.details')->forget(get_user()->account_id);
        Cache::tags('billing.invoices')->forget(get_user()->account_id);
        Cache::tags('billing.transactions')->forget(get_user()->account_id);
        Cache::tags('billing.payment_methods')->forget(get_user()->account_id);
    }

    /**
     * Create a credit card object from a request containing cc-number, name, and expirationDate
     */
    private function createCreditCardObjectFromRequest($request): CreditCard
    {
        $card = CreditCardValidator::validCreditCard(trim(str_replace(' ', '', $request->input('cc-number'))));
        if ($card['valid'] !== true) {
            throw new InvalidArgumentException(utrans('errors.invalidCreditCardNumber'));
        }

        [$year, $month] = convertExpirationDateToYearAndMonth(
            $request->input('expirationDate')
        );

        if (! CreditCardValidator::validDate($year, $month)) {
            throw new InvalidArgumentException(utrans('errors.invalidExpirationDate'));
        }

        return new CreditCard([
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
    }

    /**
     * Create a tokenized credit card object from a request
     */
    private function createTokenizedCreditCardObjectFromRequest($request): TokenizedCreditCard
    {
        [$year, $month] = convertExpirationDateToYearAndMonth(
            $request->input('expirationDate')
        );

        if (! CreditCardValidator::validDate($year, $month)) {
            throw new InvalidArgumentException(utrans('errors.invalidExpirationDate'));
        }

        return new TokenizedCreditCard([
            'customer_id' => $request->input('customerId'),
            'token' => $request->input('token'),
            'identifier' => $request->input('identifier'),
            'expiration_year' => $year,
            'expiration_month' => intval($month),
            'line1' => $request->input('line1'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'zip' => $request->input('zip'),
            'country' => $request->input('country'),
            'name' => $request->input('name'),
            'card_type' => $request->input('cardType'),
        ]);
    }

    private function createBankAccountObjectFromRequest(CreateBankAccountRequest $request): BankAccount
    {
        return new BankAccount([
            'name' => trim($request->input('name')),
            'type' => trim($request->input('account_type')),
            'account_number' => trim($request->input('account_number')),
            'routing_number' => trim($request->input('routing_number')),
        ]);
    }

    /**
     * Get cached usage based billing policy details. Cache is lower on this one in case service is changed.
     */
    private function getPolicyDetails(): mixed
    {
        if (! Cache::tags('usage_based_billing_policy_details')->has(get_user()->account_id)) {
            $policyDetails = $this->frameworkDataUsageController->getUsageBasedBillingPolicyDetails(
                get_user()->account_id
            );
            Cache::tags('usage_based_billing_policy_details')->put(
                get_user()->account_id,
                $policyDetails,
                Carbon::now()->addMinutes(10)
            );
        }

        return Cache::tags('usage_based_billing_policy_details')->get(get_user()->account_id);
    }

    /**
     * Get cached historical data usage
     */
    private function getHistoricalUsage(): mixed
    {
        if (! Cache::tags('historical_data_usage')->has(get_user()->account_id)) {
            $dataUsage = $this->formatHistoricalUsageData(
                array_slice(
                    $this->frameworkDataUsageController->getAggregatedDataUsage(get_user()->account_id),
                    0,
                    12
                )
            );
            Cache::tags('historical_data_usage')->put(
                get_user()->account_id,
                $dataUsage,
                Carbon::now()->addMinutes(60)
            );
        }

        return Cache::tags('historical_data_usage')->get(get_user()->account_id);
    }

    /**
     * Convert all historical usage to gigabytes
     */
    private function formatHistoricalUsageData($historicalUsageData): array
    {
        $formattedData = [];
        foreach ($historicalUsageData as $datum) {
            $timestamp = new Carbon($datum->start_time, 'UTC');
            array_push($formattedData, [
                'timestamp' => $timestamp->toRfc3339String(),
                'billable' => round(($datum->billable_in_bytes + $datum->billable_out_bytes) / 1000 ** 3, 2),
                'free' => round(($datum->free_in_bytes + $datum->free_out_bytes) / 1000 ** 3, 2),
            ]);
        }

        return $formattedData;
    }

    private function paginate($items, $perPage, $setDefaultOption = true, $options = [])
    {
        if ($setDefaultOption) {
            $options = ['path' => request()->url(), 'query' => request()->query()];
        }

        $requestUrl = $this->cleanUrl(request()->url());

        if (isset($options['path']) && $_SERVER['HTTP_HOST'].$options['path'] == $requestUrl) {
            $page = request()->input('page', 1); // Get the current page or default to 1
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

    public function wifiManagement(WifiRequest $request): RedirectResponse
    {

        $qcore_username = config('services.qcore.username');
        $qcore_password = config('services.qcore.password');
        $qcore_uri = config('services.qcore.qcore_uri');
        
        $qcore_data = [
            'username' => $qcore_username,
            'password' => $qcore_password,
        ];

        try {
            $qcore_response = Http::timeout(10)->post($qcore_uri.'/api/v1/api-token-auth/', $qcore_data);
            if ($qcore_response->successful()) {
                $response_data = $qcore_response->json();
                $token = $response_data['token'];
            } else {
                return redirect()->back()->with('error', 'Failed to submit Wi-Fi management data.');
            }
    
            $data = $request->only(['wifi_band', 'ssid', 'password']);
        
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $token,
                'Accept' => 'application/json',
            ])->timeout(10)->post($qcore_uri.'/api/v1/qportal/wifi-info/'.get_user()->account_id.'/update/', $data);
    
            if ($response->successful()) {
                return redirect()->back()->with('success', 'Wi-Fi management data submitted successfully.');
            } else {
                return redirect()->back()->with('error', 'Failed to submit Wi-Fi management data.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to submit Wi-Fi management data.');
        } 

    }

    public function wifiIndex(): Factory|View
    {
        $wifiData = [];

        $qcore_username = config('services.qcore.username');
        $qcore_password = config('services.qcore.password');
        $qcore_uri = config('services.qcore.qcore_uri');
        
        $qcore_data = [
            'username' => $qcore_username,
            'password' => $qcore_password,
        ];

        try {

            $qcore_response = Http::timeout(10)->post($qcore_uri.'/api/v1/api-token-auth/', $qcore_data);

            if ($qcore_response->successful()) {
                
                $response_data = $qcore_response->json();
                $token = $response_data['token'];
    
                $response = Http::withHeaders([
                    'Authorization' => 'Token ' . $token,
                    'Accept' => 'application/json',
                ])->timeout(10)->get($qcore_uri.'/api/v1/qportal/wifi-info/'.get_user()->account_id.'/');
    
                if ($response->successful()) {
                    $data = $response->json();
                    $wifiData = $data['data'] ?? [];
                }
            } 
        } catch (Exception $e) {
            $wifiData = [];
        } 


        return view('pages.wifi.index',compact('wifiData'));
    }

    public function packageIndex(): Factory|View
    {
        $qcore_username = config('services.qcore.username');
        $qcore_password = config('services.qcore.password');
        $qcore_uri = config('services.qcore.qcore_uri');

        $qcore_data = [
            'username' => $qcore_username,
            'password' => $qcore_password,
        ];

        try {
            $qcore_response = Http::timeout(10)->post($qcore_uri.'/api/v1/api-token-auth/', $qcore_data);
        
            if ($qcore_response->successful()) {
                $response_data = $qcore_response->json();
                $token = $response_data['token'];
        
                $response = Http::withHeaders([
                    'Authorization' => 'Token ' . $token,
                    'Accept' => 'application/json',
                ])->timeout(10)->get($qcore_uri.'/api/v1/qportal/services/'.get_user()->account_id.'/upgradable/list/');

                if ($response->successful()) {
                    $services = $response->json();

                }

                $response_account_info = Http::withHeaders([
                    'Authorization' => 'Token ' . $token,
                    'Accept' => 'application/json',
                ])->timeout(10)->get($qcore_uri.'/api/v1/qportal/sonar-account-info/'.get_user()->account_id.'/');
                
                if ($response_account_info->successful()) {
                    $acc_data = $response_account_info->json();
                    $account_service_id = $acc_data['account_service_id'] ?? '';
                }
            }
        } catch (Exception $e) {
            $services = [];
            $account_service_id = '';
        }
        if(empty($account_service_id)) {
            abort(404);
        }
        $paymentMethods = $this->getPaymentMethods();
        return view('pages.package.index',compact('paymentMethods', 'services', 'account_service_id'));
    }

    public function packageSubscription(Request $request): RedirectResponse
    {
        $account_id = $request->input('account_service_id');
        $new_service_id = $request->input('new_service_id');
    
        $qcore_username = config('services.qcore.username');
        $qcore_password = config('services.qcore.password');
        $qcore_uri = config('services.qcore.qcore_uri');
        
        $qcore_data = [
            'username' => $qcore_username,
            'password' => $qcore_password,
        ];
    
        try {
            $qcore_response = Http::timeout(10)->post($qcore_uri.'/api/v1/api-token-auth/', $qcore_data);
            if ($qcore_response->successful()) {
                $response_data = $qcore_response->json();
                $token = $response_data['token'];
            } else {
                return redirect()->back()->with('error', 'Failed to upgrade service.');
            }
    
            $data = [
                'new_service_id' => $new_service_id,
                'account_service_id' => $account_id,
            ];
        
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $token,
                'Accept' => 'application/json',
            ])->put($qcore_uri.'/api/v1/qportal/services/'.get_user()->account_id.'/upgrade/', $data);
    
            if ($response->successful()) {
                return redirect()->route('portal.billing.index')->with('success', 'Service upgraded successfully.');
            } else {
                return redirect()->back()->with('error', 'Failed to upgrade service.');
            }
        } catch (Exception $e) {
            // dd($e);
            return redirect()->back()->with('error', 'Failed to upgrade service.');
        }
    }
}
