<?php
// TEMPORARY DEBUG FILE - DELETE AFTER FIXING
session_start();
header('Content-Type: text/plain');

echo "=== GALLERY DEBUG ===\n\n";
echo "Session user_type: " . ($_SESSION['user_type'] ?? 'NOT SET') . "\n";
echo "PHP version: " . PHP_VERSION . "\n\n";

require_once '../includes/config.php';

echo "Config loaded OK\n";

try {
    $db = getDBConnection();
    echo "DB connection OK\n";

    // Check if tables exist
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables in DB: " . implode(', ', $tables) . "\n\n";

    // Check gallery_images
    if (in_array('gallery_images', $tables)) {
        $count = $db->query("SELECT COUNT(*) FROM gallery_images WHERE is_active=1")->fetchColumn();
        echo "gallery_images rows (active): $count\n";
    } else {
        echo "gallery_images table: DOES NOT EXIST\n";
    }

    // Check gallery_categories
    if (in_array('gallery_categories', $tables)) {
        $count = $db->query("SELECT COUNT(*) FROM gallery_categories")->fetchColumn();
        echo "gallery_categories rows: $count\n";
    } else {
        echo "gallery_categories table: DOES NOT EXIST\n";
    }

    // Check uploads folder
    $uploadDir = '../uploads/gallery/';
    echo "\nuploads/gallery/ exists: " . (is_dir($uploadDir) ? 'YES' : 'NO') . "\n";
    echo "uploads/gallery/ writable: " . (is_writable($uploadDir) ? 'YES' : 'NO') . "\n";

    // Check GD extension
    echo "\nGD extension loaded: " . (extension_loaded('gd') ? 'YES' : 'NO') . "\n";
    echo "fileinfo extension loaded: " . (extension_loaded('fileinfo') ? 'YES' : 'NO') . "\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
