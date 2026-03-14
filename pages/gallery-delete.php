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
    $input = json_decode(file_get_contents('php://input'), true);
    $id = intval($input['id'] ?? 0);

    if (!$id) throw new Exception('Image ID is required');

    $db = getDBConnection();

    $stmt = $db->prepare("SELECT file_path, thumbnail_path FROM gallery_images WHERE id = ? AND is_active = 1");
    $stmt->execute([$id]);
    $image = $stmt->fetch();

    if (!$image) throw new Exception('Image not found');

    $del = $db->prepare("UPDATE gallery_images SET is_active = 0 WHERE id = ?");
    $del->execute([$id]);

    ob_end_clean();
    echo json_encode(['success' => true, 'message' => 'Image deleted successfully']);

} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
