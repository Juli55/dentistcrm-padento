<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLabUserPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lab_user', function (Blueprint $table) {
            $table->integer('lab_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();

            $table->primary(['lab_id', 'user_id']);

            $table->foreign('lab_id')->references('id')->on('labs')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('lab_user');
    }
}
