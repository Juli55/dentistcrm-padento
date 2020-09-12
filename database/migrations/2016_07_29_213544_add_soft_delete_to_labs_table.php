<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeleteToLabsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('labs', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('lab_metas', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('lab_settings', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('timeframes', function (Blueprint $table) {
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('labs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('lab_metas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('lab_settings', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('timeframes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
