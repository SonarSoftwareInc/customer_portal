<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystemSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url')->nullable();
            $table->string('locale')->nullable();
            $table->string('mail_host')->nullable();
            $table->integer('mail_port')->nullable();
            $table->string('mail_username')->nullable();
            $table->string('mail_password')->nullable();
            $table->boolean('mail_encryption')->default(false);
            $table->string('mail_from_address')->nullable();
            $table->string('mail_from_name')->nullable();

            $table->string('isp_name')->nullable();
            $table->string('decimal_separator')->nullable();
            $table->string('thousands_separator')->nullable();
            $table->string('currency_symbol')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('login_page_message')->nullable();

            $table->boolean('data_usage_enabled')->nullable(false);
            $table->boolean('contracts_enabled')->nullable(false);

            $table->integer('password_strength_required')->nullable();

            $table->boolean('show_detailed_transactions')->nullable(false);
            $table->boolean('credit_card_payments_enabled')->nullable(true);
            $table->string('bank_payments_enabled')->nullable(false);
            $table->string('go_cardless_enabled')->nullable(false);

            $table->string('go_cardless_access_token')->nullable();
            $table->string('go_cardless_currency_code')->nullable();

            $table->boolean('paypal_enabled')->nullable(false);

            $table->string('paypal_api_client')->nullable();
            $table->string('paypal_api_client_secret')->nullable();
            $table->string('paypal_currency_code')->nullable();

            $table->boolean('ticketing_enabled')->nullable(false);
            $table->integer('inbound_email_account_id')->nullable();
            $table->integer('ticket_group_id')->nullable();
            $table->integer('ticket_priority')->default(4);

            $table->string('settings_key');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_settings');
    }
}
