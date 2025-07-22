<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vendor_id',
        'name',
        'company',
        'email',
        'phone',
        'billing_address',
        'service_addresses', // This will be cast to an array
        'customer_type',
        'status',
        'internal_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'service_addresses' => 'array', // Cast to array for easier handling
    ];

    /**
     * Get the vendor that owns the customer.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    // You can define relationships for a customer's bookings, invoices, etc. here later
    // public function bookings()
    // {
    //     return $this->hasMany(Booking::class);
    // }

    // public function invoices()
    // {
    //     return $this->hasMany(Invoice::class);
    // }
}