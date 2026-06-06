# Dataset dan Notebook ML BogorXplore

Folder ini berisi notebook eksperimen Machine Learning untuk sistem rekomendasi
wisata Bogor. Artefak yang dihasilkan dipakai oleh Flask API untuk pencarian
semantik dan rekomendasi destinasi.

## Ringkasan Dataset

- Total data clean: 296 destinasi wisata.
- Kategori: Alam, Arena, Belanja, Kuliner, Olahraga, Rekreasi, Seni Budaya.
- Sumber data clean berasal dari hasil scraping di folder `../script`.
- Metode aktif di aplikasi: N-Gram + TF-IDF untuk rekomendasi detail wisata,
  dan IndoBERT untuk pencarian semantik.

## Struktur Folder

```text
dataset/
├── checkpoints/            # checkpoint/cache model lokal
├── data/                   # hasil preprocessing, matrix, similarity, dan grafik
├── models/                 # ruang simpan model tambahan jika diperlukan
├── 01_preprocessing_v2.ipynb
├── 01_preprocessing.ipynb
├── 02_ngram_extraction_v2.ipynb
├── 02_ngram_extraction.ipynb
├── 03_indobert_embedding_v2.ipynb
├── 03_indobert_embedding.ipynb
├── 04_recommendation_system_v2.ipynb
├── 04_recommendation_system.ipynb
├── 05_evaluation_v2.ipynb
├── 05_evaluation.ipynb
└── README.md
```

## Urutan Notebook

Gunakan notebook `_v2` sebagai alur terbaru. Notebook tanpa `_v2` disimpan
sebagai versi awal/referensi.

| Urutan | Notebook | Fungsi |
| --- | --- | --- |
| 1 | `01_preprocessing_v2.ipynb` | Membersihkan teks dan membuat data preprocessing. |
| 2 | `02_ngram_extraction_v2.ipynb` | Ekstraksi unigram, bigram, trigram, dan matrix TF-IDF. |
| 3 | `03_indobert_embedding_v2.ipynb` | Membuat embedding IndoBERT dan similarity semantik. |
| 4 | `04_recommendation_system_v2.ipynb` | Similarity N-Gram + TF-IDF untuk rekomendasi detail dan IndoBERT untuk search. |
| 5 | `05_evaluation_v2.ipynb` | Evaluasi precision, recall, F1-score, dan akurasi kategori. |

## Alur Data

```text
Data clean
-> preprocessing teks
-> N-Gram + TF-IDF similarity untuk rekomendasi detail wisata
-> IndoBERT embedding dan similarity untuk pencarian semantik
-> evaluasi
```

## File Penting di `data/`

| File | Fungsi |
| --- | --- |
| `data_preprocessed.csv` | Data utama setelah preprocessing, berisi 296 baris. |
| `data_preprocessed_ngram.csv` | Data preprocessing khusus ekstraksi N-Gram. |
| `data_preprocessed_indobert.csv` | Data preprocessing khusus embedding IndoBERT. |
| `data_with_keywords.csv` | Data destinasi dengan kolom unigram, bigram, dan trigram. |
| `tfidf_matrix.npy` | Matrix TF-IDF untuk fitur N-Gram. |
| `tfidf_vectorizer.pkl` | Model/vectorizer TF-IDF. |
| `ngram_similarity.npy` | Matrix similarity dari N-Gram + TF-IDF. |
| `indobert_embeddings.npy` | Embedding IndoBERT untuk seluruh destinasi. |
| `indobert_similarity.npy` | Matrix similarity dari embedding IndoBERT. |
| `combined_similarity.npy` | Matrix similarity gabungan dari eksperimen notebook. |
| `tabel_perbandingan.csv` | Ringkasan precision, recall, dan F1-score. |
| `tabel_akurasi_kategori.csv` | Ringkasan akurasi per kategori. |
| `*.png` | Visualisasi flowchart, distribusi N-Gram, heatmap, dan evaluasi. |

## Kolom Data Utama

`data_preprocessed.csv` berisi:

```text
nama, kategori, url, url_gambar, likes, isi, deskripsi, deskripsi_clean, deskripsi_ngram
```

`data_with_keywords.csv` berisi:

```text
nama, kategori, url, url_gambar, likes, deskripsi_clean, deskripsi_ngram, unigrams, bigrams, trigrams
```

## Hasil Evaluasi Singkat

Ringkasan dari `data/tabel_perbandingan.csv`:

Baris `N-Gram + IndoBERT` di tabel ini adalah hasil eksperimen notebook.

| Metode | Top-N | Precision | Recall | F1-Score |
| --- | ---: | ---: | ---: | ---: |
| N-Gram + TF-IDF | 3 | 0.6340 | 0.0406 | 0.0723 |
| IndoBERT | 3 | 0.4550 | 0.0242 | 0.0445 |
| N-Gram + IndoBERT | 3 | 0.6340 | 0.0406 | 0.0723 |
| N-Gram + TF-IDF | 5 | 0.6074 | 0.0614 | 0.1040 |
| IndoBERT | 5 | 0.4236 | 0.0369 | 0.0649 |
| N-Gram + IndoBERT | 5 | 0.6081 | 0.0615 | 0.1041 |
| N-Gram + TF-IDF | 10 | 0.5764 | 0.1100 | 0.1697 |
| IndoBERT | 10 | 0.3892 | 0.0655 | 0.1060 |
| N-Gram + IndoBERT | 10 | 0.5760 | 0.1100 | 0.1696 |

## Sinkronisasi ke Flask API

Flask API membaca artefak dari `../flask_api/data`, bukan langsung dari
`dataset/data`. Setelah notebook menghasilkan artefak baru, salin file yang
dibutuhkan ke `flask_api/data` agar API memakai versi terbaru.

File yang biasanya perlu disinkronkan:

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

## Catatan

- Jalankan notebook berurutan karena output satu tahap dipakai tahap berikutnya.
- Hindari menghapus file `.npy` dan `.pkl` jika Flask API masih membutuhkannya.
- Jika dataset dari `../script` di-regenerate, ulangi pipeline notebook dan
  sinkronkan artefak ke `flask_api/data`.
