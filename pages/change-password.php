<?php
ob_start();
session_start();
require_once('../includes/config.php');

header('Content-Type: application/json');

// Must be logged in as teacher or student
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit();
}

$userType = $_SESSION['user_type'] ?? '';
if (!in_array($userType, ['teacher', 'student'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized user type.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

$currentPassword = trim($_POST['current_password'] ?? '');
$newPassword     = trim($_POST['new_password'] ?? '');
$confirmPassword = trim($_POST['confirm_password'] ?? '');

if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit();
}

if (strlen($newPassword) < 6) {
    echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters.']);
    exit();
}

if ($newPassword !== $confirmPassword) {
    echo json_encode(['success' => false, 'message' => 'New passwords do not match.']);
    exit();
}

$table    = ($userType === 'teacher') ? 'teachers' : 'students';
$userId   = $_SESSION['user_id'];

try {
    $conn = getDBConnection();

    // Fetch current hashed password
    $stmt = $conn->prepare("SELECT password FROM $table WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
        exit();
    }

    if (!password_verify($currentPassword, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
        exit();
    }

    $newHashed = password_hash($newPassword, PASSWORD_DEFAULT);
    $upd = $conn->prepare("UPDATE $table SET password = :password WHERE id = :id");
    $upd->execute([':password' => $newHashed, ':id' => $userId]);

    echo json_encode(['success' => true, 'message' => 'Password changed successfully.']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error. Please try again.']);
}
ob_end_flush();
