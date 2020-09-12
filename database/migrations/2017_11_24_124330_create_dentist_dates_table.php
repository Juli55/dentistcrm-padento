<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDentistDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dentist_dates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('lab_id')->unsigned();
            $table->integer('dentist_contact_id')->unsigned();
            $table->dateTime('date')->index();
            $table->boolean('archived')->default(false)->index();
            $table->text('text');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('lab_id')->references('id')->on('labs')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('dentist_contact_id')->references('id')->on('dentist_contacts')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('dentist_dates');
    }
}
