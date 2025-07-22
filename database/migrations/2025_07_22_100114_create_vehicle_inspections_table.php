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
        Schema::create('vehicle_inspections', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID for the inspection
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade'); // Link to the driver (user) who performed the inspection
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade'); // Link to the vendor (driver's employer)

            // Inspection Details
            $table->string('inspection_type'); // e.g., 'pre-trip', 'post-trip'
            $table->string('vehicle_id'); // Identifier for the vehicle (e.g., Truck #TP-1234), not a foreign key to a 'vehicles' table yet
            $table->integer('odometer_reading'); // Odometer reading at time of inspection
            $table->timestamp('inspection_datetime'); // Date and time of inspection

            // Checklist Results (store as JSON for flexibility, or individual boolean columns)
            // Example: {"lights": "ok", "tires": "defect", "brakes": "ok", ...}
            $table->json('checklist_results');

            // Defect Details
            $table->boolean('defects_found')->default(false); // Flag if any defects were found
            $table->text('defect_notes')->nullable(); // Driver's description of defects
            $table->json('defect_photos')->nullable(); // JSON array of paths/URLs to uploaded photos of defects

            // Certification
            $table->boolean('driver_certified_safe')->default(true); // Is vehicle safe to operate according to driver
            $table->text('driver_signature_image')->nullable(); // Base64 or path to image of driver's signature (conceptual)

            // Mechanic/Admin follow-up (conceptual)
            // $table->string('mechanic_status')->nullable(); // Repaired, Needs Parts, etc.
            // $table->text('mechanic_notes')->nullable();
            // $table->date('repair_date')->nullable();

            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema->dropIfExists('vehicle_inspections');
    }
};