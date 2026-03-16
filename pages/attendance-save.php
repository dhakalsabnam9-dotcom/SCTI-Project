<?php
ob_start();
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
require_once '../includes/config.php';
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) { echo json_encode(['success'=>false,'message'=>'No data received']); exit(); }

$class_name = trim($data['class_name'] ?? '');
$date       = trim($data['date'] ?? '');
$period     = trim($data['period'] ?? '');
$records    = $data['records'] ?? [];

if (!$class_name || !$date || empty($records)) {
    echo json_encode(['success'=>false,'message'=>'Class, date and records are required']); exit();
}

try {
    $db = getDBConnection();
    $saved = 0;
    foreach ($records as $rec) {
        $sid    = intval($rec['student_id']);
        $sdbid  = trim($rec['student_db_id'] ?? '');
        $status = in_array($rec['status']??'', ['present','absent','late']) ? $rec['status'] : 'present';
        $remark = trim($rec['remarks'] ?? '');
        // INSERT or UPDATE
        $stmt = $db->prepare("INSERT INTO attendance (student_id, student_db_id, class_name, attendance_date, period, status, remarks, marked_by)
            VALUES (?,?,?,?,?,?,?,?)
            ON DUPLICATE KEY UPDATE status=VALUES(status), remarks=VALUES(remarks), updated_at=NOW()");
        $stmt->execute([$sid, $sdbid, $class_name, $date, $period, $status, $remark, $_SESSION['user_id']??null]);
        $saved++;
    }
    echo json_encode(['success'=>true,'message'=>'Attendance saved for '.$saved.' students']);
} catch(Exception $e) {
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
