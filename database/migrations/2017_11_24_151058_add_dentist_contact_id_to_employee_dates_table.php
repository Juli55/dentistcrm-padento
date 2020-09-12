<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDentistContactIdToEmployeeDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_dates', function (Blueprint $table) {
            $table->integer('dentist_contact_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_dates', function (Blueprint $table) {
            $table->dropColumn('dentist_contact_id');
        });
    }
}
