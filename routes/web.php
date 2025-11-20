<?php

use App\Http\Controllers\Admin\AboutHighlightController;
use App\Http\Controllers\Admin\AboutStatController;
use App\Http\Controllers\Admin\ContactChannelController;
use App\Http\Controllers\Admin\ContactQuickFactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DestinationController;
use App\Http\Controllers\Admin\FeatureCardController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\ItineraryController;
use App\Http\Controllers\Admin\LodgeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return view('marketing');
});

// Marketing frontend routes (handled by Vue Router)
Route::get('/itineraries/{slug}', function () {
    return view('marketing');
})->where('slug', '[a-z0-9-]+');

Route::prefix('admin')->middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Content Management Routes
    Route::resource('hero-slides', HeroSlideController::class)->names('admin.hero-slides');
    Route::resource('feature-cards', FeatureCardController::class)->names('admin.feature-cards');
    Route::resource('about-stats', AboutStatController::class)->names('admin.about-stats');
    Route::resource('about-highlights', AboutHighlightController::class)->names('admin.about-highlights');
    Route::resource('itineraries', ItineraryController::class)->names('admin.itineraries');
    Route::resource('destinations', DestinationController::class)->names('admin.destinations');
    Route::resource('lodges', LodgeController::class)->names('admin.lodges');
    Route::resource('contact-channels', ContactChannelController::class)->names('admin.contact-channels');
    Route::resource('contact-quick-facts', ContactQuickFactController::class)->names('admin.contact-quick-facts');
});

require __DIR__.'/auth.php';
