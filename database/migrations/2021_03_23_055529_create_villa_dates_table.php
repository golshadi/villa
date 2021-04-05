<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVillaDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('villa_dates', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('villa_id')->unsigned();     
            $table->bigInteger('user_id')->unsigned();
            $table->timestamp('date')->default(Carbon::now());   
            $table->integer('status')->nullable();
            $table->bigInteger('special_price')->nullable();
            $table->timestamps();

            $table->foreign('villa_id')->references('id')->on('villas')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('villa_dates');
    }
}
