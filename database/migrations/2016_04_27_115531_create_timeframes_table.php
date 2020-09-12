<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimeframesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timeframes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lab_id')->unsigned();
            $table->integer('day_of_week')->unsigned();
            $table->time('start');
            $table->time('stop');
            $table->timestamps();

            $table->foreign('lab_id')->references('id')->on('labs')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('day_of_week')->references('id')->on('weekdays')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('timeframes');
    }
}
