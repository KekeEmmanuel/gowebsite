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
        Schema::create('itinerary_filters', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('key');
            $table->string('label');
            $table->unsignedSmallInteger('min_value')->nullable();
            $table->unsignedSmallInteger('max_value')->nullable();
            $table->string('region_code')->nullable();
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['type', 'key']);
            $table->index(['type', 'display_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary_filters');
    }
};
