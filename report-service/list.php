<?php
header('Content-Type: application/json');
include 'db.php';

try {
    $stmt = $pdo->query("SELECT * FROM reports ORDER BY created_at DESC");
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($reports);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
