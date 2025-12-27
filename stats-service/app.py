import requests
import redis
import json
from flask import Flask, jsonify

app = Flask(__name__)

# Koneksi ke Redis service
cache = redis.Redis(host='stats-db', port=6379, decode_responses=True)

@app.route('/')
def index():
    return "Stats Service with Redis Caching is Running!"

@app.route('/summary')
def summary():
    try:
        # 1. Cek data di Cache Redis
        cached_total = cache.get('total_reports')
        
        if cached_total:
            return jsonify({
                "status": "Success (From Redis Cache)",
                "total_reports": int(cached_total),
                "source": "Redis"
            })

        # 2. Jika tidak ada di Redis, ambil dari Report Service (PHP)
        response = requests.get("http://report-service/list.php")
        data = response.json()
        total = len(data)

        # 3. Simpan hasil ke Redis selama 60 detik (Expiration time)
        cache.setex('total_reports', 60, total)

        return jsonify({
            "status": "Success (Fresh Data)",
            "total_reports": total,
            "source": "MySQL via PHP"
        })
    except Exception as e:
        return jsonify({"error": str(e)})

if __name__ == "__main__":
    app.run(host='0.0.0.0', port=5000)