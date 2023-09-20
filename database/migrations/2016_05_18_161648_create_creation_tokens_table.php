<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('creation_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token')->index();
            $table->string('email')->index();
            $table->integer('account_id')->index();
            $table->integer('contact_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('creation_tokens');
    }
};
