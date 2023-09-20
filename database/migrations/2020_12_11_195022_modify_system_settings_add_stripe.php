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
            $table->boolean('stripe_enabled')->nullable(false)->default(false);
            $table->string('stripe_private_api_key')->nullable();
            $table->string('stripe_public_api_key')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn('stripe_enabled');
            $table->dropColumn('stripe_private_api_key');
            $table->dropColumn('stripe_public_api_key');
        });
    }
};
