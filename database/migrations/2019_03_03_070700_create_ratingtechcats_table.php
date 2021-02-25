<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatingtechcatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ratingtechcats', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rating_id')->unsigned();
            $table->foreign('rating_id')->references('id')->on('ratings')->onDelete('cascade');
            $table->integer('techcat_id')->unsigned();
            $table->foreign('techcat_id')->references('id')->on('techcats')->onDelete('cascade');
            $table->integer('applicable_id')->unsigned();
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
        Schema::dropIfExists('ratingtechcats');
    }
}
