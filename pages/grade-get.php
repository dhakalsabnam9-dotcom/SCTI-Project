<?php
ob_start();
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['teacher','student'])) {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
require_once '../includes/config.php';

$userType = $_SESSION['user_type'];

try {
    $db = getDBConnection();

    // ── Student mode: return this student's own grades as an array ────────────
    if ($userType === 'student') {
        $sid = intval($_SESSION['user_id']);
        $stmt = $db->prepare(
            "SELECT subject, exam_type, internal_marks, external_marks
             FROM grades WHERE student_id=? ORDER BY subject ASC"
        );
        $stmt->execute([$sid]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ob_end_clean();
        echo json_encode(['success'=>true,'grades'=>$rows]);
        exit();
    }

    // ── Teacher mode: return grade map for a subject/exam_type ───────────────
    $subject   = trim($_GET['subject']   ?? '');
    $exam_type = trim($_GET['exam_type'] ?? '');
    $semester  = trim($_GET['semester']  ?? '');

    if (!$subject || !$exam_type) {
        ob_end_clean();
        echo json_encode(['success'=>false,'message'=>'Subject and exam type required']); exit();
    }

    if ($semester) {
        $stmt = $db->prepare(
            "SELECT g.student_id, g.student_db_id, g.internal_marks, g.external_marks
             FROM grades g
             JOIN students s ON s.id = g.student_id
             WHERE g.subject=? AND g.exam_type=?
             AND (s.semester=? OR s.semester=?)"
        );
        $semNum = preg_replace('/[^0-9]/', '', $semester);
        $stmt->execute([$subject, $exam_type, $semester, $semNum]);
    } else {
        $stmt = $db->prepare(
            "SELECT student_id, student_db_id, internal_marks, external_marks
             FROM grades WHERE subject=? AND exam_type=?"
        );
        $stmt->execute([$subject, $exam_type]);
    }
    $rows = $stmt->fetchAll();

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
