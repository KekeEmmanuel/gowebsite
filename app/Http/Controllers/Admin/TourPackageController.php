<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class TourPackageController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/TourPackages/Index', [
            'packages' => TourPackage::orderBy('display_order')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($package) {
                    $heroImage = $package->getFirstMediaUrl('hero');
                    // Convert absolute URLs to relative paths
                    if ($heroImage && strpos($heroImage, 'http') === 0) {
                        $parsed = parse_url($heroImage);
                        $heroImage = $parsed['path'] ?? $heroImage;
                    }
                    return [
                        'id' => $package->id,
                        'title' => $package->title,
                        'slug' => $package->slug,
                        'short_description' => $package->short_description,
                        'price_from' => $package->price_from,
                        'duration_days' => $package->duration_days,
                        'is_featured' => $package->is_featured,
                        'display_order' => $package->display_order,
                        'published_at' => $package->published_at?->toISOString(),
                        'hero_image' => $heroImage ?: null,
                    ];
                }),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/TourPackages/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description' => 'required|string',
            'price_from' => 'nullable|numeric|min:0',
            'duration_days' => 'nullable|integer|min:1',
            'max_participants' => 'nullable|integer|min:1',
            'display_order' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
            'hero_image' => 'nullable|image|max:10240',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|max:10240',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $baseSlug = Str::slug($validated['title']);
        } else {
            $baseSlug = Str::slug($validated['slug']);
        }
        
        // Remove file fields from validated data before creating model
        unset($validated['hero_image'], $validated['gallery_images']);

        // Try to create with base slug first, catch duplicate and retry with unique slug
        $package = null;
        $slug = $baseSlug;
        
        try {
            // First attempt with base slug
            $validated['slug'] = $slug;
            $package = TourPackage::create($validated);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle unique constraint violation - retry with guaranteed unique slug
            if ($e->getCode() == '23505' || str_contains($e->getMessage(), 'duplicate key')) {
                try {
                    // Generate guaranteed unique slug using microtime and random string
                    $slug = $baseSlug . '-' . (int)(microtime(true) * 1000) . '-' . Str::random(8);
                    $validated['slug'] = $slug;
                    $package = TourPackage::create($validated);
                } catch (\Exception $retryException) {
                    \Log::error('Failed to create tour package after retry: ' . $retryException->getMessage(), [
                        'original_error' => $e->getMessage(),
                        'retry_error' => $retryException->getMessage(),
                        'slug' => $slug,
                        'trace' => $retryException->getTraceAsString()
                    ]);
                    return redirect()->back()
                        ->withErrors(['error' => 'Failed to create tour package. Please try again with a different title.'])
                        ->withInput();
                }
            } else {
                // Different database error
                \Log::error('Failed to create tour package: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'data' => $validated
                ]);
                return redirect()->back()
                    ->withErrors(['error' => 'Failed to create tour package: ' . $e->getMessage()])
                    ->withInput();
            }
        } catch (\Exception $e) {
            \Log::error('Failed to create tour package: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => $validated
            ]);
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create tour package: ' . $e->getMessage()])
                ->withInput();
        }
        
        if (!$package) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create tour package. Please try again.'])
                ->withInput();
        }

        // Handle hero image
        if ($request->hasFile('hero_image')) {
            try {
                $media = $package->addMediaFromRequest('hero_image')
                    ->preservingOriginal()
                    ->toMediaCollection('hero');
                \Log::info('Hero image uploaded successfully', ['media_id' => $media->id]);
            } catch (\Exception $e) {
                \Log::error('Failed to upload hero image: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'package_id' => $package->id
                ]);
                // Don't fail the entire request if image upload fails
            }
        }

        // Handle gallery images
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $index => $image) {
                try {
                    $media = $package->addMedia($image->getRealPath())
                        ->preservingOriginal()
                        ->toMediaCollection('gallery');
                    \Log::info('Gallery image uploaded successfully', ['index' => $index, 'media_id' => $media->id]);
                } catch (\Exception $e) {
                    \Log::error('Failed to upload gallery image: ' . $e->getMessage(), [
                        'index' => $index,
                        'trace' => $e->getTraceAsString(),
                        'package_id' => $package->id
                    ]);
                    // Continue with other images even if one fails
                }
            }
        }

        return redirect()->route('admin.tour-packages.index')
            ->with('success', 'Tour package created successfully.');
    }

    public function edit(TourPackage $tourPackage): Response
    {
        // Convert absolute URLs to relative paths
        $heroImage = $tourPackage->getFirstMediaUrl('hero');
        if ($heroImage && strpos($heroImage, 'http') === 0) {
            $parsed = parse_url($heroImage);
            $heroImage = $parsed['path'] ?? $heroImage;
        }
        
        $gallery = $tourPackage->getMedia('gallery')->map(function ($media) {
            $url = $media->getUrl();
            // Convert absolute URLs to relative paths
            if ($url && strpos($url, 'http') === 0) {
                $parsed = parse_url($url);
                $url = $parsed['path'] ?? $url;
            }
            return [
                'id' => $media->id,
                'url' => $url,
            ];
        });
        
        return Inertia::render('Admin/TourPackages/Edit', [
            'package' => [
                'id' => $tourPackage->id,
                'title' => $tourPackage->title,
                'slug' => $tourPackage->slug,
                'short_description' => $tourPackage->short_description,
                'description' => $tourPackage->description,
                'price_from' => $tourPackage->price_from,
                'duration_days' => $tourPackage->duration_days,
                'max_participants' => $tourPackage->max_participants,
                'display_order' => $tourPackage->display_order,
                'is_featured' => $tourPackage->is_featured,
                'published_at' => $tourPackage->published_at?->toISOString(),
                'hero_image' => $heroImage ?: null,
                'gallery' => $gallery,
            ],
        ]);
    }

    public function update(Request $request, TourPackage $tourPackage): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tour_packages,slug,' . $tourPackage->id,
            'short_description' => 'nullable|string|max:500',
            'description' => 'required|string',
            'price_from' => 'nullable|numeric|min:0',
            'duration_days' => 'nullable|integer|min:1',
            'max_participants' => 'nullable|integer|min:1',
            'display_order' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
            'hero_image' => 'nullable|image|max:10240',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|max:10240',
        ]);

        // Remove file fields from validated data before updating model
        unset($validated['hero_image'], $validated['gallery_images']);
        
        $tourPackage->update($validated);

        // Handle hero image
        if ($request->hasFile('hero_image')) {
            try {
                $tourPackage->clearMediaCollection('hero');
                $tourPackage->addMediaFromRequest('hero_image')
                    ->preservingOriginal()
                    ->toMediaCollection('hero');
            } catch (\Exception $e) {
                \Log::error('Failed to upload hero image: ' . $e->getMessage());
            }
        }

        // Handle gallery images
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                try {
                    $tourPackage->addMedia($image->getRealPath())
                        ->preservingOriginal()
                        ->toMediaCollection('gallery');
                } catch (\Exception $e) {
                    \Log::error('Failed to upload gallery image: ' . $e->getMessage());
                }
            }
        }

        return redirect()->route('admin.tour-packages.index')
            ->with('success', 'Tour package updated successfully.');
    }

    public function destroy(TourPackage $tourPackage): RedirectResponse
    {
        $tourPackage->delete();

        return redirect()->route('admin.tour-packages.index')
            ->with('success', 'Tour package deleted successfully.');
    }
}
