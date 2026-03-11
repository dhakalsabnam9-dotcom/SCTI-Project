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
    // Validate file upload
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error occurred');
    }
    
    $file = $_FILES['image'];
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    
    // Validate title
    if (empty($title)) {
        throw new Exception('Title is required');
    }
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($file['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed');
    }
    
    // Validate file size (10MB max)
    $maxSize = 10 * 1024 * 1024; // 10MB in bytes
    if ($file['size'] > $maxSize) {
        throw new Exception('File size exceeds 10MB limit');
    }
    
    // Create upload directory if it doesn't exist
    $uploadDir = '../uploads/gallery/';
    $thumbnailDir = '../uploads/gallery/thumbnails/';
    
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    if (!file_exists($thumbnailDir)) {
        mkdir($thumbnailDir, 0755, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('gallery_') . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    $thumbnailPath = $thumbnailDir . $filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Failed to move uploaded file');
    }
    
    // Create thumbnail
    createThumbnail($filepath, $thumbnailPath, 300, 300);
    
    // Store relative paths for database
    $dbFilePath = 'uploads/gallery/' . $filename;
    $dbThumbnailPath = 'uploads/gallery/thumbnails/' . $filename;
    
    // Insert into database
    $stmt = $conn->prepare("INSERT INTO gallery_images (title, description, file_path, thumbnail_path, category, created_by) VALUES (?, ?, ?, ?, ?, ?)");
    $userId = $_SESSION['user_id'] ?? null;
    $stmt->bind_param("sssssi", $title, $description, $dbFilePath, $dbThumbnailPath, $category, $userId);
    
    if (!$stmt->execute()) {
        // Delete uploaded files if database insert fails
        unlink($filepath);
        if (file_exists($thumbnailPath)) {
            unlink($thumbnailPath);
        }
        throw new Exception('Failed to save image information to database');
    }
    
    $imageId = $stmt->insert_id;
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'Image uploaded successfully',
        'image_id' => $imageId
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Function to create thumbnail
function createThumbnail($source, $destination, $maxWidth, $maxHeight) {
    $imageInfo = getimagesize($source);
    $mime = $imageInfo['mime'];
    
    // Create image resource based on type
    switch ($mime) {
        case 'image/jpeg':
            $sourceImage = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($source);
            break;
        case 'image/gif':
            $sourceImage = imagecreatefromgif($source);
            break;
        case 'image/webp':
            $sourceImage = imagecreatefromwebp($source);
            break;
        default:
            return false;
    }
    
    if (!$sourceImage) {
        return false;
    }
    
    // Get original dimensions
    $origWidth = imagesx($sourceImage);
    $origHeight = imagesy($sourceImage);
    
    // Calculate thumbnail dimensions
    $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
    $thumbWidth = round($origWidth * $ratio);
    $thumbHeight = round($origHeight * $ratio);
    
    // Create thumbnail
    $thumbnail = imagecreatetruecolor($thumbWidth, $thumbHeight);
    
    // Preserve transparency for PNG and GIF
    if ($mime == 'image/png' || $mime == 'image/gif') {
        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);
        $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
        imagefilledrectangle($thumbnail, 0, 0, $thumbWidth, $thumbHeight, $transparent);
    }
    
    // Resize image
    imagecopyresampled($thumbnail, $sourceImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $origWidth, $origHeight);
    
    // Save thumbnail
    switch ($mime) {
        case 'image/jpeg':
            imagejpeg($thumbnail, $destination, 85);
            break;
        case 'image/png':
            imagepng($thumbnail, $destination, 8);
            break;
        case 'image/gif':
            imagegif($thumbnail, $destination);
            break;
        case 'image/webp':
            imagewebp($thumbnail, $destination, 85);
            break;
    }
    
    // Free memory
    imagedestroy($sourceImage);
    imagedestroy($thumbnail);
    
    return true;
}
?>
