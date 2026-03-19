<?php
ob_start();
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
require_once '../includes/config.php';
header('Content-Type: application/json');

try {
    $raw  = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!$data) { ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Invalid data']); exit(); }

    $id         = intval($data['id']         ?? 0);
    $name       = trim($data['name']         ?? '');
    $student_id = trim($data['student_id']   ?? '');
    $username   = trim($data['username']     ?? '');
    $password   = trim($data['password']     ?? '');
    $program    = trim($data['program']      ?? '');
    $email      = trim($data['email']        ?? '');
    $phone      = trim($data['phone']        ?? '');
    $semester   = trim($data['semester']     ?? '');
    $address    = trim($data['address']      ?? '');
    $status     = in_array($data['status'] ?? '', ['active','inactive']) ? $data['status'] : 'active';

    if (!$name || !$username || !$program) {
        ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Name, username and course are required']); exit();
    }

    $db = getDBConnection();

    if ($id === 0) {
        $chk = $db->prepare("SELECT id FROM students WHERE username=? LIMIT 1");
        $chk->execute([$username]);
        if ($chk->fetch()) { ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Username already exists']); exit(); }
        if (!$password) { ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Password required for new student']); exit(); }
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO students (username, email, password, full_name, student_id, course, phone, semester, address, status, created_at) VALUES (?,?,?,?,?,?,?,?,?,?,NOW())");
        $stmt->execute([$username, $email, $hashed, $name, $student_id, $program, $phone, $semester, $address, $status]);
        ob_end_clean();
        echo json_encode(['success'=>true,'message'=>'Student added successfully','id'=>$db->lastInsertId()]);
    } else {
        if ($password) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE students SET full_name=?, username=?, email=?, password=?, student_id=?, course=?, phone=?, semester=?, address=?, status=? WHERE id=?");
            $stmt->execute([$name, $username, $email, $hashed, $student_id, $program, $phone, $semester, $address, $status, $id]);
        } else {
            $stmt = $db->prepare("UPDATE students SET full_name=?, username=?, email=?, student_id=?, course=?, phone=?, semester=?, address=?, status=? WHERE id=?");
            $stmt->execute([$name, $username, $email, $student_id, $program, $phone, $semester, $address, $status, $id]);
        }
        ob_end_clean();
        echo json_encode(['success'=>true,'message'=>'Student updated successfully']);
    }
} catch(Exception $e) {
    ob_end_clean(); echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
