<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer_payment_methods', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID for the payment method
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade'); // Link to the vendor who handles this payment method
            $table->foreignId('customer_id')->constrained()->onDelete('cascade'); // Link to the customer who owns this payment method

            // Non-sensitive payment method details
            $table->string('nickname')->nullable(); // e.g., "My Visa", "Work Card"
            $table->string('card_type'); // e.g., Visa, Mastercard, Amex
            $table->string('last_four'); // Last four digits of the card number
            $table->string('expiry_month', 2); // MM
            $table->string('expiry_year', 4); // YYYY (full year)
            
            // This is the CRITICAL part for PCI compliance: Store the token, not the full card.
            $table->string('token')->unique(); // Unique token from payment gateway (e.g., Stripe token, Braintree payment method nonce)
            
            $table->boolean('is_default')->default(false); // Whether this is the customer's default payment method

            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_payment_methods');
    }
};