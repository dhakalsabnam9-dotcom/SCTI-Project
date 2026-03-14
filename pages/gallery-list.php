<?php
ob_start();
session_start();

function sendJSON($data) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    sendJSON(['success' => false, 'message' => 'Unauthorized']);
}

require_once '../includes/config.php';

try {
    $category = trim($_GET['category'] ?? '');
    $search   = trim($_GET['search'] ?? '');

    $db = getDBConnection();

    $db->exec("CREATE TABLE IF NOT EXISTS gallery_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        file_path VARCHAR(500) NOT NULL,
        thumbnail_path VARCHAR(500),
        category VARCHAR(100),
        is_active TINYINT(1) DEFAULT 1,
        created_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $sql    = "SELECT id, title, description, file_path, thumbnail_path, category, created_at
               FROM gallery_images WHERE is_active = 1";
    $params = [];

    if ($category !== '') {
        $sql .= " AND category = ?";
        $params[] = $category;
    }
    if ($search !== '') {
        $sql .= " AND (title LIKE ? OR description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    $sql .= " ORDER BY created_at DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $images = $stmt->fetchAll();

    sendJSON(['success' => true, 'images' => $images, 'count' => count($images)]);

} catch (Exception $e) {
    sendJSON(['success' => false, 'message' => $e->getMessage()]);
}
?>
