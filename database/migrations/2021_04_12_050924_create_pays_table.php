<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pays', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('amount');
            $table->string('authority');
            $table->bigInteger('villa_reservation_id')->index()->unsigned();
            $table->bigInteger('user_reservation_id')->unsigned();
            $table->string('name');
            $table->string('refid')->nullable();
            $table->string('mobile');
            $table->string('email');
            $table->integer('status')->default(0);
            $table->timestamps();

            $table->foreign('villa_reservation_id')->references('id')->on('villa_reservation')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_reservation_id')->references('id')->on('user_reservations')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pays');
    }
}
