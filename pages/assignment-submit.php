<?php
ob_start();
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
require_once '../includes/config.php';
header('Content-Type: application/json');

try {
    $studentId    = intval($_SESSION['user_id']);
    $assignmentId = intval($_POST['assignment_id'] ?? 0);
    $notes        = trim($_POST['notes'] ?? '');

    if (!$assignmentId) { ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Invalid assignment']); exit(); }

    $filePath = null; $fileName = null;

    // Handle file upload
    if (!empty($_FILES['file']['name'])) {
        $uploadDir = '../uploads/assignments/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $origName  = basename($_FILES['file']['name']);
        $ext       = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowed   = ['pdf','doc','docx','txt','jpg','jpeg','png','zip','rar'];
        if (!in_array($ext, $allowed)) {
            ob_end_clean(); echo json_encode(['success'=>false,'message'=>'File type not allowed']); exit();
        }
        if ($_FILES['file']['size'] > 10 * 1024 * 1024) {
            ob_end_clean(); echo json_encode(['success'=>false,'message'=>'File too large (max 10MB)']); exit();
        }
        $newName  = 'sub_'.$studentId.'_'.$assignmentId.'_'.time().'.'.$ext;
        $destPath = $uploadDir . $newName;
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $destPath)) {
            ob_end_clean(); echo json_encode(['success'=>false,'message'=>'File upload failed']); exit();
        }
        $filePath = 'uploads/assignments/'.$newName;
        $fileName = $origName;
    }

    $db = getDBConnection();

    // Check if already submitted
    $chk = $db->prepare("SELECT id FROM assignment_submissions WHERE assignment_id=? AND student_id=? LIMIT 1");
    $chk->execute([$assignmentId, $studentId]);
    $existing = $chk->fetch();

    // Check if late
    $asgn = $db->prepare("SELECT due_date FROM assignments WHERE id=? LIMIT 1");
    $asgn->execute([$assignmentId]);
    $row = $asgn->fetch();
    $status = ($row && strtotime($row['due_date']) < time()) ? 'late' : 'submitted';

    if ($existing) {
        // Update existing submission
        if ($filePath) {
            $stmt = $db->prepare("UPDATE assignment_submissions SET notes=?, file_path=?, file_name=?, status=?, submitted_at=NOW() WHERE assignment_id=? AND student_id=?");
            $stmt->execute([$notes, $filePath, $fileName, $status, $assignmentId, $studentId]);
        } else {
            $stmt = $db->prepare("UPDATE assignment_submissions SET notes=?, status=?, submitted_at=NOW() WHERE assignment_id=? AND student_id=?");
            $stmt->execute([$notes, $status, $assignmentId, $studentId]);
        }
        ob_end_clean(); echo json_encode(['success'=>true,'message'=>'Submission updated successfully']);
    } else {
        $stmt = $db->prepare("INSERT INTO assignment_submissions (assignment_id, student_id, file_path, file_name, notes, status) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$assignmentId, $studentId, $filePath, $fileName, $notes, $status]);
        ob_end_clean(); echo json_encode(['success'=>true,'message'=>'Assignment submitted successfully']);
    }
} catch(Exception $e) {
    ob_end_clean(); echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
