import requests
import json

url = "http://localhost:5000/api/recommendations"
payload = {"place_id": 10, "top_n": 5}
headers = {"Content-Type": "application/json"}

try:
    response = requests.post(url, json=payload, headers=headers)
    print(f"Status: {response.status_code}")
    print(response.json())
except Exception as e:
    print(f"Error: {e}")
