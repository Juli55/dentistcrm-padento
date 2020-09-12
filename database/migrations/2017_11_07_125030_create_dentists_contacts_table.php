<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDentistsContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dentist_contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lab_id')->unsigned()->index()->nullable();
            $table->integer('tmp_lab')->unsigned()->nullable();
            $table->string('token', 60)->index()->unique();
            $table->tinyinteger('phase')->index()->default(1);
            $table->tinyinteger('confirmed')->index();
            $table->integer('confirmed_by')->index();
            $table->tinyinteger('archived')->index();
            $table->tinyinteger('direct')->index();
            $table->tinyinteger('membership')->index();
            $table->tinyinteger('queued')->index();
            $table->integer('movedback')->index();
            $table->time('workday_from')->index();
            $table->time('workday_till')->index();
            $table->time('weekend_from')->index();
            $table->time('weekend_till')->index();
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
        Schema::drop('dentist_contacts');
    }
}
