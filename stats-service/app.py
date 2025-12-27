import requests
from flask import Flask, jsonify

app = Flask(__name__)

@app.route('/')
def index():
    return "Stats Service (Python) Active"

@app.route('/summary')
def summary():
    try:
        # Python memanggil PHP menggunakan nama service Docker sebagai hostname
        response = requests.get("http://report-service/list.php")
        data = response.json()
        
        # Logika statistik sederhana
        total = len(data)
        
        return jsonify({
            "status": "Success",
            "total_reports": total,
            "message": f"Terdapat {total} laporan yang masuk ke sistem."
        })
    except Exception as e:
        return jsonify({"error": str(e), "msg": "Pastikan report-service/list.php sudah ada"})

if __name__ == "__main__":
    app.run(host='0.0.0.0', port=5000)