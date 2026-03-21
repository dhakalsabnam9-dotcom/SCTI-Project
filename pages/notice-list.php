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
        $stmt = $db->query("SELECT * FROM notices ORDER BY created_at DESC");
    } elseif ($audience && !$all) {
        // audience filter: show notices for this audience OR 'all'
        $stmt = $db->prepare("SELECT * FROM notices WHERE status=? AND (audience=? OR audience='all') ORDER BY priority='urgent' DESC, created_at DESC");
        $stmt->execute([$status, $audience]);
    } elseif ($audience && $all) {
        $stmt = $db->prepare("SELECT * FROM notices WHERE audience=? OR audience='all' ORDER BY created_at DESC");
        $stmt->execute([$audience]);
    } else {
        $stmt = $db->prepare("SELECT * FROM notices WHERE status=? ORDER BY created_at DESC");
        $stmt->execute([$status]);
    }
    $notices = $stmt->fetchAll();
    ob_end_clean();
    echo json_encode(['success' => true, 'notices' => $notices]);
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
