# Dataset - Sistem Rekomendasi Wisata Bogor

## Ekstraksi Kata Kunci dengan N-Gram dan IndoBERT untuk Rekomendasi Wisata Bogor

Folder ini berisi notebook Jupyter untuk preprocessing data dan pengembangan sistem rekomendasi wisata.

### 📊 Dataset
- **296 destinasi wisata** Kabupaten Bogor
- **7 kategori:** Arena, Olahraga, Alam, Seni Budaya, Belanja, Kuliner, Rekreasi
- Data dikumpulkan melalui web scraping dari Google Maps

### 📁 Struktur File

| No | File | Deskripsi |
|----|------|-----------|
| 0 | `00_generate_flowchart.ipynb` | Generate flowchart metodologi |
| 1 | `01_preprocessing.ipynb` | Preprocessing data (case folding, cleaning) |
| 2 | `02_ngram_extraction.ipynb` | Ekstraksi kata kunci dengan N-gram + TF-IDF |
| 3 | `03_indobert_embedding.ipynb` | Generate embedding IndoBERT |
| 4 | `04_recommendation_system.ipynb` | Sistem rekomendasi (N-gram + IndoBERT 50:50) |
| 5 | `05_evaluation.ipynb` | Evaluasi (Precision, Recall, F1-Score) |

### 🔄 Flow

```
Preprocessing → N-gram (TF-IDF) → IndoBERT (Embedding) → Cosine Similarity → Recommendation → Evaluation
```

### 📂 Folder Data

| File | Deskripsi |
|------|-----------|
| `data_preprocessed.csv` | Data hasil preprocessing |
| `tfidf_matrix.npy` | Matriks TF-IDF (296 × 5000) |
| `tfidf_vectorizer.pkl` | TF-IDF Vectorizer model |
| `indobert_embeddings.npy` | IndoBERT embeddings (296 × 768) |
| `combined_similarity.npy` | Matriks similarity gabungan (296 × 296) |
