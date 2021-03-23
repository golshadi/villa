<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVillaReservationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('villa_reservation', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('villa_id')->unsigned();     
            $table->timestamp('reserve_date');       
            $table->bigInteger('user_id')->unsigned();
            $table->integer('passengers_number');
            $table->bigInteger('final_cost');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->integer('status');
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
        Schema::dropIfExists('villa_reservation');
    }
}
