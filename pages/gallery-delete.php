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
    $input = json_decode(file_get_contents('php://input'), true);
    $id = intval($input['id'] ?? 0);

    if (!$id) throw new Exception('Image ID is required');

    $db = getDBConnection();

    $stmt = $db->prepare("SELECT id FROM gallery_images WHERE id = ? AND is_active = 1");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) throw new Exception('Image not found');

    $del = $db->prepare("UPDATE gallery_images SET is_active = 0 WHERE id = ?");
    $del->execute([$id]);

    sendJSON(['success' => true, 'message' => 'Image deleted successfully']);

} catch (Exception $e) {
    sendJSON(['success' => false, 'message' => $e->getMessage()]);
}
?>
