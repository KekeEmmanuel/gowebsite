<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Itinerary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'itinerary_id' => 'required|exists:itineraries,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'country' => 'nullable|string|max:255',
            'number_of_travellers' => 'required|integer|min:1|max:50',
            'preferred_travel_date' => 'nullable|date|after_or_equal:today',
            'special_requests' => 'nullable|string|max:2000',
        ]);

        // Verify itinerary exists and is published
        $itinerary = Itinerary::published()->findOrFail($validated['itinerary_id']);

        $booking = Booking::create([
            ...$validated,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Booking request submitted successfully. We will contact you soon!',
            'booking' => [
                'id' => $booking->id,
                'itinerary' => [
                    'title' => $itinerary->title,
                    'slug' => $itinerary->slug,
                ],
            ],
        ], 201);
    }
}
