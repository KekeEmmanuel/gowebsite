<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItineraryFilter extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'key',
        'label',
        'min_value',
        'max_value',
        'region_code',
        'display_order',
        'is_active',
        'meta',
    ];

    protected $casts = [
        'min_value' => 'integer',
        'max_value' => 'integer',
        'display_order' => 'integer',
        'is_active' => 'boolean',
        'meta' => 'array',
    ];

    public function itineraries()
    {
        return $this->belongsToMany(Itinerary::class, 'itinerary_filter_map')
            ->withTimestamps();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }
}
