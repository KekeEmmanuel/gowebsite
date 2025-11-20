<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lodge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LodgeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Lodges/Index', [
            'lodges' => Lodge::orderBy('display_order')->get()->map(function ($lodge) {
                return [
                    'id' => $lodge->id,
                    'name' => $lodge->name,
                    'location' => $lodge->location,
                    'mood' => $lodge->mood,
                    'display_order' => $lodge->display_order,
                    'is_active' => $lodge->is_active,
                    'image_base64' => $lodge->image_base64,
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
            'location' => 'required|string|max:255',
            'mood' => 'required|string',
            'image_base64' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
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

        $lodge = Lodge::create($validated);

        return redirect()->route('admin.lodges.index')
            ->with('success', 'Lodge created successfully.');
    }

    public function edit(Lodge $lodge): Response
    {
        return Inertia::render('Admin/Lodges/Edit', [
            'lodge' => [
                'id' => $lodge->id,
                'name' => $lodge->name,
                'location' => $lodge->location,
                'mood' => $lodge->mood,
                'display_order' => $lodge->display_order,
                'is_active' => $lodge->is_active,
                'image_base64' => $lodge->image_base64,
            ],
        ]);
    }

    public function update(Request $request, Lodge $lodge): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'mood' => 'required|string',
            'image_base64' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
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

        $lodge->update($validated);

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
