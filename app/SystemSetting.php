<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $guarded = [];

    protected $attributes = [
        'isp_name' => 'ISP',
        'mail_encryption' => false,
        'ticket_priority' => 4,
        'data_usage_enabled' => false,
        'contracts_enabled' => false,
        'show_detailed_transactions' => false,
        'credit_card_payments_enabled' => true,
        'bank_payments_enabled' => false,
        'go_cardless_enabled' => false,
        'paypal_enabled' => false,
        'ticketing_enabled' => false,
        'mail_port' => 25,
        'decimal_separator' => '.',
        'thousands_separator' => ',',
        'currency_symbol' => '$',
        'country' => 'US',
        'state' => 'WI',
        'password_strength_required' => 1,
        'go_cardless_currency_code' => 'EUR',
        'paypal_currency_code' => 'USD',
        'stripe_enabled' => false,
        'return_refund_policy_link' => '',
        'privacy_policy_link' => '',
        'customer_service_contact_email' => '',
        'customer_service_contact_phone' => '',
        'company_address' => '',
        'transaction_currency' => 'USD',
        'delivery_policy_link' => '',
        'consumer_data_privacy_policy_link' => '',
        'secure_checkout_policy_link' => '',
        'terms_and_conditions_link' => '',

    ];

    protected $casts = [
        'show_detailed_transactions' => 'boolean',
        'enable_bank_payments' => 'boolean',
        'enable_credit_card_payments' => 'boolean',
        'paypal_enabled' => 'boolean',
        'enable_gocardless' => 'boolean',
        'ticketing_enabled' => 'boolean',
        'data_usage_enabled' => 'boolean',
        'contracts_enabled' => 'boolean',
        'stripe_enabled' => 'boolean',
    ];
}
