<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCriteriafuncsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('criteriafuncs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('description');
            $table->integer('criteria_id')->unsigned();
            $table->foreign('criteria_id')->references('id')->on('criterias')->onDelete('cascade');
            $table->integer('responsetype_id')->unsigned();
            $table->foreign('responsetype_id')->references('id')->on('responsetypes')->onDelete('cascade');
            $table->boolean('askinput')->default(false);
            $table->boolean('askvalue')->default(false);
            $table->boolean('askrange')->default(false);
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
        Schema::dropIfExists('criteriafuncs');
    }
}
