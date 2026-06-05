<?php

use App\Http\Controllers\HomeController;
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

// SEO - Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
