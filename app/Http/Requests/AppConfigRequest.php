<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppConfigRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'url' => 'required|url',
            'locale' => 'required|in:' . implode(",", array_keys(getAvailableLanguages())),
            'mail_host' => 'required|string',
            'mail_username' => 'required|string',
            'mail_password' => 'required|string',
            'mail_port' => 'required|integer|between:0,65535',
            'mail_encryption' => 'boolean',
            'mail_from_address' => 'required|string',
            'mail_from_name' => 'required|string',
            'isp_name' => 'required|string',
            'decimal_separator' => 'required|string',
            'thousands_separator' => 'required|string',
            'currency_symbol' => 'required|string',
            'country' => 'required|in:' . implode(",", array_keys(countries())),
            'state' => 'required|string',
            'data_usage_enabled' => 'boolean',
            'contracts_enabled' => 'boolean',
            'password_strength_required' => 'integer|between:1,5',
            'show_detailed_transactions' => 'boolean',
            'credit_card_payments_enabled' => 'boolean',
            'bank_payments_enabled' => 'boolean',
            'go_cardless_enabled' => 'boolean',
            'go_cardless_environment' => 'boolean',
            'go_cardless_access_token' => 'required_if:go_cardless_enabled,1',
            'go_cardless_currency_code' => 'required_if:go_cardless_enabled,1',
            'paypal_enabled' => 'boolean',
            'paypal_api_client' => 'required_if:paypal_enabled,1',
            'paypal_api_client_secret' => 'required_if:paypal_enabled,1',
            'paypal_currency_code' => 'string|required_if:paypal_enabled,1',
            'ticketing_enabled' => 'boolean',
            'inbound_email_account_id' => 'integer|min:1|required_if:ticketing_enabled,1',
            'ticket_group_id' => 'integer|min:1|required_if:ticketing_enabled,1',
            'ticket_priority' => 'integer|between:1,4|required_if:ticketing_enabled,1',
        ];
    }
}
