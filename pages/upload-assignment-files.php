<?php
session_start();

// Check if user is logged in and is teacher
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Set upload directory
$uploadDir = '../uploads/assignments/';

// Create directory if it doesn't exist
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => '', 'files' => []];
    
    // Check if files were uploaded
    if (isset($_FILES['files'])) {
        $files = $_FILES['files'];
        $fileCount = count($files['name']);
        
        // Process each file
        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $fileName = $files['name'][$i];
                $fileTmpName = $files['tmp_name'][$i];
                $fileSize = $files['size'][$i];
                $fileType = $files['type'][$i];
                
                // Get file extension
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                // Allowed extensions
                $allowedExt = ['pdf', 'doc', 'docx', 'zip', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png'];
                
                // Validate file extension
                if (!in_array($fileExt, $allowedExt)) {
                    $response['message'] = "File type not allowed: $fileName";
                    continue;
                }
                
                // Validate file size (10MB max)
                if ($fileSize > 10 * 1024 * 1024) {
                    $response['message'] = "File too large: $fileName (max 10MB)";
                    continue;
                }
                
                // Generate unique filename
                $uniqueFileName = time() . '_' . uniqid() . '_' . $fileName;
                $uploadPath = $uploadDir . $uniqueFileName;
                
                // Move uploaded file
                if (move_uploaded_file($fileTmpName, $uploadPath)) {
                    $response['files'][] = [
                        'original_name' => $fileName,
                        'saved_name' => $uniqueFileName,
                        'size' => $fileSize,
                        'path' => $uploadPath
                    ];
                    $response['success'] = true;
                } else {
                    $response['message'] = "Failed to upload: $fileName";
                }
            } else {
                $response['message'] = "Upload error for file: " . $files['name'][$i];
            }
        }
        
        if ($response['success']) {
            $response['message'] = count($response['files']) . ' file(s) uploaded successfully';
        }
    } else {
        $response['message'] = 'No files received';
    }
    
    echo json_encode($response);
    exit();
}
?>
