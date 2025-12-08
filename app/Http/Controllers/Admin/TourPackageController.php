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
                        'hero_image' => $package->getFirstMediaUrl('hero'),
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
            'hero_image' => 'nullable|image|max:10240',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|max:10240',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $package = TourPackage::create($validated);

        // Handle hero image
        if ($request->hasFile('hero_image')) {
            $package->addMediaFromRequest('hero_image')
                ->toMediaCollection('hero');
        }

        // Handle gallery images
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $package->addMediaFromRequest('gallery_images')
                    ->toMediaCollection('gallery');
            }
        }

        return redirect()->route('admin.tour-packages.index')
            ->with('success', 'Tour package created successfully.');
    }

    public function edit(TourPackage $tourPackage): Response
    {
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
                'hero_image' => $tourPackage->getFirstMediaUrl('hero'),
                'gallery' => $tourPackage->getMedia('gallery')->map(fn ($media) => [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                ]),
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

        $tourPackage->update($validated);

        // Handle hero image
        if ($request->hasFile('hero_image')) {
            $tourPackage->clearMediaCollection('hero');
            $tourPackage->addMediaFromRequest('hero_image')
                ->toMediaCollection('hero');
        }

        // Handle gallery images
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $tourPackage->addMedia($image->getRealPath())
                    ->toMediaCollection('gallery');
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
