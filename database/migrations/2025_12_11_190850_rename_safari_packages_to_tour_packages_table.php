<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if safari_packages table exists and needs to be renamed
        if (Schema::hasTable('safari_packages') && !Schema::hasTable('tour_packages')) {
            // First, handle the foreign key constraint in bookings table
            if (Schema::hasColumn('bookings', 'safari_package_id')) {
                try {
                    DB::statement('ALTER TABLE bookings DROP CONSTRAINT IF EXISTS bookings_safari_package_id_foreign');
                } catch (\Exception $e) {
                    // Constraint might not exist or have a different name
                }
                
                // Rename the foreign key column in bookings table
                Schema::table('bookings', function (Blueprint $table) {
                    $table->renameColumn('safari_package_id', 'tour_package_id');
                });
            }
            
            // Rename the table
            Schema::rename('safari_packages', 'tour_packages');
            
            // Recreate the foreign key constraint if it doesn't exist
            if (Schema::hasColumn('bookings', 'tour_package_id')) {
                try {
                    Schema::table('bookings', function (Blueprint $table) {
                        $table->foreign('tour_package_id')
                            ->references('id')
                            ->on('tour_packages')
                            ->onDelete('set null');
                    });
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only rollback if tour_packages exists
        if (Schema::hasTable('tour_packages') && !Schema::hasTable('safari_packages')) {
            // Drop the foreign key constraint
            if (Schema::hasColumn('bookings', 'tour_package_id')) {
                try {
                    DB::statement('ALTER TABLE bookings DROP CONSTRAINT IF EXISTS bookings_tour_package_id_foreign');
                } catch (\Exception $e) {
                    // Constraint might not exist
                }
                
                // Rename the foreign key column back
                Schema::table('bookings', function (Blueprint $table) {
                    $table->renameColumn('tour_package_id', 'safari_package_id');
                });
            }
            
            // Rename the table back
            Schema::rename('tour_packages', 'safari_packages');
            
            // Recreate the foreign key constraint
            if (Schema::hasColumn('bookings', 'safari_package_id')) {
                try {
                    Schema::table('bookings', function (Blueprint $table) {
                        $table->foreign('safari_package_id')
                            ->references('id')
                            ->on('safari_packages')
                            ->onDelete('set null');
                    });
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
            }
        }
    }
};
