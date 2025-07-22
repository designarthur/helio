<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
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
        'invoice_number',
        'issue_date',
        'due_date',
        'items', // JSON cast
        'total_amount',
        'balance_due',
        'status',
        'notes',
        'linked_booking_id',
        'linked_quote_id', // Now included in the model, linked after quote migration
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'items' => 'array', // Cast to array for easier handling of JSON column
        'total_amount' => 'decimal:2',
        'balance_due' => 'decimal:2',
    ];

    /**
     * Get the vendor that owns the invoice.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the customer that the invoice belongs to.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the booking that this invoice is linked to.
     */
    public function linkedBooking()
    {
        return $this->belongsTo(Booking::class, 'linked_booking_id');
    }

    /**
     * Get the quote that this invoice was generated from.
     */
    public function linkedQuote()
    {
        return $this->belongsTo(Quote::class, 'linked_quote_id');
    }

    /**
     * Get the payments for the invoice.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}