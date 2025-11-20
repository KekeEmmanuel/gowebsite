<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactChannel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContactChannelController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/ContactChannels/Index', [
            'contactChannels' => ContactChannel::orderBy('display_order')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/ContactChannels/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'value' => 'required|string|max:255',
            'detail' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        ContactChannel::create($validated);

        return redirect()->route('admin.contact-channels.index')
            ->with('success', 'Contact channel created successfully.');
    }

    public function edit(ContactChannel $contactChannel): Response
    {
        return Inertia::render('Admin/ContactChannels/Edit', [
            'contactChannel' => $contactChannel,
        ]);
    }

    public function update(Request $request, ContactChannel $contactChannel): RedirectResponse
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'value' => 'required|string|max:255',
            'detail' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $contactChannel->update($validated);

        return redirect()->route('admin.contact-channels.index')
            ->with('success', 'Contact channel updated successfully.');
    }

    public function destroy(ContactChannel $contactChannel): RedirectResponse
    {
        $contactChannel->delete();

        return redirect()->route('admin.contact-channels.index')
            ->with('success', 'Contact channel deleted successfully.');
    }
}
