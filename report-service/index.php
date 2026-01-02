<?php
include 'db.php';

header('Content-Type: application/json');

// Endpoint untuk mendapatkan list laporan
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $sql = "SELECT * FROM reports ORDER BY id DESC LIMIT 100";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $reports = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Dapatkan jumlah komentar
            $commentSql = "SELECT COUNT(*) as count FROM comments WHERE report_id = ?";
            $commentStmt = $pdo->prepare($commentSql);
            $commentStmt->execute([$row['id']]);
            $commentCount = $commentStmt->fetch(PDO::FETCH_ASSOC)['count'];

            $row['comment_count'] = $commentCount;

            // Parse photos dari JSON atau string
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
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Endpoint untuk membuat laporan baru
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['REQUEST_URI'], '/create.php') !== false) {
    try {
        $title = $_POST['title'] ?? null;
        $description = $_POST['description'] ?? '';
        $category = $_POST['category'] ?? 'Umum';
        $latitude = $_POST['latitude'] ?? 0;
        $longitude = $_POST['longitude'] ?? 0;
        $address = $_POST['address'] ?? '';
        $user_id = $_POST['user_id'] ?? '0';
        $user_name = $_POST['user_name'] ?? 'Anonim';

        if (!$title) {
            http_response_code(400);
            echo json_encode(['error' => 'Title is required']);
            exit;
        }

        // Proses upload foto
        $photos = [];
        if (!empty($_FILES['photos'])) {
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $photoFiles = $_FILES['photos'];
            $fileCount = count($photoFiles['name']);

            for ($i = 0; $i < $fileCount; $i++) {
                if ($photoFiles['error'][$i] === UPLOAD_ERR_OK) {
                    $fileName = time() . '_' . $i . '_' . basename($photoFiles['name'][$i]);
                    $filePath = $uploadDir . $fileName;

                    if (move_uploaded_file($photoFiles['tmp_name'][$i], $filePath)) {
                        $photos[] = '/uploads/' . $fileName;
                    }
                }
            }
        }

        $photosJson = json_encode($photos);

        $sql = "INSERT INTO reports (title, description, category, latitude, longitude, address, status, photos, user_id, user_name, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $description, $category, $latitude, $longitude, $address, 'pending', $photosJson, $user_id, $user_name]);

        $reportId = $pdo->lastInsertId();

        http_response_code(200);
        echo json_encode([
            'id' => $reportId,
            'message' => 'Report created successfully',
            'title' => $title,
            'photos' => $photos
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Endpoint untuk detail laporan
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    try {
        $id = $_GET['id'];
        $sql = "SELECT * FROM reports WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        $report = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($report) {
            // Dapatkan jumlah komentar
            $commentSql = "SELECT COUNT(*) as count FROM comments WHERE report_id = ?";
            $commentStmt = $pdo->prepare($commentSql);
            $commentStmt->execute([$id]);
            $report['comment_count'] = $commentStmt->fetch(PDO::FETCH_ASSOC)['count'];

            // Parse photos
            if (!empty($report['photos'])) {
                $report['photos'] = json_decode($report['photos'], true) ?? explode(',', $report['photos']);
            } else {
                $report['photos'] = [];
            }

            echo json_encode($report);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Report not found']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Default response
else {
    http_response_code(200);
    echo json_encode(['message' => 'Report Service Ready']);
}
