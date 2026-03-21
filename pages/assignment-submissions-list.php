<?php
ob_start();
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
require_once '../includes/config.php';

$aid = intval($_GET['assignment_id'] ?? 0);
if (!$aid) { ob_end_clean(); echo json_encode(['success'=>false,'message'=>'assignment_id required']); exit(); }

try {
    $db  = getDBConnection();
    $tid = intval($_SESSION['user_id'] ?? 0);

    // Verify ownership
    $chk = $db->prepare("SELECT id FROM assignments WHERE id=? AND created_by=?");
    $chk->execute([$aid, $tid]);
    if (!$chk->fetch()) { ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Not authorized']); exit(); }

    // All active students
    $students = $db->query("SELECT id, full_name, student_id, course, semester FROM students WHERE status='active' ORDER BY full_name ASC")->fetchAll();

    // Submissions for this assignment
    $stmt = $db->prepare("SELECT s.*, st.full_name, st.student_id as sid FROM assignment_submissions s JOIN students st ON st.id=s.student_id WHERE s.assignment_id=?");
    $stmt->execute([$aid]);
    $subs = $stmt->fetchAll();

    ob_end_clean();
    echo json_encode(['success'=>true,'submissions'=>$subs,'all_students'=>$students]);
} catch(Exception $e) {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
