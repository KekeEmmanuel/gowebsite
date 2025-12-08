<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'tour_package' => $this->whenLoaded('tourPackage', fn () => new TourPackageResource($this->tourPackage)),
            'tour_package_id' => $this->tour_package_id,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'travel_date' => optional($this->travel_date)->toDateString(),
            'number_of_travelers' => $this->number_of_travelers,
            'customization_data' => $this->customization_data,
            'special_requests' => $this->special_requests,
            'status' => $this->status,
            'admin_notes' => $this->when($request->user()?->isAdmin() ?? false, $this->admin_notes),
            'completed_at' => optional($this->completed_at)->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
