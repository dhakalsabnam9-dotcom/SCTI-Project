<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo json_encode(['success'=>false,'message'=>'Not authenticated']); exit();
}
if (!isset($_SESSION['first_login']) || !$_SESSION['first_login']) {
    echo json_encode(['success'=>false,'message'=>'Not a first login']); exit();
}

$userType = $_SESSION['user_type'] ?? '';
if (!in_array($userType, ['student','teacher'])) {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}

require_once '../includes/config.php';

$newPass  = trim($_POST['new_password'] ?? '');
$confPass = trim($_POST['confirm_password'] ?? '');

if (empty($newPass) || empty($confPass)) {
    echo json_encode(['success'=>false,'message'=>'Both fields are required']); exit();
}
if (strlen($newPass) < 6) {
    echo json_encode(['success'=>false,'message'=>'Password must be at least 6 characters']); exit();
}
if ($newPass !== $confPass) {
    echo json_encode(['success'=>false,'message'=>'Passwords do not match']); exit();
}

try {
    $table  = ($userType === 'teacher') ? 'teachers' : 'students';
    $hashed = password_hash($newPass, PASSWORD_BCRYPT);
    $db     = getDBConnection();
    $stmt   = $db->prepare("UPDATE $table SET password=?, updated_at=NOW() WHERE id=?");
    $stmt->execute([$hashed, $_SESSION['user_id']]);

    // Clear first_login flag
    unset($_SESSION['first_login']);

    echo json_encode(['success'=>true,'message'=>'Password updated successfully']);
} catch(Exception $e) {
    echo json_encode(['success'=>false,'message'=>'Server error. Try again.']);
}
