<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactChannelResource;
use App\Models\ContactChannel;

class ContactChannelController extends Controller
{
    public function index()
    {
        $channels = ContactChannel::query()
            ->active()
            ->get();

        return ContactChannelResource::collection($channels);
    }
}
