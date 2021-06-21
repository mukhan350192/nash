<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShortUrlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('short_url', function (Blueprint $table) {
            $table->id();
            $table->string('iin');
            $table->integer('leadID');
            $table->double('amount');
            $table->double('amountPayment');
            $table->string('token');
            $table->string('client_type');
            $table->string('companyName')->nullable();
            $table->integer('code');
            $table->string('phone');
            $table->string('fio');
            $table->integer('status');
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
        Schema::dropIfExists('short_url');
    }
}
