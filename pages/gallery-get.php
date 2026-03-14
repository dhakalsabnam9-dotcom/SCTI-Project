<?php
ob_start();
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
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

    ob_end_clean();
    echo json_encode(['success' => true, 'image' => $image]);

} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
