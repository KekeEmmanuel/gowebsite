<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DestinationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'region' => $this->region,
            'teaser' => $this->teaser,
            'description' => $this->description,
            'map_embed_url' => $this->map_embed_url,
            'is_featured' => $this->is_featured,
            'published_at' => optional($this->published_at)->toISOString(),
            'hero_image' => $this->when(
                $this->relationLoaded('media') || $this->hasMedia('hero'),
                fn () => [
                    'url' => $this->getFirstMediaUrl('hero'),
                    'grid' => $this->getFirstMediaUrl('hero', 'grid'),
                    'thumb' => $this->getFirstMediaUrl('hero', 'thumb'),
                ]
            ),
            'gallery' => $this->when(
                $this->relationLoaded('media') || $this->hasMedia('gallery'),
                fn () => $this->getMedia('gallery')->map(fn ($media) => [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'grid' => $media->getUrl('grid'),
                    'thumb' => $media->getUrl('thumb'),
                    'name' => $media->name,
                    'alt' => $media->getCustomProperty('alt'),
                ])
            ),
            'seo_meta' => $this->seo_meta,
        ];
    }
}
