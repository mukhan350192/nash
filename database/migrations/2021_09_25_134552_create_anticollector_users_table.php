<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnticollectorUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anticollector_users', function (Blueprint $table) {
            $table->id();
            $table->string('fio');
            $table->string('phone');
            $table->string('iin');
            $table->string('email');
            $table->string('password');
            $table->string('token');
            $table->string('organization')->nullable();
            $table->string('type')->nullable();
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
        Schema::dropIfExists('anticollector_users');
    }
}
