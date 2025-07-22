<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vendor_id', // Add vendor_id
        'name',
        'email',
        'password',
        'phone', // Add phone
        'license_number', // Add driver fields
        'license_expiry',
        'cdl_class',
        'assigned_vehicle',
        'driver_notes',
        'role', // Add role (e.g., 'user', 'customer', 'driver', 'admin', 'vendor_admin')
        'status', // Add status
        'certifications', // Add certifications
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'license_expiry' => 'date', // Cast license_expiry to a date object
        'certifications' => 'array', // Cast certifications to an array for easier handling
    ];

    /**
     * Get the vendor that employs the user (if they are a driver/staff).
     * Or, if this user is a customer, this might link to their primary vendor.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    // You can define other relationships here, e.g., bookings assigned to this driver
    // public function assignedBookings()
    // {
    //     return $this->hasMany(Booking::class, 'driver_id');
    // }
}