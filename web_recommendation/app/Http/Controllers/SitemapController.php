<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index()
    {
        // Cache sitemap for 1 hour for better performance
        $content = Cache::remember('sitemap_xml', 3600, function () {
            $places = Place::select('id', 'nama', 'url_gambar', 'updated_at')->get();
            return view('sitemap.index', compact('places'))->render();
        });

        return response($content, 200)
            ->header('Content-Type', 'application/xml')
            ->header('X-Robots-Tag', 'noindex'); // Sitemap itself shouldn't be indexed
    }
}
