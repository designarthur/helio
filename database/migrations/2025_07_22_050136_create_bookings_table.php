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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID for the booking
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade'); // Link to vendor
            $table->foreignId('customer_id')->constrained()->onDelete('cascade'); // Link to customer
            $table->foreignId('equipment_id')->constrained()->onDelete('restrict'); // Link to equipment, restrict deletion if booked

            // Core Booking Information
            $table->date('rental_start_date');
            $table->date('rental_end_date');
            $table->string('delivery_address');
            $table->string('pickup_address')->nullable(); // Can be same as delivery or different
            $table->string('status')->default('Pending'); // Pending, Confirmed, Delivered, Completed, Cancelled
            $table->decimal('total_price', 10, 2); // Calculated total price for the booking
            $table->text('booking_notes')->nullable(); // General notes for the booking

            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null'); // Link to driver (from users table, assuming users are staff/drivers)

            // Type-specific booking details (nullable to accommodate different equipment types)
            // Dumpster specific
            $table->decimal('estimated_tonnage', 8, 2)->nullable();
            $table->text('prohibited_materials_ack')->nullable(); // Acknowledgment of prohibited materials

            // Temporary Toilet specific
            $table->string('requested_service_freq')->nullable(); // Weekly, Bi-weekly, Event-specific
            $table->text('toilet_special_requests')->nullable(); // e.g., specific placement, extra supplies

            // Storage Container specific
            $table->text('container_placement_notes')->nullable(); // e.g., place on concrete pad
            $table->text('container_security_access')->nullable(); // e.g., gate code, call prior

            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};