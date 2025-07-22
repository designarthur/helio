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
        Schema::create('customers', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID for the customer
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade'); // Foreign key to the vendors table

            // General Customer Information
            $table->string('name'); // Full Name
            $table->string('company')->nullable(); // Company Name (optional)
            $table->string('email')->unique(); // Email (unique per customer across platform)
            $table->string('phone'); // Phone Number
            $table->string('billing_address'); // Primary Billing Address
            $table->text('service_addresses')->nullable(); // Comma-separated or JSON string for multiple service addresses
            $table->string('customer_type'); // Residential, Commercial
            $table->string('status')->default('Active'); // Active, Inactive, On Hold
            $table->text('internal_notes')->nullable(); // Private notes for vendor staff

            // Future fields could include:
            // $table->json('contact_preferences')->nullable(); // e.g., preferred_notification: SMS/Email
            // $table->string('source')->nullable(); // How customer found vendor
            // $table->decimal('total_spend', 12, 2)->default(0); // Calculated field
            // $table->decimal('outstanding_balance', 12, 2)->default(0); // Calculated field

            $table->timestamps(); // created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};