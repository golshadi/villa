<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('searches', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('villa_id')->unsigned();     
            $table->string('city');
            $table->string('village')->nullable();
            $table->integer('passengers_count');
            $table->integer('bedroom');
            $table->bigInteger('normal_cost');
            $table->string('category');            
            $table->boolean('discount')->default(false); 
            $table->boolean('disinfected')->default(false); 
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
        Schema::dropIfExists('searches');
    }
}
