<?php
ob_start();
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
require_once '../includes/config.php';

$date  = trim($_GET['date']  ?? '');
$class = trim($_GET['class'] ?? '');
$tid   = $_SESSION['user_id'] ?? 0;

try {
    $db = getDBConnection();
    $sql = "SELECT a.*, s.full_name as student_name
            FROM attendance a
            LEFT JOIN students s ON s.id = a.student_id
            WHERE a.marked_by = ?";
    $params = [$tid];
    if ($date)  { $sql .= " AND a.attendance_date = ?"; $params[] = $date; }
    if ($class) { $sql .= " AND a.class_name LIKE ?";   $params[] = '%'.$class.'%'; }
    $sql .= " ORDER BY a.attendance_date DESC, s.full_name ASC LIMIT 200";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    ob_end_clean();
    echo json_encode(['success'=>true,'records'=>$rows]);
} catch(Exception $e) {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
