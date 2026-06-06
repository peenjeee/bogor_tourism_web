# BogorXplore

BogorXplore adalah website pariwisata Bogor dengan sistem rekomendasi berbasis
Machine Learning. Aplikasi web Laravel menampilkan destinasi wisata, sedangkan
Flask API menyediakan pencarian semantik berbasis IndoBERT dan rekomendasi
detail wisata berbasis N-Gram + TF-IDF.

## Struktur Project

```text
bogor_tourism_web/
├── dataset/                # notebook dan data eksperimen ML
├── flask_api/              # Flask API untuk search dan rekomendasi
├── script/                 # data scraping/cleaning yang dipakai seeder
├── web_recommendation/     # Laravel 12 web application
├── QUICKSTART.md           # panduan setup singkat
├── README.md
└── setup.ps1               # setup otomatis Windows
```

## Kebutuhan

- PHP 8.2 atau lebih baru
- Composer
- Node.js 18 atau lebih baru
- Python 3.10 atau lebih baru
- MySQL

## Quick Start

Setup otomatis dari root repo:

```powershell
.\setup.ps1
```

Jalankan aplikasi dengan tiga terminal:

```bash
cd flask_api
python app.py
```

```bash
cd web_recommendation
php artisan serve
```

```bash
cd web_recommendation
npm run dev
```

Akses:

- Web: `http://localhost:8000`
- API: `http://localhost:5000`

Panduan lebih rinci ada di `QUICKSTART.md`.

## Setup Manual Ringkas

Flask API:

```bash
cd flask_api
pip install -r requirements.txt
python app.py
```

Laravel:

```powershell
cd web_recommendation
composer install
npm install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed --class=PlaceSeeder
npm run dev
php artisan serve
```

Default lokal memakai MySQL. Sesuaikan `.env` jika kredensial MySQL kamu
berbeda:

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

Buat database sebelum migration jika belum ada:

```sql
CREATE DATABASE IF NOT EXISTS jurnal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## Fitur

| Fitur | Deskripsi |
| --- | --- |
| Landing page | Halaman depan dengan destinasi populer |
| Daftar wisata | Grid destinasi dengan filter kategori dan pencarian |
| Detail wisata | Informasi destinasi, gambar, dan rekomendasi terkait |
| Search semantik | Pencarian dari Flask API berbasis IndoBERT |
| Rekomendasi detail | Content-based recommendation dari N-Gram + TF-IDF |
| Responsive UI | Blade, Tailwind CSS, dan interaksi ringan di frontend |

## Metode Rekomendasi

- N-Gram + TF-IDF dipakai untuk rekomendasi di halaman detail wisata.
- IndoBERT embedding dipakai untuk pencarian semantik.
- Cosine similarity dipakai untuk menghitung skor kemiripan per metode.

## API Utama

| Method | Endpoint | Fungsi |
| --- | --- | --- |
| GET | `/` | Health check |
| GET | `/api/places` | Daftar destinasi dengan pagination |
| GET | `/api/places/id` | Detail destinasi, contoh `/api/places/1` |
| GET | `/api/search?q=<query>` | Pencarian semantik |
| POST | `/api/recommendations` | Rekomendasi destinasi |

## Tech Stack

- Backend web: Laravel 12, Livewire, MySQL
- Frontend: Blade, Tailwind CSS, Vite, SweetAlert2
- API/ML: Flask, Pandas, NumPy, scikit-learn, Transformers, PyTorch, Sastrawi

## Lisensi

Educational Project - BogorXplore.
