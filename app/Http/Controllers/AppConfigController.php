<?php
namespace App\Http\Controllers;

use App\Http\Requests\AppConfigRequest;
use App\Http\Requests\SettingsAuthRequest;
use App\SystemSetting;
use App\Traits\Throttles;
use Carbon\Carbon;
use Exception;
use SonarSoftware\CustomerPortalFramework\Helpers\HttpHelper;
use View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class AppConfigController extends Controller
{
    use Throttles;

    public function authenticate(SettingsAuthRequest $request)
    {
        $systemSetting = SystemSetting::first();
        if (!$systemSetting) {
            return redirect()->back()->withErrors(trans('errors.noKeyFound'));
        }

        if ($this->getThrottleValue("settings", $request->getClientIp()) > 10) {
            return redirect()->back()->withErrors(utrans("errors.tooManyFailedAuthenticationAttempts",[],$request));
        }

        if ($systemSetting->settings_key && $systemSetting->settings_key == $request->input('key')) {
            $this->resetThrottleValue("settings", $request->getClientIp());
            $request->session()->put('settings_authenticated', 1);
            return redirect()->action('AppConfigController@show');
        }

        $this->incrementThrottleValue("settings", $request->getClientIp());
        return redirect()->back()->withErrors(trans('errors.invalidSettingsKey'));
    }

    public function show(Request $request)
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
                'id' => 1
            ]);

            $paypalCurrency = $this->paypalCurrency();

            return view("pages.config.show", compact(
                    'inboundEmailAccounts',
                    'ticketGroups',
                    'systemSetting',
                    'paypalCurrency'
                )
            );
        }

        return view("pages.config.auth");
    }

    public function save(AppConfigRequest $request)
    {
        if ($request->session()->get('settings_authenticated') === 1) {
            $systemSetting = SystemSetting::firstOrNew([
                'id' => 1
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
                'paypal_enabled',
                'paypal_api_client',
                'paypal_api_client_secret',
                'paypal_currency_code',
                'ticketing_enabled',
                'inbound_email_account_id',
                'ticket_group_id',
                'ticket_priority',
            ]));



            $systemSetting->save();

            return redirect()->action('AppConfigController@show');
        }

        abort(401);
    }

    private function paypalCurrency():array
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
        ];
    }
}
