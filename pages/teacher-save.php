<?php
ob_start();
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>'Unauthorized']);
    exit();
}
require_once '../includes/config.php';
header('Content-Type: application/json');

try {
    $raw  = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!$data) { ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Invalid data']); exit(); }

    $id         = isset($data['id']) ? intval($data['id']) : 0;
    $name       = trim($data['name']       ?? '');
    $teacher_id = trim($data['teacher_id'] ?? '');
    $username   = trim($data['username']   ?? '');
    $password   = trim($data['password']   ?? '');
    $dept       = trim($data['department'] ?? '');
    $email      = trim($data['email']      ?? '');
    $phone      = trim($data['phone']      ?? '');
    $qual       = trim($data['qualification'] ?? '');
    $exp        = trim($data['experience'] ?? '');
    $designation= trim($data['designation'] ?? '');
    $address    = trim($data['address']    ?? '');
    $subjects   = trim($data['subjects']   ?? '');
    $status     = in_array($data['status'] ?? '', ['active','inactive']) ? $data['status'] : 'active';

    if (!$name || !$username || !$dept) {
        ob_end_clean();
        echo json_encode(['success'=>false,'message'=>'Name, username and department are required']);
        exit();
    }

    $db = getDBConnection();

    if ($id === 0) {
        // INSERT — check duplicate username
        $chk = $db->prepare("SELECT id FROM teachers WHERE username=? LIMIT 1");
        $chk->execute([$username]);
        if ($chk->fetch()) {
            ob_end_clean();
            echo json_encode(['success'=>false,'message'=>'Username already exists']);
            exit();
        }
        if (!$password) {
            ob_end_clean();
            echo json_encode(['success'=>false,'message'=>'Password is required for new teacher']);
            exit();
        }
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO teachers (username, email, password, full_name, teacher_id, department, phone, qualification, experience, designation, address, subjects, status, created_at)
                              VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())");
        $stmt->execute([$username, $email, $hashed, $name, $teacher_id, $dept, $phone, $qual, $exp, $designation, $address, $subjects, $status]);
        $newId = $db->lastInsertId();
        ob_end_clean();
        echo json_encode(['success'=>true,'message'=>'Teacher added successfully','id'=>$newId]);
    } else {
        // UPDATE — only hash password if a new one is provided
        if ($password) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE teachers SET full_name=?, username=?, email=?, password=?, teacher_id=?, department=?, phone=?, qualification=?, experience=?, designation=?, address=?, subjects=?, status=? WHERE id=?");
            $stmt->execute([$name, $username, $email, $hashed, $teacher_id, $dept, $phone, $qual, $exp, $designation, $address, $subjects, $status, $id]);
        } else {
            $stmt = $db->prepare("UPDATE teachers SET full_name=?, username=?, email=?, teacher_id=?, department=?, phone=?, qualification=?, experience=?, designation=?, address=?, subjects=?, status=? WHERE id=?");
            $stmt->execute([$name, $username, $email, $teacher_id, $dept, $phone, $qual, $exp, $designation, $address, $subjects, $status, $id]);
        }
        ob_end_clean();
        echo json_encode(['success'=>true,'message'=>'Teacher updated successfully']);
    }
} catch(Exception $e) {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
