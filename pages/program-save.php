<?php
ob_start();
session_start();
require_once '../includes/config.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) $data = $_POST;
$id          = isset($data['id']) ? (int)$data['id'] : 0;
$title       = trim($data['title'] ?? '');
$code        = trim($data['code'] ?? '');
$icon        = trim($data['icon'] ?? 'fa-graduation-cap');
$color       = trim($data['color'] ?? 'blue');
$duration    = trim($data['duration'] ?? '3 years');
$affiliation = trim($data['affiliation'] ?? 'CTEVT');
$assessment  = trim($data['assessment'] ?? '50% Internal + 50% External');
$description = trim($data['description'] ?? '');
$content     = trim($data['content'] ?? '');
$status      = $data['status'] ?? 'active';
if (!$title || !$code) {
    ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Title and code required']); exit();
}
try {
    $db = getDBConnection();
    if ($id > 0) {
        $stmt = $db->prepare("UPDATE programs SET title=?,code=?,icon=?,color=?,duration=?,affiliation=?,assessment=?,description=?,content=?,status=?,updated_at=NOW() WHERE id=?");
        $stmt->execute([$title,$code,$icon,$color,$duration,$affiliation,$assessment,$description,$content,$status,$id]);
        $msg = 'Program updated';
    } else {
        $stmt = $db->prepare("INSERT INTO programs (title,code,icon,color,duration,affiliation,assessment,description,content,status) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$title,$code,$icon,$color,$duration,$affiliation,$assessment,$description,$content,$status]);
        $id = $db->lastInsertId();
        $msg = 'Program created';
    }
    ob_end_clean();
    echo json_encode(['success'=>true,'message'=>$msg,'id'=>$id]);
} catch(Exception $e) {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
