<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('fio');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('companyName')->nullable();
            $table->string('position')->nullable();
            $table->string('phone');
            $table->string('iin')->nullable();
            $table->string('type');
            $table->string('token')->nullable();
            $table->string('sphere')->nullable();
            $table->string('description')->nullable();
            $table->string('amount')->nullable();
            $table->string('materials')->nullable();
            $table->string('paymentType')->nullable();
            $table->string('amountPayment')->nullable();
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
        Schema::dropIfExists('users');
    }
}
