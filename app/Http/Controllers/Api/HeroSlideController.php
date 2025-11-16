<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HeroSlideResource;
use App\Models\HeroSlide;

class HeroSlideController extends Controller
{
    public function index()
    {
        $slides = HeroSlide::query()
            ->with('media')
            ->active()
            ->get();

        return HeroSlideResource::collection($slides);
    }
}
