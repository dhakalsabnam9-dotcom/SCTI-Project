<?php
session_start();
if (!isset($_SESSION['user_type'])) { header('Location: ../index.php'); exit(); }
require_once '../includes/config.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) { echo 'Invalid request'; exit(); }

try {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM materials WHERE id=?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) { echo 'File not found'; exit(); }

    $filePath = '../uploads/materials/' . $row['file_name'];
    if (!file_exists($filePath)) { echo 'File missing on server'; exit(); }

    // Increment download count
    $db->prepare("UPDATE materials SET downloads = downloads + 1 WHERE id=?")->execute([$id]);

    $mime = mime_content_type($filePath) ?: 'application/octet-stream';
    header('Content-Type: ' . $mime);
    header('Content-Disposition: attachment; filename="' . $row['title'] . '.' . pathinfo($row['file_name'], PATHINFO_EXTENSION) . '"');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit();
} catch(Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
