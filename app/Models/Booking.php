<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vendor_id',
        'customer_id',
        'equipment_id',
        'rental_start_date',
        'rental_end_date',
        'delivery_address',
        'pickup_address',
        'status',
        'total_price',
        'booking_notes',
        'driver_id',
        'estimated_tonnage',
        'prohibited_materials_ack',
        'requested_service_freq',
        'toilet_special_requests',
        'container_placement_notes',
        'container_security_access',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rental_start_date' => 'date',
        'rental_end_date' => 'date',
        'total_price' => 'decimal:2',
        'estimated_tonnage' => 'decimal:2',
    ];

    /**
     * Get the vendor that owns the booking.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the customer that owns the booking.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the equipment associated with the booking.
     */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Get the driver assigned to the booking.
     * Assuming 'users' table is where drivers are stored.
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id'); // Assuming 'User' model for drivers
    }
}