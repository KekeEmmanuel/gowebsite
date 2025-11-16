<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TestimonialResource;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'featured' => ['nullable', 'boolean'],
            'limit' => ['nullable', 'integer', 'between:1,24'],
        ]);

        $limit = $validated['limit'] ?? 12;

        $query = Testimonial::query()
            ->with('media')
            ->published()
            ->orderByDesc('published_at');

        if (array_key_exists('featured', $validated)) {
            $query->where('is_featured', (bool) $validated['featured']);
        }

        $testimonials = $query->limit($limit)->get();

        return TestimonialResource::collection($testimonials);
    }
}
