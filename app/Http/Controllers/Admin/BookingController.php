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
    public function index(): Response
    {
        return Inertia::render('Admin/Bookings/Index', [
            'bookings' => Booking::with(['itinerary:id,title,slug'])
                ->latest()
                ->get()
                ->map(function ($booking) {
                    return [
                        'id' => $booking->id,
                        'itinerary' => [
                            'title' => $booking->itinerary->title,
                            'slug' => $booking->itinerary->slug,
                        ],
                        'full_name' => $booking->full_name,
                        'email' => $booking->email,
                        'phone' => $booking->phone,
                        'country' => $booking->country,
                        'number_of_travellers' => $booking->number_of_travellers,
                        'preferred_travel_date' => $booking->preferred_travel_date?->format('Y-m-d'),
                        'special_requests' => $booking->special_requests,
                        'status' => $booking->status,
                        'admin_notes' => $booking->admin_notes,
                        'created_at' => $booking->created_at->format('Y-m-d H:i:s'),
                    ];
                }),
        ]);
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $booking->update($validated);

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking updated successfully.');
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        $booking->delete();

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking deleted successfully.');
    }
}
