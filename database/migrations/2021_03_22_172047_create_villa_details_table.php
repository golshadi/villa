<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVillaDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('villa_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('villa_id')->unsigned();            
            $table->integer('standard_capacity');
            $table->integer('max_capacity');
            $table->string('rent_type');
            $table->integer('bedroom');
            $table->integer('ir_toilet');
            $table->integer('eu_toilet');
            $table->integer('shower');
            $table->boolean('shared_bathroom');
            $table->longText('places');
            $table->string('view');
            $table->string('area');

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
        Schema::dropIfExists('villa_details');
    }
}
