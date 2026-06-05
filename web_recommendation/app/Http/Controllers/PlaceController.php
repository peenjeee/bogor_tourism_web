<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Services\FlaskApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PlaceController extends Controller
{
    protected $flaskApi;

    public function __construct(FlaskApiService $flaskApi)
    {
        $this->flaskApi = $flaskApi;
    }

    /**
     * Display listing of all places
     */
    public function index(Request $request)
    {
        $perPage = 18;
        $category = $request->get('category');
        $search = $request->get('search');

        // Build query
        $query = Place::query();

        // Apply search filter using Semantic Search (IndoBERT) from Flask API
        if ($search) {
            try {
                // Call Semantic Search API
                $apiResponse = $this->flaskApi->semanticSearch($search);
                \Log::info('Semantic Search Response', ['query' => $search, 'data_count' => count($apiResponse['data'] ?? [])]);

                // Handle nested API response: {data: {data: [...], status: 'success'}}
                $innerData = $apiResponse['data'] ?? [];
                $actualData = $innerData['data'] ?? $innerData; // Support both nested and flat
                $status = $innerData['status'] ?? ($apiResponse['status'] ?? 'error');

                if ($status === 'success' && !empty($actualData) && is_array($actualData)) {
                    // Extract Names from API results (More reliable than IDs)
                    $placeNames = array_column($actualData, 'nama');

                    if (!empty($placeNames)) {
                        $orderedIds = [];

                        // Iterate through each name provided by API and find it in DB
                        foreach ($placeNames as $name) {
                            \Log::info('Processing Name', ['name' => $name, 'hex' => bin2hex($name)]);
                            // 1. Try Exact Match
                            $place = Place::where('nama', $name)->first();

                            // 2. Try Fuzzy Match (Contains)
                            if (!$place) {
                                // Try finding substring matches
                                $place = Place::where('nama', 'LIKE', "%{$name}%")->first();
                            }

                            // 3. Try Multi-Word Intersection (Best for "Garuda Waterland" -> "Garuda Park Waterland")
                            if (!$place) {
                                $words = explode(' ', $name);
                                if (count($words) > 0) {
                                    $q = Place::query();
                                    foreach ($words as $word) {
                                        // Filter out short words/symbols to avoid noise
                                        $cleanWord = trim($word);
                                        if (strlen($cleanWord) > 2) {
                                            $q->where('nama', 'LIKE', "%{$cleanWord}%");
                                        }
                                    }
                                    $place = $q->first();
                                }
                            }

                            // 4. Fallback: First Word Only (Last Resort)
                            if (!$place && !empty($words[0])) {
                                $place = Place::where('nama', 'LIKE', "%{$words[0]}%")->first();
                            }

                            if ($place) {
                                \Log::info('Match Found', ['api_name' => $name, 'db_name' => $place->nama, 'id' => $place->id]);
                                // Avoid duplicates
                                if (!in_array($place->id, $orderedIds)) {
                                    $orderedIds[] = $place->id;
                                }
                            } else {
                                \Log::warning('No Match Found', ['api_name' => $name]);
                            }
                        }

                        if (!empty($orderedIds)) {
                            \Log::info('OrderedIds Found', ['ids' => $orderedIds, 'count' => count($orderedIds)]);
                            // 3. Filter query by these sorted IDs
                            $query->whereIn('id', $orderedIds);

                            // 4. Apply sorting
                            $idsString = implode(',', $orderedIds);
                            $query->orderByRaw("FIELD(id, $idsString)");
                            \Log::info('Query SQL', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);
                        } else {
                            // Fallback to SQL LIKE search if mapping failed
                            $query->where(function ($q) use ($search) {
                                $q->where('nama', 'like', "%{$search}%")
                                    ->orWhere('deskripsi', 'like', "%{$search}%")
                                    ->orWhere('alamat', 'like', "%{$search}%")
                                    ->orWhere('tags', 'like', "%{$search}%");
                            });
                        }
                    } else {
                        // Fallback if no APIs returned
                        $query->where('id', 0);
                    }
                } else {
                    // Fallback to SQL LIKE search if API fails or returns no results
                    $query->where(function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%")
                            ->orWhere('deskripsi', 'like', "%{$search}%")
                            ->orWhere('alamat', 'like', "%{$search}%")
                            ->orWhere('tags', 'like', "%{$search}%");
                    });
                }
            } catch (\Exception $e) {
                // Fallback to SQL LIKE search on error
                \Log::error('Semantic search failed: ' . $e->getMessage());
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%")
                        ->orWhere('alamat', 'like', "%{$search}%")
                        ->orWhere('tags', 'like', "%{$search}%");
                });
            }
        }

        // Apply category filter
        if ($category) {
            $query->byCategory($category);
        }

        // Paginate
        // Note: orderByRaw needs to be careful with other orderBys. 
        // If sorting by relevance (search), we might want to skip 'orderBy likes'.
        if (!$search) {
            $query->orderBy('likes', 'desc');
        }

        $places = $query->paginate($perPage)
            ->appends(['category' => $category, 'search' => $search]);

        // Cache categories for filter dropdown (6 hours)
        $categories = Cache::remember('place_categories', 21600, function () {
            return Place::select('kategori')
                ->distinct()
                ->whereNotNull('kategori')
                ->orderBy('kategori')
                ->pluck('kategori');
        });

        return view('places.index', compact('places', 'categories', 'category', 'search'));
    }

    /**
     * Display single place with recommendations
     */
    public function show($id)
    {
        // Cache individual place for 30 minutes
        $place = Cache::remember("place_{$id}", 1800, function () use ($id) {
            return Place::findOrFail($id);
        });

        // Get recommendations from Flask API with caching
        $cacheKey = "recommendations_{$id}";
        $recommendations = [];
        $apiError = false;

        // Try to get from cache first (cache for 1 hour)
        $cachedRecommendations = Cache::get($cacheKey);

        if ($cachedRecommendations !== null) {
            $recommendations = $cachedRecommendations['data'];
            $apiError = $cachedRecommendations['error'];
        } else {
            try {
                // Use getRecommendations (N-gram) for detail page similarity
                // Pass $place->nama to ensure correct mapping despite ID differences
                $apiResponse = $this->flaskApi->getRecommendations($place->id, $place->nama);

                if ($apiResponse && $apiResponse['status'] === 'success') {
                    // Extract data from 'data' -> 'recommendations' or direct list depending on API
                    // Based on app.py: data: { place: ..., recommendations: [], ... }
                    $recData = $apiResponse['data']['recommendations'] ?? [];

                    // Get Names (More reliable than IDs across different systems)
                    $recNames = array_column($recData, 'nama');

                    // Query Laravel DB by Names for full data
                    if (!empty($recNames)) {
                        // Apply similar fuzzy matching logic here if needed, but usually exact match works better for recommendations lists
                        // We filter strictly first
                        $recommendations = Place::whereIn('nama', $recNames)->get();

                        // Sort by API order (relevance)
                        $recommendations = $recommendations->sortBy(function ($p) use ($recNames) {
                            return array_search($p->nama, $recNames);
                        })->values();
                    }
                } else {
                    $apiError = true;
                }
            } catch (\Exception $e) {
                // API call failed, try fallback
                $apiError = true;
                \Log::error('Failed to get recommendations', [
                    'place_id' => $id,
                    'place_name' => $place->nama,
                    'error' => $e->getMessage()
                ]);
            }

            // Cache the result (even if error, cache for shorter time)
            Cache::put($cacheKey, [
                'data' => $recommendations,
                'error' => $apiError
            ], $apiError ? 300 : 3600); // 5 mins on error, 1 hour on success
        }

        // Fallback: if API failed or empty, use similar category places
        if (empty($recommendations) || (is_object($recommendations) && $recommendations->isEmpty())) {
            $recommendations = Cache::remember("fallback_recommendations_{$place->kategori}_{$id}", 3600, function () use ($place, $id) {
                return Place::where('kategori', $place->kategori)
                    ->where('id', '!=', $id)
                    ->orderBy('likes', 'desc')
                    ->limit(6)
                    ->get();
            });
        }

        return view('places.show', compact('place', 'recommendations', 'apiError'));
    }
}
