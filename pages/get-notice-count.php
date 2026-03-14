<?php
ob_start();
session_start();
require_once '../includes/config.php';
header('Content-Type: application/json');
try {
    $db   = getDBConnection();
    $stmt = $db->query("SELECT COUNT(*) as count FROM notices WHERE status = 'active'");
    $row  = $stmt->fetch();
    ob_end_clean();
    echo json_encode(['success' => true, 'count' => (int)$row['count']]);
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'count' => 0]);
}
