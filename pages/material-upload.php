<?php
ob_start();
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
require_once '../includes/config.php';

$title   = trim($_POST['title'] ?? '');
$subject = trim($_POST['subject'] ?? '');

if (!$title || !$subject || empty($_FILES['file']['name'])) {
    echo json_encode(['success'=>false,'message'=>'Title, subject and file are required.']); exit();
}

$file     = $_FILES['file'];
$origName = basename($file['name']);
$ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
$allowed  = ['pdf','doc','docx','ppt','pptx','xls','xlsx','zip','rar','mp4','avi','mkv','txt'];

if (!in_array($ext, $allowed)) {
    echo json_encode(['success'=>false,'message'=>'File type not allowed.']); exit();
}

if ($file['size'] > 200 * 1024 * 1024) {
    echo json_encode(['success'=>false,'message'=>'File too large (max 200MB).']); exit();
}

// Determine type label
$typeMap = [
    'pdf'=>'PDF','doc'=>'DOC','docx'=>'DOC','ppt'=>'PPT','pptx'=>'PPT',
    'xls'=>'XLS','xlsx'=>'XLS','zip'=>'ZIP','rar'=>'ZIP',
    'mp4'=>'MP4','avi'=>'MP4','mkv'=>'MP4','txt'=>'TXT'
];
$fileType = $typeMap[$ext] ?? strtoupper($ext);

$newName  = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $origName);
$uploadDir = '../uploads/materials/';
$filePath  = $uploadDir . $newName;

if (!move_uploaded_file($file['tmp_name'], $filePath)) {
    echo json_encode(['success'=>false,'message'=>'Upload failed. Check folder permissions.']); exit();
}

$sizeBytes = $file['size'];
if ($sizeBytes >= 1048576)      $sizeStr = round($sizeBytes/1048576, 1) . ' MB';
elseif ($sizeBytes >= 1024)     $sizeStr = round($sizeBytes/1024, 1) . ' KB';
else                             $sizeStr = $sizeBytes . ' B';
$fileSize = $fileType . ', ' . $sizeStr;

try {
    $db = getDBConnection();
    $db->exec("CREATE TABLE IF NOT EXISTS materials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        subject VARCHAR(100) NOT NULL,
        file_type VARCHAR(20) NOT NULL,
        file_name VARCHAR(255) NOT NULL,
        file_path VARCHAR(500) NOT NULL,
        file_size VARCHAR(30),
        downloads INT DEFAULT 0,
        uploaded_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    $stmt = $db->prepare("INSERT INTO materials (title, subject, file_type, file_name, file_path, file_size, uploaded_by) VALUES (?,?,?,?,?,?,?)");
    $stmt->execute([$title, $subject, $fileType, $newName, 'uploads/materials/'.$newName, $fileSize, $_SESSION['user_id']??null]);
    $id = $db->lastInsertId();
    echo json_encode(['success'=>true,'message'=>'Material uploaded successfully.','id'=>$id,'file_size'=>$fileSize,'file_type'=>$fileType,'file_name'=>$newName]);
} catch(Exception $e) {
    unlink($filePath);
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
ob_end_flush();
