<?php
$host = 'report-db'; // Nama service di docker-compose
$db   = 'db_report';
$user = 'root';
$pass = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ensure tables exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS reports (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description LONGTEXT,
            category VARCHAR(100) DEFAULT 'Umum',
            latitude DECIMAL(10, 8),
            longitude DECIMAL(11, 8),
            address VARCHAR(500),
            status VARCHAR(50) DEFAULT 'pending',
            photos LONGTEXT,
            user_id INT,
            user_name VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_status (status),
            INDEX idx_category (category),
            INDEX idx_created (created_at)
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS comments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            report_id INT NOT NULL,
            user VARCHAR(100),
            message LONGTEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (report_id) REFERENCES reports(id) ON DELETE CASCADE
        )
    ");
} catch (PDOException $e) {
    die("Koneksi MySQL Gagal: " . $e->getMessage());
}
