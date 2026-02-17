<?php
// admin/delete_gallery.php
require_once 'auth.php';
require_once '../db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
    // Fetch filename for deletion
    $stmt = $conn->prepare("SELECT filename FROM gallery WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($filename);
    $fileFound = $stmt->fetch();
    $stmt->close();

    if ($fileFound && $filename) {
        $filePath = __DIR__ . '/../uploads/' . $filename;
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    // Delete DB row
    $stmt = $conn->prepare("DELETE FROM gallery WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

header('Location: admin_index.php');
exit;
