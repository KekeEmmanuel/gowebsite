<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItineraryResource;
use App\Models\Itinerary;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItineraryController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'service' => ['nullable', 'integer', Rule::exists('service_types', 'id')],
            'destination' => ['nullable', 'integer', Rule::exists('destinations', 'id')],
            'duration_min' => ['nullable', 'integer', 'min:1'],
            'duration_max' => ['nullable', 'integer', 'min:1'],
            'price_min' => ['nullable', 'numeric', 'min:0'],
            'price_max' => ['nullable', 'numeric', 'min:0'],
            'region' => ['nullable', 'string'],
            'difficulty' => ['nullable', 'string'],
            'sort' => ['nullable', Rule::in(['price', 'duration', 'popular', 'recent'])],
            'order' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['nullable', 'integer', 'between:1,50'],
        ]);

        $perPage = $validated['per_page'] ?? 12;
        $sort = $validated['sort'] ?? 'recent';
        $order = $validated['order'] ?? 'desc';

        $query = Itinerary::query()
            ->with([
                'serviceType:id,name,slug',
                'destination:id,name,slug,region',
            ])
            ->published()
            ->orderBy('is_featured', 'desc') // Featured first
            ->orderBy('display_order')
            ->orderBy('published_at', 'desc');

        if ($validated['service'] ?? false) {
            $query->where('service_type_id', $validated['service']);
        }

        if ($validated['destination'] ?? false) {
            $query->where('destination_id', $validated['destination']);
        }

        if ($validated['duration_min'] ?? false) {
            $query->where('duration_days', '>=', $validated['duration_min']);
        }

        if ($validated['duration_max'] ?? false) {
            $query->where('duration_days', '<=', $validated['duration_max']);
        }

        if ($validated['price_min'] ?? false) {
            $query->where('price_from', '>=', $validated['price_min']);
        }

        if ($validated['price_max'] ?? false) {
            $query->where('price_from', '<=', $validated['price_max']);
        }

        if ($validated['region'] ?? false) {
            $query->whereHas('destination', function ($subQuery) use ($validated) {
                $subQuery->where('region', $validated['region']);
            });
        }

        if ($validated['difficulty'] ?? false) {
            $query->where('difficulty', $validated['difficulty']);
        }

        $query->when(
            $sort === 'price',
            fn ($q) => $q->orderBy('price_from', $order),
            fn ($q) => $q->when(
                $sort === 'duration',
                fn ($q) => $q->orderBy('duration_days', $order),
                fn ($q) => $q->when(
                    $sort === 'popular',
                    fn ($q) => $q->orderBy('display_order')->orderBy('published_at', 'desc'),
                    fn ($q) => $q->orderBy('published_at', 'desc')
                )
            )
        );

        return ItineraryResource::collection(
            $query->paginate($perPage)->withQueryString()
        );
    }

    public function show(string $slug)
    {
        $itinerary = Itinerary::query()
            ->with([
                'serviceType:id,name,slug',
                'destination:id,name,slug,region,map_embed_url',
            ])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        return new ItineraryResource($itinerary);
    }
}
