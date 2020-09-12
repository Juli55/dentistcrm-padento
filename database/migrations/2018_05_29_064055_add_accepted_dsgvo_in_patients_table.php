<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAcceptedDsgvoInPatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dateTime('accepted_dsgvo_at')->nullable();
            $table->text('accepted_dsgvo_text')->nullable();
            $table->dateTime('estimated_deletation_at')->nullable();
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
            $table->dropColumn('accepted_dsgvo_at');
            $table->dropColumn('accepted_dsgvo_text');
            $table->dropColumn('estimated_deletation_at');
        });
    }
}
