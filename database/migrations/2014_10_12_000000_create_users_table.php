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
            $table->string('fullname');
            $table->string('phone_number');
            $table->string('email')->unique()->nullable();
            $table->string('national_code')->nullable();
            $table->string('job')->nullable();
            $table->string('education')->nullable();
            $table->string('foreign_language')->nullable();
            $table->string('card_number')->nullable();
            $table->string('shaba_number')->nullable();
            $table->string('avatar')->nullable();
            $table->bigInteger('sms_code')->nullable();
            $table->bigInteger('sms_expire')->nullable();
            $table->integer('isAdmin')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
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
