<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use CtoVmm\Ratingrequeststate;

class CreateRatingrequeststatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ratingrequeststates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->timestamps();
        });
        //Necessary to seed here because the next migrations create foreign constraints with default value
        //in existing records
        Ratingrequeststate::create(['title' => 'Undefined']);
        Ratingrequeststate::create(['title' => 'Estimated']);
        Ratingrequeststate::create(['title' => 'Confirmed']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ratingrequeststates');
    }
}
