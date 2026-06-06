# Quick Start - BogorXplore

Panduan cepat untuk menjalankan BogorXplore di Windows/Laragon.

## Kebutuhan

- PHP 8.2 atau lebih baru
- Composer
- Node.js 18 atau lebih baru
- Python 3.10 atau lebih baru
- MySQL
- Laragon direkomendasikan

Project Laravel memakai MySQL. Contoh lokal di repo ini memakai database
`bogorxplore` dengan user `root` dan password kosong, mengikuti default Laragon.

## Setup Otomatis

Dari root repo:

```powershell
.\setup.ps1
```

Script ini akan:

- install dependency Flask dari `flask_api/requirements.txt`
- install dependency Laravel dan NPM
- membuat `web_recommendation/.env` jika belum ada
- memastikan `FLASK_API_URL=http://localhost:5000`
- memastikan konfigurasi database memakai MySQL
- mencoba membuat database MySQL jika `mysql` CLI tersedia
- menjalankan migration, `PlaceSeeder`, build asset, dan clear cache Laravel

## Setup Manual

### 1. Flask API

```bash
cd flask_api
pip install -r requirements.txt
python app.py
```

API berjalan di `http://localhost:5000`.

Tes cepat:

```bash
curl http://localhost:5000/
curl "http://localhost:5000/api/search?q=air%20terjun&limit=5"
curl "http://localhost:5000/api/places?limit=5"
```

### 2. Laravel

```powershell
cd web_recommendation
composer install
npm install
Copy-Item .env.example .env
php artisan key:generate
```

Pastikan `.env` berisi:

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

Pastikan database sudah ada:

```sql
CREATE DATABASE IF NOT EXISTS bogorxplore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Lalu jalankan database setup dari folder `web_recommendation`:

```bash
php artisan migrate
php artisan db:seed --class=PlaceSeeder
php artisan optimize:clear
```

### 3. Jalankan Server

Terminal 1:

```bash
cd flask_api
python app.py
```

Terminal 2:

```bash
cd web_recommendation
php artisan serve
```

Terminal 3:

```bash
cd web_recommendation
npm run dev
```

## Akses

- Web: `http://localhost:8000`
- API: `http://localhost:5000`

## Struktur Singkat

```text
bogor_tourism_web/
├── dataset/                # notebook dan data eksperimen ML
├── flask_api/              # Flask recommendation API
├── script/                 # data hasil scraping/cleaning
├── web_recommendation/     # Laravel web application
├── QUICKSTART.md
├── README.md
└── setup.ps1
```

## Troubleshooting

| Masalah | Solusi |
| --- | --- |
| Flask API gagal start | Jalankan `pip install -r requirements.txt` dari `flask_api` |
| Search/rekomendasi kosong | Pastikan `python app.py` aktif di port 5000 |
| Database gagal konek | Pastikan MySQL aktif dan `DB_*` di `.env` benar |
| Tabel `places` tidak ada | Jalankan `php artisan migrate` dari `web_recommendation` |
| Data wisata kosong | Jalankan `php artisan db:seed --class=PlaceSeeder` |
| Asset tidak muncul | Jalankan `npm run dev` atau `npm run build` |
| Cache Laravel bermasalah | Jalankan `php artisan optimize:clear` |

## Catatan

`PlaceSeeder` mengambil data dari CSV terlebih dahulu, terutama
`script/bogor_tourism_data_clean.csv`, lalu fallback ke data di `flask_api/data`
atau `database/seeders/data`.
