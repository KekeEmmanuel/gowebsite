<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TourPackage extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'price_from',
        'duration_days',
        'max_participants',
        'is_featured',
        'display_order',
        'published_at',
    ];

    protected $casts = [
        'price_from' => 'decimal:2',
        'duration_days' => 'integer',
        'max_participants' => 'integer',
        'is_featured' => 'boolean',
        'display_order' => 'integer',
        'published_at' => 'datetime',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('is_featured', 'desc')
            ->orderBy('display_order')
            ->orderBy('created_at', 'desc');
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('hero')
            ->singleFile();

        $this
            ->addMediaCollection('gallery')
            ->useFallbackUrl('/images/placeholders/package.jpg');
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
