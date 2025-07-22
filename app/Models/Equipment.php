<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vendor_id',
        'internal_id',
        'type',
        'size',
        'status',
        'location',
        'description',
        'purchase_date',
        'purchase_cost',
        'supplier_manufacturer',
        'base_daily_rate',
        'default_rental_period',
        'min_rental_period',
        'extended_daily_rate',
        'delivery_fee',
        'pickup_fee',
        'damage_waiver_cost',
        'dumpster_dimensions',
        'max_tonnage',
        'overage_per_ton_fee',
        'disposal_rate_per_ton',
        'dumpster_container_type',
        'gate_type',
        'prohibited_materials',
        'toilet_capacity',
        'service_frequency',
        'toilet_features',
        'storage_container_type',
        'door_type',
        'condition',
        'security_features',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_date' => 'date',
        'purchase_cost' => 'decimal:2',
        'base_daily_rate' => 'decimal:2',
        'extended_daily_rate' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'pickup_fee' => 'decimal:2',
        'damage_waiver_cost' => 'decimal:2',
        'max_tonnage' => 'decimal:2',
        'overage_per_ton_fee' => 'decimal:2',
        'disposal_rate_per_ton' => 'decimal:2',
        'min_rental_period' => 'integer',
        'default_rental_period' => 'integer',
        'prohibited_materials' => 'array', // Assuming comma-separated values in DB, cast to array for easier use
        'toilet_features' => 'array', // Assuming comma-separated values in DB, cast to array for easier use
        'security_features' => 'array', // Assuming comma-separated values in DB, cast to array for easier use
    ];


    /**
     * Get the vendor that owns the equipment.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}