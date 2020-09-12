<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePatientPropsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_props', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 64)->index();
            $table->string('default', 128)->index();
            $table->string('type', 64)->index();
            $table->integer('user_id')->unsigned()->index();
            $table->string('category_id')->index();
            $table->enum('status', ['aktiv','deaktiviert'])->index();
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
        Schema::drop('patient_props');
    }
}
