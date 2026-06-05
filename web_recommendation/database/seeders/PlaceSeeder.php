<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Place;
use Illuminate\Support\Facades\DB;

class PlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data structure: nama, isi (full content stored as deskripsi), likes
     * Web side parses deskripsi to show Fasilitas, Harga, Jam, etc.
     */
    public function run(): void
    {
        // Clear existing data and reset IDs from 1.
        DB::table('places')->truncate();

        // Load scraped data
        $jsonPath = null;
        $jsonCandidates = [
            base_path('database/seeders/data/bogor_tourism_data.json'),
            base_path('../flask_api/data/bogor_tourism_data.json'),
            base_path('../script/bogor_tourism_data_clean.json'),
        ];

        foreach ($jsonCandidates as $candidate) {
            if (file_exists($candidate)) {
                $jsonPath = $candidate;
                break;
            }
        }

        if ($jsonPath === null) {
            $this->command->error("Data file not found");
            return;
        }

        $this->command->info("Loading data from: {$jsonPath}");

        $jsonData = json_decode(file_get_contents($jsonPath), true);

        if (!$jsonData) {
            $this->command->error("Failed to parse JSON data");
            return;
        }

        $this->command->info("Found " . count($jsonData) . " scraped destinations");

        $cleanData = [];
        $seenUrls = [];

        foreach ($jsonData as $item) {
            $nama = trim($item['nama'] ?? '');
            $url = trim($item['url'] ?? '');

            if (empty($nama) || empty($url) || isset($seenUrls[$url])) {
                continue;
            }

            $seenUrls[$url] = true;
            $nameKey = trim(preg_replace('/[^a-z0-9]+/', ' ', strtolower($nama)));
            $likes = (int) ($item['likes'] ?? 0);

            if (!isset($cleanData[$nameKey]) || $likes > (int) ($cleanData[$nameKey]['likes'] ?? 0)) {
                $cleanData[$nameKey] = $item;
            }
        }

        $jsonData = array_values($cleanData);
        $this->command->info("Using " . count($jsonData) . " unique destinations");

        $imported = 0;

        foreach ($jsonData as $item) {
            $nama = trim($item['nama'] ?? '');
            $url = trim($item['url'] ?? '');

            // The project dataset keeps 296 places: unique URL, then unique normalized name.
            if (empty($nama) || empty($url)) {
                continue;
            }

            try {
                // Map kategori to main 7 categories
                $kategori = $item['kategori'] ?? '';
                $kategoriMap = [
                    'arena' => 'Arena',
                    'olahraga' => 'Olahraga',
                    'alam' => 'Alam',
                    'senibudaya' => 'Seni Budaya',
                    'seni budaya' => 'Seni Budaya',
                    'belanja' => 'Belanja',
                    'kuliner' => 'Kuliner',
                    'rekreasi' => 'Rekreasi'
                ];
                $kategoriLower = strtolower($kategori);
                $kategori = $kategoriMap[$kategoriLower] ?? $kategori;

                // Get full content - prefer 'isi' field, fallback to 'deskripsi'
                $fullContent = $item['isi'] ?? $item['deskripsi'] ?? '';

                Place::create([
                    'nama' => $nama,
                    'kategori' => $kategori,
                    'label' => $kategori,
                    // Store full content as deskripsi - web side will parse it
                    'deskripsi' => $fullContent,
                    // All other fields null - will be parsed from deskripsi on web display
                    'alamat' => null,
                    'fasilitas' => null,
                    'harga_tiket' => null,
                    'jam_operasional' => null,
                    'telepon' => null,
                    'url' => $url,
                    'url_gambar' => $item['url_gambar'] ?? null,
                    'tags' => null,
                    'likes' => (int) ($item['likes'] ?? 0),
                    'author' => null,
                    'sumber' => null,
                ]);
                $imported++;
            } catch (\Exception $e) {
                // Skip silently
            }
        }

        $this->command->info("✅ Imported {$imported} places! Content will be parsed on web display.");
    }
}
