<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBenchFeatureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bench_feature', function (Blueprint $table) {
            $table->increments('id');
            $table->string('value')->nullable();
            $table->tinyInteger('status')->default('1');    //1->Pending confirm 2->Confirmed
            $table->text('comments')->nullable();
            $table->integer('bench_id')->unsigned();
            $table->foreign('bench_id')->references('id')->on('benches')->onDelete('cascade');
            $table->integer('feature_id')->unsigned();
            $table->foreign('feature_id')->references('id')->on('features')->onDelete('cascade');     
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
        Schema::dropIfExists('bench_feature');
    }
}
