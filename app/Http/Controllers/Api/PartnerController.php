<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PartnerResource;
use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'featured' => ['nullable', 'boolean'],
        ]);

        $query = Partner::query()
            ->with('media')
            ->orderBy('display_order')
            ->orderBy('name');

        if (array_key_exists('featured', $validated)) {
            $query->where('is_featured', (bool) $validated['featured']);
        }

        $partners = $query->get();

        return PartnerResource::collection($partners);
    }
}
