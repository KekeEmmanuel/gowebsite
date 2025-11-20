<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Itinerary extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'summary',
        'badge',
        'image_base64',
        'slug',
        'service_type_id',
        'destination_id',
        'duration_days',
        'price_from',
        'difficulty',
        'highlights',
        'inclusions',
        'exclusions',
        'seo_meta',
        'tags',
        'hero_media_id',
        'is_featured',
        'display_order',
        'published_at',
        'external_id',
    ];

    protected $casts = [
        'duration_days' => 'integer',
        'price_from' => 'decimal:2',
        'highlights' => 'array',
        'inclusions' => 'array',
        'exclusions' => 'array',
        'seo_meta' => 'array',
        'tags' => 'array',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function days()
    {
        return $this->hasMany(ItineraryDay::class)->orderBy('day_number');
    }

    public function filters()
    {
        return $this->belongsToMany(ItineraryFilter::class, 'itinerary_filter_map')
            ->withTimestamps();
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
            ->addMediaCollection('gallery')
            ->useFallbackUrl('/images/placeholders/itinerary.jpg');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->width(480)
            ->height(320)
            ->fit('crop', 480, 320)
            ->performOnCollections('hero', 'gallery');

        $this
            ->addMediaConversion('cover')
            ->width(1280)
            ->height(720)
            ->fit('crop', 1280, 720)
            ->performOnCollections('hero', 'gallery');
    }
}
