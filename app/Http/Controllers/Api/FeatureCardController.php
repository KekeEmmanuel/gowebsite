<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FeatureCardResource;
use App\Models\FeatureCard;

class FeatureCardController extends Controller
{
    public function index()
    {
        $cards = FeatureCard::query()
            ->active()
            ->get();

        return FeatureCardResource::collection($cards);
    }
}
