<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatingtechrequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ratingtechrequests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rating_id')->unsigned();
            $table->foreign('rating_id')->references('id')->on('ratings')->onDelete('cascade');
            $table->integer('techrequest_id')->unsigned();
            $table->foreign('techrequest_id')->references('id')->on('techrequests')->onDelete('cascade');
            $table->integer('criticality_id')->unsigned();
            $table->foreign('criticality_id')->references('id')->on('criticalities')->onDelete('cascade');
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
        Schema::dropIfExists('ratingtechrequests');
    }
}
