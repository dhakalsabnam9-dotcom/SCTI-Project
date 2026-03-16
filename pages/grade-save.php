<?php
ob_start();
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
require_once '../includes/config.php';
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) { echo json_encode(['success'=>false,'message'=>'No data']); exit(); }

$subject   = trim($data['subject'] ?? '');
$exam_type = trim($data['exam_type'] ?? '');
$records   = $data['records'] ?? [];

if (!$subject || empty($records)) {
    echo json_encode(['success'=>false,'message'=>'Subject and records required']); exit();
}

try {
    $db = getDBConnection();
    // Ensure grades table exists
    $db->exec("CREATE TABLE IF NOT EXISTS grades (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        student_db_id VARCHAR(50),
        subject VARCHAR(100) NOT NULL,
        exam_type VARCHAR(50),
        internal_marks DECIMAL(5,2) DEFAULT 0,
        external_marks DECIMAL(5,2) DEFAULT 0,
        marked_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_grade (student_id, subject, exam_type)
    )");
    $saved = 0;
    foreach ($records as $rec) {
        $sid    = intval($rec['student_id']);
        $sdbid  = trim($rec['student_db_id'] ?? '');
        $int    = floatval($rec['internal']);
        $ext    = floatval($rec['external']);
        $stmt = $db->prepare("INSERT INTO grades (student_id, student_db_id, subject, exam_type, internal_marks, external_marks, marked_by)
            VALUES (?,?,?,?,?,?,?)
            ON DUPLICATE KEY UPDATE internal_marks=VALUES(internal_marks), external_marks=VALUES(external_marks), updated_at=NOW()");
        $stmt->execute([$sid, $sdbid, $subject, $exam_type, $int, $ext, $_SESSION['user_id']??null]);
        $saved++;
    }
    echo json_encode(['success'=>true,'message'=>'Grades saved for '.$saved.' students']);
} catch(Exception $e) {
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
