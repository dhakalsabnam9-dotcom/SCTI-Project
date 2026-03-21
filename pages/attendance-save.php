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
$period      = trim($data['period'] ?? '');
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
        $sid         = intval($rec['student_id'] ?? 0);
        $status      = in_array($rec['status'] ?? '', ['present','absent','late']) ? $rec['status'] : 'present';
        $remark      = trim($rec['remarks'] ?? '');
        $late_reason = trim($rec['late_reason'] ?? '');

        if (!$sid) continue;

        $upd = $db->prepare("UPDATE attendance SET status=?, remarks=?, late_reason=?, class_name=?, period=?, marked_by=?
                              WHERE student_id=? AND attendance_date=?");
        $upd->execute([$status, $remark, $late_reason, $class_name, $period, $teacher_id, $sid, $date]);

        if ($upd->rowCount() === 0) {
            $ins = $db->prepare("INSERT INTO attendance (student_id, class_name, attendance_date, period, status, remarks, late_reason, marked_by)
                                 VALUES (?,?,?,?,?,?,?,?)");
            $ins->execute([$sid, $class_name, $date, $period, $status, $remark, $late_reason, $teacher_id]);
        }
        $saved++;
    }

    ob_end_clean();
    echo json_encode(['success'=>true,'message'=>'Attendance saved for '.$saved.' student(s)']);
} catch(Exception $e) {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
