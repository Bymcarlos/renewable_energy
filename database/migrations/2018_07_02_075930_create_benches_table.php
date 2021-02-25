<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBenchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('benches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('comments')->nullable();
            $table->integer('entity_id')->unsigned();
            $table->foreign('entity_id')->references('id')->on('entities');
            $table->integer('area_component_id')->unsigned();
            $table->foreign('area_component_id')->references('id')->on('area_component');
            $table->tinyInteger('status')->default('1');
            $table->integer('country_id')->unsigned()->default('1');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->integer('benchtype_id')->unsigned()->default('1');
            $table->foreign('benchtype_id')->references('id')->on('benchtypes');
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
        Schema::dropIfExists('benches');
    }
}
