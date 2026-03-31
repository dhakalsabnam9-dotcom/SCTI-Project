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

    // Student count for a specific program+semester
    if (isset($_GET['count'])) {
        $prog = trim($_GET['program'] ?? '');
        $sem  = trim($_GET['semester'] ?? '');
        $semNum = intval(preg_replace('/[^0-9]/','',$sem));
        $stmt = $db->prepare("SELECT COUNT(*) FROM students WHERE status='active' AND course=? AND (semester=? OR semester=?)");
        $stmt->execute([$prog, $sem, $semNum]);
        ob_end_clean();
        echo json_encode(['count' => intval($stmt->fetchColumn())]);
        exit();
    }

    // Default: get distinct program+semester combos
    $rows = $db->query("SELECT DISTINCT course, semester FROM students WHERE status='active' AND course IS NOT NULL AND course != '' ORDER BY course, semester ASC")->fetchAll();
    $classes = [];
    foreach ($rows as $r) {
        $sem = trim($r['semester'] ?? '');
        if (is_numeric($sem)) $sem = 'Semester ' . $sem;
        $sem = preg_replace('/^Sem\s+Semester/i', 'Semester', $sem);
        $label = $r['course'] . ($sem ? ' ' . $sem : '');
        $classes[] = $label;
    }
    ob_end_clean();
    echo json_encode(['success'=>true,'classes'=>array_values(array_unique($classes))]);
} catch(Exception $e) {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
