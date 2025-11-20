<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutHighlight;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AboutHighlightController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/AboutHighlights/Index', [
            'aboutHighlights' => AboutHighlight::orderBy('display_order')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/AboutHighlights/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'copy' => 'required|string',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        AboutHighlight::create($validated);

        return redirect()->route('admin.about-highlights.index')
            ->with('success', 'About highlight created successfully.');
    }

    public function edit(AboutHighlight $aboutHighlight): Response
    {
        return Inertia::render('Admin/AboutHighlights/Edit', [
            'aboutHighlight' => $aboutHighlight,
        ]);
    }

    public function update(Request $request, AboutHighlight $aboutHighlight): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'copy' => 'required|string',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $aboutHighlight->update($validated);

        return redirect()->route('admin.about-highlights.index')
            ->with('success', 'About highlight updated successfully.');
    }

    public function destroy(AboutHighlight $aboutHighlight): RedirectResponse
    {
        $aboutHighlight->delete();

        return redirect()->route('admin.about-highlights.index')
            ->with('success', 'About highlight deleted successfully.');
    }
}
