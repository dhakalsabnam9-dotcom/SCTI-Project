<?php
ob_start();
session_start();
require_once '../includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) $data = $_POST;

$id          = isset($data['id']) ? (int)$data['id'] : 0;
$title       = trim($data['title'] ?? '');
$description = trim($data['description'] ?? '');
$category    = $data['category'] ?? 'general';
$priority    = $data['priority'] ?? 'normal';
$status      = $data['status'] ?? 'active';
$notice_date = $data['notice_date'] ?? date('Y-m-d');
$audience    = $data['audience'] ?? 'all';
$allowed_audiences = ['all','student','teacher','emergency'];
if (!in_array($audience, $allowed_audiences)) $audience = 'all';

if (!$title || !$description) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Title and description required']);
    exit();
}

try {
    $db = getDBConnection();
    if ($id > 0) {
        $stmt = $db->prepare("UPDATE notices SET title=?, description=?, category=?, audience=?, priority=?, status=?, notice_date=?, updated_at=NOW() WHERE id=?");
        $stmt->execute([$title, $description, $category, $audience, $priority, $status, $notice_date, $id]);
        $msg = 'Notice updated';
    } else {
        $admin_id = $_SESSION['user_id'] ?? null;
        $stmt = $db->prepare("INSERT INTO notices (title, description, category, audience, priority, status, notice_date, created_by) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$title, $description, $category, $audience, $priority, $status, $notice_date, $admin_id]);
        $id = $db->lastInsertId();
        $msg = 'Notice created';
    }
    ob_end_clean();
    echo json_encode(['success' => true, 'message' => $msg, 'id' => $id]);
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
