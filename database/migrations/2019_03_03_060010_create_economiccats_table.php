<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEconomiccatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('economiccats', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->tinyInteger('type'); //1->Business, 2->Alternative
            $table->integer('economicsheet_id')->unsigned();
            $table->foreign('economicsheet_id')->references('id')->on('economicsheets')->onDelete('cascade');
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
        Schema::dropIfExists('economiccats');
    }
}
