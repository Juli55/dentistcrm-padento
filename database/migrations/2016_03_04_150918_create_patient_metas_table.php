<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePatientMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_metas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id')->unsigned();
            $table->string('name', 64)->index();
            $table->string('email', 128)->index();
            $table->string('tel', 128)->index();
            $table->string('mobile', 128);
            $table->string('city', 128);
            $table->string('street', 128);
            $table->string('zip', 5)->index();
            $table->enum('has_dentist', ['yes','no','ns'])->index()->nullable();
            $table->enum('has_fear', ['yes','no','ns'])->index()->nullable();
            $table->enum('has_sdi', ['yes','no','ns'])->index()->nullable();
            $table->enum('is_satisfied',['yes','no','ns'])->index()->nullable();
            $table->enum('has_financing', ['yes','no','ns'])->index()->nullable();
            $table->enum('has_tacs', ['yes','no','ns'])->index()->nullable();
            $table->string('insurance', 64)->index()->nullable();
            $table->string('ref', 255)->nullable();
            $table->string('orig_ref', 255)->nullable();
            $table->string('orig_page', 255)->nullable();
            $table->timestamps();
            
            $table->foreign('patient_id')->references('id')->on('patients')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('patient_metas');
    }
}
