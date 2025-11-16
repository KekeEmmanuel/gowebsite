<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItineraryDayResource extends JsonResource
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
            'day_number' => $this->day_number,
            'title' => $this->title,
            'description' => $this->description,
            'accommodation' => $this->accommodation,
            'meals' => $this->meals,
            'meta' => $this->meta,
        ];
    }
}
