<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimerequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timerequests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('label')->nullable()->default('weeks');
            $table->integer('ordering')->default(0);
            $table->integer('settable')->default(0);//0->No settings, 1->Sett editables, 2->Sett no editables
            $table->integer('state')->default(0);//0:Undefined, -1:pending user validate
            $table->integer('timesubcat_id')->unsigned();
            $table->foreign('timesubcat_id')->references('id')->on('timesubcats')->onDelete('cascade');
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
        Schema::dropIfExists('timerequests');
    }
}
