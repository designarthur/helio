<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleInspection extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vehicle_inspections'; // Explicitly set table name

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'driver_id',
        'vendor_id',
        'inspection_type',
        'vehicle_id',
        'odometer_reading',
        'inspection_datetime',
        'checklist_results', // JSON cast
        'defects_found',
        'defect_notes',
        'defect_photos', // JSON cast
        'driver_certified_safe',
        'driver_signature_image',
        // 'mechanic_status', // If added in migration
        // 'mechanic_notes',
        // 'repair_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'inspection_datetime' => 'datetime',
        'checklist_results' => 'array', // Cast JSON columns to array
        'defects_found' => 'boolean',
        'defect_photos' => 'array', // Cast JSON columns to array
        'driver_certified_safe' => 'boolean',
        // 'repair_date' => 'date', // If added in migration
    ];

    /**
     * Get the driver who performed the inspection.
     */
    public function driver()
    {
        return $this->belongsTo(User::class); // Assuming User model represents drivers
    }

    /**
     * Get the vendor that owns this inspection record.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}