<?php

namespace App\Providers;

use Exception;
use App\SystemSetting;
use App\Services\QCoreService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        try {
            $systemSetting = SystemSetting::firstOrNew([
                'id' => 1,
            ]);

            config([
                'app.name' => $systemSetting->isp_name,
                'app.url' => $systemSetting->url ?? 'http://localhost',
                'app.locale' => $systemSetting->locale,
                'customer_portal.company_name' => $systemSetting->isp_name,
                'customer_portal.decimal_separator' => $systemSetting->decimal_separator,
                'customer_portal.thousands_separator' => $systemSetting->thousands_separator,
                'customer_portal.currency_symbol' => $systemSetting->currency_symbol,
                'customer_portal.country' => $systemSetting->country,
                'customer_portal.state' => $systemSetting->state,
                'customer_portal.login_page_message' => $systemSetting->login_page_message,
                'customer_portal.show_detailed_transactions' => $systemSetting->show_detailed_transactions,
                'customer_portal.enable_bank_payments' => $systemSetting->bank_payments_enabled,
                'customer_portal.enable_credit_card_payments' => $systemSetting->credit_card_payments_enabled,
                'customer_portal.paypal_enabled' => $systemSetting->paypal_enabled,
                'customer_portal.paypal_api_client_id' => $systemSetting->paypal_api_client,
                'customer_portal.paypal_api_client_secret' => $systemSetting->paypal_api_client_secret,
                'customer_portal.stripe_enabled' => $systemSetting->stripe_enabled,
                'customer_portal.stripe_private_api_key' => $systemSetting->stripe_private_api_key,
                'customer_portal.stripe_public_api_key' => $systemSetting->stripe_public_api_key,
                'customer_portal.paypal_currency_code' => $systemSetting->paypal_currency_code,
                'customer_portal.enable_gocardless' => $systemSetting->go_cardless_enabled,
                'customer_portal.gocardless_access_token' => $systemSetting->go_cardless_access_token,
                'customer_portal.gocardless_currency_code' => $systemSetting->go_cardless_currency_code,
                'customer_portal.ticketing_enabled' => $systemSetting->ticketing_enabled,
                'customer_portal.inbound_email_account_id' => $systemSetting->inbound_email_account_id,
                'customer_portal.ticket_group_id' => $systemSetting->ticket_group_id,
                'customer_portal.ticket_priority' => $systemSetting->ticket_priority,
                'customer_portal.from_address' => $systemSetting->mail_from_address,
                'customer_portal.from_name' => $systemSetting->mail_from_name,
                'customer_portal.data_usage_enabled' => $systemSetting->data_usage_enabled,
                'customer_portal.contracts_enabled' => $systemSetting->contracts_enabled,
                'customer_portal.password_strength_required' => $systemSetting->password_strength_required,
                'mail.mailers.smtp.host' => $systemSetting->mail_host,
                'mail.mailers.smtp.port' => $systemSetting->mail_port,
                'mail.mailers.smtp.username' => $systemSetting->mail_username,
                'mail.mailers.smtp.password' => $systemSetting->mail_password,
                'mail.mailers.smtp.encryption' => $systemSetting->mail_encryption ? 'tls' : null,
                'mail.from' => [
                    'address' => $systemSetting->mail_from_address,
                    'name' => $systemSetting->mail_from_name,
                ],
            ]);
        } catch (Exception $e) {
            //
        }
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(QCoreService::class, function ($app) {
            return new QCoreService();
        });
    }
}
