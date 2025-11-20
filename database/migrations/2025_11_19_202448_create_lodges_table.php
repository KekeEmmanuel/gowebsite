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
        Schema::create('lodges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->text('mood'); // Description/mood text
            $table->foreignId('hero_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lodges');
    }
};
