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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID for the vendor
            $table->string('company_name'); // Business Name
            $table->string('email')->unique(); // Contact Email (unique)
            $table->string('password'); // Password for the vendor's admin user
            $table->string('phone')->nullable(); // Contact Phone
            $table->string('primary_address')->nullable(); // Primary Business Address
            $table->string('operating_hours')->nullable();
            $table->text('service_areas')->nullable(); // Comma-separated or JSON string for service areas
            $table->string('status')->default('Pending Approval'); // Active, Pending Approval, Suspended
            $table->string('subscription_plan')->default('Basic'); // Basic, Professional, Enterprise

            // Branding Settings (can be stored as JSON or separate columns if more complex)
            $table->json('branding_settings')->nullable(); // Stores logoUrl, primaryColor, etc.

            $table->rememberToken(); // For "remember me" functionality
            $table->timestamps(); // created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};