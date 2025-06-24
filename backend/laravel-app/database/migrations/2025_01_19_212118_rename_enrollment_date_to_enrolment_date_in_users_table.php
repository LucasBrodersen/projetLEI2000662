<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Rename the 'enrollmentDate' column to 'enrolment_date'
            $table->renameColumn('enrollmentDate', 'enrolment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Rollback by renaming it back to 'enrollmentDate'
            $table->renameColumn('enrolment_date', 'enrollmentDate');
        });
    }
};
