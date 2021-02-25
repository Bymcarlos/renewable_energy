<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('features', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('help')->nullable();
            $table->integer('order')->default('1');
            $table->integer('question_id')->unsigned();
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            $table->integer('question_root')->default('1'); //1->is the feature root of the question or not (0)
            $table->integer('responsetype_id')->unsigned();
            $table->foreign('responsetype_id')->references('id')->on('responsetypes');
            $table->integer('unit_id')->unsigned();
            $table->foreign('unit_id')->references('id')->on('units');
            $table->string('brand_name')->nullable();
            $table->string('brand_value')->nullable();
            $table->integer('brand_value_unit')->default(1)->unsigned();
            $table->foreign('brand_value_unit')->references('id')->on('units');
            $table->tinyInteger('importable')->default('1');    //Excel for export/import for the laboratories 0->NO, 1->YES
            $table->tinyInteger('parameter')->default('0');     //Reports, to show in advanced filters 0->NO 1->YES
            $table->tinyInteger('rating_req')->default('0');    //Rating tool: is a field for Rating tool? 0->NO 1->YES
            $table->tinyInteger('rating_crit')->default('1');    //Rating tool: criticality? 1->Undefined, 2->Primary, 3->Secondary, 4->Terciary
            $table->tinyInteger('rating_func')->default('1');    //Rating tool: value function? 1->Undefined, 2->Binary, 3->Scale
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
        Schema::dropIfExists('features');
    }
}
