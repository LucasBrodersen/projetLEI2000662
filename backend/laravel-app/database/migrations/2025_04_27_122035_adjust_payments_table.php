<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // First, drop the old foreign key
            $table->dropForeign(['customer_id']);

            // Rename the column
            $table->renameColumn('customer_id', 'user_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            // Now add the new foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Add product_id and payment_intent_id columns
            $table->string('product_id')->nullable()->after('amount');
            $table->string('payment_intent_id')->nullable()->after('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop the new foreign key
            $table->dropForeign(['user_id']);

            // Rename back
            $table->renameColumn('user_id', 'customer_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            // Restore the original foreign key
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

            // Drop added columns
            $table->dropColumn('product_id');
            $table->dropColumn('payment_intent_id');
        });
    }
};
