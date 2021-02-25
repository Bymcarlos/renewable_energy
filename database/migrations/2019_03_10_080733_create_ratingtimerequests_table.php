<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatingtimerequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ratingtimerequests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ratingbench_id')->unsigned();
            $table->foreign('ratingbench_id')->references('id')->on('ratingbenches')->onDelete('cascade');
            $table->integer('timerequest_id')->unsigned();
            $table->foreign('timerequest_id')->references('id')->on('timerequests')->onDelete('cascade');
            $table->decimal('value',6,2)->nullable();
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
        Schema::dropIfExists('ratingtimerequests');
    }
}
