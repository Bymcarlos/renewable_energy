<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimecatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timecats', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->tinyInteger('type'); //1->Availability, 2->Executions, 3->Flexibility
            $table->integer('score_weight');  //To calculate timing rating score (Weight of Avail, Exec, Flex on final score)
            $table->integer('timesheet_id')->unsigned();
            $table->foreign('timesheet_id')->references('id')->on('timesheets')->onDelete('cascade');
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
        Schema::dropIfExists('timecats');
    }
}
