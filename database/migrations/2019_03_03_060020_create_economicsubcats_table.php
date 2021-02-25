<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEconomicsubcatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('economicsubcats', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->boolean('administrable')->default(true);
            $table->boolean('weighted')->default(false);
            $table->integer('economiccat_id')->unsigned();
            $table->foreign('economiccat_id')->references('id')->on('economiccats')->onDelete('cascade');
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
        Schema::dropIfExists('economicsubcats');
    }
}
