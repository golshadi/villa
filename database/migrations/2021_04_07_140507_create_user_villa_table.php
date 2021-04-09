<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserVillaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_villa', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('villa_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('villa_id')->references('id')->on('villas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_villa');
    }
}
