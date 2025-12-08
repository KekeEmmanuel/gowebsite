<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LodgeResource;
use App\Models\Lodge;
use Illuminate\Http\Request;

class LodgeController extends Controller
{
    public function index(Request $request)
    {
        $query = Lodge::query()
            ->with('media')
            ->published()
            ->ordered();

        // Filter by featured
        if ($request->has('featured') && $request->boolean('featured')) {
            $query->featured();
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $lodges = $query->get();

        return LodgeResource::collection($lodges);
    }

    public function show(string $slug)
    {
        $lodge = Lodge::query()
            ->with('media')
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        return new LodgeResource($lodge);
    }
}
