<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatingeconomicrequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ratingeconomicrequests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ratingbench_id')->unsigned();
            $table->foreign('ratingbench_id')->references('id')->on('ratingbenches')->onDelete('cascade');
            $table->integer('economicrequest_id')->unsigned();
            $table->foreign('economicrequest_id')->references('id')->on('economicrequests')->onDelete('cascade');
            $table->decimal('value',10,2)->nullable();
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
        Schema::dropIfExists('ratingeconomicrequests');
    }
}
