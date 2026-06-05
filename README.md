# 🏞️ BogorXplore - Sistem Rekomendasi Wisata Bogor

Website pariwisata Bogor dengan sistem rekomendasi berbasis Machine Learning menggunakan **N-Gram + IndoBERT** untuk menghasilkan rekomendasi yang akurat berdasarkan kesamaan leksikal dan semantik.

## 📁 Struktur Project

```
bogor_tourism_web/
├── dataset/                # Notebooks & data preprocessing (00-05)
│   ├── 00_generate_flowchart.ipynb
│   ├── 01_preprocessing.ipynb
│   ├── 02_ngram_extraction.ipynb
│   ├── 03_indobert_embedding.ipynb
│   ├── 04_recommendation_system.ipynb
│   ├── 05_evaluation.ipynb
│   └── data/               # Dataset & pre-computed models
├── flask_api/              # Flask ML API (Recommendation Engine)
└── web_recommendation/     # Laravel Web Application
```

## 🚀 Quick Start

### Prerequisites
- PHP >= 8.1 + Composer
- Node.js >= 18 + NPM
- Python >= 3.10
- MySQL

### 1. Clone & Setup

```powershell
# Gunakan setup script otomatis
.\setup.ps1
```

### 2. Manual Setup

**Flask API:**
```bash
cd flask_api
pip install -r requirements.txt
python app.py
# API runs on http://localhost:5000
```

**Laravel Web:**
```bash
cd web_recommendation
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run dev
php artisan serve
# Web runs on http://localhost:8000
```

## 🎯 Features

| Feature | Deskripsi |
|---------|-----------|
| 🏠 Landing Page | Hero section + destinasi populer |
| 📋 Daftar Wisata | Grid 296 tempat wisata dengan filter & search |
| 🔍 Detail Wisata | Info lengkap + peta lokasi Google Maps |
| 🤖 Rekomendasi ML | N-Gram + IndoBERT (50:50) + Cosine Similarity |
| 📱 Responsive | Mobile-first dengan Tailwind CSS |
| 🔔 SweetAlert2 | Toast notification untuk pencarian |

## 🧠 Metode Rekomendasi

- **N-Gram (Unigram, Bigram, Trigram):** Menangkap pola leksikal berbasis kata kunci
- **IndoBERT:** Representasi semantik yang memahami makna teks
- **Cosine Similarity:** Mengukur kemiripan antar destinasi
- **296 Destinasi Wisata** dalam 7 kategori

## 🛠️ Tech Stack

**Backend:** Laravel 12, Flask 3.0, MySQL  
**Frontend:** Blade, Tailwind CSS, Livewire  
**ML/NLP:** TF-IDF, N-Gram, IndoBERT, Cosine Similarity, Sastrawi

## 📄 License

Educational Project - BogorXplore 2025
