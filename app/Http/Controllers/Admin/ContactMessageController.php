<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContactMessageController extends Controller
{
    public function index(Request $request): Response
    {
        $status = $request->get('status', 'all');
        
        $query = ContactMessage::with('serviceType')
            ->orderBy('created_at', 'desc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $messages = $query->get()->map(function ($message) {
            return [
                'id' => $message->id,
                'name' => $message->name,
                'email' => $message->email,
                'phone' => $message->phone,
                'service_type' => $message->serviceType ? [
                    'id' => $message->serviceType->id,
                    'name' => $message->serviceType->name,
                ] : null,
                'message' => $message->message,
                'status' => $message->status,
                'utm_source' => $message->utm_source,
                'utm_medium' => $message->utm_medium,
                'utm_campaign' => $message->utm_campaign,
                'resolved_at' => $message->resolved_at?->toISOString(),
                'meta' => $message->meta,
                'created_at' => $message->created_at->toISOString(),
            ];
        });

        return Inertia::render('Admin/ContactMessages/Index', [
            'messages' => $messages,
            'filters' => [
                'status' => $status,
            ],
            'stats' => [
                'total' => ContactMessage::count(),
                'new' => ContactMessage::where('status', ContactMessage::STATUS_NEW)->count(),
                'in_progress' => ContactMessage::where('status', ContactMessage::STATUS_IN_PROGRESS)->count(),
                'closed' => ContactMessage::where('status', ContactMessage::STATUS_CLOSED)->count(),
            ],
        ]);
    }

    public function show(ContactMessage $contactMessage): Response
    {
        return Inertia::render('Admin/ContactMessages/Show', [
            'message' => [
                'id' => $contactMessage->id,
                'name' => $contactMessage->name,
                'email' => $contactMessage->email,
                'phone' => $contactMessage->phone,
                'service_type' => $contactMessage->serviceType ? [
                    'id' => $contactMessage->serviceType->id,
                    'name' => $contactMessage->serviceType->name,
                ] : null,
                'message' => $contactMessage->message,
                'status' => $contactMessage->status,
                'utm_source' => $contactMessage->utm_source,
                'utm_medium' => $contactMessage->utm_medium,
                'utm_campaign' => $contactMessage->utm_campaign,
                'resolved_at' => $contactMessage->resolved_at?->toISOString(),
                'meta' => $contactMessage->meta,
                'created_at' => $contactMessage->created_at->toISOString(),
                'updated_at' => $contactMessage->updated_at->toISOString(),
            ],
        ]);
    }

    public function update(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:new,in_progress,closed'],
        ]);

        $contactMessage->update([
            'status' => $validated['status'],
        ]);

        if ($validated['status'] === ContactMessage::STATUS_CLOSED) {
            $contactMessage->markResolved();
        }

        return redirect()->route('admin.contact-messages.show', $contactMessage)
            ->with('success', 'Message status updated successfully.');
    }

    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->delete();

        return redirect()->route('admin.contact-messages.index')
            ->with('success', 'Message deleted successfully.');
    }
}
