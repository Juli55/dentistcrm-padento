<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLabMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lab_metas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lab_id')->unsigned();
            $table->text('hello');
            $table->string('contact_person', 128);
            $table->string('special1', 128);
            $table->string('special2', 128);
            $table->string('special3', 128);
            $table->string('special4', 128);
            $table->string('special5', 128);
            $table->text('text');
            $table->string('contact_email', 128);
            $table->string('tel', 128);
            $table->integer('count')->unsigned()->index();
            $table->string('street', 128);
            $table->string('city', 64);
            $table->string('zip', 5);
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
        Schema::drop('lab_metas');
    }
}
