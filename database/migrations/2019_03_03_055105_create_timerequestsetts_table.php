<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimerequestsettsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timerequestsetts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('percent');
            $table->integer('value')->nullable();
            $table->string('label');
            $table->integer('timerequest_id')->unsigned();
            $table->foreign('timerequest_id')->references('id')->on('timerequests')->onDelete('cascade');
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
        Schema::dropIfExists('timerequestsetts');
    }
}
