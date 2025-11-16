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
        Schema::create('itinerary_filter_map', function (Blueprint $table) {
            $table->foreignId('itinerary_id')->constrained()->cascadeOnDelete();
            $table->foreignId('filter_id')->constrained('itinerary_filters')->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['itinerary_id', 'filter_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary_filter_map');
    }
};
