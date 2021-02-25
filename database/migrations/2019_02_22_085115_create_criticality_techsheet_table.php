<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCriticalityTechsheetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('criticality_techsheet', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('techsheet_id')->unsigned();
            $table->foreign('techsheet_id')->references('id')->on('techsheets')->onDelete('cascade');
            $table->integer('criticality_id')->unsigned();
            $table->foreign('criticality_id')->references('id')->on('criticalities')->onDelete('cascade');
            $table->integer('score_weight');  //To calculate technical requirements score1 (0-1000)
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
        Schema::dropIfExists('criticality_techsheet');
    }
}
