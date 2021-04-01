<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVillasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('villas', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type');
            $table->string('phone_number');
            $table->string('story');
            $table->string('state');
            $table->string('city');
            $table->string('village');
            $table->string('postal_code');
            $table->text('address');
            $table->string('long');
            $table->string('lat');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('visit_count')->default(0);
            $table->string('main_img');
            $table->integer('score')->nullable();
            $table->integer('status');
            $table->timestamps();

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
        Schema::dropIfExists('villas');
    }
}
