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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID for the expense
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade'); // Link to vendor

            // Expense Details
            $table->date('date'); // Date the expense occurred
            $table->string('description');
            $table->string('category'); // e.g., Fuel, Maintenance, Salaries, Office Supplies, Marketing, Utilities, Other
            $table->decimal('amount', 10, 2);
            $table->string('vendor_name')->nullable(); // Who the expense was paid to (e.g., Gas Station, Supplier)
            $table->text('notes')->nullable();
            // Optional: for attachments
            // $table->string('receipt_path')->nullable(); // Path to uploaded receipt image/PDF

            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};