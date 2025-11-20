<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeatureCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'icon' => $this->icon,
            'title' => $this->title,
            'headline' => $this->headline,
            'copy' => $this->copy,
            'count_value' => $this->count_value,
        ];
    }
}
