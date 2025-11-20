<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Destination extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'region',
        'teaser',
        'tag',
        'description',
        'image_base64',
        'map_embed_url',
        'hero_media_id',
        'is_featured',
        'display_order',
        'published_at',
        'seo_meta',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'display_order' => 'integer',
        'published_at' => 'datetime',
        'seo_meta' => 'array',
    ];

    public function itineraries()
    {
        return $this->hasMany(Itinerary::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('hero')
            ->singleFile();

        $this
            ->addMediaCollection('gallery');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->width(512)
            ->height(512)
            ->performOnCollections('hero', 'gallery');

        $this
            ->addMediaConversion('grid')
            ->width(800)
            ->height(600)
            ->performOnCollections('hero', 'gallery');
    }
}
