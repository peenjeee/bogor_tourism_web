import requests
import json

url = "http://localhost:5000/api/search"
params = {"q": "wisata air terjun yang sejuk", "limit": 5}

try:
    response = requests.get(url, params=params)
    print(f"Status: {response.status_code}")
    data = response.json()
    print("Total Results:", data.get('total'))
    for item in data.get('data', []):
        print(f"ID: {item['id']} | Na: {item['nama']}")
except Exception as e:
    print(f"Error: {e}")
