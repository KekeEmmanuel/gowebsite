<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
                    $heroImage = null;
                    try {
                        // Check if package has media before trying to access it
                        if ($package->hasMedia('hero')) {
                            $heroImage = $package->getFirstMediaUrl('hero');
                            // Convert absolute URLs to relative paths
                            if ($heroImage && strpos($heroImage, 'http') === 0) {
                                $parsed = parse_url($heroImage);
                                $heroImage = $parsed['path'] ?? $heroImage;
                            }
                        }
                    } catch (\Exception $e) {
                        // Log error but don't break the page
                        \Log::warning('Failed to get hero image for tour package: ' . $e->getMessage(), [
                            'package_id' => $package->id,
                            'error' => $e->getMessage()
                        ]);
                        $heroImage = null;
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
            'slug' => 'nullable|string|max:255|unique:tour_packages,slug',
            'short_description' => 'nullable|string|max:500',
            'description' => 'required|string',
            'price_from' => 'nullable|numeric|min:0',
            'duration_days' => 'nullable|integer|min:1',
            'max_participants' => 'nullable|integer|min:1',
            'display_order' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
            'hero_image' => 'nullable|image|max:51200',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|max:51200',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Remove file fields from validated data before creating model
        unset($validated['hero_image'], $validated['gallery_images']);

        $package = TourPackage::create($validated);

        $uploadErrors = [];

        // Handle hero image
        if ($request->hasFile('hero_image')) {
            try {
                $media = $package->addMediaFromRequest('hero_image')
                    ->preservingOriginal()
                    ->toMediaCollection('hero');
            } catch (\Exception $e) {
                \Log::error('Failed to upload hero image: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                $uploadErrors['hero_image'] = 'The hero image failed to upload.';
            }
        }

        // Handle gallery images
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $index => $image) {
                try {
                    $media = $package->addMedia($image->getRealPath())
                        ->preservingOriginal()
                        ->toMediaCollection('gallery');
                } catch (\Exception $e) {
                    \Log::error('Failed to upload gallery image: ' . $e->getMessage(), [
                        'index' => $index,
                        'trace' => $e->getTraceAsString()
                    ]);
                    $uploadErrors["gallery_images.{$index}"] = "The gallery_images.{$index} failed to upload.";
                }
            }
        }

        // If there were upload errors, return with validation errors
        if (!empty($uploadErrors)) {
            // Delete the package if uploads failed to avoid orphaned records
            try {
                $package->delete();
            } catch (\Exception $e) {
                \Log::error('Failed to delete package after upload error: ' . $e->getMessage());
            }
            
            return redirect()->back()
                ->withErrors($uploadErrors)
                ->withInput();
        }

        return redirect()->route('admin.tour-packages.index')
            ->with('success', 'Tour package created successfully.');
    }

    public function edit(TourPackage $tourPackage): Response
    {
        // Convert absolute URLs to relative paths
        $heroImage = null;
        try {
            if ($tourPackage->hasMedia('hero')) {
                $heroImage = $tourPackage->getFirstMediaUrl('hero');
                if ($heroImage && strpos($heroImage, 'http') === 0) {
                    $parsed = parse_url($heroImage);
                    $heroImage = $parsed['path'] ?? $heroImage;
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to get hero image for tour package: ' . $e->getMessage(), [
                'package_id' => $tourPackage->id,
            ]);
            $heroImage = null;
        }
        
        $gallery = [];
        try {
            if ($tourPackage->hasMedia('gallery')) {
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
                })->toArray();
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to get gallery images for tour package: ' . $e->getMessage(), [
                'package_id' => $tourPackage->id,
            ]);
            $gallery = [];
        }
        
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
            'hero_image' => 'nullable|image|max:51200',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|max:51200',
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


