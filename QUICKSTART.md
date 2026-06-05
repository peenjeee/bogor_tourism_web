# 🚀 Quick Start Guide - BogorXplore

Panduan cepat untuk menjalankan project BogorXplore - Sistem Rekomendasi Wisata Bogor.

## Prerequisites

- PHP >= 8.1 + Composer
- Node.js >= 18 + npm
- Python >= 3.10
- MySQL
- Laragon (recommended) atau XAMPP

## 📦 One-Click Setup (Windows)

```powershell
.\setup.ps1
```

## 📦 Manual Installation

### 1. Flask API Setup

```bash
cd flask_api
pip install -r requirements.txt
python app.py
# API runs on http://localhost:5000
```

**Test API:**
```bash
curl http://localhost:5000/
curl "http://localhost:5000/api/search?q=air%20terjun&limit=5"
curl http://localhost:5000/api/places?limit=5
```

### 2. Laravel Setup

```bash
cd web_recommendation
composer install
npm install
cp .env.example .env
php artisan key:generate
```

### 3. Database Configuration

Edit `.env`:
```env
DB_DATABASE=bogor_tourism
DB_USERNAME=root
DB_PASSWORD=your_password
FLASK_API_URL=http://localhost:5000
```

Create database:
```sql
CREATE DATABASE bogor_tourism CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. Run Migrations & Seed

```bash
php artisan migrate
php artisan db:seed --class=PlaceSeeder
```

### 5. Start Development Servers

**Terminal 1 - Flask API:**
```bash
cd flask_api && python app.py
```

**Terminal 2 - Laravel:**
```bash
cd web_recommendation && php artisan serve
```

**Terminal 3 - Vite:**
```bash
cd web_recommendation && npm run dev
```

## 🎯 Akses Website

- 🌐 **Web:** http://localhost:8000
- 🤖 **API:** http://localhost:5000

## 📂 Struktur Project

```
bogor_tourism_web/
├── dataset/                # Notebooks 00-05 & data
├── flask_api/              # ML Recommendation API
├── web_recommendation/     # Laravel Website
├── README.md
└── QUICKSTART.md           # This file
```

## 🔧 Troubleshooting

| Problem | Solution |
|---------|----------|
| Flask API error | `pip install -r requirements.txt` |
| No styles | `npm run dev` atau `npm run build` |
| No recommendations | Pastikan Flask API running di port 5000 |
| Database error | Check `.env` credentials |
| Cache issues | `php artisan optimize:clear` |

## 🚀 Production

```bash
# Build assets
npm run build

# Optimize Laravel
php artisan optimize

# Flask with Gunicorn
gunicorn -w 4 -b 0.0.0.0:5000 app:app
```

---

**Ready to explore BogorXplore! 🏞️**
