<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->string('return_refund_policy_link')->nullable();
            $table->string('privacy_policy_link')->nullable();
            $table->string('customer_service_contact_email')->nullable();
            $table->string('customer_service_contact_phone')->nullable();
            $table->string('company_address')->nullable();
            $table->string('transaction_currency')->nullable();
            $table->string('delivery_policy_link')->nullable();
            $table->string('consumer_data_privacy_policy_link')->nullable();
            $table->string('secure_checkout_policy_link')->nullable();
            $table->string('terms_and_conditions_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};
