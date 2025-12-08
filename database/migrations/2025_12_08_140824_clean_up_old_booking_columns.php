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
        Schema::table('bookings', function (Blueprint $table) {
            // Make itinerary_id nullable if it exists (for backward compatibility)
            if (Schema::hasColumn('bookings', 'itinerary_id')) {
                $table->foreignId('itinerary_id')->nullable()->change();
            }
            
            // Remove old columns that are no longer needed
            $columnsToDrop = [
                'number_of_travellers', // Old column (we use number_of_travelers)
                'preferred_travel_date', // Old column (we use travel_date)
                'first_name', // Old column (we use full_name)
                'last_name', // Old column (we use full_name)
                'country', // Old column (not needed)
            ];
            
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Re-add dropped columns if needed (for rollback)
            if (!Schema::hasColumn('bookings', 'number_of_travellers')) {
                $table->unsignedSmallInteger('number_of_travellers')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'preferred_travel_date')) {
                $table->date('preferred_travel_date')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'first_name')) {
                $table->string('first_name')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'last_name')) {
                $table->string('last_name')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'country')) {
                $table->string('country')->nullable();
            }
        });
    }
};
