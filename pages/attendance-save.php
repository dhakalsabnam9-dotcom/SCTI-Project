<?php
ob_start();
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
require_once '../includes/config.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) { ob_end_clean(); echo json_encode(['success'=>false,'message'=>'No data received']); exit(); }

$class_name  = trim($data['class_name'] ?? '');
$date        = trim($data['date'] ?? '');
$period      = trim($data['period'] ?? '') ?: null;  // normalize empty string to NULL
$records     = $data['records'] ?? [];
$teacher_id  = $_SESSION['user_id'] ?? null;

if (!$class_name || !$date || empty($records)) {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>'Class, date and records are required']); exit();
}

try {
    $db = getDBConnection();
    $saved = 0;

    foreach ($records as $rec) {
        $sid          = intval($rec['student_id'] ?? 0);
        $status       = in_array($rec['status'] ?? '', ['present','absent','late']) ? $rec['status'] : 'present';
        $remark       = trim($rec['remarks'] ?? '');
        $late_reason  = trim($rec['late_reason'] ?? '');
        $absent_reason= trim($rec['absent_reason'] ?? '');

        if (!$sid) continue;

        // Use INSERT ... ON DUPLICATE KEY UPDATE to handle unique constraint gracefully
        $upsert = $db->prepare("INSERT INTO attendance (student_id, class_name, attendance_date, period, status, remarks, late_reason, absent_reason, marked_by)
                                VALUES (?,?,?,?,?,?,?,?,?)
                                ON DUPLICATE KEY UPDATE
                                  status=VALUES(status),
                                  remarks=VALUES(remarks),
                                  late_reason=VALUES(late_reason),
                                  absent_reason=VALUES(absent_reason),
                                  class_name=VALUES(class_name),
                                  period=VALUES(period),
                                  marked_by=VALUES(marked_by)");
        $upsert->execute([$sid, $class_name, $date, $period, $status, $remark, $late_reason, $absent_reason, $teacher_id]);
        $saved++;
    }

    ob_end_clean();
    echo json_encode(['success'=>true,'message'=>'Attendance saved for '.$saved.' student(s)']);
} catch(Exception $e) {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
