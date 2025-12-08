<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tour_package_id' => ['required', 'exists:tour_packages,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:255'],
            'travel_date' => ['nullable', 'date', 'after_or_equal:today'],
            'number_of_travelers' => ['required', 'integer', 'min:1', 'max:100'],
            'customization_data' => ['nullable', 'array'],
            'special_requests' => ['nullable', 'string', 'max:5000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $booking = Booking::create([
            'tour_package_id' => $request->tour_package_id,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'whatsapp' => $request->whatsapp,
            'travel_date' => $request->travel_date,
            'number_of_travelers' => $request->number_of_travelers,
            'customization_data' => $request->customization_data,
            'special_requests' => $request->special_requests,
            'status' => 'pending',
        ]);

        return (new BookingResource($booking->load('tourPackage')))
            ->response()
            ->setStatusCode(201);
    }
}
