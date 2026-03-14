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
    $id    = intval($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $desc  = trim($_POST['description'] ?? '');
    $cat   = trim($_POST['category'] ?? '') ?: null;

    if (!$id)    throw new Exception('Image ID is required');
    if (!$title) throw new Exception('Title is required');

    $db = getDBConnection();

    $check = $db->prepare("SELECT id FROM gallery_images WHERE id = ? AND is_active = 1");
    $check->execute([$id]);
    if (!$check->fetch()) throw new Exception('Image not found');

    $stmt = $db->prepare("UPDATE gallery_images SET title = ?, description = ?, category = ? WHERE id = ? AND is_active = 1");
    $stmt->execute([$title, $desc, $cat, $id]);

    sendJSON(['success' => true, 'message' => 'Updated successfully']);

} catch (Exception $e) {
    sendJSON(['success' => false, 'message' => $e->getMessage()]);
}
?>
