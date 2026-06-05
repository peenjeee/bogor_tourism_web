<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlaskApiService
{
    protected $baseUrl;
    protected $timeout;

    public function __construct()
    {
        $this->baseUrl = env('FLASK_API_URL', 'http://localhost:5000');
        $this->timeout = 30; // seconds
    }

    /**
     * Get all places from Flask API
     *
     * @param int $limit
     * @param int $offset
     * @param string|null $category
     * @return array|null
     */
    public function getAllPlaces($limit = 20, $offset = 0, $category = null)
    {
        try {
            $params = [
                'limit' => $limit,
                'offset' => $offset,
            ];

            if ($category) {
                $params['category'] = $category;
            }

            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/api/places", $params);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Flask API Error: getAllPlaces', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Flask API Exception: getAllPlaces', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get single place details from Flask API
     *
     * @param int $placeId
     * @return array|null
     */
    public function getPlace($placeId)
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/api/places/{$placeId}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Flask API Error: getPlace', [
                'place_id' => $placeId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Flask API Exception: getPlace', [
                'place_id' => $placeId,
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get recommendations for a place
     *
     * @param int $placeId
     * @param string|null $placeName
     * @param int $topN
     * @return array|null
     */
    public function getRecommendations($placeId, $placeName = null, $topN = 10)
    {
        try {
            $payload = [
                'top_n' => $topN
            ];

            $payload['place_id'] = $placeId;

            // Use place_name if provided for accurate matching
            if ($placeName) {
                $payload['place_name'] = $placeName;
            }

            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/api/recommendations", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Flask API Error: getRecommendations', [
                'place_id' => $placeId,
                'place_name' => $placeName,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Flask API Exception: getRecommendations', [
                'place_id' => $placeId,
                'place_name' => $placeName,
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Check if Flask API is available
     *
     * @return bool
     */
    public function isAvailable()
    {
        try {
            $response = Http::timeout(5)->get($this->baseUrl);
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Semantic search using IndoBERT similarity
     *
     * @param string $query
     * @param int|null $limit
     * @return array|null
     */
    public function semanticSearch($query, $limit = null)
    {
        try {
            $params = ['q' => $query];
            if ($limit !== null) {
                $params['limit'] = $limit;
            }

            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/api/search", $params);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Flask API Error: semanticSearch', [
                'query' => $query,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Flask API Exception: semanticSearch', [
                'query' => $query,
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }
}
