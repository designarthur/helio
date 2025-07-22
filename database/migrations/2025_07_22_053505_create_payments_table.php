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
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID for the payment
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade'); // Link to vendor
            $table->foreignId('customer_id')->constrained()->onDelete('cascade'); // Link to customer

            // Payment Details
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('set null'); // Link to the invoice being paid
            $table->date('payment_date');
            $table->decimal('amount', 10, 2);
            $table->string('method'); // e.g., Credit Card, ACH, Check, Cash, System
            $table->string('transaction_id')->nullable()->unique(); // Gateway transaction ID for online payments
            $table->text('notes')->nullable(); // Any notes about the payment

            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};