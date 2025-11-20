<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DestinationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Destinations/Index', [
            'destinations' => Destination::orderBy('display_order')->orderBy('name')->get()->map(function ($destination) {
                return [
                    'id' => $destination->id,
                    'name' => $destination->name,
                    'slug' => $destination->slug,
                    'region' => $destination->region,
                    'teaser' => $destination->teaser,
                    'tag' => $destination->tag,
                    'description' => $destination->description,
                    'is_featured' => $destination->is_featured,
                    'display_order' => $destination->display_order,
                    'published_at' => $destination->published_at,
                    'image_base64' => $destination->image_base64,
                ];
            }),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Destinations/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:destinations,slug',
            'region' => 'nullable|string|max:255',
            'teaser' => 'nullable|string|max:255',
            'tag' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image_base64' => 'nullable|string',
            'map_embed_url' => 'nullable|url',
            'is_featured' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
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

        $destination = Destination::create($validated);

        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destination created successfully.');
    }

    public function edit(Destination $destination): Response
    {
        return Inertia::render('Admin/Destinations/Edit', [
            'destination' => [
                'id' => $destination->id,
                'name' => $destination->name,
                'slug' => $destination->slug,
                'region' => $destination->region,
                'teaser' => $destination->teaser,
                'tag' => $destination->tag,
                'description' => $destination->description,
                'map_embed_url' => $destination->map_embed_url,
                'is_featured' => $destination->is_featured,
                'display_order' => $destination->display_order,
                'published_at' => $destination->published_at,
                'image_base64' => $destination->image_base64,
            ],
        ]);
    }

    public function update(Request $request, Destination $destination): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:destinations,slug,' . $destination->id,
            'region' => 'nullable|string|max:255',
            'teaser' => 'nullable|string|max:255',
            'tag' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image_base64' => 'nullable|string',
            'map_embed_url' => 'nullable|url',
            'is_featured' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
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

        $destination->update($validated);

        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destination updated successfully.');
    }

    public function destroy(Destination $destination): RedirectResponse
    {
        $destination->delete();

        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destination deleted successfully.');
    }
}
