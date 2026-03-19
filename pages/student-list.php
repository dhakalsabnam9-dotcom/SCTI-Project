<?php
ob_start();
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
require_once '../includes/config.php';
header('Content-Type: application/json');
try {
    $db = getDBConnection();
    $rows = $db->query("SELECT id, full_name, student_id, username, email, phone, course as program, semester, address, status, created_at FROM students ORDER BY id DESC")->fetchAll();
    ob_end_clean();
    echo json_encode(['success'=>true,'students'=>$rows]);
} catch(Exception $e) {
    ob_end_clean(); echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
