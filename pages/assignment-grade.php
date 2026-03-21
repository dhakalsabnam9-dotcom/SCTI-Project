<?php
ob_start();
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
require_once '../includes/config.php';
$data = json_decode(file_get_contents('php://input'), true);
$sub_id   = intval($data['submission_id'] ?? 0);
$grade    = trim($data['grade'] ?? '');
$feedback = trim($data['feedback'] ?? '');
if (!$sub_id || $grade === '') { ob_end_clean(); echo json_encode(['success'=>false,'message'=>'submission_id and grade required']); exit(); }
try {
    $db = getDBConnection();
    $stmt = $db->prepare("UPDATE assignment_submissions SET grade=?, feedback=?, status='graded', graded_at=NOW() WHERE id=?");
    $stmt->execute([$grade, $feedback, $sub_id]);
    ob_end_clean();
    echo json_encode(['success'=>true,'message'=>'Grade saved']);
} catch(Exception $e) {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
