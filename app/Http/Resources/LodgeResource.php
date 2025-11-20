<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LodgeResource extends JsonResource
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
            'location' => $this->location,
            'mood' => $this->mood,
            'image' => $this->image_base64 ?: $this->when(
                $this->relationLoaded('media') || $this->hasMedia('hero'),
                fn () => $this->getFirstMediaUrl('hero')
            ),
            'display_order' => $this->display_order,
            'is_active' => $this->is_active,
        ];
    }
}
