<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDentistTodosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dentist_todos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contact_id')->nullable();
            $table->text('title');
            $table->dateTime('completed_at')->nullable();
            $table->integer('completer_id')->nullable();
            $table->integer('is_queued')->boolean();
            $table->unsignedInteger('order')->index()->nullable();
            $table->unsignedInteger('creator_id')->nullable();
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
        Schema::drop('dentist_todos');
    }
}
