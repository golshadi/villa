<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVillaRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('villa_rules', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('villa_id')->unsigned();     
            $table->bigInteger('normal_cost');       
            $table->bigInteger('special_cost');       
            $table->bigInteger('normal_extra_cost');       
            $table->bigInteger('special_extra_cost');       
            $table->bigInteger('weekly_discount');       
            $table->bigInteger('monthly_discount');       
            $table->longText('auth_rules');       
            $table->longText('special_rules');  
            $table->integer('min_reserve');     
            $table->integer('max_reserve'); 
            $table->longText('suitable_for');       
            $table->string('arrival_time');
            $table->string('exit_time');
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
        Schema::dropIfExists('villa_cost');
    }
}
