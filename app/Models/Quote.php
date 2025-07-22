<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
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
        'quote_date',
        'expiry_date',
        'items', // JSON cast
        'delivery_fee',
        'pickup_fee',
        'damage_waiver',
        'total_amount',
        'notes',
        'status',
        'linked_booking_id',
        'linked_invoice_id', // Now included in the model
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quote_date' => 'date',
        'expiry_date' => 'date',
        'items' => 'array', // Cast to array for easier handling of JSON column
        'delivery_fee' => 'decimal:2',
        'pickup_fee' => 'decimal:2',
        'damage_waiver' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the vendor that owns the quote.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the customer that the quote belongs to.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the booking that this quote was linked to.
     */
    public function linkedBooking()
    {
        return $this->belongsTo(Booking::class, 'linked_booking_id');
    }

    /**
     * Get the invoice that this quote was linked to.
     */
    public function linkedInvoice()
    {
        return $this->belongsTo(Invoice::class, 'linked_invoice_id'); // Link to Invoice model
    }
}