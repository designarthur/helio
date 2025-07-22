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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID for the quote
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade'); // Link to vendor
            $table->foreignId('customer_id')->constrained()->onDelete('cascade'); // Link to customer

            // Quote Details
            $table->date('quote_date');
            $table->date('expiry_date')->nullable(); // Date the quote expires
            $table->json('items'); // JSON array of quoted items (e.g., equipment_id, rentalDays, unitPrice, itemTotalPrice)
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('pickup_fee', 10, 2)->default(0);
            $table->decimal('damage_waiver', 10, 2)->default(0); // Damage waiver cost
            $table->decimal('total_amount', 10, 2); // Calculated total price for the quote
            $table->text('notes')->nullable(); // General notes/terms for the quote
            $table->string('status')->default('Draft'); // Draft, Sent, Accepted, Rejected, Expired

            // Link to Booking if converted (nullable as it's not always converted immediately)
            // Note: linked_invoice_id is NOT included here to avoid dependency issues.
            // It will be added in a separate migration AFTER the invoices table is guaranteed to exist.
            $table->foreignId('linked_booking_id')->nullable()->constrained('bookings')->onDelete('set null');

            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};