<?php
return [
    /**
     * BASIC CONFIGURATION OPTIONS
     * YOU SHOULD NOT EDIT THESE PARAMETERS HERE!
     * Use the /settings page to update this.
     */

    /*
     * Company name to be presented to users (will be shown in the title, for example)
     */
    'company_name' => env('ISP_NAME', 'ISP'),

    /*
     * What character should be used to separate decimals (e.g. 100.34)
     */
    'decimal_separator' => env('DECIMAL_SEPARATOR,', '.'),

    /*
     * What character should be used to separate thousands (e.g. 1,000,000)
     */
    'thousands_separator' => env('THOUSANDS_SEPARATOR', ','),

    /*
     * What currency symbol do you use (e.g. $, £, ¥)
     */
    'currency_symbol' => env('CURRENCY_SYMBOL', '$'),

    /*
     * The country your ISP operates in
     */
    'country' => env('COUNTRY', 'US'),

    /*
     * The state or province your ISP operates in. Should be a two character code for the US and Canada (e.g. WI, AB) and the full name for other countries.
     */
    'state' => env('STATE', null),

    /*
     * A message to display on the login page
     */
    'login_page_message' => env('LOGIN_PAGE_MESSAGE', null),

    /**
     * BILLING CONFIGURATION OPTIONS
     */

    /*
     * Do you want to show detailed transactions or just invoices?
     */
    'show_detailed_transactions' => env('SHOW_DETAILED_TRANSACTIONS', false),

    /*
     * Do you want to allow ACH/eCheck payments?
     */
    'enable_bank_payments' => env('ENABLE_BANK_PAYMENTS', false),

    /*
     * Optionally limit bank payments only for accounts created before this date (YYYY-MM-DD)
     */
    'bank_payments_only_before' => env('BANK_PAYMENTS_ONLY_BEFORE', false),

    /*
     * Do you want to allow credit card payments?
     */
    'enable_credit_card_payments' => env("ENABLE_CREDIT_CARD_PAYMENTS",true),

    /*
     * If you wish to allow PayPal payments via the portal, set this to true
     */
    'paypal_enabled' => env('PAYPAL_ENABLED', false),

    /*
     * If paypal_enabled is true, these must both be set to valid, live, REST API credentials. These can be
     * generated at https://developer.paypal.com under 'My Apps and Credentials' by clicking 'Create App'.
     * Be sure to create LIVE credentials and not SANDBOX!
     */
    'paypal_api_client_id' => env('PAYPAL_API_CLIENT_ID', 'foo'),
    'paypal_api_client_secret' => env('PAYPAL_API_CLIENT_SECRET', 'bar'),

    /*
     * If you wish to allow Stripe payments via the portal, set this to true
     */
    'stripe_enabled' => env('STRIPE_ENABLED', false),

    /*
     * If stripe_enabled is true, these should be set to valid publishable and secret
     * Stripe Api Keys. These can be found at https://dashboard.stripe.com/ under 'Developers ->
     * API Keys`. Be sure to create LIVE credentials and not SANDBOX!
     */
    'stripe_private_api_key' => env('STRIPE_PRIVATE_API_KEY', 'foo'),
    'stripe_public_api_key' => env('STRIPE_PUBLIC_API_KEY', 'foo'),

    /*
     * You must input a valid currency code to use for PayPal from https://developer.paypal.com/docs/classic/api/currency_codes/
     * A sane default (USD) is provided, but ensure this is updated if you are not using US dollars.
     */
    'paypal_currency_code' => env('PAYPAL_CURRENCY_CODE', 'USD'),

    /*
     * These settings all relate to GoCardless integration.
     */
    'enable_gocardless' => env('ENABLE_GOCARDLESS', false),
    'gocardless_access_token' => env('GOCARDLESS_ACCESS_TOKEN', null),
    'gocardless_environment' => env('GOCARDLESS_ENVIRONMENT', \GoCardlessPro\Environment::LIVE),
    'gocardless_currency_code' => env('GOCARDLESS_CURRENCY_CODE', 'EUR'),

    /**
     * TICKETING OPTIONS
     */

    /*
     * If you wish to allow users to open tickets, and respond to their public tickets, then set this to true
     */
    'ticketing_enabled' => env('TICKETING_ENABLED', false),

    /*
     * If ticketing is enabled, you must set the ID of an inbound email account here that will be used to create new tickets
     */
    'inbound_email_account_id' => env('INBOUND_EMAIL_ACCOUNT_ID', null),

    /*
     * Which ticket group ID should tickets created via the portal be assigned to?
     */
    'ticket_group_id' => env('TICKET_GROUP_ID', null),

    /*
     * What priority should tickets be created at? 4 is low, 3 is medium, 2 is high, 1 is critical.
     */
    'ticket_priority' => env('TICKET_PRIORITY', 4),

    /**
     * EMAIL CONFIGURATION
     */

    /*
     * What address do you want outbound emails to be sent from?
     */
    'from_address' => env('FROM_ADDRESS', 'donotreply@isp-portal.net'),

    /*
     * What name should outbound emails be sent from?
     */
    'from_name' => env('FROM_NAME', 'ISP'),

    /**
     * DATA USAGE
     */
    'data_usage_enabled' => env('DATA_USAGE_ENABLED', true),

    /**
     * CONTRACTS
     */
    'contracts_enabled' => env('CONTRACTS_ENABLED', false),

    /**
     * PASSWORD STRENGTH
     */
    'password_strength_required' => env('PASSWORD_STRENGTH_REQUIRED', 2),
];
