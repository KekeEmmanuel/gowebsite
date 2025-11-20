<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LodgeResource;
use App\Models\Lodge;

class LodgeController extends Controller
{
    public function index()
    {
        $lodges = Lodge::query()
            ->active()
            ->get();

        return LodgeResource::collection($lodges);
    }
}
