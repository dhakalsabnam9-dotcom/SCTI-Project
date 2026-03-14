<?php
ob_start();
session_start();
require_once '../includes/config.php';
header('Content-Type: application/json');

try {
    $db = getDBConnection();
    $status = isset($_GET['status']) ? $_GET['status'] : 'active';
    $all    = ($status === 'all');

    if ($all) {
        $stmt = $db->query("SELECT * FROM notices ORDER BY created_at DESC");
    } else {
        $stmt = $db->prepare("SELECT * FROM notices WHERE status = ? ORDER BY created_at DESC");
        $stmt->execute([$status]);
    }
    $notices = $stmt->fetchAll();
    ob_end_clean();
    echo json_encode(['success' => true, 'notices' => $notices]);
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
