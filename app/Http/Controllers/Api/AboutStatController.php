<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AboutStatResource;
use App\Models\AboutStat;

class AboutStatController extends Controller
{
    public function index()
    {
        $stats = AboutStat::query()
            ->active()
            ->get();

        return AboutStatResource::collection($stats);
    }
}
