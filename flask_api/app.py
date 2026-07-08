from flask import Flask, jsonify, request
from flask_cors import CORS
from models.recommender import TourismRecommender
import os

app = Flask(__name__)
CORS(app)  # Enable CORS for Laravel

BASE_DIR = os.path.dirname(os.path.abspath(__file__))

# Initialize recommender
print("Initializing Tourism Recommender...")
recommender = TourismRecommender(data_path=os.path.join(BASE_DIR, 'data'))
print("Ready!")

@app.route('/')
def index():
    """API Health Check"""
    return jsonify({
        'status': 'success',
        'message': 'Bogor Tourism Recommendation API',
        'version': '1.0.0',
        'endpoints': {
            'GET /api/places': 'Get all tourism places with pagination',
            'GET /api/places/id': 'Get specific place details',
            'GET /api/search?q=query': 'Semantic search using IndoBERT',
            'POST /api/recommendations': 'Get recommendations for a place'
        }
    })

@app.route('/api/places', methods=['GET'])
def get_places():
    """
    Get all tourism places with pagination
    Query params:
        - limit: number of items per page (default: 20)
        - offset: starting position (default: 0)
        - category: filter by category (optional)
    """
    try:
        limit = request.args.get('limit', 20, type=int)
        offset = request.args.get('offset', 0, type=int)
        category = request.args.get('category', None, type=str)
        
        result = recommender.get_all_places(limit=limit, offset=offset)
        
        # Filter by category if provided
        if category:
            result['data'] = [p for p in result['data'] if p['kategori'].lower() == category.lower()]
            result['total'] = len(result['data'])
        
        return jsonify({
            'status': 'success',
            'data': result
        })
    except Exception as e:
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500

@app.route('/api/places/<int:place_id>', methods=['GET'])
def get_place(place_id):
    """Get detailed information about a specific place"""
    try:
        place = recommender.get_place_by_id(place_id)
        
        if place is None:
            return jsonify({
                'status': 'error',
                'message': f'Place with ID {place_id} not found'
            }), 404
        
        return jsonify({
            'status': 'success',
            'data': place
        })
    except Exception as e:
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500

@app.route('/api/recommendations', methods=['POST'])
def get_recommendations():
    """
    Get recommendations for a place
    Request body:
        - place_id: ID of the place (optional if place_name provided)
        - place_name: Name of the place (optional if place_id provided)
        - top_n: number of recommendations (default: 10)
    """
    try:
        data = request.get_json()
        
        if not data:
            return jsonify({
                'status': 'error',
                'message': 'Request body is required'
            }), 400
        
        place_idx = None
        place_details = None
        top_n = data.get('top_n', 10)
        
        print(f"REQUEST: {data}")

        # 1. Try lookup by NAME first (Most reliable if IDs don't match)
        if 'place_name' in data and data['place_name']:
            print(f"   Searching by name: {data['place_name']}")
            search_result = recommender.search_by_name(data['place_name'])
            if search_result:
                place_idx = search_result['query_idx']
                # Get details using this index
                place_details = recommender.get_place_by_id(place_idx)
                print(f"   Found by name: Index {place_idx} -> {place_details['nama']}")
        
        # 2. Fallback to ID if provided and Name lookup failed
        if place_idx is None and 'place_id' in data:
            print(f"   Searching by ID: {data['place_id']}")
            try:
                # Assuming the ID passed IS the index (fallback)
                potential_idx = int(data['place_id'])
                # Check if valid index
                if 0 <= potential_idx < len(recommender.df):
                    place_idx = potential_idx
                    place_details = recommender.get_place_by_id(place_idx)
                    print(f"   Using ID as Index: Index {place_idx} -> {place_details['nama']}")
                else:
                    print(f"   ID {potential_idx} is out of bounds (0-{len(recommender.df)-1})")
            except ValueError:
                pass
        
        if place_idx is None:
            print("   Place not found")
            return jsonify({
                'status': 'error',
                'message': 'Place not found. Provide valid place_name or place_id.'
            }), 404
        
        # Get recommendations using the resolved INDEX
        recommendations = recommender.get_recommendations(place_idx, top_n=top_n)
        
        return jsonify({
            'status': 'success',
            'data': {
                'place': place_details,
                'recommendations': recommendations,
                'total_recommendations': len(recommendations)
            }
        })
    except Exception as e:
        import traceback
        print(f"API ERROR: {str(e)}")
        traceback.print_exc()
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500

@app.route('/api/search', methods=['GET'])
def semantic_search():
    """
    Semantic search using IndoBERT similarity.
    Query params:
        - q: search query string
        - limit: max results (default: None = all results)
    """
    try:
        query = request.args.get('q', '', type=str)
        limit = request.args.get('limit', None, type=int)
        
        if not query:
            return jsonify({
                'status': 'error',
                'message': 'Query parameter "q" is required'
            }), 400
        
        results = recommender.semantic_search(query, top_n=limit)
        if isinstance(results, dict):
            if results.get('status') != 'success':
                return jsonify(results), 500
            results = results.get('data', [])
        
        return jsonify({
            'status': 'success',
            'query': query,
            'total': len(results),
            'data': results
        })
    except Exception as e:
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500

@app.errorhandler(404)
def not_found(error):
    return jsonify({
        'status': 'error',
        'message': 'Endpoint not found'
    }), 404

@app.errorhandler(500)
def internal_error(error):
    return jsonify({
        'status': 'error',
        'message': 'Internal server error'
    }), 500

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)
