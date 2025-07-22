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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID for the invoice
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade'); // Link to vendor
            $table->foreignId('customer_id')->constrained()->onDelete('cascade'); // Link to customer

            // Invoice Details
            $table->string('invoice_number')->unique(); // Unique invoice number (e.g., INV-001)
            $table->date('issue_date');
            $table->date('due_date');
            $table->json('items'); // JSON array of invoiced items (description, amount, unitPrice, quantity)
            $table->decimal('total_amount', 10, 2);
            $table->decimal('balance_due', 10, 2);
            $table->string('status')->default('Sent'); // Draft, Sent, Partially Paid, Paid, Overdue, Voided
            $table->text('notes')->nullable(); // Internal/customer notes on the invoice

            // Links to other related records (nullable as not all invoices come from bookings/quotes)
            $table->foreignId('linked_booking_id')->nullable()->constrained('bookings')->onDelete('set null');
            // linked_quote_id will be added in a later migration to avoid circular dependency
            // $table->foreignId('linked_quote_id')->nullable()->constrained('quotes')->onDelete('set null');

            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};