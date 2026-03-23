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
$duration    = trim($data['duration'] ?? '');
$semesters   = isset($data['semesters']) && $data['semesters'] !== '' ? (int)$data['semesters'] : null;
$affiliation = trim($data['affiliation'] ?? '');
$seats       = isset($data['seats']) && $data['seats'] !== '' ? (int)$data['seats'] : null;
$fee         = trim($data['fee'] ?? '');
$assessment  = trim($data['assessment'] ?? '');
$eligibility = trim($data['eligibility'] ?? '');
$description = trim($data['description'] ?? '');
$content     = trim($data['content'] ?? '');
$status      = $data['status'] ?? 'active';
$featured    = isset($data['featured']) ? (int)$data['featured'] : 0;

if (!$title || !$code) {
    ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Title and code required']); exit();
}
try {
    $db = getDBConnection();
    // Ensure new columns exist
    foreach (['semesters INT DEFAULT 6','fee VARCHAR(100)','seats INT','featured TINYINT(1) DEFAULT 0','eligibility TEXT'] as $col) {
        try { $db->exec("ALTER TABLE programs ADD COLUMN IF NOT EXISTS $col"); } catch(Exception $e) {}
    }
    if ($id > 0) {
        $stmt = $db->prepare("UPDATE programs SET title=?,code=?,icon=?,color=?,duration=?,semesters=?,affiliation=?,seats=?,fee=?,assessment=?,eligibility=?,description=?,content=?,status=?,featured=?,updated_at=NOW() WHERE id=?");
        $stmt->execute([$title,$code,$icon,$color,$duration,$semesters,$affiliation,$seats,$fee,$assessment,$eligibility,$description,$content,$status,$featured,$id]);
        $msg = 'Course updated';
    } else {
        $stmt = $db->prepare("INSERT INTO programs (title,code,icon,color,duration,semesters,affiliation,seats,fee,assessment,eligibility,description,content,status,featured) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$title,$code,$icon,$color,$duration,$semesters,$affiliation,$seats,$fee,$assessment,$eligibility,$description,$content,$status,$featured]);
        $id = $db->lastInsertId();
        $msg = 'Course created';
    }
    ob_end_clean();
    echo json_encode(['success'=>true,'message'=>$msg,'id'=>$id]);
} catch(Exception $e) {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
