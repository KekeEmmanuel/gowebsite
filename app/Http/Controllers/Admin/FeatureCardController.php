<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeatureCard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeatureCardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/FeatureCards/Index', [
            'featureCards' => FeatureCard::orderBy('display_order')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/FeatureCards/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'icon' => 'required|string|in:travellers,pricing,support',
            'title' => 'required|string|max:255',
            'headline' => 'nullable|string|max:255',
            'copy' => 'required|string',
            'count_value' => 'nullable|integer|min:0',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        FeatureCard::create($validated);

        return redirect()->route('admin.feature-cards.index')
            ->with('success', 'Feature card created successfully.');
    }

    public function edit(FeatureCard $featureCard): Response
    {
        return Inertia::render('Admin/FeatureCards/Edit', [
            'featureCard' => $featureCard,
        ]);
    }

    public function update(Request $request, FeatureCard $featureCard): RedirectResponse
    {
        $validated = $request->validate([
            'icon' => 'required|string|in:travellers,pricing,support',
            'title' => 'required|string|max:255',
            'headline' => 'nullable|string|max:255',
            'copy' => 'required|string',
            'count_value' => 'nullable|integer|min:0',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $featureCard->update($validated);

        return redirect()->route('admin.feature-cards.index')
            ->with('success', 'Feature card updated successfully.');
    }

    public function destroy(FeatureCard $featureCard): RedirectResponse
    {
        $featureCard->delete();

        return redirect()->route('admin.feature-cards.index')
            ->with('success', 'Feature card deleted successfully.');
    }
}
