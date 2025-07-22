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
        Schema::table('quotes', function (Blueprint $table) {
            // Add linked_invoice_id foreign key if it doesn't already exist
            if (!Schema::hasColumn('quotes', 'linked_invoice_id')) {
                $table->foreignId('linked_invoice_id')->nullable()->constrained('invoices')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            // Drop foreign key first if it exists
            if (Schema::hasColumn('quotes', 'linked_invoice_id')) {
                $table->dropForeign(['linked_invoice_id']);
                $table->dropColumn('linked_invoice_id');
            }
        });
    }
};