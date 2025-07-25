<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class Vendor extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // Ensure HasApiTokens is used here

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_name',
        'email',
        'password',
        'phone',
        'primary_address',
        'operating_hours',
        'service_areas',
        'status',
        'subscription_plan',
        'branding_settings',
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
        'branding_settings' => 'array',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}