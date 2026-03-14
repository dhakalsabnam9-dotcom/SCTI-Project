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

$method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    $db = getDBConnection();

    $db->exec("CREATE TABLE IF NOT EXISTS gallery_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    if ($method === 'GET') {
        $stmt = $db->query("SELECT * FROM gallery_categories ORDER BY name ASC");
        sendJSON(['success' => true, 'categories' => $stmt->fetchAll()]);

    } elseif ($method === 'POST' && $action === 'add') {
        $name = trim($_POST['name'] ?? '');
        if (empty($name)) throw new Exception('Category name is required');
        $stmt = $db->prepare("INSERT INTO gallery_categories (name) VALUES (?)");
        $stmt->execute([$name]);
        sendJSON(['success' => true, 'id' => $db->lastInsertId(), 'name' => $name]);

    } elseif ($method === 'POST' && $action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        if (!$id) throw new Exception('Invalid category ID');
        $stmt = $db->prepare("DELETE FROM gallery_categories WHERE id = ?");
        $stmt->execute([$id]);
        sendJSON(['success' => true]);

    } else {
        sendJSON(['success' => false, 'message' => 'Invalid request']);
    }

} catch (Exception $e) {
    sendJSON(['success' => false, 'message' => $e->getMessage()]);
}
?>
