<?php
ob_start();
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
require_once '../includes/config.php';
header('Content-Type: application/json');
try {
    $db   = getDBConnection();
    $stmt = $db->query("SELECT id, full_name as name, username, email, teacher_id, department, phone, qualification, experience, subjects, status, created_at FROM teachers ORDER BY id DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ob_end_clean();
    echo json_encode(['success'=>true,'teachers'=>$rows]);
} catch(Exception $e) {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
