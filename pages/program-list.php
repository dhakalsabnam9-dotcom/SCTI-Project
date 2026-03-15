<?php
ob_start();
session_start();
require_once '../includes/config.php';
header('Content-Type: application/json');
try {
    $db     = getDBConnection();
    $status = isset($_GET['status']) ? $_GET['status'] : 'all';
    if ($status === 'all') {
        $stmt = $db->query("SELECT * FROM programs ORDER BY sort_order ASC, id ASC");
    } else {
        $stmt = $db->prepare("SELECT * FROM programs WHERE status=? ORDER BY sort_order ASC, id ASC");
        $stmt->execute([$status]);
    }
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ob_end_clean();
    echo json_encode(['success'=>true,'programs'=>$programs]);
} catch(Exception $e) {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
