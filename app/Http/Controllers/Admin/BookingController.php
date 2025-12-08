<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function index(Request $request): Response
    {
        $status = $request->get('status', 'all');
        
        $query = Booking::with('tourPackage')
            ->orderBy('created_at', 'desc');

        if ($status === 'pending') {
            $query->pending();
        } elseif ($status === 'completed') {
            $query->completed();
        }

        return Inertia::render('Admin/Bookings/Index', [
            'bookings' => $query->get()->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'tour_package' => $booking->tourPackage ? [
                        'id' => $booking->tourPackage->id,
                        'title' => $booking->tourPackage->title,
                        'slug' => $booking->tourPackage->slug,
                    ] : null,
                    'full_name' => $booking->full_name,
                    'email' => $booking->email,
                    'phone' => $booking->phone,
                    'whatsapp' => $booking->whatsapp,
                    'travel_date' => $booking->travel_date?->toDateString(),
                    'number_of_travelers' => $booking->number_of_travelers,
                    'customization_data' => $booking->customization_data,
                    'special_requests' => $booking->special_requests,
                    'status' => $booking->status,
                    'admin_notes' => $booking->admin_notes,
                    'completed_at' => $booking->completed_at?->toISOString(),
                    'created_at' => $booking->created_at->toISOString(),
                ];
            }),
            'filters' => [
                'status' => $status,
            ],
        ]);
    }

    public function show(Booking $booking): Response
    {
        $booking->load('tourPackage');

        return Inertia::render('Admin/Bookings/Show', [
            'booking' => [
                'id' => $booking->id,
                'tour_package' => $booking->tourPackage ? [
                    'id' => $booking->tourPackage->id,
                    'title' => $booking->tourPackage->title,
                    'slug' => $booking->tourPackage->slug,
                    'description' => $booking->tourPackage->description,
                    'price_from' => $booking->tourPackage->price_from,
                    'duration_days' => $booking->tourPackage->duration_days,
                ] : null,
                'full_name' => $booking->full_name,
                'email' => $booking->email,
                'phone' => $booking->phone,
                'whatsapp' => $booking->whatsapp,
                'travel_date' => $booking->travel_date?->toDateString(),
                'number_of_travelers' => $booking->number_of_travelers,
                'customization_data' => $booking->customization_data,
                'special_requests' => $booking->special_requests,
                'status' => $booking->status,
                'admin_notes' => $booking->admin_notes,
                'completed_at' => $booking->completed_at?->toISOString(),
                'created_at' => $booking->created_at->toISOString(),
                'updated_at' => $booking->updated_at->toISOString(),
            ],
        ]);
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:5000',
            'status' => 'required|in:pending,completed',
        ]);

        $booking->update($validated);

        if ($validated['status'] === 'completed' && !$booking->completed_at) {
            $booking->markAsCompleted();
        }

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Booking updated successfully.');
    }

    public function markCompleted(Booking $booking): RedirectResponse
    {
        $booking->markAsCompleted();

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Booking marked as completed.');
    }
}
