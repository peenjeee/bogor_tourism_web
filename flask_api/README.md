# 🤖 Flask API - BogorXplore Recommendation Engine

Flask API untuk menyediakan data wisata dan rekomendasi berbasis Machine Learning menggunakan kombinasi **N-Gram + IndoBERT**.

## 🚀 Quick Start

```bash
cd flask_api
pip install -r requirements.txt
python app.py
# API runs on http://localhost:5000
```

## 📡 API Endpoints

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/` | Health check |
| GET | `/api/places` | Get all places (paginated) |
| GET | `/api/places/<id>` | Get place detail |
| GET | `/api/search?q=<query>` | Semantic search |
| POST | `/api/recommendations` | Get ML recommendations |

### Semantic Search
```
GET /api/search?q=air terjun&limit=10
```

### Get All Places
```
GET /api/places?limit=20&offset=0&category=Alam
```

### Get Recommendations
```
POST /api/recommendations
Content-Type: application/json

{
    "place_id": 1,
    "top_n": 10
}
```

## 🧠 ML Model

**Content-Based Filtering dengan N-Gram + IndoBERT:**

| Komponen | Deskripsi |
|----------|-----------|
| **N-Gram** | Unigram, Bigram, Trigram + TF-IDF (5000 features) |
| **IndoBERT** | Sentence embedding 768 dimensi |
| **Similarity** | Cosine Similarity |
| **Formula** | `sim_final = 0.5 × sim_ngram + 0.5 × sim_indobert` |

## 📁 Struktur

```
flask_api/
├── app.py              # Main Flask application
├── models/
│   ├── __init__.py
│   ├── recommender.py  # ML recommendation engine
│   └── preprocessor.py # Text preprocessing
├── data/
│   ├── bogor_tourism_data.csv
│   ├── data_preprocessed.csv
│   ├── combined_similarity.npy
│   ├── tfidf_matrix.npy
│   ├── tfidf_vectorizer.pkl
│   └── indobert_embeddings.npy
└── requirements.txt
```

## 📦 Dependencies

- Flask 3.0
- Pandas, NumPy
- Scikit-learn
- Transformers (IndoBERT)
- PyTorch
- Sastrawi
- Flask-CORS

## 📄 License

Educational Project - BogorXplore 2025
