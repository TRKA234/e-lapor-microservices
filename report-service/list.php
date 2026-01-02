<?php
header('Content-Type: application/json');
include 'db.php';

try {
    // Query dengan filter & pagination
    $category = $_GET['category'] ?? null;
    $status = $_GET['status'] ?? null;
    $search = $_GET['search'] ?? null;
    $limit = intval($_GET['limit'] ?? 100);
    $offset = intval($_GET['offset'] ?? 0);

    $query = "SELECT * FROM reports WHERE 1=1";
    $params = [];

    if ($category && $category !== 'Semua') {
        $query .= " AND category = ?";
        $params[] = $category;
    }

    if ($status && $status !== 'Semua') {
        $query .= " AND status = ?";
        $params[] = $status;
    }

    if ($search) {
        $query .= " AND (title LIKE ? OR description LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    $query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    $reports = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Dapatkan jumlah komentar
        $commentSql = "SELECT COUNT(*) as count FROM comments WHERE report_id = ?";
        $commentStmt = $pdo->prepare($commentSql);
        $commentStmt->execute([$row['id']]);
        $row['comment_count'] = $commentStmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Parse photos
        if (!empty($row['photos'])) {
            $row['photos'] = json_decode($row['photos'], true) ?? explode(',', $row['photos']);
        } else {
            $row['photos'] = [];
        }

        $reports[] = $row;
    }

    echo json_encode($reports);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
