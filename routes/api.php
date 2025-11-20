<?php

use App\Http\Controllers\Api\AboutHighlightController;
use App\Http\Controllers\Api\AboutStatController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\DestinationController;
use App\Http\Controllers\Api\FeatureCardController;
use App\Http\Controllers\Api\HeroSlideController;
use App\Http\Controllers\Api\ItineraryController;
use App\Http\Controllers\Api\LodgeController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\ServiceTypeController;
use App\Http\Controllers\Api\TestimonialController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:60,1'])->group(function (): void {
    Route::get('/service-types', [ServiceTypeController::class, 'index'])->name('api.service-types.index');
    Route::get('/service-types/{slug}', [ServiceTypeController::class, 'show'])->name('api.service-types.show');

    Route::get('/itineraries', [ItineraryController::class, 'index'])->name('api.itineraries.index');
    Route::get('/itineraries/{slug}', [ItineraryController::class, 'show'])->name('api.itineraries.show');

    Route::get('/destinations', [DestinationController::class, 'index'])->name('api.destinations.index');
    Route::get('/destinations/{slug}', [DestinationController::class, 'show'])->name('api.destinations.show');

    Route::get('/testimonials', [TestimonialController::class, 'index'])->name('api.testimonials.index');
    Route::get('/partners', [PartnerController::class, 'index'])->name('api.partners.index');
    Route::get('/hero-slides', [HeroSlideController::class, 'index'])->name('api.hero-slides.index');
    Route::get('/feature-cards', [FeatureCardController::class, 'index'])->name('api.feature-cards.index');
    Route::get('/about-stats', [AboutStatController::class, 'index'])->name('api.about-stats.index');
    Route::get('/about-highlights', [AboutHighlightController::class, 'index'])->name('api.about-highlights.index');
    Route::get('/lodges', [LodgeController::class, 'index'])->name('api.lodges.index');

    Route::post('/contact', [ContactController::class, 'store'])
        ->middleware('throttle:5,10')
        ->name('api.contact.store');
});
