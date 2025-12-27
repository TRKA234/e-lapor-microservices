<?php
include 'db.php';

// Cek parameter 'title' (untuk GPS) atau 'lapor' (untuk tes awal)
$title = $_GET['title'] ?? $_GET['lapor'] ?? null;
$lat = $_GET['lat'] ?? 0;
$lng = $_GET['lng'] ?? 0;

if ($title) {
    try {
        $sql = "INSERT INTO reports (title, latitude, longitude) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $lat, $lng]);

        echo "<h1>Berhasil!</h1>";
        echo "Laporan: $title <br> Koordinat: $lat, $lng <br>";
        echo "<a href='index.php'>Kembali</a>";
    } catch (Exception $e) {
        echo "Gagal Simpan ke MySQL: " . $e->getMessage();
    }
} else {
    echo "<h1>Report Service (PHP) Ready</h1>";
    echo "Silakan coba link ini: <a href='?title=Jalan+Rusak&lat=-1.23&lng=116.8'>Klik untuk Tes Input GPS</a>";
}
