<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit();
}
require_once '../includes/config.php';
try {
    $id    = intval($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $desc  = trim($_POST['description'] ?? '');
    $cat   = trim($_POST['category'] ?? '');
    if (!$id)    throw new Exception('Image ID is required');
    if (!$title) throw new Exception('Title is required');
    $db   = getDBConnection();
    $stmt = $db->prepare("UPDATE gallery_images SET title=?, description=?, category=? WHERE id=? AND is_active=1");
    $stmt->execute([$title, $desc, $cat, $id]);
    if ($stmt->rowCount() === 0) throw new Exception('Image not found or no changes made');
    echo json_encode(['success' => true, 'message' => 'Updated successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
