<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_id')->unique();
            $table->string('stripe_customer_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('amount_due');
            $table->integer('amount_paid');
            $table->integer('amount_remaining');
            $table->string('currency');
            $table->string('status');
            $table->string('invoice_pdf');
            $table->string('hosted_invoice_url');
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable(); // Adiciona a coluna updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}