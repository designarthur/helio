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
        Schema::create('driver_logs', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID for the log entry
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade'); // Link to the driver (user)
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade'); // Link to the vendor (driver's employer)

            // Log Entry Details
            $table->string('status'); // e.g., 'OFF_DUTY', 'SLEEPER_BERTH', 'DRIVING', 'ON_DUTY_NOT_DRIVING'
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable(); // Null if current active status
            $table->integer('duration_minutes')->nullable(); // Calculated duration
            $table->string('location')->nullable(); // Location at time of status change
            $table->text('remarks')->nullable(); // Driver's notes/remarks for this log segment

            // Compliance flags (conceptual, can be derived or explicitly stored)
            // $table->boolean('violation_flag')->default(false);
            // $table->text('violation_details')->nullable();

            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema->dropIfExists('driver_logs');
    }
};