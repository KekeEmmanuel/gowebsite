<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HeroSlideController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/HeroSlides/Index', [
            'heroSlides' => HeroSlide::orderBy('position')
                ->get()
                ->map(function ($slide) {
                    return [
                        'id' => $slide->id,
                        'title' => $slide->title,
                        'label' => $slide->label,
                        'subtitle' => $slide->subtitle,
                        'description' => $slide->description,
                        'cta_label' => $slide->cta_label,
                        'cta_url' => $slide->cta_url,
                        'position' => $slide->position,
                        'is_active' => $slide->is_active,
                        'image_base64' => $slide->image_base64,
                    ];
                }),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/HeroSlides/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'label' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image_base64' => 'nullable|string',
            'cta_label' => 'required|string|max:255',
            'cta_url' => 'required|string|max:255',
            'position' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // If image_base64 is provided, store it directly
        // Otherwise, handle file upload and convert to base64
        if ($request->has('image_base64') && $request->image_base64) {
            // Already base64, store as is
            $validated['image_base64'] = $request->image_base64;
        } elseif ($request->hasFile('image')) {
            // Convert uploaded file to base64
            $file = $request->file('image');
            $imageData = file_get_contents($file->getRealPath());
            $base64 = base64_encode($imageData);
            $mimeType = $file->getMimeType();
            $validated['image_base64'] = 'data:' . $mimeType . ';base64,' . $base64;
        }

        $slide = HeroSlide::create($validated);

        return redirect()->route('admin.hero-slides.index')
            ->with('success', 'Hero slide created successfully.');
    }

    public function edit(HeroSlide $heroSlide): Response
    {
        return Inertia::render('Admin/HeroSlides/Edit', [
            'heroSlide' => [
                'id' => $heroSlide->id,
                'title' => $heroSlide->title,
                'label' => $heroSlide->label,
                'subtitle' => $heroSlide->subtitle,
                'description' => $heroSlide->description,
                'cta_label' => $heroSlide->cta_label,
                'cta_url' => $heroSlide->cta_url,
                'position' => $heroSlide->position,
                'is_active' => $heroSlide->is_active,
                'image_base64' => $heroSlide->image_base64,
            ],
        ]);
    }

    public function update(Request $request, HeroSlide $heroSlide): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'label' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image_base64' => 'nullable|string',
            'cta_label' => 'required|string|max:255',
            'cta_url' => 'required|string|max:255',
            'position' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Handle image_base64
        if ($request->has('image_base64') && $request->image_base64) {
            // Already base64, store as is
            $validated['image_base64'] = $request->image_base64;
        } elseif ($request->hasFile('image')) {
            // Convert uploaded file to base64
            $file = $request->file('image');
            $imageData = file_get_contents($file->getRealPath());
            $base64 = base64_encode($imageData);
            $mimeType = $file->getMimeType();
            $validated['image_base64'] = 'data:' . $mimeType . ';base64,' . $base64;
        } elseif (!$request->has('image_base64') || !$request->image_base64) {
            // Keep existing image if no new one provided
            unset($validated['image_base64']);
        }

        $heroSlide->update($validated);

        return redirect()->route('admin.hero-slides.index')
            ->with('success', 'Hero slide updated successfully.');
    }

    public function destroy(HeroSlide $heroSlide): RedirectResponse
    {
        $heroSlide->delete();

        return redirect()->route('admin.hero-slides.index')
            ->with('success', 'Hero slide deleted successfully.');
    }
}
