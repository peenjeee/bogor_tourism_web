---
title: Bogor Xplore API
colorFrom: green
colorTo: blue
sdk: docker
app_port: 7860
pinned: false
license: mit
short_description: Flask API for Bogor tourism recommendations
---

# BogorXplore Flask API

Flask API untuk data wisata, pencarian semantik, dan rekomendasi destinasi
BogorXplore. API ini dipakai oleh aplikasi Laravel di `../web_recommendation`.

## Ringkasan

- Local development port: `5000`.
- Docker/Hugging Face Space port: `7860`.
- Dataset mentah dan response API: 296 destinasi dari
  `data/bogor_tourism_data.csv`.
- Search memakai IndoBERT similarity/query embedding.
- Rekomendasi detail wisata memakai precomputed N-Gram + TF-IDF similarity.

## Quick Start Lokal

Dari root repo:

```powershell
cd flask_api
pip install -r requirements.txt
python app.py
```

API berjalan di:

```text
http://localhost:5000
```

Catatan: startup pertama dapat lebih lama karena model IndoBERT
`indobenchmark/indobert-base-p1` dimuat oleh Transformers.

## Endpoint

| Method | Endpoint | Fungsi |
| --- | --- | --- |
| GET | `/` | Health check dan daftar endpoint utama. |
| GET | `/api/places` | Daftar destinasi dengan pagination dan filter kategori. |
| GET | `/api/places/id` | Detail destinasi berdasarkan index internal API, contoh `/api/places/1`. |
| GET | `/api/search?q=query` | Pencarian semantik berbasis IndoBERT. |
| POST | `/api/recommendations` | Rekomendasi destinasi berdasarkan `place_name` atau `place_id`. |

## Contoh Request

Health check:

```powershell
curl http://localhost:5000/
```

Daftar destinasi:

```powershell
curl "http://localhost:5000/api/places?limit=20&offset=0&category=Alam"
```

Pencarian semantik:

```powershell
curl "http://localhost:5000/api/search?q=air%20terjun&limit=10"
```

Rekomendasi:

```powershell
curl -X POST "http://localhost:5000/api/recommendations" `
  -H "Content-Type: application/json" `
  -d "{\"place_name\":\"Curug Ciampea\",\"top_n\":5}"
```

Payload rekomendasi:

```json
{
  "place_name": "Curug Ciampea",
  "top_n": 5
}
```

`place_name` lebih aman dipakai dari Laravel karena `place_id` adalah index
internal data API, bukan primary key database MySQL Laravel.

## Struktur

```text
flask_api/
├── api/                    # ruang tambahan untuk modul API jika diperlukan
├── data/                   # dataset dan artefak ML precomputed
├── models/
│   ├── __init__.py
│   ├── preprocessor.py
│   └── recommender.py
├── .dockerignore
├── .gitattributes
├── .python-version
├── app.py
├── Dockerfile
├── README.md
├── reproduce_error.py
├── requirements.txt
├── test_api.py
└── test_search.py
```

## File Data Penting

| File | Fungsi |
| --- | --- |
| `data/bogor_tourism_data.csv` | Dataset clean mentah, 296 destinasi. |
| `data/bogor_tourism_data.json` | Dataset clean dalam format JSON. |
| `data/data_preprocessed.csv` | Data hasil preprocessing notebook. |
| `data/data_with_keywords.csv` | Data dengan unigram, bigram, dan trigram; dipakai recommender. |
| `data/tfidf_matrix.npy` | Matrix TF-IDF. |
| `data/tfidf_vectorizer.pkl` | Vectorizer TF-IDF untuk query/search. |
| `data/ngram_similarity.npy` | Similarity N-Gram precomputed. |
| `data/indobert_embeddings.npy` | Embedding IndoBERT. |
| `data/indobert_similarity.npy` | Similarity IndoBERT precomputed. |
| `data/combined_similarity.npy` | Artefak similarity gabungan dari notebook. |

## Model Rekomendasi

Pipeline ML utama dikembangkan di `../dataset`:

```text
Data clean
-> preprocessing
-> N-Gram + TF-IDF
-> IndoBERT embedding
-> cosine similarity per metode
-> search IndoBERT dan rekomendasi detail N-Gram + TF-IDF
```

Implementasi API saat ini:

- `/api/search` memakai IndoBERT. Jika query sama persis dengan nama tempat,
  API memakai `indobert_similarity.npy`; jika tidak, query di-encode langsung.
- `/api/recommendations` mencari tempat dari `place_name` terlebih dahulu,
  lalu mengambil rekomendasi detail wisata dari `ngram_similarity.npy`
  (N-Gram + TF-IDF).

## Sinkronisasi Artefak

Jika notebook di `../dataset` menghasilkan artefak baru, salin file yang
dibutuhkan ke `flask_api/data` sebelum menjalankan API atau deploy ulang.

File yang biasanya disinkronkan:

```text
data_preprocessed.csv
data_with_keywords.csv
tfidf_matrix.npy
tfidf_vectorizer.pkl
ngram_similarity.npy
indobert_embeddings.npy
indobert_similarity.npy
tabel_perbandingan.csv
tabel_akurasi_kategori.csv
```

## Docker dan Hugging Face Space

Build lokal dari folder `flask_api`:

```powershell
docker build -t bogor-xplore-api .
docker run --rm -p 7860:7860 bogor-xplore-api
```

Docker menjalankan:

```text
gunicorn --bind 0.0.0.0:7860 --timeout 300 app:app
```

File `.github/workflows/sync-flask-api-to-hf.yml` melakukan sync folder
`flask_api/` ke Hugging Face Space saat ada push ke branch `main` yang mengubah
folder ini atau workflow tersebut. Workflow membutuhkan secret `HF_TOKEN`.

## Test Manual

Jalankan API dulu, lalu:

```powershell
python test_api.py
python test_search.py
```

`test_api.py` memanggil endpoint rekomendasi, sedangkan `test_search.py`
memanggil endpoint pencarian semantik.

## Dependencies

Daftar lengkap ada di `requirements.txt`.

Paket utama:

- Flask
- Flask-CORS
- Pandas
- NumPy
- scikit-learn
- Transformers
- PyTorch
- Sastrawi
- Gunicorn

## Troubleshooting

| Masalah | Solusi |
| --- | --- |
| API lama saat startup | Tunggu model IndoBERT selesai dimuat. |
| `ModuleNotFoundError` | Jalankan `pip install -r requirements.txt`. |
| File `.npy` atau `.pkl` tidak ditemukan | Sinkronkan artefak dari `../dataset/data` ke `flask_api/data`. |
| Rekomendasi tidak sesuai tempat Laravel | Kirim `place_name` selain `place_id` dari Laravel. |
| Deploy Hugging Face gagal | Pastikan `HF_TOKEN` ada dan Git LFS asset `flask_api/data/**` ikut ter-pull. |

## Lisensi

Educational Project - BogorXplore.
