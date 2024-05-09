<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RevertPasswordResetsTableName extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('password_reset_tokens')) {
            Schema::rename('password_reset_tokens', 'password_resets');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('password_resets')) {
            Schema::rename('password_resets', 'password_reset_tokens');
        }
    }
};
