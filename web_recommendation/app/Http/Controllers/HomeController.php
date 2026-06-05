<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Display landing page with featured destinations
     */
    public function index()
    {
        // Cache featured places for 1 hour (3600 seconds)
        $featuredPlaces = Cache::remember('featured_places', 3600, function () {
            return Place::popular(6)->get();
        });

        // Cache categories for 6 hours (21600 seconds)
        $categories = Cache::remember('category_stats', 21600, function () {
            return Place::select('kategori')
                ->selectRaw('count(*) as count')
                ->groupBy('kategori')
                ->get();
        });

        $places_count = Cache::remember('places_count', 21600, function () {
            return Place::count();
        });

        return view('landing', compact('featuredPlaces', 'categories', 'places_count'));
    }
}
