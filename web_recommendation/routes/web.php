<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocationRecommendationController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing Page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Places
Route::get('/places', [PlaceController::class, 'index'])->name('places.index');
Route::get('/places/{id}', [PlaceController::class, 'show'])->name('places.show');

// Location based recommendations
Route::get('/recommendations', [LocationRecommendationController::class, 'index'])->name('recommendations.location');

// SEO - Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
