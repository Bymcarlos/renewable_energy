<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimesubcatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timesubcats', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->boolean('administrable')->default(true);
            $table->integer('timecat_id')->unsigned();
            $table->foreign('timecat_id')->references('id')->on('timecats')->onDelete('cascade');
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
        Schema::dropIfExists('timesubcats');
    }
}
