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
        Schema::create('go_cardless_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token')->unique()->index();
            $table->integer('account_id')->index();
            $table->string('redirect_flow_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('go_cardless_tokens');
    }
};
