<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TourPackageResource;
use App\Models\TourPackage;
use Illuminate\Http\Request;

class TourPackageController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'featured' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'between:1,50'],
        ]);

        $perPage = $validated['per_page'] ?? 12;

        $query = TourPackage::query()
            ->with(['media'])
            ->published()
            ->ordered();

        if ($validated['featured'] ?? false) {
            $query->featured();
        }

        return TourPackageResource::collection(
            $query->paginate($perPage)->withQueryString()
        );
    }

    public function show(string $slug)
    {
        $package = TourPackage::query()
            ->with(['media'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        return new TourPackageResource($package);
    }
}
