<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JunkRemovalJob extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'junk_removal_jobs'; // Explicitly set table name as it's plural but unconventional

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vendor_id',
        'customer_id',
        'job_number',
        'requested_date',
        'requested_time',
        'job_location',
        'description_of_junk',
        'volume_estimate',
        'weight_estimate',
        'crew_requirements',
        'assigned_driver', // This is driver_id in the migration, named for clarity in model context
        'estimated_price',
        'status',
        'job_notes',
        'customer_uploaded_images', // Cast to array
        'customer_uploaded_videos', // Cast to array
        // 'ai_analysis_results', // If added in migration
        // 'final_weight', // If added in migration
        // 'disposal_notes', // If added in migration
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'requested_date' => 'date',
        'estimated_price' => 'decimal:2',
        'customer_uploaded_images' => 'array', // Cast JSON columns to array
        'customer_uploaded_videos' => 'array', // Cast JSON columns to array
        // 'ai_analysis_results' => 'array', // If added in migration
        // 'final_weight' => 'decimal:2', // If added in migration
    ];

    /**
     * Get the vendor that owns the junk removal job.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the customer that the junk removal job belongs to.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the driver assigned to the junk removal job.
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'assigned_driver'); // Link to User model with custom foreign key
    }

    // You can define relationships for quotes/invoices linked to this job later
}