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
        Schema::table('users', function (Blueprint $table) {
            // Add vendor_id to link drivers to their respective vendor employer
            // Only add if it doesn't exist
            if (!Schema::hasColumn('users', 'vendor_id')) {
                $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('cascade');
            }

            // Driver-specific fields - Add only if they don't exist
            if (!Schema::hasColumn('users', 'phone')) { // Only if phone column doesn't exist at all
                 $table->string('phone')->nullable()->after('email'); // If adding phone for the first time
            } else {
                 $table->string('phone')->nullable()->change(); // If phone exists, just make it nullable
            }

            if (!Schema::hasColumn('users', 'license_number')) {
                $table->string('license_number')->nullable();
            }
            if (!Schema::hasColumn('users', 'license_expiry')) {
                $table->date('license_expiry')->nullable();
            }
            if (!Schema::hasColumn('users', 'cdl_class')) {
                $table->string('cdl_class')->nullable();
            }
            if (!Schema::hasColumn('users', 'assigned_vehicle')) {
                $table->string('assigned_vehicle')->nullable();
            }
            if (!Schema::hasColumn('users', 'driver_notes')) {
                $table->text('driver_notes')->nullable();
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user')->after('email'); // 'user', 'driver', 'admin', 'vendor_admin'
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('Active')->after('role'); // Active, On Leave, Inactive
            }
            if (!Schema::hasColumn('users', 'certifications')) {
                $table->text('certifications')->nullable(); // JSON or comma-separated for easier parsing
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key first if it exists
            if (Schema::hasColumn('users', 'vendor_id')) {
                $table->dropForeign(['vendor_id']);
                $table->dropColumn('vendor_id');
            }

            // Drop driver-specific fields if they exist
            if (Schema::hasColumn('users', 'license_number')) {
                $table->dropColumn([
                    'license_number',
                    'license_expiry',
                    'cdl_class',
                    'assigned_vehicle',
                    'driver_notes',
                    'role',
                    'status',
                    'certifications',
                ]);
            }
            // If you made phone nullable and need to revert it to not-nullable upon rollback (optional)
            // if (Schema::hasColumn('users', 'phone')) {
            //     $table->string('phone')->nullable(false)->change();
            // }
        });
    }
};