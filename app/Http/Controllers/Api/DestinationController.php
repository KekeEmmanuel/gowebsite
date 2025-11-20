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
            'featured' => ['nullable'],
            'search' => ['nullable', 'string', 'max:120'],
            'per_page' => ['nullable', 'integer', 'between:1,50'],
        ]);

        $perPage = $validated['per_page'] ?? 20;

        $query = Destination::query()
            ->published()
            ->orderBy('display_order')
            ->orderBy('name');

        if ($validated['region'] ?? false) {
            $query->where('region', $validated['region']);
        }

        // Handle featured parameter - can be string 'true'/'false' or boolean
        if ($request->has('featured')) {
            $featuredValue = $request->input('featured');
            $isFeatured = filter_var($featuredValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($isFeatured !== null) {
                $query->where('is_featured', $isFeatured);
            }
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
                'itineraries' => function ($query) {
                    $query
                        ->published()
                        ->with(['serviceType:id,name,slug'])
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
