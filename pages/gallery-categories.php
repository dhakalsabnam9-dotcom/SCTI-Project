<?php
ob_start();
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit();
}

require_once '../includes/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    $db = getDBConnection();

    $db->exec("CREATE TABLE IF NOT EXISTS gallery_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    if ($method === 'GET') {
        $stmt = $db->query("SELECT * FROM gallery_categories ORDER BY name ASC");
        $cats = $stmt->fetchAll();
        ob_end_clean();
        echo json_encode(['success' => true, 'categories' => $cats]);

    } elseif ($method === 'POST' && $action === 'add') {
        $name = trim($_POST['name'] ?? '');
        if (empty($name)) throw new Exception('Category name is required');
        $stmt = $db->prepare("INSERT INTO gallery_categories (name) VALUES (?)");
        $stmt->execute([$name]);
        $id = $db->lastInsertId();
        ob_end_clean();
        echo json_encode(['success' => true, 'id' => $id, 'name' => $name]);

    } elseif ($method === 'POST' && $action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        if (!$id) throw new Exception('Invalid category ID');
        $stmt = $db->prepare("DELETE FROM gallery_categories WHERE id = ?");
        $stmt->execute([$id]);
        ob_end_clean();
        echo json_encode(['success' => true]);

    } else {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }

} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
