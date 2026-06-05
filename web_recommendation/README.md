# 🌐 BogorXplore Web - Laravel Application

Laravel-based web application untuk menampilkan destinasi wisata Bogor dengan integrasi sistem rekomendasi ML menggunakan **N-Gram + IndoBERT**.

## 🚀 Features

- **Landing Page** - Hero section dengan destinasi populer
- **Places Listing** - Grid view dengan Livewire pagination & filter
- **Place Detail** - Info lengkap + Google Maps embed + rekomendasi ML
- **Semantic Search** - Live search menggunakan N-Gram + IndoBERT
- **Responsive** - Mobile-first design dengan Tailwind CSS
- **SweetAlert2** - Toast notifications untuk feedback

## 📦 Tech Stack

- Laravel 12 + Livewire 3
- Tailwind CSS + Vite
- MySQL Database
- SweetAlert2 untuk notifikasi
- AOS untuk animasi scroll

## 🛠️ Installation

```bash
# Install dependencies
composer install
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate --seed

# Development
npm run dev
php artisan serve
```

## 📁 Struktur Penting

```
web_recommendation/
├── app/
│   ├── Http/Controllers/PlaceController.php
│   ├── Livewire/PlacesList.php
│   ├── Models/Place.php
│   └── Services/FlaskApiService.php
├── resources/views/
│   ├── landing.blade.php
│   ├── layouts/app.blade.php
│   ├── livewire/places-list.blade.php
│   └── places/show.blade.php
└── .env (FLASK_API_URL=http://localhost:5000)
```

## 🔗 API Integration

Aplikasi ini terhubung ke Flask API untuk mendapatkan rekomendasi ML.

```env
FLASK_API_URL=http://localhost:5000
```

**Metode Rekomendasi:**
- Semantic Search menggunakan N-Gram + IndoBERT
- 296 destinasi wisata dalam 7 kategori

## 📄 License

Educational Project - BogorXplore 2025
