<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AboutHighlightResource;
use App\Models\AboutHighlight;

class AboutHighlightController extends Controller
{
    public function index()
    {
        $highlights = AboutHighlight::query()
            ->active()
            ->get();

        return AboutHighlightResource::collection($highlights);
    }
}
