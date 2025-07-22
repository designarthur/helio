<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vendor_id',
        'date',
        'description',
        'category',
        'amount',
        'vendor_name', // Column for who the expense was paid to
        'notes',
        // 'receipt_path', // If added in migration
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the vendor that owns the expense.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}