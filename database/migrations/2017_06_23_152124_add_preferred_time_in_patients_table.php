<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPreferredTimeInPatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->time('workday_from')->nullable();
            $table->time('workday_till')->nullable();
            $table->time('weekend_from')->nullable();
            $table->time('weekend_till')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('workday_from');
            $table->dropColumn('workday_till');
            $table->dropColumn('weekend_from');
            $table->dropColumn('weekend_till');
        });
    }
}
