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
    $category = $_GET['category'] ?? '';
    $search = $_GET['search'] ?? '';
    
    // Build query
    $sql = "SELECT id, title, description, file_path, thumbnail_path, category, created_at 
            FROM gallery_images 
            WHERE is_active = 1";
    
    $params = [];
    $types = '';
    
    // Add category filter
    if (!empty($category)) {
        $sql .= " AND category = ?";
        $params[] = $category;
        $types .= 's';
    }
    
    // Add search filter
    if (!empty($search)) {
        $sql .= " AND (title LIKE ? OR description LIKE ?)";
        $searchTerm = '%' . $search . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= 'ss';
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    // Prepare and execute query
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $images = [];
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
    
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'images' => $images,
        'count' => count($images)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
