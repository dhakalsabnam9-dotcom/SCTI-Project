<?php
ob_start();
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
require_once '../includes/config.php';

$subject   = trim($_GET['subject']   ?? '');
$exam_type = trim($_GET['exam_type'] ?? '');

if (!$subject || !$exam_type) {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>'Subject and exam type required']); exit();
}

try {
    $db = getDBConnection();
    $stmt = $db->prepare(
        "SELECT student_id, student_db_id, internal_marks, external_marks
         FROM grades WHERE subject=? AND exam_type=?"
    );
    $stmt->execute([$subject, $exam_type]);
    $rows = $stmt->fetchAll();

    // Key by student_id for easy lookup
    $map = [];
    foreach ($rows as $r) {
        $map[$r['student_id']] = [
            'internal' => $r['internal_marks'],
            'external' => $r['external_marks']
        ];
    }
    ob_end_clean();
    echo json_encode(['success'=>true,'grades'=>$map]);
} catch(Exception $e) {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
