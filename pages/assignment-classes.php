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
    $rows = $db->query("SELECT DISTINCT course, semester FROM students WHERE status='active' AND course IS NOT NULL AND course != '' ORDER BY course, semester ASC")->fetchAll();
    $classes = [];
    foreach ($rows as $r) {
        $sem = trim($r['semester'] ?? '');
        if (is_numeric($sem)) $sem = 'Semester ' . $sem;
        $label = $r['course'] . ($sem ? ' ' . $sem : '');
        $classes[] = $label;
    }
    ob_end_clean();
    echo json_encode(['success'=>true,'classes'=>array_values(array_unique($classes))]);
} catch(Exception $e) {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
