<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnerResource extends JsonResource
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
            'website_url' => $this->website_url,
            'display_order' => $this->display_order,
            'is_featured' => $this->is_featured,
            'logo' => $this->when(
                $this->relationLoaded('media') || $this->hasMedia('logo'),
                fn () => [
                    'url' => $this->getFirstMediaUrl('logo'),
                    'thumb' => $this->getFirstMediaUrl('logo', 'thumb'),
                ]
            ),
            'meta' => $this->meta,
        ];
    }
}
