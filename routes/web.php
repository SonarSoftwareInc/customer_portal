<?php

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
Route::get("/settings", "AppConfigController@show");
Route::get("/settings/subdivisions/{country}", "SubdivisionController@authenticate");
Route::post("/settings", "AppConfigController@save");
Route::post("/settings/auth", "AppConfigController@authenticate");

Route::group(['middleware' => ['language']], function () {
    Route::group(['middleware' => ['web','guest']], function () {
        //Unauthenticated routes
        Route::get('/', 'AuthenticationController@index');
        Route::post('/', 'AuthenticationController@authenticate');
        Route::get('/register', 'AuthenticationController@showRegistrationForm');
        Route::post('/register', 'AuthenticationController@lookupEmail');
        Route::get('/create/{token}', 'AuthenticationController@showCreationForm');
        Route::post('/create/{token}', 'AuthenticationController@createAccount');
        Route::get('/reset', 'AuthenticationController@showResetPasswordForm');
        Route::post('/reset', 'AuthenticationController@sendResetEmail');
        Route::get('/reset/{token}', 'AuthenticationController@showNewPasswordForm');
        Route::post('/reset/{token}', 'AuthenticationController@updateContactWithNewPassword');
    });

    /**
     * Authenticated routes.
     */
    Route::group(['prefix' => 'portal', 'middleware' => ['web','auth']], function () {
		/**
         * Billing routes
         */
        Route::group(['prefix' => 'billing'], function () {
            Route::get('/', 'BillingController@index');
            Route::get('/transaction', 'BillingController@index');
            Route::get('/invoices', 'BillingController@index');
            Route::get('/invoices/{invoices}', 'BillingController@getInvoicePdf');
            Route::get('/payment_methods/{type}/create', 'BillingController@createPaymentMethod');
            Route::post('/payment_methods/card', 'BillingController@storeCard');
            Route::post('/payment_methods/bank', 'BillingController@storeBank');
            Route::delete('/payment_methods/{payment_methods}', 'BillingController@deletePaymentMethod');
            Route::patch('/payment_methods/{payment_methods}/toggle_auto', 'BillingController@toggleAutoPay');
            Route::get('/payment', 'BillingController@makePayment');
            Route::post('/payment', 'BillingController@submitPayment');

            /** Paypal Routes */
            Route::get('/paypal/{temporary_token}/complete', 'PayPalController@completePayment');
            Route::get('/paypal/{temporary_token}/cancel', 'PayPalController@cancelPayment');

            /** Subdivisions for cards */
            Route::get("subdivisions/{country}", "SubdivisionController@show");

            /** GoCardless success */
            Route::get("debit_add_success","GoCardlessController@handleReturnRedirect");
        });

        /**
         * Profile routes
         */
        Route::group(['prefix' => 'profile'], function () {
            Route::get("/", "ProfileController@show");
            Route::patch("/", "ProfileController@update");
            Route::patch("/password", "ProfileController@updatePassword");
        });

        /**
         * Ticketing routes
         */
        Route::group(['prefix' => 'tickets', 'middleware' => ['tickets']], function () {
            Route::get("/", "TicketController@index");
            Route::get("/create", "TicketController@create");
            Route::post("/", "TicketController@store");
            Route::get("/{tickets}", "TicketController@show");
            Route::post("/{tickets}/reply", "TicketController@postReply");
        });

        /**
         * Data usage routes
         */
        Route::group(['prefix' => 'data_usage', 'middleware' => ['data_usage']], function () {
            Route::get("/", "DataUsageController@index");
            Route::get("/top_off", "DataUsageController@showTopOff");
            Route::post("/add_top_off", "DataUsageController@addTopOff");
        });

        /**
         * Contract routes
         */
        Route::group(['prefix' => 'contracts', 'middleware' => ['contracts']], function () {
            Route::get("/", "ContractController@index");
            Route::get("/{contracts}", "ContractController@downloadContractPdf");
        });
    });

    Route::get('/logout', 'AuthenticationController@logout');
    Route::post("/language","LanguageController@update");
});
