<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class HeroSlide extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'subtitle',
        'cta_label',
        'cta_url',
        'media_id',
        'position',
        'is_active',
        'schedule_start',
        'schedule_end',
        'meta',
    ];

    protected $casts = [
        'position' => 'integer',
        'is_active' => 'boolean',
        'schedule_start' => 'datetime',
        'schedule_end' => 'datetime',
        'meta' => 'array',
    ];

    public function scopeActive(Builder $query): Builder
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('schedule_start')->orWhere('schedule_start', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('schedule_end')->orWhere('schedule_end', '>=', $now);
            })
            ->orderBy('position');
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('slide')
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('hero')
            ->width(1920)
            ->height(1080)
            ->performOnCollections('slide');
    }
}
