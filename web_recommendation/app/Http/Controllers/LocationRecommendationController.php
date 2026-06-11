<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Services\PlaceLocationService;
use Illuminate\Http\Request;

class LocationRecommendationController extends Controller
{
    public function index(Request $request, PlaceLocationService $locations)
    {
        $places = Place::query()
            ->select([
                'id',
                'nama',
                'kategori',
                'deskripsi',
                'alamat',
                'latitude',
                'longitude',
                'url_gambar',
                'tags',
                'likes',
            ])
            ->orderBy('nama')
            ->get();

        $placesForClient = $locations->forClient($places);
        $requestedPlaceId = (int) ($request->query('place_id') ?: $request->query('placeId'));
        $initialPlaceId = collect($placesForClient)->contains('id', $requestedPlaceId)
            ? $requestedPlaceId
            : ($placesForClient[0]['id'] ?? null);

        return view('recommendations.location', [
            'places' => $placesForClient,
            'initialPlaceId' => $initialPlaceId,
        ]);
    }
}
