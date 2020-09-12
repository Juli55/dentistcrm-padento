<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDentistLabPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dentist_lab', function (Blueprint $table) {
            $table->integer('dentist_id')->unsigned()->index();
            $table->integer('lab_id')->unsigned()->index();

            $table->primary(['dentist_id', 'lab_id']);

            $table->foreign('dentist_id')->references('id')->on('dentists')->onDelete('cascade');
            $table->foreign('lab_id')->references('id')->on('labs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('dentist_lab');
    }
}
