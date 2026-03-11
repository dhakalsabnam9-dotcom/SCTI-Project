<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../includes/config.php';

try {
    $category = $_GET['category'] ?? '';
    
    // Build query - only show active images
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
    
    $sql .= " ORDER BY display_order ASC, created_at DESC";
    
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
        'message' => $e->getMessage(),
        'images' => [],
        'count' => 0
    ]);
}
?>
