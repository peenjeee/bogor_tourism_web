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
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('places')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Load scraped data
        $jsonPath = base_path('../flask_api/data/bogor_tourism_data.json');

        if (!file_exists($jsonPath)) {
            $this->command->error("Data file not found: {$jsonPath}");
            return;
        }

        $this->command->info("Loading data from: {$jsonPath}");

        $jsonData = json_decode(file_get_contents($jsonPath), true);

        if (!$jsonData) {
            $this->command->error("Failed to parse JSON data");
            return;
        }

        $this->command->info("Found " . count($jsonData) . " destinations");

        $imported = 0;
        $seenNames = [];

        foreach ($jsonData as $item) {
            $nama = trim($item['nama'] ?? '');

            // Skip empty names or duplicates
            if (empty($nama) || isset($seenNames[$nama])) {
                continue;
            }
            $seenNames[$nama] = true;

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
                    'url' => $item['url'] ?? null,
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

