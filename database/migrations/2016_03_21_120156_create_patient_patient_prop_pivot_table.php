<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePatientPatientPropPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_patient_prop', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id')->unsigned()->index();
            $table->integer('patient_prop_id')->unsigned()->index();
            $table->string('value')->nullable()->index();
            $table->enum('status', ['aktiv','deaktiviert'])->default('Aktiv');
            $table->timestamps();
            #$table->primary(['patient_id', 'patient_prop_id']);

            $table->foreign('patient_id')->references('id')->on('patients')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('patient_prop_id')->references('id')->on('patient_props')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('status')->references('status')->on('patient_props')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('patient_patient_prop');
    }
}
