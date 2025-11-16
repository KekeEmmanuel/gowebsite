<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceTypeResource;
use App\Models\ServiceType;

class ServiceTypeController extends Controller
{
    public function index()
    {
        $serviceTypes = ServiceType::query()
            ->active()
            ->ordered()
            ->get();

        return ServiceTypeResource::collection($serviceTypes);
    }

    public function show(string $slug)
    {
        $serviceType = ServiceType::query()
            ->where('slug', $slug)
            ->firstOrFail();

        return new ServiceTypeResource($serviceType);
    }
}
