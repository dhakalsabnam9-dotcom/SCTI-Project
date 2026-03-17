<?php
ob_start();
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
require_once '../includes/config.php';

$id = intval($_POST['id'] ?? 0);
if (!$id) { echo json_encode(['success'=>false,'message'=>'Invalid ID']); exit(); }

try {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT file_name FROM materials WHERE id=?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) { echo json_encode(['success'=>false,'message'=>'Material not found']); exit(); }

    $filePath = '../uploads/materials/' . $row['file_name'];
    if (file_exists($filePath)) unlink($filePath);

    $db->prepare("DELETE FROM materials WHERE id=?")->execute([$id]);
    echo json_encode(['success'=>true,'message'=>'Material deleted.']);
} catch(Exception $e) {
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
ob_end_flush();
