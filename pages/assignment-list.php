<?php
ob_start();
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
require_once '../includes/config.php';
try {
    $db = getDBConnection();
    $stmt = $db->query("SELECT * FROM assignments ORDER BY created_at DESC");
    $rows = $stmt->fetchAll();
    echo json_encode(['success'=>true,'assignments'=>$rows]);
} catch(Exception $e) {
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
