<?php
$host = 'report-db'; // Nama service di docker-compose
$db   = 'db_report';
$user = 'root';
$pass = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi MySQL Gagal: " . $e->getMessage());
}
