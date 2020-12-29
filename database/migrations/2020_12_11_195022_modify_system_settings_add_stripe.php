<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifySystemSettingsAddStripe extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->boolean('stripe_enabled')->nullable(false)->default(false);
            $table->string('stripe_private_api_key')->nullable();
            $table->string('stripe_public_api_key')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn('stripe_enabled');
            $table->dropColumn('stripe_private_api_key');
            $table->dropColumn('stripe_public_api_key');
        });
    }
}
