import pandas as pd
import numpy as np
import os
import pickle
import warnings
warnings.filterwarnings('ignore')

from sklearn.metrics.pairwise import cosine_similarity

# For IndoBERT query embedding
import torch
from transformers import AutoTokenizer, AutoModel


class TourismRecommender:
    """
    Sistem rekomendasi wisata menggunakan kombinasi N-Gram + IndoBERT
    Menggunakan kombinasi N-Gram dan IndoBERT (notebook 01-05)
    
    Data: 296 destinasi wisata Kabupaten Bogor
    Formula: sim_final = 0.5 × sim_ngram + 0.5 × sim_indobert
    """
    
    def __init__(self, data_path='data/'):
        print("🚀 Initializing Tourism Recommender...")
        
        self.data_path = data_path
        
        # Load pre-computed data dari dataset (V2 Updated)
        ngram_similarity_path = os.path.join(data_path, 'ngram_similarity.npy')
        indobert_similarity_path = os.path.join(data_path, 'indobert_similarity.npy')
        data_csv_path = os.path.join(data_path, 'data_with_keywords.csv')
        tfidf_matrix_path = os.path.join(data_path, 'tfidf_matrix.npy')
        tfidf_vectorizer_path = os.path.join(data_path, 'tfidf_vectorizer.pkl')
        indobert_embeddings_path = os.path.join(data_path, 'indobert_embeddings.npy')
        
        # Load data
        print("   📂 Loading pre-computed data from dataset...")
        self.df = pd.read_csv(data_csv_path)
        
        # ========== PERBAIKAN SHAPE MISMATCH (Sync with Notebook V2) ==========
        # Filter row yang deskripsinya kosong agar shape menjadi 295
        if 'deskripsi_clean' in self.df.columns:
            text_col = 'deskripsi_clean' 
        else:
            text_col = 'deskripsi_ngram'
            
        initial_len = len(self.df)
        mask = self.df[text_col].fillna('').astype(str).str.strip() != ''
        self.df = self.df[mask].reset_index(drop=True)
        print(f"   Note: Filtered empty rows. {initial_len} -> {len(self.df)} items.")
        # ======================================================================
        
        # Map deskripsi_clean to deskripsi for compatibility
        if 'deskripsi_clean' in self.df.columns and 'deskripsi' not in self.df.columns:
            self.df['deskripsi'] = self.df['deskripsi_clean']
        
        # Load similarity matrices
        self.ngram_sim = np.load(ngram_similarity_path)
        self.indobert_sim = np.load(indobert_similarity_path)
        print(f"   ✅ Loaded ngram_similarity: {self.ngram_sim.shape}")
        print(f"   ✅ Loaded indobert_similarity: {self.indobert_sim.shape}")
        
        # Load TF-IDF untuk query search (if needed)
        self.tfidf_matrix = np.load(tfidf_matrix_path)
        # Note: self.tfidf_matrix might be 296 rows (old). If using for search, might mismatch?
        # Ideally we should use the filtered tfidf matrix, but for now we focus on similarity.
        
        with open(tfidf_vectorizer_path, 'rb') as f:
            self.tfidf_vectorizer = pickle.load(f)
        
        # Load IndoBERT embeddings untuk query search
        self.indobert_embeddings = np.load(indobert_embeddings_path)
        # Check shape consistency for IndoBERT embeddings
        if len(self.df) != len(self.indobert_embeddings):
             # If embeddings are 295, it's fine. If 296, we might need to filter it too?
             # For now, we assume embeddings.npy in data folder was already generated from filtered data in notebook?
             # Notebook 03_indobert_embedding_v2.ipynb generates it.
             # If notebook saved it, it should match the filtered DF.
             print(f"   ⚠️ Warning: DF length {len(self.df)} != Embeddings length {len(self.indobert_embeddings)}")

        
        # Load IndoBERT model untuk query embedding
        print("   📥 Loading IndoBERT model for query encoding...")
        self.device = torch.device('cuda' if torch.cuda.is_available() else 'cpu')
        self.tokenizer = AutoTokenizer.from_pretrained("indobenchmark/indobert-base-p1")
        self.model = AutoModel.from_pretrained("indobenchmark/indobert-base-p1")
        self.model.eval()
        self.model.to(self.device)
        print(f"   ✅ IndoBERT model loaded! Device: {self.device}")
        
        print(f"\n✅ Recommender ready!")
        print(f"   Total destinations: {len(self.df)}")
        print(f"   Method: IndoBERT (Search) & N-Gram (Detail Recommendations)")
    
    def _get_query_embedding(self, text):
        """Get IndoBERT embedding for query text (768-dim)"""
        if not text:
            return np.zeros(768)
        try:
            inputs = self.tokenizer(text, return_tensors='pt', truncation=True, max_length=256, padding=True)
            inputs = {k: v.to(self.device) for k, v in inputs.items()}
            with torch.no_grad():
                outputs = self.model(**inputs)
            return outputs.last_hidden_state.mean(dim=1).squeeze().cpu().numpy()
        except:
            return np.zeros(768)
    
    def semantic_search(self, query, top_n=10):
        """
        Semantic search ONLY using IndoBERT embeddings.
        Used for Search feature.
        """
        # Encode query with IndoBERT
        query_embedding = self._get_query_embedding(query)
        
        # Calculate cosine similarity with all items
        bert_scores = cosine_similarity([query_embedding], self.indobert_embeddings).flatten()
        
        # Sort and get top_n
        sorted_indices = np.argsort(bert_scores)[::-1]
        top_indices = sorted_indices[:top_n] if top_n else sorted_indices
        
        # Build result list
        results = []
        for idx in top_indices:
            place = self.df.iloc[idx]
            results.append({
                # No 'id' column in CSV, so we use Index as ID for API consistency
                'id': int(place['id']) if 'id' in place else int(idx),
                'nama': str(place['nama']) if pd.notna(place['nama']) else '',
                'kategori': str(place['kategori']) if pd.notna(place['kategori']) else '',
                'label': str(place.get('label', '')) if pd.notna(place.get('label')) else '',
                'deskripsi': str(place['deskripsi'])[:200] + '...' if pd.notna(place['deskripsi']) and len(str(place['deskripsi'])) > 200 else (str(place['deskripsi']) if pd.notna(place['deskripsi']) else ''),
                'url_gambar': str(place.get('url_gambar', '')) if pd.notna(place.get('url_gambar')) else '',
                'alamat': str(place.get('alamat', '')) if pd.notna(place.get('alamat')) else '',
                'harga_tiket': str(place.get('harga_tiket', '')) if pd.notna(place.get('harga_tiket')) else '',
                'similarity_score': round(float(bert_scores[idx]), 4)
            })
        
        return results
    
    def get_recommendations(self, place_idx, top_n=10):
        """
        Get recommendations using Pre-computed N-Gram Matrix (Sync with Notebook V2).
        Used for Detail Page Recommendations.
        IMPORTANT: Input `place_idx` MUST be the DataFrame Index (0 to N-1).
        """
        # Validate index
        if place_idx is None or place_idx < 0 or place_idx >= len(self.df):
            print(f"❌ Invalid Place Index: {place_idx}")
            return []
        
        # Use pre-computed N-Gram similarity matrix directly
        # self.ngram_sim is (n_items, n_items)
        ngram_scores = self.ngram_sim[place_idx]
        
        # Sort scores
        sim_scores = list(enumerate(ngram_scores))
        
        # Sort by similarity (excluding the place itself)
        sim_scores = sorted(sim_scores, key=lambda x: x[1], reverse=True)
        
        # Filter: Exclude self (where index == place_idx) and take top_n
        # We start from index 1 because index 0 is the item itself (score 1.0)
        # Note: In standard argsort[::-1], the first item is self. 
        # Here we manually filtered x[0] != place_idx which is safer.
        sim_scores = [x for x in sim_scores if x[0] != place_idx][:top_n]
        
        # Build recommendations
        recommendations = []
        for sim_idx, score in sim_scores:
            place = self.df.iloc[sim_idx]
            recommendations.append({
                'id': int(place['id']) if 'id' in place else int(sim_idx),
                'nama': str(place['nama']) if pd.notna(place['nama']) else '',
                'kategori': str(place['kategori']) if pd.notna(place['kategori']) else '',
                'label': str(place.get('label', '')) if pd.notna(place.get('label')) else '',
                'deskripsi': str(place['deskripsi'])[:200] + '...' if pd.notna(place['deskripsi']) and len(str(place['deskripsi'])) > 200 else (str(place['deskripsi']) if pd.notna(place['deskripsi']) else ''),
                'url_gambar': str(place.get('url_gambar', '')) if pd.notna(place.get('url_gambar')) else '',
                'alamat': str(place.get('alamat', '')) if pd.notna(place.get('alamat')) else '',
                'harga_tiket': str(place.get('harga_tiket', '')) if pd.notna(place.get('harga_tiket')) else '',
                'similarity_score': round(float(score), 4)
            })
        
        return recommendations
    
    def get_place_by_id(self, place_id):
        """Get detailed information for a specific place"""
        if place_id < 0 or place_id >= len(self.df):
            return None
        
        place = self.df.iloc[place_id]
        return {
            'id': int(place_id),
            'nama': str(place['nama']) if pd.notna(place['nama']) else '',
            'kategori': str(place['kategori']) if pd.notna(place['kategori']) else '',
            'label': str(place.get('label', '')) if pd.notna(place.get('label')) else '',
            'deskripsi': str(place['deskripsi']) if pd.notna(place['deskripsi']) else '',
            'alamat': str(place.get('alamat', '')) if pd.notna(place.get('alamat')) else '',
            'fasilitas': str(place.get('fasilitas', '')) if pd.notna(place.get('fasilitas')) else '',
            'harga_tiket': str(place.get('harga_tiket', '')) if pd.notna(place.get('harga_tiket')) else '',
            'jam_operasional': str(place.get('jam_operasional', '')) if pd.notna(place.get('jam_operasional')) else '',
            'telepon': str(place.get('telepon', '')) if pd.notna(place.get('telepon')) else '',
            'url': str(place.get('url', '')) if pd.notna(place.get('url')) else '',
            'url_gambar': str(place.get('url_gambar', '')) if pd.notna(place.get('url_gambar')) else '',
            'tags': str(place.get('tags', '')) if pd.notna(place.get('tags')) else '',
            'likes': int(place.get('likes', 0)) if pd.notna(place.get('likes')) else 0
        }
    
    def get_all_places(self, limit=None, offset=0):
        """Get all places with optional pagination"""
        total = len(self.df)
        
        if limit:
            df_slice = self.df.iloc[offset:offset+limit]
        else:
            df_slice = self.df.iloc[offset:]
        
        places = []
        for idx, place in df_slice.iterrows():
            places.append({
                'id': int(idx),
                'nama': str(place['nama']) if pd.notna(place['nama']) else '',
                'kategori': str(place['kategori']) if pd.notna(place['kategori']) else '',
                'label': str(place.get('label', '')) if pd.notna(place.get('label')) else '',
                'deskripsi': str(place['deskripsi'])[:200] + '...' if pd.notna(place['deskripsi']) and len(str(place['deskripsi'])) > 200 else (str(place['deskripsi']) if pd.notna(place['deskripsi']) else ''),
                'url_gambar': str(place.get('url_gambar', '')) if pd.notna(place.get('url_gambar')) else '',
                'harga_tiket': str(place.get('harga_tiket', '')) if pd.notna(place.get('harga_tiket')) else '',
                'likes': int(place.get('likes', 0)) if pd.notna(place.get('likes')) else 0
            })
        
        return {
            'total': total,
            'limit': limit,
            'offset': offset,
            'data': places
        }
    
    def search_by_name(self, query):
        """Search places by name (partial match)"""
        matches = self.df[self.df['nama'].str.lower().str.contains(query.lower(), na=False)]
        if len(matches) == 0:
            return None
        # Use integer index from reset_index'd DataFrame
        # matches.index[0] is now the correct row position (0 to N-1)
        return {
            'query_name': matches.iloc[0]['nama'],
            'query_idx': int(matches.index[0])
        }
    
    def get_categories(self):
        """Get all unique categories"""
        return self.df['kategori'].unique().tolist()
    
    def get_labels(self):
        """Get all unique labels"""
        if 'label' in self.df.columns:
            return self.df['label'].dropna().unique().tolist()
        return []

    def semantic_search(self, query, top_n=10):
        """
        Semantic search using IndoBERT similarity matrix (matching notebook behavior)
        If query matches an existing place name, use pre-computed similarity matrix.
        Otherwise, fall back to on-the-fly query encoding.
        """
        try:
            if top_n is None:
                top_n = 10
            
            # Normalize query for comparison
            query_lower = query.strip().lower()
            
            # Check if query matches an existing place name
            place_idx = None
            for idx, row in self.df.iterrows():
                if row['nama'].strip().lower() == query_lower:
                    place_idx = idx
                    break
            
            if place_idx is not None:
                # Use pre-computed IndoBERT similarity matrix (like notebook)
                print(f"   📊 Using IndoBERT similarity matrix for place: {self.df.iloc[place_idx]['nama']}")
                sim_scores = self.indobert_sim[place_idx]
                
                # Get top N (excluding itself at index 0)
                top_indices = sim_scores.argsort()[::-1][1:top_n+1]
            else:
                # Fall back to on-the-fly query encoding
                print(f"   🔍 Query not found as place name, using query encoding...")
                query_emb = self._get_query_embedding(query)
                query_emb = query_emb.reshape(1, -1)
                sim_scores = cosine_similarity(query_emb, self.indobert_embeddings)[0]
                top_indices = sim_scores.argsort()[::-1][:top_n]
            
            results = []
            for idx in top_indices:
                # SAFEGUARD: Ignore indices that are out of bounds
                if idx >= len(self.df):
                    print(f"   ⚠️ Ignored out-of-bounds index: {idx}")
                    continue
                    
                place = self.df.iloc[idx]
                results.append({
                    'id': int(idx),
                    'nama': str(place['nama']),
                    'score': float(sim_scores[idx]),
                    'deskripsi': str(place['deskripsi'])[:200] if pd.notna(place.get('deskripsi')) else '',
                    'kategori': str(place['kategori']) if pd.notna(place.get('kategori')) else '',
                    'url_gambar': str(place['url_gambar']) if pd.notna(place.get('url_gambar')) else ''
                })
                
            return {
                'status': 'success',
                'data': results
            }
            
        except Exception as e:
            print(f"Error in semantic_search: {e}")
            import traceback
            traceback.print_exc()
            return {
                'status': 'error',
                'message': str(e)
            }

