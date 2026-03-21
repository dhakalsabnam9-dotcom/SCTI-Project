<?php
ob_start();
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
require_once '../includes/config.php';
$data = json_decode(file_get_contents('php://input'), true);
$assignmentId = intval($data['assignment_id'] ?? 0);
$studentId    = intval($_SESSION['user_id']);
if (!$assignmentId) { ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Invalid assignment']); exit(); }
try {
    $db = getDBConnection();
    // Check deadline — cannot delete after deadline
    $asgn = $db->prepare("SELECT due_date FROM assignments WHERE id=? LIMIT 1");
    $asgn->execute([$assignmentId]);
    $row = $asgn->fetch();
    if (!$row) { ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Assignment not found']); exit(); }
    if (strtotime($row['due_date']) < time()) {
        ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Deadline has passed. You cannot delete your submission.']); exit();
    }
    $stmt = $db->prepare("DELETE FROM assignment_submissions WHERE assignment_id=? AND student_id=?");
    $stmt->execute([$assignmentId, $studentId]);
    ob_end_clean(); echo json_encode(['success'=>true,'message'=>'Submission deleted. You can resubmit before the deadline.']);
} catch(Exception $e) {
    ob_end_clean(); echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
