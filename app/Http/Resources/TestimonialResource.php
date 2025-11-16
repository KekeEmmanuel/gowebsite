<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
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
            'guest_name' => $this->guest_name,
            'origin_country' => $this->origin_country,
            'quote' => $this->quote,
            'rating' => $this->rating,
            'is_featured' => $this->is_featured,
            'published_at' => optional($this->published_at)->toISOString(),
            'portrait' => $this->when(
                $this->relationLoaded('media') || $this->hasMedia('portrait'),
                fn () => [
                    'url' => $this->getFirstMediaUrl('portrait'),
                    'thumb' => $this->getFirstMediaUrl('portrait', 'thumb'),
                ]
            ),
        ];
    }
}
