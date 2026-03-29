<?php
ob_start();
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
require_once '../includes/config.php';
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) $data = $_POST;

$id          = isset($data['id']) ? intval($data['id']) : 0;
$title       = trim($data['title'] ?? '');
$class_name  = trim($data['class_name'] ?? '');
$description = trim($data['description'] ?? '');
$due_date    = trim($data['due_date'] ?? '');
$assign_date = trim($data['assign_date'] ?? '');
$total_points= intval($data['total_points'] ?? 100);
$status      = in_array($data['status']??'', ['active','upcoming','graded','pending']) ? $data['status'] : 'active';
$teacher_id  = intval($_SESSION['user_id'] ?? 0);

if (!$title || !$class_name || !$due_date) {
    ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Title, class and deadline are required']); exit();
}

// Normalize datetime
if (strlen($due_date) === 10) $due_date .= ' 23:59:00';
else $due_date = str_replace('T', ' ', $due_date);

if ($assign_date) {
    if (strlen($assign_date) === 10) $assign_date .= ' 00:00:00';
    else $assign_date = str_replace('T', ' ', $assign_date);
} else {
    $assign_date = date('Y-m-d H:i:s');
}

try {
    $db = getDBConnection();
    if ($id > 0) {
        // Only teacher who created it can edit
        $chk = $db->prepare("SELECT id FROM assignments WHERE id=? AND created_by=?");
        $chk->execute([$id, $teacher_id]);
        if (!$chk->fetch()) { ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Not authorized to edit this assignment']); exit(); }

        $stmt = $db->prepare("UPDATE assignments SET title=?,class_name=?,description=?,due_date=?,assign_date=?,total_points=?,status=?,updated_at=NOW() WHERE id=?");
        $stmt->execute([$title,$class_name,$description,$due_date,$assign_date,$total_points,$status,$id]);
        ob_end_clean(); echo json_encode(['success'=>true,'message'=>'Assignment updated','id'=>$id]);
    } else {
        $stmt = $db->prepare("INSERT INTO assignments (title,class_name,description,due_date,assign_date,total_points,status,created_by) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$title,$class_name,$description,$due_date,$assign_date,$total_points,$status,$teacher_id]);
        ob_end_clean(); echo json_encode(['success'=>true,'message'=>'Assignment created','id'=>$db->lastInsertId()]);
    }
} catch(Exception $e) {
    ob_end_clean(); echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
