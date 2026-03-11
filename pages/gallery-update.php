<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

require_once '../includes/config.php';

try {
    $id = $_POST['id'] ?? 0;
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    
    if (empty($id)) {
        throw new Exception('Image ID is required');
    }
    
    if (empty($title)) {
        throw new Exception('Title is required');
    }
    
    // Check if image exists
    $checkStmt = $conn->prepare("SELECT id FROM gallery_images WHERE id = ? AND is_active = 1");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows === 0) {
        throw new Exception('Image not found');
    }
    $checkStmt->close();
    
    // Update image
    $stmt = $conn->prepare("UPDATE gallery_images SET title = ?, description = ?, category = ? WHERE id = ?");
    $stmt->bind_param("sssi", $title, $description, $category, $id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to update image');
    }
    
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'Image updated successfully'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
