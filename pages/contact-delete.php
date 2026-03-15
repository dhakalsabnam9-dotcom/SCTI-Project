<?php
ob_start();
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>'Unauthorized']);
    exit();
}
require_once '../includes/config.php';
header('Content-Type: application/json');
try {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = isset($input['id']) ? (int)$input['id'] : 0;
    if (!$id) { ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Invalid ID']); exit(); }
    $db = getDBConnection();
    $stmt = $db->prepare("DELETE FROM contacts WHERE id=?");
    $stmt->execute([$id]);
    ob_end_clean();
    echo json_encode(['success'=>true,'message'=>'Deleted']);
} catch(Exception $e) {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
