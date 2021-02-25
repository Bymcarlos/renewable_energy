<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInputcatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inputcats', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('order')->default('0');
            $table->integer('inputsheet_id')->unsigned();
            $table->foreign('inputsheet_id')->references('id')->on('inputsheets')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inputcats');
    }
}
