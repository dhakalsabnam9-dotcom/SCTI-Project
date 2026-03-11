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
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? 0;
    
    if (empty($id)) {
        throw new Exception('Image ID is required');
    }
    
    // Get image file paths before deleting
    $stmt = $conn->prepare("SELECT file_path, thumbnail_path FROM gallery_images WHERE id = ? AND is_active = 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Image not found');
    }
    
    $image = $result->fetch_assoc();
    $stmt->close();
    
    // Soft delete (set is_active to 0)
    $deleteStmt = $conn->prepare("UPDATE gallery_images SET is_active = 0 WHERE id = ?");
    $deleteStmt->bind_param("i", $id);
    
    if (!$deleteStmt->execute()) {
        throw new Exception('Failed to delete image');
    }
    
    $deleteStmt->close();
    
    // Optionally delete physical files (uncomment if you want hard delete)
    /*
    $filePath = '../' . $image['file_path'];
    $thumbnailPath = '../' . $image['thumbnail_path'];
    
    if (file_exists($filePath)) {
        unlink($filePath);
    }
    
    if (file_exists($thumbnailPath)) {
        unlink($thumbnailPath);
    }
    */
    
    echo json_encode([
        'success' => true,
        'message' => 'Image deleted successfully'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
