<?php

namespace App\Services;

use App\Models\Place;
use Illuminate\Support\Collection;

class PlaceLocationService
{
    private const EARTH_RADIUS_KM = 6371.0088;

    private const BOGOR_CENTER = [106.806, -6.596];

    private const AREA_ANCHORS = [
        ['pattern' => '/sentul|babakan madang|bojong koneng|gunung pancar/i', 'center' => [106.881, -6.565], 'spread' => 0.045],
        ['pattern' => '/ciawi|gadog|megamen?dung|cilember|cibedug/i', 'center' => [106.869, -6.665], 'spread' => 0.04],
        ['pattern' => '/cisarua|puncak|tugu|cibeureum|cibodas/i', 'center' => [106.945, -6.704], 'spread' => 0.05],
        ['pattern' => '/dramaga|ipb|situ burung|cifor/i', 'center' => [106.733, -6.575], 'spread' => 0.035],
        ['pattern' => '/cibinong|citeureup|bojonggede|tajur halang/i', 'center' => [106.829, -6.485], 'spread' => 0.04],
        ['pattern' => '/parung|ciseeng|gunung sindur|rumpin|kemang/i', 'center' => [106.682, -6.421], 'spread' => 0.055],
        ['pattern' => '/ciampea|cibungbulang|pamijahan|gunung salak|halimun/i', 'center' => [106.694, -6.673], 'spread' => 0.055],
        ['pattern' => '/leuwiliang|leuwisadeng|nanggung|jasinga|tenjo/i', 'center' => [106.562, -6.586], 'spread' => 0.065],
        ['pattern' => '/ciomas|tamansari|cijeruk|cigombong|caringin/i', 'center' => [106.759, -6.687], 'spread' => 0.045],
        ['pattern' => '/jonggol|cileungsi|gunung putri|klapanunggal/i', 'center' => [106.997, -6.451], 'spread' => 0.06],
        ['pattern' => '/sukamakmur|curug ciherang|villa khayangan/i', 'center' => [107.047, -6.642], 'spread' => 0.05],
        ['pattern' => '/cariu|tanjungsari/i', 'center' => [107.157, -6.553], 'spread' => 0.065],
        ['pattern' => '/kebun raya|suryakencana|botani|pajajaran|bogor kota|kota bogor/i', 'center' => self::BOGOR_CENTER, 'spread' => 0.03],
        ['pattern' => '/kuliner|belanja|cafe|coffee|resto|restaurant|bakery|mall/i', 'center' => [106.807, -6.589], 'spread' => 0.04],
        ['pattern' => '/curug|air terjun|leuwi/i', 'center' => [106.731, -6.704], 'spread' => 0.06],
    ];

    public function resolve(Place $place): array
    {
        if ($this->hasValidCoordinates($place->latitude, $place->longitude)) {
            return [
                'latitude' => (float) $place->latitude,
                'longitude' => (float) $place->longitude,
                'source' => 'database',
            ];
        }

        $anchor = $this->chooseAnchor($place);
        $seed = $this->hashText("{$place->id}-{$place->nama}");
        $offset = $this->offsetFromHash($seed, $anchor['spread']);

        return [
            'latitude' => $anchor['center'][1] + $offset['latitude'],
            'longitude' => $anchor['center'][0] + $offset['longitude'],
            'source' => 'estimasi area',
        ];
    }

    public function forClient(Collection $places): array
    {
        return $places->map(function (Place $place) {
            $coordinate = $this->resolve($place);

            return [
                'id' => $place->id,
                'nama' => $place->nama,
                'kategori' => $place->kategori,
                'alamat' => $this->displayAddress($place),
                'deskripsi' => $place->short_description,
                'url_gambar' => $place->url_gambar,
                'likes' => (int) ($place->likes ?? 0),
                'latitude' => $coordinate['latitude'],
                'longitude' => $coordinate['longitude'],
                'coordinate_source' => $coordinate['source'],
            ];
        })->values()->all();
    }

    public function distanceKm(array $start, array $end): float
    {
        $startLat = deg2rad((float) $start['latitude']);
        $endLat = deg2rad((float) $end['latitude']);
        $deltaLat = deg2rad((float) $end['latitude'] - (float) $start['latitude']);
        $deltaLng = deg2rad((float) $end['longitude'] - (float) $start['longitude']);

        $a = sin($deltaLat / 2) ** 2
            + cos($startLat) * cos($endLat) * sin($deltaLng / 2) ** 2;

        return self::EARTH_RADIUS_KM * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function hasValidCoordinates($latitude, $longitude): bool
    {
        if ($latitude === null || $longitude === null) {
            return false;
        }

        $latitude = (float) $latitude;
        $longitude = (float) $longitude;

        return $latitude >= -7.3
            && $latitude <= -5.8
            && $longitude >= 106.2
            && $longitude <= 107.5;
    }

    private function searchableText(Place $place): string
    {
        return implode(' ', array_filter([
            $place->nama,
            $place->kategori,
            $place->label,
            $place->alamat,
            $place->tags,
            $place->deskripsi,
        ]));
    }

    private function displayAddress(Place $place): string
    {
        if ($place->alamat) {
            return $place->alamat;
        }

        if (preg_match('/(?:alamat|lokasi)\s*\n([^\n]+)/i', (string) $place->deskripsi, $match)) {
            return trim($match[1]);
        }

        return 'Bogor, Jawa Barat';
    }

    private function chooseAnchor(Place $place): array
    {
        $haystack = $this->searchableText($place);

        foreach (self::AREA_ANCHORS as $anchor) {
            if (preg_match($anchor['pattern'], $haystack)) {
                return $anchor;
            }
        }

        return [
            'pattern' => '/bogor/i',
            'center' => self::BOGOR_CENTER,
            'spread' => 0.08,
        ];
    }

    private function hashText(string $value): int
    {
        $hash = 0;
        $length = strlen($value);

        for ($index = 0; $index < $length; $index++) {
            $hash = (($hash * 31) + ord($value[$index])) & 0xFFFFFFFF;
        }

        return $hash;
    }

    private function offsetFromHash(int $seed, float $spread): array
    {
        $longitudeUnit = (($seed % 2000) / 1999 - 0.5) * 2;
        $latitudeUnit = (((int) floor($seed / 2000) % 2000) / 1999 - 0.5) * 2;

        return [
            'longitude' => $longitudeUnit * $spread,
            'latitude' => $latitudeUnit * $spread,
        ];
    }
}
