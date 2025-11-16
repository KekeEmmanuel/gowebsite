<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HeroSlideResource extends JsonResource
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
            'subtitle' => $this->subtitle,
            'cta_label' => $this->cta_label,
            'cta_url' => $this->cta_url,
            'position' => $this->position,
            'is_active' => $this->is_active,
            'schedule_start' => optional($this->schedule_start)->toISOString(),
            'schedule_end' => optional($this->schedule_end)->toISOString(),
            'image' => $this->when(
                $this->relationLoaded('media') || $this->hasMedia('slide'),
                fn () => [
                    'url' => $this->getFirstMediaUrl('slide'),
                    'hero' => $this->getFirstMediaUrl('slide', 'hero'),
                ]
            ),
            'meta' => $this->meta,
        ];
    }
}
