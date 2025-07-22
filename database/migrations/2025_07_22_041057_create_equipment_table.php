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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID for the equipment
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade'); // Foreign key to the vendors table

            // General Equipment Information
            $table->string('internal_id')->nullable()->unique(); // Vendor-defined asset tag/serial number, can be null but unique if exists
            $table->string('type'); // Dumpster, Temporary Toilet, Storage Container
            $table->string('size'); // e.g., 20-yard, 40ft, Standard
            $table->string('status')->default('Available'); // Available, On Rent, In Maintenance, Out of Service, Reserved
            $table->string('location')->nullable(); // Current physical location (e.g., Yard A, Customer Site)
            $table->text('description')->nullable(); // Internal vendor notes about the equipment
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 10, 2)->nullable();
            $table->string('supplier_manufacturer')->nullable();

            // Pricing & Rules
            $table->decimal('base_daily_rate', 10, 2); // Base rental rate per day (required)
            $table->integer('default_rental_period')->nullable(); // e.g., 7 days
            $table->integer('min_rental_period')->default(1); // Minimum rental period in days
            $table->decimal('extended_daily_rate', 10, 2)->nullable(); // Daily rate after default period
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('pickup_fee', 10, 2)->default(0);
            $table->decimal('damage_waiver_cost', 10, 2)->default(0);

            // Type-specific attributes (nullable to accommodate different types)
            // Dumpsters
            $table->string('dumpster_dimensions')->nullable(); // e.g., "20x8x5 ft"
            $table->decimal('max_tonnage', 8, 2)->nullable(); // Max weight limit for dumpsters
            $table->decimal('overage_per_ton_fee', 8, 2)->nullable(); // Per ton fee for exceeding max tonnage
            $table->decimal('disposal_rate_per_ton', 8, 2)->nullable(); // Landfill/disposal fee
            $table->string('dumpster_container_type')->nullable(); // Roll-off, Front-load, Rear-load
            $table->string('gate_type')->nullable(); // Walk-in Door, No Door
            $table->text('prohibited_materials')->nullable(); // Comma-separated list

            // Temporary Toilets
            $table->string('toilet_capacity')->nullable(); // e.g., "60 Gallons", "200 Uses"
            $table->string('service_frequency')->nullable(); // Weekly, Bi-weekly, Event-specific
            $table->text('toilet_features')->nullable(); // Comma-separated list (e.g., Sink, Urinal)

            // Storage Containers
            $table->string('storage_container_type')->nullable(); // Standard, High Cube, Open Top, Reefer
            $table->string('door_type')->nullable(); // Roll-up, Swing-out
            $table->string('condition')->nullable(); // Wind & Watertight, Cargo Worthy
            $table->text('security_features')->nullable(); // Comma-separated list (e.g., Lockbox, Reinforced Doors)

            // You can add fields for image paths or maintenance logs later if needed,
            // or create separate tables for them.

            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};