<?php
ob_start();
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
require_once '../includes/config.php';
try {
    $db  = getDBConnection();
    $tid = intval($_SESSION['user_id'] ?? 0);
    $stmt = $db->prepare("
        SELECT a.*,
               COUNT(DISTINCT s.id)         AS sub_count,
               COUNT(DISTINCT st.id)        AS total_students
        FROM assignments a
        LEFT JOIN assignment_submissions s  ON s.assignment_id = a.id
        LEFT JOIN students st               ON st.status = 'active'
        WHERE a.created_by = ?
        GROUP BY a.id
        ORDER BY a.created_at DESC
    ");
    $stmt->execute([$tid]);
    $rows = $stmt->fetchAll();
    ob_end_clean();
    echo json_encode(['success'=>true,'assignments'=>$rows]);
} catch(Exception $e) {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
