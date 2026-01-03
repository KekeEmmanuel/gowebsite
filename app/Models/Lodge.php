<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Lodge extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'location',
        'type',
        'mood',
        'short_description',
        'description',
        'amenities',
        'price_from',
        'capacity',
        'hero_media_id',
        'display_order',
        'is_active',
        'is_featured',
        'published_at',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'amenities' => 'array',
        'price_from' => 'decimal:2',
        'capacity' => 'integer',
        'published_at' => 'datetime',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('display_order');
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
        // Disable optimization if fileinfo is not available to prevent mime_content_type() errors
        $conversion = $this
            ->addMediaConversion('thumb')
            ->width(480)
            ->height(320);
        
        if (!extension_loaded('fileinfo')) {
            $conversion->nonOptimized();
        }
        
        $conversion->performOnCollections('hero', 'gallery');

        $conversion = $this
            ->addMediaConversion('cover')
            ->width(1280)
            ->height(720);
        
        if (!extension_loaded('fileinfo')) {
            $conversion->nonOptimized();
        }
        
        $conversion->performOnCollections('hero', 'gallery');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('is_featured', 'desc')
            ->orderBy('display_order')
            ->orderBy('created_at', 'desc');
    }
}
