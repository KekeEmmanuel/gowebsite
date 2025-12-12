<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactQuickFactResource;
use App\Models\ContactQuickFact;

class ContactQuickFactController extends Controller
{
    public function index()
    {
        $facts = ContactQuickFact::query()
            ->active()
            ->get();

        return ContactQuickFactResource::collection($facts);
    }
}
