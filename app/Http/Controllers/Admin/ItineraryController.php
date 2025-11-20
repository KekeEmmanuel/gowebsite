<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\Itinerary;
use App\Models\ServiceType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ItineraryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Itineraries/Index', [
            'itineraries' => Itinerary::with(['serviceType', 'destination'])
                ->orderBy('display_order')
                ->get()
                ->map(function ($itinerary) {
                    return [
                        'id' => $itinerary->id,
                        'title' => $itinerary->title,
                        'summary' => $itinerary->summary,
                        'badge' => $itinerary->badge,
                        'slug' => $itinerary->slug,
                        'duration_days' => $itinerary->duration_days,
                        'price_from' => $itinerary->price_from,
                        'is_featured' => $itinerary->is_featured,
                        'display_order' => $itinerary->display_order,
                        'published_at' => $itinerary->published_at,
                        'image_base64' => $itinerary->image_base64,
                    ];
                }),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Itineraries/Create', [
            'serviceTypes' => ServiceType::all(),
            'destinations' => Destination::all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'badge' => 'nullable|string|max:255',
            'image_base64' => 'nullable|string',
            'slug' => 'required|string|max:255|unique:itineraries,slug',
            'service_type_id' => 'required|exists:service_types,id',
            'destination_id' => 'nullable|exists:destinations,id',
            'duration_days' => 'required|integer|min:1',
            'price_from' => 'nullable|numeric|min:0',
            'difficulty' => 'nullable|string|max:255',
            'highlights' => 'nullable|array',
            'inclusions' => 'nullable|array',
            'exclusions' => 'nullable|array',
            'tags' => 'nullable|array',
            'display_order' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        // Handle image_base64
        if ($request->has('image_base64') && $request->image_base64) {
            $validated['image_base64'] = $request->image_base64;
        } elseif ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageData = file_get_contents($file->getRealPath());
            $base64 = base64_encode($imageData);
            $mimeType = $file->getMimeType();
            $validated['image_base64'] = 'data:' . $mimeType . ';base64,' . $base64;
        }

        $itinerary = Itinerary::create($validated);

        return redirect()->route('admin.itineraries.index')
            ->with('success', 'Itinerary created successfully.');
    }

    public function edit(Itinerary $itinerary): Response
    {
        return Inertia::render('Admin/Itineraries/Edit', [
            'itinerary' => [
                'id' => $itinerary->id,
                'title' => $itinerary->title,
                'summary' => $itinerary->summary,
                'badge' => $itinerary->badge,
                'slug' => $itinerary->slug,
                'service_type_id' => $itinerary->service_type_id,
                'destination_id' => $itinerary->destination_id,
                'duration_days' => $itinerary->duration_days,
                'price_from' => $itinerary->price_from,
                'difficulty' => $itinerary->difficulty,
                'highlights' => $itinerary->highlights,
                'inclusions' => $itinerary->inclusions,
                'exclusions' => $itinerary->exclusions,
                'tags' => $itinerary->tags,
                'display_order' => $itinerary->display_order,
                'is_featured' => $itinerary->is_featured,
                'published_at' => $itinerary->published_at,
                'image_base64' => $itinerary->image_base64,
            ],
            'serviceTypes' => ServiceType::all(),
            'destinations' => Destination::all(),
        ]);
    }

    public function update(Request $request, Itinerary $itinerary): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'badge' => 'nullable|string|max:255',
            'image_base64' => 'nullable|string',
            'slug' => 'required|string|max:255|unique:itineraries,slug,' . $itinerary->id,
            'service_type_id' => 'required|exists:service_types,id',
            'destination_id' => 'nullable|exists:destinations,id',
            'duration_days' => 'required|integer|min:1',
            'price_from' => 'nullable|numeric|min:0',
            'difficulty' => 'nullable|string|max:255',
            'highlights' => 'nullable|array',
            'inclusions' => 'nullable|array',
            'exclusions' => 'nullable|array',
            'tags' => 'nullable|array',
            'display_order' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        // Handle image_base64
        if ($request->has('image_base64') && $request->image_base64) {
            $validated['image_base64'] = $request->image_base64;
        } elseif ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageData = file_get_contents($file->getRealPath());
            $base64 = base64_encode($imageData);
            $mimeType = $file->getMimeType();
            $validated['image_base64'] = 'data:' . $mimeType . ';base64,' . $base64;
        } elseif (!$request->has('image_base64') || !$request->image_base64) {
            unset($validated['image_base64']);
        }

        $itinerary->update($validated);

        return redirect()->route('admin.itineraries.index')
            ->with('success', 'Itinerary updated successfully.');
    }

    public function destroy(Itinerary $itinerary): RedirectResponse
    {
        $itinerary->delete();

        return redirect()->route('admin.itineraries.index')
            ->with('success', 'Itinerary deleted successfully.');
    }
}
