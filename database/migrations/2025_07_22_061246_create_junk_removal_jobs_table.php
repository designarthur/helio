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
        Schema::create('junk_removal_jobs', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID for the job
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade'); // Link to vendor
            $table->foreignId('customer_id')->constrained()->onDelete('cascade'); // Link to customer

            // Job Details
            $table->string('job_number')->unique(); // Custom job ID (e.g., JR001)
            $table->date('requested_date');
            $table->time('requested_time')->nullable();
            $table->string('job_location');
            $table->text('description_of_junk'); // Detailed description of items
            $table->string('volume_estimate')->nullable(); // e.g., "2 cu yards", "1/2 truckload"
            $table->string('weight_estimate')->nullable(); // e.g., "500 lbs", "0.25 tons"
            $table->integer('crew_requirements')->default(1); // Number of crew members needed
            $table->foreignId('assigned_driver')->nullable()->constrained('users')->onDelete('set null'); // Link to assigned driver (from users table)
            $table->decimal('estimated_price', 10, 2);
            $table->string('status')->default('Pending Quote'); // Pending Quote, Quoted, Scheduled, In Progress, Completed, Cancelled
            $table->text('job_notes')->nullable(); // Internal notes for the job

            // Fields for Advanced Junk Removal (Visuals for Quoting) - Conceptual for now
            $table->json('customer_uploaded_images')->nullable(); // URLs or paths to uploaded images
            $table->json('customer_uploaded_videos')->nullable(); // URLs or paths to uploaded videos
            // $table->json('ai_analysis_results')->nullable(); // Results from AI estimation
            // $table->decimal('final_weight', 10, 2)->nullable(); // Actual measured weight
            // $table->text('disposal_notes')->nullable(); // Where items were disposed

            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('junk_removal_jobs');
    }
};