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
            // Package relationship
            if (!Schema::hasColumn('bookings', 'tour_package_id')) {
                $table->foreignId('tour_package_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }
            
            // Customer information
            if (!Schema::hasColumn('bookings', 'full_name')) {
                $table->string('full_name')->after('tour_package_id');
            }
            if (!Schema::hasColumn('bookings', 'email')) {
                $table->string('email')->after('full_name');
            }
            if (!Schema::hasColumn('bookings', 'phone')) {
                $table->string('phone')->after('email');
            }
            if (!Schema::hasColumn('bookings', 'whatsapp')) {
                $table->string('whatsapp')->nullable()->after('phone');
            }
            
            // Travel details
            if (!Schema::hasColumn('bookings', 'travel_date')) {
                $table->date('travel_date')->nullable()->after('whatsapp');
            }
            if (!Schema::hasColumn('bookings', 'number_of_travelers')) {
                $table->unsignedSmallInteger('number_of_travelers')->default(1)->after('travel_date');
            }
            
            // Customization data (JSON field for flexible package customization)
            // Stores: locations, days per location, travelers, special preferences, etc.
            if (!Schema::hasColumn('bookings', 'customization_data')) {
                $table->json('customization_data')->nullable()->after('number_of_travelers');
            }
            
            // Special requests/notes
            if (!Schema::hasColumn('bookings', 'special_requests')) {
                $table->text('special_requests')->nullable()->after('customization_data');
            }
            
            // Status: pending or completed
            if (!Schema::hasColumn('bookings', 'status')) {
                $table->enum('status', ['pending', 'completed'])->default('pending')->after('special_requests');
            }
            
            // Admin fields
            if (!Schema::hasColumn('bookings', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('status');
            }
            if (!Schema::hasColumn('bookings', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('admin_notes');
            }
        });
        
        // Add indexes separately to avoid issues
        if (Schema::hasColumn('bookings', 'status')) {
            try {
                Schema::table('bookings', function (Blueprint $table) {
                    $table->index('status');
                });
            } catch (\Exception $e) {
                // Index might already exist
            }
        }
        
        if (Schema::hasColumn('bookings', 'created_at')) {
            try {
                Schema::table('bookings', function (Blueprint $table) {
                    $table->index('created_at');
                });
            } catch (\Exception $e) {
                // Index might already exist
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['tour_package_id']);
            $table->dropColumn([
                'tour_package_id',
                'full_name',
                'email',
                'phone',
                'whatsapp',
                'travel_date',
                'number_of_travelers',
                'customization_data',
                'special_requests',
                'status',
                'admin_notes',
                'completed_at',
            ]);
        });
    }
};
