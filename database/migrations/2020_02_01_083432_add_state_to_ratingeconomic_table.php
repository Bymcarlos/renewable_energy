<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStateToRatingeconomicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ratingeconomicrequests', function (Blueprint $table) {
            $table->integer('ratingrequeststate_id')->unsigned()->default('1');
            $table->foreign('ratingrequeststate_id')->references('id')->on('ratingrequeststates')->onDelete('cascade');
        });
        //update ratingeconomicrequests set ratingrequeststate_id=3 WHERE value IS NOT NULL
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ratingeconomicrequests', function (Blueprint $table) {
            $table->dropForeign(['ratingrequeststate_id']);
            $table->dropColumn('ratingrequeststate_id');
        });
    }
}
