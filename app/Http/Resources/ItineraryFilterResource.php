<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItineraryFilterResource extends JsonResource
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
            'type' => $this->type,
            'key' => $this->key,
            'label' => $this->label,
            'min_value' => $this->min_value,
            'max_value' => $this->max_value,
            'region_code' => $this->region_code,
            'display_order' => $this->display_order,
            'is_active' => $this->is_active,
            'meta' => $this->meta,
        ];
    }
}
