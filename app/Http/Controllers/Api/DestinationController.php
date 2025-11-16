<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DestinationResource;
use App\Http\Resources\ItineraryResource;
use App\Models\Destination;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'region' => ['nullable', 'string'],
            'featured' => ['nullable', 'boolean'],
            'search' => ['nullable', 'string', 'max:120'],
            'per_page' => ['nullable', 'integer', 'between:1,50'],
        ]);

        $perPage = $validated['per_page'] ?? 20;

        $query = Destination::query()
            ->with('media')
            ->published()
            ->orderBy('name');

        if ($validated['region'] ?? false) {
            $query->where('region', $validated['region']);
        }

        if (array_key_exists('featured', $validated)) {
            $query->where('is_featured', (bool) $validated['featured']);
        }

        if ($validated['search'] ?? false) {
            $query->where(function ($searchQuery) use ($validated) {
                $term = '%'.$validated['search'].'%';
                $searchQuery
                    ->where('name', 'ILIKE', $term)
                    ->orWhere('teaser', 'ILIKE', $term)
                    ->orWhere('description', 'ILIKE', $term);
            });
        }

        return DestinationResource::collection(
            $query->paginate($perPage)->withQueryString()
        );
    }

    public function show(string $slug)
    {
        $destination = Destination::query()
            ->with([
                'media',
                'itineraries' => function ($query) {
                    $query
                        ->published()
                        ->with(['serviceType:id,name,slug', 'media'])
                        ->limit(6);
                },
            ])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return (new DestinationResource($destination))
            ->additional([
                'itineraries' => ItineraryResource::collection(
                    $destination->itineraries
                ),
            ]);
    }
}
