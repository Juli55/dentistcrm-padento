<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lab_id')->unsigned()->index()->nullable();
            $table->integer('tmp_lab')->unsigned()->nullable();
            $table->string('token', 60)->index()->unique();
            $table->string('email', 60)->index()->unique();
            $table->tinyinteger('phase')->index()->default(1);
            $table->tinyinteger('confirmed')->index();
            $table->tinyinteger('archived')->index();
            $table->tinyinteger('direct')->index();
            $table->tinyinteger('queued')->index();
            $table->integer('membership')->index();
            $table->softDeletes();
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
        Schema::drop('patients');
    }
}
