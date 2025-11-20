<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItineraryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Build meta string for frontend (e.g., "7 days · Serengeti & Grumeti")
        $metaParts = [];
        if ($this->duration_days) {
            $metaParts[] = $this->duration_days . ' days';
        }
        if ($this->relationLoaded('destination') && $this->destination) {
            $metaParts[] = $this->destination->name;
        } elseif ($this->relationLoaded('serviceType') && $this->serviceType) {
            $metaParts[] = $this->serviceType->name;
        }
        $meta = !empty($metaParts) ? implode(' · ', $metaParts) : null;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'badge' => $this->badge,
            'meta' => $meta,
            'image' => $this->image_base64 ?: $this->when(
                $this->relationLoaded('media') || $this->hasMedia('hero'),
                fn () => $this->getFirstMediaUrl('hero') ?: $this->getFirstMediaUrl('hero', 'cover')
            ),
            'service_type' => $this->whenLoaded('serviceType', fn () => new ServiceTypeResource($this->serviceType)),
            'destination' => $this->whenLoaded('destination', fn () => new DestinationResource($this->destination)),
            'duration_days' => $this->duration_days,
            'price_from' => $this->price_from,
            'difficulty' => $this->difficulty,
            'highlights' => $this->highlights ?? [],
            'inclusions' => $this->inclusions,
            'exclusions' => $this->exclusions,
            'tags' => $this->tags,
            'is_featured' => $this->is_featured,
            'display_order' => $this->display_order,
            'published_at' => optional($this->published_at)->toISOString(),
            'hero_image' => $this->when(
                $this->relationLoaded('media') || $this->hasMedia('hero'),
                fn () => [
                    'url' => $this->getFirstMediaUrl('hero'),
                    'thumb' => $this->getFirstMediaUrl('hero', 'thumb'),
                    'cover' => $this->getFirstMediaUrl('hero', 'cover'),
                ]
            ),
            'gallery' => $this->when(
                $this->relationLoaded('media') || $this->hasMedia('gallery'),
                fn () => $this->getMedia('gallery')->map(fn ($media) => [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'thumb' => $media->getUrl('thumb'),
                    'cover' => $media->getUrl('cover'),
                    'name' => $media->name,
                    'alt' => $media->getCustomProperty('alt'),
                ])
            ),
            'filters' => $this->whenLoaded('filters', fn () => $this->filters ? ItineraryFilterResource::collection($this->filters) : []),
            'days' => $this->whenLoaded('days', fn () => $this->days ? ItineraryDayResource::collection($this->days) : []),
            'links' => [
                'self' => route('api.itineraries.show', $this->slug),
            ],
            'meta_details' => [
                'days_count' => $this->when(isset($this->days_count), $this->days_count),
                'seo' => $this->seo_meta,
            ],
        ];
    }
}
