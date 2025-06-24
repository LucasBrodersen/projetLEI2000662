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
            // Add the 'enrollmentDate' column, ensuring it stores only the date (no time)
            $table->date('enrollmentDate')->nullable(); // Use nullable if the date is optional
            $table->enum('status', ['active', 'inactive', 'suspended', 'pending'])->default('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the 'enrollmentDate' column in case of rollback
            $table->dropColumn('enrollmentDate');
            $table->dropColumn('status');
        });
    }
};
