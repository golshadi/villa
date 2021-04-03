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
            $table->string('village')->nullable();
            $table->string('postal_code')->nullable();
            $table->text('address');
            $table->string('long');
            $table->string('lat');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('visit_count')->default(0);
            $table->string('main_img')->nullable();
            $table->integer('score')->nullable();
            $table->integer('status')->default(0);
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
