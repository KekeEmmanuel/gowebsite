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
        // Rename the table
        Schema::rename('tour_packages', 'safari_packages');
        
        // Rename the foreign key column in bookings table
        if (Schema::hasColumn('bookings', 'tour_package_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->renameColumn('tour_package_id', 'safari_package_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename the foreign key column back
        if (Schema::hasColumn('bookings', 'safari_package_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->renameColumn('safari_package_id', 'tour_package_id');
            });
        }
        
        // Rename the table back
        Schema::rename('safari_packages', 'tour_packages');
    }
};
