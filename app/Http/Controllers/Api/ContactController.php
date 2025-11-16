<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    public function store(ContactMessageRequest $request): JsonResponse
    {
        $data = $request->validated();

        $message = ContactMessage::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'service_type_id' => $data['service_type_id'] ?? null,
            'message' => $data['message'],
            'status' => ContactMessage::STATUS_NEW,
            'utm_source' => $data['utm_source'] ?? null,
            'utm_medium' => $data['utm_medium'] ?? null,
            'utm_campaign' => $data['utm_campaign'] ?? null,
            'meta' => [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
        ]);

        // @todo Dispatch notification/email job once mail channel is configured.

        return response()->json([
            'status' => 'received',
            'message_id' => $message->id,
        ], 202);
    }
}
