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

        [$jsonData, $sourcePath] = $this->loadTourismData();

        if ($sourcePath === null) {
            $this->command->error("Data file not found");
            return;
        }

        if (empty($jsonData)) {
            $this->command->error("Failed to parse tourism data");
            return;
        }

        $this->command->info("Loading data from: {$sourcePath}");
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
                $this->command->warn("Skipped {$nama}: {$e->getMessage()}");
            }
        }

        $this->command->info("Imported {$imported} places. Content will be parsed on web display.");
    }

    /**
     * Load the 296-place dataset. CSV is preferred because the generated JSON
     * can contain JavaScript-style NaN values, which are invalid JSON in PHP.
     */
    private function loadTourismData(): array
    {
        $csvCandidates = [
            base_path('../script/bogor_tourism_data_clean.csv'),
            base_path('../flask_api/data/bogor_tourism_data.csv'),
            base_path('database/seeders/data/bogor_tourism_data.csv'),
        ];

        foreach ($csvCandidates as $candidate) {
            if (!file_exists($candidate)) {
                continue;
            }

            $data = $this->loadCsvData($candidate);

            if (!empty($data)) {
                return [$data, $candidate];
            }
        }

        $jsonCandidates = [
            base_path('database/seeders/data/bogor_tourism_data.json'),
            base_path('../flask_api/data/bogor_tourism_data.json'),
            base_path('../script/bogor_tourism_data_clean.json'),
        ];

        foreach ($jsonCandidates as $candidate) {
            if (!file_exists($candidate)) {
                continue;
            }

            $data = $this->loadJsonData($candidate);

            if (!empty($data)) {
                return [$data, $candidate];
            }
        }

        return [[], null];
    }

    private function loadCsvData(string $path): array
    {
        $handle = fopen($path, 'r');

        if ($handle === false) {
            return [];
        }

        $header = fgetcsv($handle);

        if ($header === false) {
            fclose($handle);
            return [];
        }

        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
        $header = array_map('trim', $header);
        $data = [];

        while (($row = fgetcsv($handle)) !== false) {
            if ($row === [null]) {
                continue;
            }

            $row = array_pad($row, count($header), null);
            $data[] = array_combine($header, array_slice($row, 0, count($header)));
        }

        fclose($handle);

        return $data;
    }

    private function loadJsonData(string $path): array
    {
        $contents = file_get_contents($path);

        if ($contents === false) {
            return [];
        }

        $data = json_decode($contents, true);

        if (is_array($data)) {
            return $data;
        }

        $contents = preg_replace('/:\s*NaN\b/', ': null', $contents);
        $data = json_decode($contents, true);

        return is_array($data) ? $data : [];
    }
}
