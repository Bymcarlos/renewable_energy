<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTechcatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('techcats', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('order')->default('0');
            $table->integer('techsheet_id')->unsigned();
            $table->foreign('techsheet_id')->references('id')->on('techsheets')->onDelete('cascade');
            $table->integer('applicable_id')->unsigned()->default(3);
            $table->foreign('applicable_id')->references('id')->on('applicables')->onDelete('cascade');
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
        Schema::dropIfExists('techcats');
    }
}
