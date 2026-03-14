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
    sendJSON(['success' => false, 'message' => 'Unauthorized access']);
}

require_once '../includes/config.php';

try {
    $id = intval($_GET['id'] ?? 0);
    if (!$id) throw new Exception('Image ID is required');

    $db = getDBConnection();

    $stmt = $db->prepare("SELECT id, title, description, file_path, thumbnail_path, category, created_at FROM gallery_images WHERE id = ? AND is_active = 1");
    $stmt->execute([$id]);
    $image = $stmt->fetch();

    if (!$image) throw new Exception('Image not found');

    sendJSON(['success' => true, 'image' => $image]);

} catch (Exception $e) {
    sendJSON(['success' => false, 'message' => $e->getMessage()]);
}
?>
