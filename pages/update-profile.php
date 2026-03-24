<?php
ob_start();
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], ['teacher','student'])) {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
require_once '../includes/config.php';

$userId   = $_SESSION['user_id'] ?? 0;
$userType = $_SESSION['user_type'];
$table    = $userType === 'teacher' ? 'teachers' : 'students';

$fullName   = trim($_POST['full_name']        ?? '');
$phone      = trim($_POST['phone']            ?? '');
$address    = trim($_POST['address']          ?? '');

if (!$fullName) { echo json_encode(['success'=>false,'message'=>'Full name is required.']); exit(); }

try {
    $db = getDBConnection();

    if ($userType === 'teacher') {
        $dept    = trim($_POST['department']       ?? '');
        $desig   = trim($_POST['designation']      ?? '');
        $qual    = trim($_POST['qualification']    ?? '');
        $exp     = trim($_POST['experience_years'] ?? '');
        $stmt = $db->prepare("UPDATE teachers SET full_name=?, phone=?, address=?, department=?, designation=?, qualification=?, experience=? WHERE id=?");
        $stmt->execute([$fullName, $phone, $address, $dept, $desig, $qual, $exp ?: null, $userId]);
    } else {
        $qual = trim($_POST['qualification'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $stmt = $db->prepare("UPDATE students SET full_name=?, phone=?, address=?, qualification=?, email=? WHERE id=?");
        $stmt->execute([$fullName, $phone, $address, $qual, $email, $userId]);
    }

    // Update session name
    $_SESSION['full_name'] = $fullName;
    echo json_encode(['success'=>true,'message'=>'Profile updated successfully.']);
} catch(Exception $e) {
    echo json_encode(['success'=>false,'message'=>'Update failed: '.$e->getMessage()]);
}
ob_end_flush();
