<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTechrequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('techrequests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('help')->nullable();
            $table->integer('ordering')->default('0');
            $table->integer('techcat_id')->unsigned();
            $table->foreign('techcat_id')->references('id')->on('techcats')->onDelete('cascade'); 
            $table->integer('inputrequest_id')->nullable()->unsigned();
            $table->foreign('inputrequest_id')->references('id')->on('inputrequests')->onDelete('cascade');
            $table->integer('feature_id')->nullable()->unsigned();
            $table->foreign('feature_id')->references('id')->on('features')->onDelete('cascade');
            $table->integer('criticality_id')->unsigned();
            $table->foreign('criticality_id')->references('id')->on('criticalities');
            $table->integer('criteriafunc_id')->unsigned();
            $table->foreign('criteriafunc_id')->references('id')->on('criteriafuncs');
            $table->decimal('inputfactor', 10, 2)->nullable();   //To apply in some rating calculates (binary->ID*factor>DB?)
            $table->decimal('value', 10, 2)->nullable();   //For criteria functions to compare with a value
            $table->decimal('range_x', 10, 2)->nullable();   //For criteria functions to compare with a range X-Y
            $table->decimal('range_y', 10, 2)->nullable();
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
        Schema::dropIfExists('techrequests');
    }
}
