<?php

use App\Http\Controllers\AppConfigController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DataUsageController;
use App\Http\Controllers\GoCardlessController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\SubdivisionController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/**
 * Admin routes
 */
Route::get('/settings', [AppConfigController::class, 'show']);
Route::get('/settings/subdivisions/{country}', [SubdivisionController::class, 'authenticate']);
Route::post('/settings', [AppConfigController::class, 'save']);
Route::post('/settings/auth', [AppConfigController::class, 'authenticate']);

Route::middleware('language')->group(function () {
    Route::middleware(['web', 'guest'])->group(function () {
        //Unauthenticated routes
        Route::get('/', [AuthenticationController::class, 'index']);
        Route::post('/', [AuthenticationController::class, 'authenticate']);
        Route::get('/register', [AuthenticationController::class, 'showRegistrationForm']);
        Route::post('/register', [AuthenticationController::class, 'lookupEmail']);
        Route::get('/create/{token}', [AuthenticationController::class, 'showCreationForm']);
        Route::post('/create/{token}', [AuthenticationController::class, 'createAccount']);
        Route::get('/reset', [AuthenticationController::class, 'showResetPasswordForm']);
        Route::post('/reset', [AuthenticationController::class, 'sendResetEmail']);
        Route::get('/reset/{token}', [AuthenticationController::class, 'showNewPasswordForm']);
        Route::post('/reset/{token}', [AuthenticationController::class, 'updateContactWithNewPassword']);
    });

    /**
     * Authenticated routes.
     */
    Route::prefix('portal')->middleware(['web', 'auth'])->group(function () {
        /**
         * Billing routes
         */
        Route::prefix('billing')->group(function () {
            Route::get('/', [BillingController::class, 'index'])->name('portal.billing.index');;
            Route::get('/transaction', [BillingController::class, 'index']);
            Route::get('/invoices', [BillingController::class, 'index']);
            Route::get('/invoices/{invoices}', [BillingController::class, 'getInvoicePdf']);
            Route::get('/payment_methods/{type}/create', [BillingController::class, 'createPaymentMethod']);
            Route::post('/payment_methods/card', [BillingController::class, 'storeCard']);
            Route::post('/payment_methods/tokenized_card/', [BillingController::class, 'storeTokenizedCard']);
            Route::post('/payment_methods/bank', [BillingController::class, 'storeBank']);
            Route::delete('/payment_methods/{payment_methods}', [BillingController::class, 'deletePaymentMethod']);
            Route::patch('/payment_methods/{payment_methods}/toggle_auto', [BillingController::class, 'toggleAutoPay']);
            Route::get('/payment', [BillingController::class, 'makePayment']);
            Route::post('/payment', [BillingController::class, 'submitPayment']);
            Route::post('/tokenized_payment', [BillingController::class, 'submitTokenizedPayment']);
            Route::patch('/wifi-data/update', [BillingController::class, 'wifiManagement'])->name('wifi.update');

            /** Paypal Routes */
            Route::get('/paypal/{temporary_token}/complete', [PayPalController::class, 'completePayment']);
            Route::get('/paypal/{temporary_token}/cancel', [PayPalController::class, 'cancelPayment']);

            /** Stripe Routes */
            Route::get('/stripe/{id}', [StripeController::class, 'paymentMethod']);

            /** Subdivisions for cards */
            Route::get('subdivisions/{country}', [SubdivisionController::class, 'show']);

            /** GoCardless success */
            Route::get('debit_add_success', [GoCardlessController::class, 'handleReturnRedirect']);
        });

        /**
         * Profile routes
         */
        Route::prefix('profile')->group(function () {
            Route::get('/', [ProfileController::class, 'show']);
            Route::patch('/', [ProfileController::class, 'update']);
            Route::patch('/password', [ProfileController::class, 'updatePassword']);
        });

        /**
         * Ticketing routes
         */
        Route::prefix('tickets')->middleware('tickets')->group(function () {
            Route::get('/', [TicketController::class, 'index']);
            Route::get('/create', [TicketController::class, 'create']);
            Route::post('/', [TicketController::class, 'store']);
            Route::get('/{tickets}', [TicketController::class, 'show']);
            Route::post('/{tickets}/reply', [TicketController::class, 'postReply']);
        });

        /**
         * Data usage routes
         */
        Route::prefix('data_usage')->middleware('data_usage')->group(function () {
            Route::get('/', [DataUsageController::class, 'index']);
            Route::get('/top_off', [DataUsageController::class, 'showTopOff']);
            Route::post('/add_top_off', [DataUsageController::class, 'addTopOff']);
        });

        /**
         * Contract routes
         */
        Route::prefix('contracts')->middleware('contracts')->group(function () {
            Route::get('/', [ContractController::class, 'index']);
            Route::get('/{contracts}', [ContractController::class, 'downloadContractPdf']);
        });

        Route::prefix('wifi-management')->group(function () {
            Route::get('/', [BillingController::class, 'wifiIndex']);
        });

        Route::prefix('service-upgrade')->group(function () {
            Route::get('/', [BillingController::class, 'packageIndex']);
            Route::put('/upgrade', [BillingController::class, 'packageSubscription']);
        });
    });

    Route::get('/logout', [AuthenticationController::class, 'logout']);
    Route::post('/language', [LanguageController::class, 'update']);


});
