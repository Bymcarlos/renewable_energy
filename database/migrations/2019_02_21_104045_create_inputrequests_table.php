<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInputrequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inputrequests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('help')->nullable();
            $table->integer('order')->default('0');
            $table->integer('inputcat_id')->unsigned();
            $table->foreign('inputcat_id')->references('id')->on('inputcats')->onDelete('cascade');
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
        Schema::dropIfExists('inputrequests');
    }
}
