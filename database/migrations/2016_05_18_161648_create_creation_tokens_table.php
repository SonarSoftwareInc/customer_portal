<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreationTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('creation_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->string("token")->index();
            $table->string('email')->index();
            $table->integer("account_id")->index();
            $table->integer("contact_id")->index();
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
        Schema::drop('creation_tokens');
    }
}
