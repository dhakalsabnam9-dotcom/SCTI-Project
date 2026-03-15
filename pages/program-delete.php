<?php
ob_start();
session_start();
require_once '../includes/config.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
$data = json_decode(file_get_contents('php://input'), true);
$id   = isset($data['id']) ? (int)$data['id'] : 0;
if (!$id) {
    ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Invalid ID']); exit();
}
try {
    $db   = getDBConnection();
    $stmt = $db->prepare("DELETE FROM programs WHERE id=?");
    $stmt->execute([$id]);
    ob_end_clean();
    echo json_encode(['success'=>true,'message'=>'Program deleted']);
} catch(Exception $e) {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
