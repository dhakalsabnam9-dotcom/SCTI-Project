<?php
ob_start();
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
require_once '../includes/config.php';
try {
    $db = getDBConnection();
    $action = $_GET['action'] ?? 'subjects';

    if ($action === 'semesters') {
        // Get semesters for a given subject
        $subj = trim($_GET['subject'] ?? '');
        $rows = $db->prepare("SELECT DISTINCT semester FROM subjects WHERE name=? ORDER BY semester ASC");
        $rows->execute([$subj]);
        $sems = $rows->fetchAll(PDO::FETCH_COLUMN);
        ob_end_clean();
        echo json_encode(['success'=>true,'semesters'=>$sems]);
        exit();
    }

    // Default: get all subjects
    $rows = $db->query("SELECT DISTINCT name FROM subjects ORDER BY name ASC")->fetchAll(PDO::FETCH_COLUMN);
    ob_end_clean();
    echo json_encode(['success'=>true,'subjects'=>$rows]);
} catch(Exception $e) {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
?>
