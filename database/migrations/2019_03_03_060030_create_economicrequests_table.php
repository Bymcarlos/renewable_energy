<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEconomicrequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('economicrequests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('help')->nullable();
            $table->integer('ordering')->default(0);
            $table->decimal('weight',6,2)->default(0);
            $table->integer('unit_id')->unsigned()->default(72); //Euros
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->integer('economicsubcat_id')->unsigned();
            $table->foreign('economicsubcat_id')->references('id')->on('economicsubcats')->onDelete('cascade');
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
        Schema::dropIfExists('economicrequests');
    }
}
