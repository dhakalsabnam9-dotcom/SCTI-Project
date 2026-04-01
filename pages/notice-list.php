<?php
ob_start();
session_start();
require_once '../includes/config.php';
header('Content-Type: application/json');

try {
    $db       = getDBConnection();
    $status   = isset($_GET['status'])   ? $_GET['status']   : 'active';
    $audience = isset($_GET['audience']) ? $_GET['audience'] : '';
    $all      = ($status === 'all');

    if ($all && !$audience) {
        $stmt = $db->query("SELECT n.*, a.full_name as created_by_name FROM notices n LEFT JOIN admins a ON a.id=n.created_by ORDER BY n.created_at DESC");
    } elseif ($audience && !$all) {
        $stmt = $db->prepare("SELECT n.*, a.full_name as created_by_name FROM notices n LEFT JOIN admins a ON a.id=n.created_by WHERE n.status=? AND (n.audience=? OR n.audience='all') ORDER BY n.priority='urgent' DESC, n.created_at DESC");
        $stmt->execute([$status, $audience]);
    } elseif ($audience && $all) {
        $stmt = $db->prepare("SELECT n.*, a.full_name as created_by_name FROM notices n LEFT JOIN admins a ON a.id=n.created_by WHERE n.audience=? OR n.audience='all' ORDER BY n.created_at DESC");
        $stmt->execute([$audience]);
    } else {
        $stmt = $db->prepare("SELECT n.*, a.full_name as created_by_name FROM notices n LEFT JOIN admins a ON a.id=n.created_by WHERE n.status=? ORDER BY n.created_at DESC");
        $stmt->execute([$status]);
    }
    $notices = $stmt->fetchAll();
    ob_end_clean();
    echo json_encode(['success' => true, 'notices' => $notices]);
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
