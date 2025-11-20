<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutHighlight;
use App\Models\AboutStat;
use App\Models\ContactChannel;
use App\Models\ContactMessage;
use App\Models\Destination;
use App\Models\FeatureCard;
use App\Models\HeroSlide;
use App\Models\Itinerary;
use App\Models\Lodge;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'heroSlides' => HeroSlide::count(),
                'featureCards' => FeatureCard::count(),
                'itineraries' => Itinerary::count(),
                'destinations' => Destination::count(),
                'lodges' => Lodge::count(),
                'aboutStats' => AboutStat::count(),
                'aboutHighlights' => AboutHighlight::count(),
                'contactChannels' => ContactChannel::count(),
                'contactMessages' => ContactMessage::count(),
                'activeHeroSlides' => HeroSlide::where('is_active', true)->count(),
                'activeItineraries' => Itinerary::whereNotNull('published_at')->count(),
                'featuredDestinations' => Destination::where('is_featured', true)->count(),
            ],
            'recentMessages' => ContactMessage::latest()->limit(5)->get(),
        ]);
    }
}

