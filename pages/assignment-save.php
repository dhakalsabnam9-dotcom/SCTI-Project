<?php
ob_start();
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
require_once '../includes/config.php';
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) $data = $_POST;

$id          = isset($data['id']) ? intval($data['id']) : 0;
$title       = trim($data['title'] ?? '');
$class_name  = trim($data['class_name'] ?? '');
$description = trim($data['description'] ?? '');
$due_date    = trim($data['due_date'] ?? '');
$total_points= intval($data['total_points'] ?? 100);
$status      = in_array($data['status']??'', ['active','upcoming','graded','pending']) ? $data['status'] : 'active';

if (!$title || !$class_name || !$due_date) {
    echo json_encode(['success'=>false,'message'=>'Title, class and due date are required']); exit();
}

try {
    $db = getDBConnection();
    if ($id > 0) {
        $stmt = $db->prepare("UPDATE assignments SET title=?,class_name=?,description=?,due_date=?,total_points=?,status=?,updated_at=NOW() WHERE id=?");
        $stmt->execute([$title,$class_name,$description,$due_date,$total_points,$status,$id]);
        echo json_encode(['success'=>true,'message'=>'Assignment updated','id'=>$id]);
    } else {
        $stmt = $db->prepare("INSERT INTO assignments (title,class_name,description,due_date,total_points,status,created_by) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$title,$class_name,$description,$due_date,$total_points,$status,$_SESSION['user_id']??null]);
        echo json_encode(['success'=>true,'message'=>'Assignment created','id'=>$db->lastInsertId()]);
    }
} catch(Exception $e) {
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
