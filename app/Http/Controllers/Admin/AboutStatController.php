<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutStat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AboutStatController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/AboutStats/Index', [
            'aboutStats' => AboutStat::orderBy('display_order')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/AboutStats/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'value' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        AboutStat::create($validated);

        return redirect()->route('admin.about-stats.index')
            ->with('success', 'About stat created successfully.');
    }

    public function edit(AboutStat $aboutStat): Response
    {
        return Inertia::render('Admin/AboutStats/Edit', [
            'aboutStat' => $aboutStat,
        ]);
    }

    public function update(Request $request, AboutStat $aboutStat): RedirectResponse
    {
        $validated = $request->validate([
            'value' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $aboutStat->update($validated);

        return redirect()->route('admin.about-stats.index')
            ->with('success', 'About stat updated successfully.');
    }

    public function destroy(AboutStat $aboutStat): RedirectResponse
    {
        $aboutStat->delete();

        return redirect()->route('admin.about-stats.index')
            ->with('success', 'About stat deleted successfully.');
    }
}
