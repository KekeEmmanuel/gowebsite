<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SafariPackageResource;
use App\Models\SafariPackage;
use Illuminate\Http\Request;

class SafariPackageController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'featured' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'between:1,50'],
        ]);

        $perPage = $validated['per_page'] ?? 12;

        $query = SafariPackage::query()
            ->with(['media'])
            ->published()
            ->ordered();

        if ($validated['featured'] ?? false) {
            $query->featured();
        }

        return SafariPackageResource::collection(
            $query->paginate($perPage)->withQueryString()
        );
    }

    public function show(string $slug)
    {
        $package = SafariPackage::query()
            ->with(['media'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        return new SafariPackageResource($package);
    }
}
