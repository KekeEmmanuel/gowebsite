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
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary ?? $this->when(isset($this->highlights), fn () => $this->highlights[0] ?? null),
            'service_type' => $this->whenLoaded('serviceType', fn () => new ServiceTypeResource($this->serviceType)),
            'destination' => $this->whenLoaded('destination', fn () => new DestinationResource($this->destination)),
            'duration_days' => $this->duration_days,
            'price_from' => $this->price_from,
            'difficulty' => $this->difficulty,
            'highlights' => $this->highlights,
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
            'filters' => $this->whenLoaded('filters', fn () => ItineraryFilterResource::collection($this->filters)),
            'days' => $this->whenLoaded('days', fn () => ItineraryDayResource::collection($this->days)),
            'links' => [
                'self' => route('api.itineraries.show', $this->slug),
            ],
            'meta' => [
                'days_count' => $this->when(isset($this->days_count), $this->days_count),
                'seo' => $this->seo_meta,
            ],
        ];
    }
}
