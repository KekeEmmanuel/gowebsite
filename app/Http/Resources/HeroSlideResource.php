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
        // Use base64 image if available, otherwise fallback to media library
        $imageUrl = $this->image_base64;
        
        if (!$imageUrl && ($this->relationLoaded('media') || $this->hasMedia('slide'))) {
            $imageUrl = $this->getFirstMediaUrl('slide') ?: $this->getFirstMediaUrl('slide', 'hero');
        }

        return [
            'id' => $this->id,
            'label' => $this->label ?? $this->subtitle,
            'title' => $this->title,
            'description' => $this->description ?? $this->subtitle,
            'ctaLabel' => $this->cta_label,
            'ctaHref' => $this->cta_url,
            'image' => $imageUrl,
            'position' => $this->position,
            'is_active' => $this->is_active,
        ];
    }
}
