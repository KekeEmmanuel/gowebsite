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
        Schema::table('lodges', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
            $table->text('description')->nullable()->after('mood');
            $table->string('short_description')->nullable()->after('mood');
            $table->enum('type', ['lodge', 'camp'])->default('lodge')->after('location');
            $table->json('amenities')->nullable()->after('description');
            $table->decimal('price_from', 10, 2)->nullable()->after('amenities');
            $table->integer('capacity')->nullable()->after('price_from');
            $table->boolean('is_featured')->default(false)->after('is_active');
            $table->timestamp('published_at')->nullable()->after('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lodges', function (Blueprint $table) {
            $table->dropColumn([
                'slug',
                'description',
                'short_description',
                'type',
                'amenities',
                'price_from',
                'capacity',
                'is_featured',
                'published_at',
            ]);
        });
    }
};
