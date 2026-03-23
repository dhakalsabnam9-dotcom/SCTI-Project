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
    if (!file_exists($filePath)) {
        http_response_code(404);
        echo '<div style="font-family:sans-serif;text-align:center;padding:60px 20px">
            <div style="font-size:48px;margin-bottom:16px">📂</div>
            <h2 style="color:#dc3545">File Not Found</h2>
            <p style="color:#666">The file <strong>'.htmlspecialchars($row['title']).'</strong> has not been uploaded to the server yet.</p>
            <p style="color:#999;font-size:13px">Please ask your teacher to re-upload this material.</p>
            <a href="javascript:history.back()" style="display:inline-block;margin-top:16px;padding:10px 24px;background:#004080;color:white;border-radius:8px;text-decoration:none">Go Back</a>
        </div>';
        exit();
    }

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
