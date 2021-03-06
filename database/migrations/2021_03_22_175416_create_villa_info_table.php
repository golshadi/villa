<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVillaInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('villa_info', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('villa_id')->unsigned();            
            $table->longText('general_fac');
            $table->longText('kitchen_fac');
            $table->longText('temp_fac');
            $table->bigInteger('chef')->nullable();
            $table->bigInteger('host')->nullable();
            $table->bigInteger('tour_guide')->nullable();
            $table->bigInteger('bodyguard')->nullable();
            $table->longText('catering')->nullable();
            $table->timestamps();

            $table->foreign('villa_id')->references('id')->on('villas')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('villa_info');
    }
}
