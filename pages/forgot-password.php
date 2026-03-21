<?php
ob_start();
session_start();
header('Content-Type: application/json');
require_once '../includes/config.php';

$step = trim($_POST['step'] ?? '');

$tableMap = ['student'=>'students','teacher'=>'teachers','admin'=>'admins'];

try {
    $db = getDBConnection();

    // ── STEP 1: verify username + full_name ──────────────────────────────────
    if ($step === 'verify') {
        $userType = trim($_POST['user_type'] ?? '');
        $username = trim($_POST['username']  ?? '');
        $fullName = trim($_POST['full_name'] ?? '');

        if (!$userType || !$username || !$fullName) {
            ob_end_clean();
            echo json_encode(['success'=>false,'message'=>'All fields are required.']); exit();
        }
        if (!isset($tableMap[$userType])) {
            ob_end_clean();
            echo json_encode(['success'=>false,'message'=>'Invalid user type.']); exit();
        }
        $table = $tableMap[$userType];

        $stmt = $db->prepare("SELECT id, full_name, phone FROM `$table` WHERE username=? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user) {
            ob_end_clean();
            echo json_encode(['success'=>false,'message'=>'Username not found.']); exit();
        }

        if (strtolower(trim($user['full_name'])) !== strtolower($fullName)) {
            ob_end_clean();
            echo json_encode(['success'=>false,'message'=>'Full name does not match our records.']); exit();
        }

        // Store verified identity in session
        $_SESSION['fp_user_id']  = $user['id'];
        $_SESSION['fp_table']    = $table;
        $_SESSION['fp_verified'] = true;

        // Mask phone for display hint
        $phone  = $user['phone'] ?? '';
        $masked = '';
        if ($phone) {
            $len    = strlen($phone);
            $masked = substr($phone, 0, 3) . str_repeat('*', max(0, $len - 5)) . substr($phone, -2);
        }

        ob_end_clean();
        echo json_encode(['success'=>true, 'phone_hint'=>$masked]); exit();
    }

    // ── STEP 2: set new password ─────────────────────────────────────────────
    if ($step === 'reset') {
        // Must have completed step 1 in this session
        if (empty($_SESSION['fp_verified']) || empty($_SESSION['fp_user_id']) || empty($_SESSION['fp_table'])) {
            ob_end_clean();
            echo json_encode(['success'=>false,'message'=>'Session expired. Please start again.']); exit();
        }

        $newPass  = $_POST['new_password']     ?? '';
        $confPass = $_POST['confirm_password'] ?? '';

        if (strlen($newPass) < 6) {
            ob_end_clean();
            echo json_encode(['success'=>false,'message'=>'Password must be at least 6 characters.']); exit();
        }
        if ($newPass !== $confPass) {
            ob_end_clean();
            echo json_encode(['success'=>false,'message'=>'Passwords do not match.']); exit();
        }

        $hashed = password_hash($newPass, PASSWORD_DEFAULT);
        $uid    = (int)$_SESSION['fp_user_id'];
        $tbl    = $_SESSION['fp_table'];

        $upd = $db->prepare("UPDATE `$tbl` SET password=?, updated_at=NOW() WHERE id=?");
        $upd->execute([$hashed, $uid]);

        // Clear session flags
        unset($_SESSION['fp_user_id'], $_SESSION['fp_table'], $_SESSION['fp_verified']);

        ob_end_clean();
        echo json_encode(['success'=>true]); exit();
    }

    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>'Unknown step.']);

} catch(Exception $e) {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
