<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lodge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class LodgeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Lodges/Index', [
            'lodges' => Lodge::orderBy('display_order')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($lodge) {
                    $heroImage = $lodge->getFirstMediaUrl('hero');
                    // Convert absolute URLs to relative paths
                    if ($heroImage && strpos($heroImage, 'http') === 0) {
                        $parsed = parse_url($heroImage);
                        $heroImage = $parsed['path'] ?? $heroImage;
                    }
                    return [
                        'id' => $lodge->id,
                        'name' => $lodge->name,
                        'slug' => $lodge->slug,
                        'location' => $lodge->location,
                        'type' => $lodge->type,
                        'short_description' => $lodge->short_description,
                        'price_from' => $lodge->price_from,
                        'capacity' => $lodge->capacity,
                        'is_featured' => $lodge->is_featured,
                        'display_order' => $lodge->display_order,
                        'is_active' => $lodge->is_active,
                        'published_at' => $lodge->published_at?->toISOString(),
                        'hero_image' => $heroImage ?: null,
                    ];
                }),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Lodges/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:lodges,slug',
            'location' => 'required|string|max:255',
            'type' => 'required|in:lodge,camp',
            'mood' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:255',
            'price_from' => 'nullable|numeric|min:0',
            'capacity' => 'nullable|integer|min:1',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
            'hero_image' => 'nullable|image|max:10240',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|max:10240',
        ]);

        // Remove image files from validated data before creating the lodge
        $lodgeData = $validated;
        unset($lodgeData['hero_image']);
        unset($lodgeData['gallery_images']);

        // Generate slug if not provided
        if (empty($lodgeData['slug'])) {
            $lodgeData['slug'] = Str::slug($lodgeData['name']);
        }

        $lodge = Lodge::create($lodgeData);

        // Handle hero image
        if ($request->hasFile('hero_image')) {
            try {
                $media = $lodge->addMediaFromRequest('hero_image')
                    ->preservingOriginal()
                    ->toMediaCollection('hero');
                \Log::info('Hero image uploaded successfully', ['media_id' => $media->id]);
            } catch (\Exception $e) {
                \Log::error('Failed to upload hero image: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // Handle gallery images
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $index => $image) {
                try {
                    $media = $lodge->addMedia($image->getRealPath())
                        ->preservingOriginal()
                        ->toMediaCollection('gallery');
                    \Log::info('Gallery image uploaded successfully', ['index' => $index, 'media_id' => $media->id]);
                } catch (\Exception $e) {
                    \Log::error('Failed to upload gallery image: ' . $e->getMessage(), [
                        'index' => $index,
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
        }

        return redirect()->route('admin.lodges.index')
            ->with('success', 'Lodge created successfully.');
    }

    public function edit(Lodge $lodge): Response
    {
        $makeRelativeUrl = function ($url) {
            if (empty($url)) return null;
            if (strpos($url, 'localhost') !== false || strpos($url, 'http') === 0) {
                $parsed = parse_url($url);
                return $parsed['path'] ?? null;
            }
            return $url;
        };

        $heroImage = $lodge->getFirstMediaUrl('hero');
        $gallery = $lodge->getMedia('gallery')->map(fn ($media) => [
            'id' => $media->id,
            'url' => $makeRelativeUrl($media->getUrl()),
        ]);

        return Inertia::render('Admin/Lodges/Edit', [
            'lodge' => [
                'id' => $lodge->id,
                'name' => $lodge->name,
                'slug' => $lodge->slug,
                'location' => $lodge->location,
                'type' => $lodge->type,
                'mood' => $lodge->mood,
                'short_description' => $lodge->short_description,
                'description' => $lodge->description,
                'amenities' => $lodge->amenities ?? [],
                'price_from' => $lodge->price_from,
                'capacity' => $lodge->capacity,
                'display_order' => $lodge->display_order,
                'is_active' => $lodge->is_active,
                'is_featured' => $lodge->is_featured,
                'published_at' => $lodge->published_at?->toISOString(),
                'hero_image' => $makeRelativeUrl($heroImage),
                'gallery' => $gallery,
            ],
        ]);
    }

    public function update(Request $request, Lodge $lodge): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:lodges,slug,' . $lodge->id,
            'location' => 'required|string|max:255',
            'type' => 'required|in:lodge,camp',
            'mood' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:255',
            'price_from' => 'nullable|numeric|min:0',
            'capacity' => 'nullable|integer|min:1',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
            'hero_image' => 'nullable|image|max:10240',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|max:10240',
        ]);

        // Remove image files from validated data before updating the lodge
        $lodgeData = $validated;
        unset($lodgeData['hero_image']);
        unset($lodgeData['gallery_images']);

        $lodge->update($lodgeData);

        // Handle hero image
        if ($request->hasFile('hero_image')) {
            try {
                $lodge->clearMediaCollection('hero');
                $media = $lodge->addMediaFromRequest('hero_image')
                    ->preservingOriginal()
                    ->toMediaCollection('hero');
                \Log::info('Hero image updated successfully', ['media_id' => $media->id]);
            } catch (\Exception $e) {
                \Log::error('Failed to update hero image: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // Handle gallery images
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $index => $image) {
                try {
                    $media = $lodge->addMedia($image->getRealPath())
                        ->preservingOriginal()
                        ->toMediaCollection('gallery');
                    \Log::info('Gallery image added successfully during update', ['index' => $index, 'media_id' => $media->id]);
                } catch (\Exception $e) {
                    \Log::error('Failed to add gallery image during update: ' . $e->getMessage(), [
                        'index' => $index,
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
        }

        return redirect()->route('admin.lodges.index')
            ->with('success', 'Lodge updated successfully.');
    }

    public function destroy(Lodge $lodge): RedirectResponse
    {
        $lodge->delete();

        return redirect()->route('admin.lodges.index')
            ->with('success', 'Lodge deleted successfully.');
    }
}
