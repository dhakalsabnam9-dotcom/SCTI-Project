<?php
ob_start();
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    ob_end_clean(); echo json_encode(['success'=>false,'count'=>0]); exit();
}
require_once '../includes/config.php';
try {
    $db = getDBConnection();
    // Try gallery_images first, fall back to gallery table
    try {
        $count = $db->query("SELECT COUNT(*) FROM gallery_images WHERE is_active=1")->fetchColumn();
    } catch(Exception $e) {
        try {
            $count = $db->query("SELECT COUNT(*) FROM gallery")->fetchColumn();
        } catch(Exception $e2) {
            $count = 0;
        }
    }
    ob_end_clean();
    echo json_encode(['success'=>true,'count'=>intval($count)]);
} catch(Exception $e) {
    ob_end_clean();
    echo json_encode(['success'=>false,'count'=>0]);
}
