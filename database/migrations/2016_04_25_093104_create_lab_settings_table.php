<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLabSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lab_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lab_id')->unsigned();
            $table->string('name')->index();
            $table->string('description');
            $table->string('value')->nullable()->index();
            $table->string('second_value')->nullable()->index();
            $table->string('category')->nullable()->index();
            $table->timestamps();
            
            $table->foreign('lab_id')->references('id')->on('labs')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('lab_settings');
    }
}
