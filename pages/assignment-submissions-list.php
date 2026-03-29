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

    // Verify ownership and get class_name
    $chk = $db->prepare("SELECT id, class_name FROM assignments WHERE id=? AND created_by=?");
    $chk->execute([$aid, $tid]);
    $asgn = $chk->fetch();
    if (!$asgn) { ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Not authorized']); exit(); }

    // Only students matching the assignment's class
    $className = $asgn['class_name'];
    if (preg_match('/^(.+?)\s+Semester\s+(\d+)$/i', $className, $m)) {
        $course = trim($m[1]);
        $sem    = 'Semester ' . $m[2];
        $semNum = intval($m[2]);
        $stmt2  = $db->prepare("SELECT id, full_name, student_id, course, semester FROM students WHERE status='active' AND course=? AND (semester=? OR semester=?) ORDER BY full_name ASC");
        $stmt2->execute([$course, $sem, $semNum]);
    } else {
        $stmt2 = $db->prepare("SELECT id, full_name, student_id, course, semester FROM students WHERE status='active' AND course=? ORDER BY full_name ASC");
        $stmt2->execute([$className]);
    }
    $students = $stmt2->fetchAll();

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
