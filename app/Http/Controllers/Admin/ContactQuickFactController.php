<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactQuickFact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContactQuickFactController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/ContactQuickFacts/Index', [
            'contactQuickFacts' => ContactQuickFact::orderBy('display_order')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/ContactQuickFacts/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fact' => 'required|string',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        ContactQuickFact::create($validated);

        return redirect()->route('admin.contact-quick-facts.index')
            ->with('success', 'Contact quick fact created successfully.');
    }

    public function edit(ContactQuickFact $contactQuickFact): Response
    {
        return Inertia::render('Admin/ContactQuickFacts/Edit', [
            'contactQuickFact' => $contactQuickFact,
        ]);
    }

    public function update(Request $request, ContactQuickFact $contactQuickFact): RedirectResponse
    {
        $validated = $request->validate([
            'fact' => 'required|string',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $contactQuickFact->update($validated);

        return redirect()->route('admin.contact-quick-facts.index')
            ->with('success', 'Contact quick fact updated successfully.');
    }

    public function destroy(ContactQuickFact $contactQuickFact): RedirectResponse
    {
        $contactQuickFact->delete();

        return redirect()->route('admin.contact-quick-facts.index')
            ->with('success', 'Contact quick fact deleted successfully.');
    }
}
