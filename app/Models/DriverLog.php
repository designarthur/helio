<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'driver_logs'; // Explicitly set table name

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'driver_id',
        'vendor_id',
        'status',
        'start_time',
        'end_time',
        'duration_minutes',
        'location',
        'remarks',
        // 'violation_flag', // If added in migration
        // 'violation_details',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration_minutes' => 'integer',
        // 'violation_flag' => 'boolean', // If added in migration
    ];

    /**
     * Get the driver who owns the log entry.
     */
    public function driver()
    {
        return $this->belongsTo(User::class); // Assuming User model represents drivers
    }

    /**
     * Get the vendor that owns this log entry.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}