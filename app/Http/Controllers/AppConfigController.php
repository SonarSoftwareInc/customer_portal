<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppConfigRequest;
use App\Http\Requests\SettingsAuthRequest;
use App\SystemSetting;
use App\Traits\Throttles;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use SonarSoftware\CustomerPortalFramework\Helpers\HttpHelper;
use ZipArchive;

class AppConfigController extends Controller
{
    use Throttles;

    public function authenticate(SettingsAuthRequest $request): RedirectResponse
    {
        $systemSetting = SystemSetting::first();
        if (! $systemSetting) {
            return redirect()->back()->withErrors(trans('errors.noKeyFound'));
        }

        if ($this->getThrottleValue('settings', $request->getClientIp()) > 10) {
            return redirect()->back()->withErrors(utrans('errors.tooManyFailedAuthenticationAttempts', [], $request));
        }

        if ($systemSetting->settings_key && $systemSetting->settings_key == $request->input('key')) {
            $this->resetThrottleValue('settings', $request->getClientIp());
            $request->session()->put('settings_authenticated', 1);

            return redirect()->action([AppConfigController::class, 'show']);
        }

        $this->incrementThrottleValue('settings', $request->getClientIp());

        return redirect()->back()->withErrors(trans('errors.invalidSettingsKey'));
    }

    public function show(Request $request): View
    {
        if ($request->session()->get('settings_authenticated') === 1) {
            $httpHelper = new HttpHelper();
            $inboundEmailAccountResult = $httpHelper->get('/system/tickets/inbound_email_accounts');
            $inboundEmailAccounts = [];
            foreach ($inboundEmailAccountResult as $inboundEmailAccountDatum) {
                $inboundEmailAccounts[$inboundEmailAccountDatum->id] = $inboundEmailAccountDatum->name;
            }
            $ticketGroupResult = $httpHelper->get('/system/tickets/ticket_groups');
            $ticketGroups = [];
            foreach ($ticketGroupResult as $ticketGroupDatum) {
                $ticketGroups[$ticketGroupDatum->id] = $ticketGroupDatum->name;
            }

            $systemSetting = SystemSetting::firstOrNew([
                'id' => 1,
            ]);

            $paypalCurrency = $this->paypalCurrency();

            $currencies = $this->allCurrencies();

            return view('pages.config.show', compact(
                'inboundEmailAccounts',
                'ticketGroups',
                'systemSetting',
                'paypalCurrency',
                'currencies',
            ));
        }

        return view('pages.config.auth');
    }

    public function save(AppConfigRequest $request): RedirectResponse
    {
        if ($request->session()->get('settings_authenticated') === 1) {
            $systemSetting = SystemSetting::firstOrNew([
                'id' => 1,
            ]);

            /**
             * Image Upload
             */
            if ($request->hasFile('image')) {
                $request->file('image');
                $request->file('image')->move(base_path('public/assets/img/'), 'logo.png');
            }

            if ($request->hasFile('cover')) {
                $request->file('cover');
                $request->file('cover')->move(base_path('public/assets/img/'), 'cover.png');
            }

            if ($request->hasFile('fcclabels')) {
                $request->file('fcclabels');
                $request->file('fcclabels')->move(base_path('public/assets/fcclabels/'), 'labels.zip');
                $zip = new ZipArchive;
                if ($zip->open(base_path('public/assets/fcclabels/labels.zip')) === TRUE) {
                    $zip->extractTo(base_path('public/assets/fcclabels/'));
                    $zip->close();
                } else {
                    echo 'failed';
                }
            }

            /**
             * System Settings
             */
            $systemSetting->fill($request->only([
                'url',
                'locale',
                'mail_host',
                'mail_port',
                'mail_username',
                'mail_password',
                'mail_encryption',
                'mail_from_address',
                'mail_from_name',
                'isp_name',
                'decimal_separator',
                'thousands_separator',
                'currency_symbol',
                'country',
                'state',
                'login_page_message',
                'data_usage_enabled',
                'contracts_enabled',
                'password_strength_required',
                'show_detailed_transactions',
                'credit_card_payments_enabled',
                'bank_payments_enabled',
                'go_cardless_enabled',
                'go_cardless_environment',
                'go_cardless_access_token',
                'go_cardless_currency_code',
                'stripe_enabled',
                'stripe_private_api_key',
                'stripe_public_api_key',
                'paypal_enabled',
                'paypal_api_client',
                'paypal_api_client_secret',
                'paypal_currency_code',
                'ticketing_enabled',
                'inbound_email_account_id',
                'ticket_group_id',
                'ticket_priority',
                'return_refund_policy_link',
                'privacy_policy_link',
                'customer_service_contact_email',
                'customer_service_contact_phone',
                'company_address',
                'transaction_currency',
                'delivery_policy_link',
                'consumer_data_privacy_policy_link',
                'secure_checkout_policy_link',
                'terms_and_conditions_link',
            ]));

            /**
             * CHANGE LOGIN MESSAGE TO HTMLENTITIES
             */
            $systemSetting->login_page_message = htmlentities($request->input('login_page_message'), ENT_QUOTES);

            $systemSetting->save();

            return redirect()->action([AppConfigController::class, 'show']);
        }

        abort(401);
    }

    private function paypalCurrency(): array
    {
        return [
            'USD' => 'USD',
            'BRL' => 'BRL',
            'CAD' => 'CAD',
            'CZK' => 'CZK',
            'DKK' => 'DKK',
            'EUR' => 'EUR',
            'HKD' => 'HKD',
            'HUF' => 'HUF',
            'INR' => 'INR',
            'ILS' => 'ILS',
            'JPY' => 'JPY',
            'MYR' => 'MYR',
            'MXN' => 'MXN',
            'TWD' => 'TWD',
            'NZD' => 'NZD',
            'NOK' => 'NOK',
            'PHP' => 'PHP',
            'PLN' => 'PLN',
            'GBP' => 'GBP',
            'RUB' => 'RUB',
            'SGD' => 'SGD',
            'SEK' => 'SEK',
            'CHF' => 'CHF',
            'THB' => 'THB',
            'XCD' => 'XCD',
        ];
    }

    private function allCurrencies(): array
    {
        return [
            'USD' => 'USD',
            'CAD' => 'CAD',
            'EUR' => 'EUR',
            'GBP' => 'GBP',
            'AED' => 'AED',
            'AFN' => 'AFN',
            'ALL' => 'ALL',
            'AMD' => 'AMD',
            'ANG' => 'ANG',
            'AOA' => 'AOA',
            'ARS' => 'ARS',
            'AUD' => 'AUD',
            'AWG' => 'AWG',
            'AZN' => 'AZN',
            'BAM' => 'BAM',
            'BBD' => 'BBD',
            'BDT' => 'BDT',
            'BGN' => 'BGN',
            'BMD' => 'BMD',
            'BND' => 'BND',
            'BOB' => 'BOB',
            'BRL' => 'BRL',
            'BSD' => 'BSD',
            'BTN' => 'BTN',
            'BWP' => 'BWP',
            'BYN' => 'BYN',
            'BZD' => 'BZD',
            'CDF' => 'CDF',
            'CHF' => 'CHF',
            'CNY' => 'CNY',
            'COP' => 'COP',
            'CRC' => 'CRC',
            'CUC' => 'CUC',
            'CUP' => 'CUP',
            'CVE' => 'CVE',
            'CZK' => 'CZK',
            'DKK' => 'DKK',
            'DOP' => 'DOP',
            'DZD' => 'DZD',
            'EGP' => 'EGP',
            'ERN' => 'ERN',
            'ETB' => 'ETB',
            'FJD' => 'FJD',
            'FKP' => 'FKP',
            'GEL' => 'GEL',
            'GHS' => 'GHS',
            'GIP' => 'GIP',
            'GMD' => 'GMD',
            'GTQ' => 'GTQ',
            'HKD' => 'HKD',
            'HNL' => 'HNL',
            'HTG' => 'HTG',
            'HUF' => 'HUF',
            'IDR' => 'IDR',
            'ILS' => 'ILS',
            'INR' => 'INR',
            'IRR' => 'IRR',
            'JMD' => 'JMD',
            'KES' => 'KES',
            'KGS' => 'KGS',
            'KHR' => 'KHR',
            'KPW' => 'KPW',
            'KYD' => 'KYD',
            'KZT' => 'KZT',
            'LAK' => 'LAK',
            'LBP' => 'LBP',
            'LKR' => 'LKR',
            'LRD' => 'LRD',
            'LSL' => 'LSL',
            'MAD' => 'MAD',
            'MDL' => 'MDL',
            'MGA' => 'MGA',
            'MKD' => 'MKD',
            'MMK' => 'MMK',
            'MNT' => 'MNT',
            'MOP' => 'MOP',
            'MRU' => 'MRU',
            'MUR' => 'MUR',
            'MVR' => 'MVR',
            'MWK' => 'MWK',
            'MXN' => 'MXN',
            'MYR' => 'MYR',
            'MZN' => 'MZN',
            'NAD' => 'NAD',
            'NGN' => 'NGN',
            'NIO' => 'NIO',
            'NOK' => 'NOK',
            'NPR' => 'NPR',
            'NZD' => 'NZD',
            'PAB' => 'PAB',
            'PEN' => 'PEN',
            'PGK' => 'PGK',
            'PHP' => 'PHP',
            'PKR' => 'PKR',
            'PLN' => 'PLN',
            'QAR' => 'QAR',
            'RON' => 'RON',
            'RSD' => 'RSD',
            'RUB' => 'RUB',
            'SAR' => 'SAR',
            'SBD' => 'SBD',
            'SCR' => 'SCR',
            'SDG' => 'SDG',
            'SEK' => 'SEK',
            'SGD' => 'SGD',
            'SHP' => 'SHP',
            'SLL' => 'SLL',
            'SOS' => 'SOS',
            'SRD' => 'SRD',
            'SSP' => 'SSP',
            'STN' => 'STN',
            'SVC' => 'SVC',
            'SYP' => 'SYP',
            'SZL' => 'SZL',
            'THB' => 'THB',
            'TJS' => 'TJS',
            'TMT' => 'TMT',
            'TOP' => 'TOP',
            'TRY' => 'TRY',
            'TTD' => 'TTD',
            'TWD' => 'TWD',
            'TZS' => 'TZS',
            'UAH' => 'UAH',
            'UYU' => 'UYU',
            'UZS' => 'UZS',
            'VES' => 'VES',
            'WST' => 'WST',
            'XCD' => 'XCD',
            'YER' => 'YER',
            'ZAR' => 'ZAR',
            'ZMW' => 'ZMW',
            'ZWL' => 'ZWL',
        ];
    }
}
