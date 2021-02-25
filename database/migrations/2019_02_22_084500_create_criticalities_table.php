<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCriticalitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('criticalities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->tinyInteger('type');    //1->Definitive value, 2->Selection between 2 definitive values
            $table->tinyInteger('tbcps');
            $table->tinyInteger('tbcst');
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
        Schema::dropIfExists('criticalities');
    }
}
