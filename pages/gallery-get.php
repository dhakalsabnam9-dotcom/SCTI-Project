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
    $id = $_GET['id'] ?? 0;
    
    if (empty($id)) {
        throw new Exception('Image ID is required');
    }
    
    $stmt = $conn->prepare("SELECT id, title, description, file_path, thumbnail_path, category, created_at FROM gallery_images WHERE id = ? AND is_active = 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Image not found');
    }
    
    $image = $result->fetch_assoc();
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'image' => $image
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
