
import os
import sys
from models.recommender import TourismRecommender

# Initialize recommender
try:
    print("Init Recommender...")
    rec = TourismRecommender()
    
    query = "wisata air terjun yang sejuk"
    print(f"Testing Search: {query}")
    
    results = rec.semantic_search(query, top_n=10)
    print("Results:", results)
    
except Exception as e:
    print("CRASHED:")
    print(e)
    import traceback
    traceback.print_exc()
