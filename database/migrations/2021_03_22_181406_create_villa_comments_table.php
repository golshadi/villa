<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVillaCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('villa_comments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('villa_id')->unsigned();     
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('parent_id')->unsigned()->default(0);
            $table->longText('text');
            $table->float('total_score')->nullable();
            $table->float('cleaning')->nullable();
            $table->float('ad_compliance')->nullable();
            $table->float('hospitality')->nullable();
            $table->float('hosting_quality')->nullable();
            
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
        Schema::dropIfExists('villa_comments');
    }
}
