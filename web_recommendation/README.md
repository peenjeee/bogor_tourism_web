# BogorXplore Web

Laravel 12 application untuk menampilkan destinasi wisata Bogor, pencarian,
detail tempat, dan rekomendasi wisata dari Flask ML API.

## Fitur

- Landing page dengan destinasi populer.
- Daftar wisata dengan filter kategori dan pencarian.
- Detail wisata dengan informasi lengkap, gambar, dan rekomendasi N-Gram + TF-IDF.
- Semantic search melalui Flask API berbasis IndoBERT.
- Fallback pencarian SQL ketika Flask API tidak aktif.
- UI responsive dengan Blade, Tailwind CSS, Vite, SweetAlert2, dan AOS.

## Tech Stack

- PHP 8.2+
- Laravel 12
- Livewire 3
- MySQL
- Tailwind CSS + Vite
- Flask API di `../flask_api`

## Setup

Jalankan dari folder `web_recommendation`:

```powershell
composer install
npm install
Copy-Item .env.example .env
php artisan key:generate
```

Pastikan `.env` memakai MySQL dan Flask API lokal:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bogorxplore
DB_USERNAME=root
DB_PASSWORD=

FLASK_API_URL=http://localhost:5000
FLASK_API_TIMEOUT=30
```

Buat database jika belum ada:

```sql
CREATE DATABASE IF NOT EXISTS bogorxplore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Lalu jalankan migration dan seeder:

```powershell
php artisan migrate
php artisan db:seed --class=PlaceSeeder
php artisan optimize:clear
```

## Menjalankan Aplikasi

Terminal 1, dari root repo:

```powershell
cd flask_api
python app.py
```

Terminal 2, dari folder `web_recommendation`:

```powershell
php artisan serve
```

Terminal 3, dari folder `web_recommendation`:

```powershell
npm run dev
```

Akses aplikasi di `http://localhost:8000`.

## Integrasi Flask API

Laravel membaca URL API dari `config/services.php`:

```php
'flask' => [
    'base_url' => env('FLASK_API_URL', 'http://localhost:5000'),
    'timeout' => env('FLASK_API_TIMEOUT', 30),
],
```

Endpoint Flask yang dipakai:

| Method | Endpoint | Fungsi |
| --- | --- | --- |
| GET | `/api/places` | Daftar destinasi |
| GET | `/api/places/id` | Detail destinasi, contoh `/api/places/1` |
| GET | `/api/search?q=<query>` | Pencarian semantik |
| POST | `/api/recommendations` | Rekomendasi destinasi |

## Struktur Penting

```text
web_recommendation/
├── app/
│   ├── Http/Controllers/
│   │   ├── HomeController.php
│   │   ├── PlaceController.php
│   │   └── FlaskProxyController.php
│   ├── Livewire/PlacesList.php
│   ├── Models/Place.php
│   └── Services/FlaskApiService.php
├── database/
│   ├── migrations/
│   └── seeders/PlaceSeeder.php
├── resources/views/
│   ├── landing.blade.php
│   ├── layouts/app.blade.php
│   ├── livewire/places-list.blade.php
│   └── places/show.blade.php
└── routes/web.php
```

## Troubleshooting

| Masalah | Solusi |
| --- | --- |
| Database connection error | Pastikan MySQL aktif dan `DB_*` di `.env` benar |
| Tabel `places` kosong | Jalankan `php artisan db:seed --class=PlaceSeeder` |
| Rekomendasi tidak muncul | Jalankan Flask API: `cd ../flask_api; python app.py` |
| CSS/JS tidak update | Jalankan `npm run dev` |
| Cache config bermasalah | Jalankan `php artisan optimize:clear` |

## Lisensi

Educational Project - BogorXplore.
