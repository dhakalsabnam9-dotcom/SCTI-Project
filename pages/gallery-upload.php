<?php
ob_start();
session_start();

// Always return JSON, no matter what
function sendJSON($data) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    sendJSON(['success' => false, 'message' => 'Unauthorized']);
}

require_once '../includes/config.php';

// Check file was sent
if (!isset($_FILES['image'])) {
    sendJSON(['success' => false, 'message' => 'No file field received']);
}

$errCode = $_FILES['image']['error'];
if ($errCode !== UPLOAD_ERR_OK) {
    $errMessages = [
        UPLOAD_ERR_INI_SIZE   => 'File too large (php.ini limit)',
        UPLOAD_ERR_FORM_SIZE  => 'File too large (form limit)',
        UPLOAD_ERR_PARTIAL    => 'File only partially uploaded',
        UPLOAD_ERR_NO_FILE    => 'No file selected',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temp folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write to disk',
        UPLOAD_ERR_EXTENSION  => 'Upload blocked by PHP extension',
    ];
    sendJSON(['success' => false, 'message' => $errMessages[$errCode] ?? "Upload error code: $errCode"]);
}

$file     = $_FILES['image'];
$title    = trim($_POST['title'] ?? '');
$desc     = trim($_POST['description'] ?? '');
$category = trim($_POST['category'] ?? '');

if (empty($title)) {
    sendJSON(['success' => false, 'message' => 'Title is required']);
}

if ($file['size'] > 10 * 1024 * 1024) {
    sendJSON(['success' => false, 'message' => 'File exceeds 10MB limit']);
}

// Detect MIME type
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($file['tmp_name']);
$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
if (!array_key_exists($mime, $allowed)) {
    sendJSON(['success' => false, 'message' => "Invalid file type: $mime. Only JPG, PNG, GIF, WEBP allowed"]);
}

$ext      = $allowed[$mime];
$filename = 'gallery_' . uniqid() . '_' . time() . '.' . $ext;

$uploadDir = '../uploads/gallery/';
$thumbDir  = '../uploads/gallery/thumbnails/';

// Create dirs if missing
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
    sendJSON(['success' => false, 'message' => 'Cannot create upload directory']);
}
if (!is_dir($thumbDir) && !mkdir($thumbDir, 0755, true)) {
    sendJSON(['success' => false, 'message' => 'Cannot create thumbnails directory']);
}

// Check writable
if (!is_writable($uploadDir)) {
    sendJSON(['success' => false, 'message' => 'Upload directory is not writable']);
}

$filepath  = $uploadDir . $filename;
$thumbPath = $thumbDir  . $filename;

if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    sendJSON(['success' => false, 'message' => 'move_uploaded_file() failed — check folder permissions']);
}

// Create thumbnail (non-fatal if GD missing)
if (extension_loaded('gd')) {
    createThumb($filepath, $thumbPath, 400, 300, $mime);
} else {
    copy($filepath, $thumbPath);
}

$dbFile  = 'uploads/gallery/' . $filename;
$dbThumb = 'uploads/gallery/thumbnails/' . $filename;

try {
    $db = getDBConnection();

    $db->exec("CREATE TABLE IF NOT EXISTS gallery_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        file_path VARCHAR(500) NOT NULL,
        thumbnail_path VARCHAR(500),
        category VARCHAR(100),
        is_active TINYINT(1) DEFAULT 1,
        created_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $stmt = $db->prepare("INSERT INTO gallery_images
        (title, description, file_path, thumbnail_path, category, created_by)
        VALUES (?,?,?,?,?,?)");
    $stmt->execute([$title, $desc, $dbFile, $dbThumb, $category ?: null, $_SESSION['user_id'] ?? null]);

    sendJSON(['success' => true, 'message' => 'Image uploaded successfully', 'image_id' => $db->lastInsertId()]);

} catch (Exception $e) {
    sendJSON(['success' => false, 'message' => 'DB error: ' . $e->getMessage()]);
}

function createThumb($src, $dst, $maxW, $maxH, $mime) {
    $info = @getimagesize($src);
    if (!$info) return;
    if ($mime === 'image/jpeg')      $img = @imagecreatefromjpeg($src);
    elseif ($mime === 'image/png')   $img = @imagecreatefrompng($src);
    elseif ($mime === 'image/gif')   $img = @imagecreatefromgif($src);
    elseif ($mime === 'image/webp')  $img = @imagecreatefromwebp($src);
    else return;
    if (!$img) return;
    $ow = imagesx($img); $oh = imagesy($img);
    $r  = min($maxW / $ow, $maxH / $oh);
    $tw = (int)round($ow * $r); $th = (int)round($oh * $r);
    $thumb = imagecreatetruecolor($tw, $th);
    if ($mime === 'image/png' || $mime === 'image/gif') {
        imagealphablending($thumb, false); imagesavealpha($thumb, true);
        imagefilledrectangle($thumb, 0, 0, $tw, $th, imagecolorallocatealpha($thumb, 255, 255, 255, 127));
    }
    imagecopyresampled($thumb, $img, 0, 0, 0, 0, $tw, $th, $ow, $oh);
    if ($mime === 'image/jpeg')     imagejpeg($thumb, $dst, 85);
    elseif ($mime === 'image/png')  imagepng($thumb, $dst, 8);
    elseif ($mime === 'image/gif')  imagegif($thumb, $dst);
    elseif ($mime === 'image/webp') imagewebp($thumb, $dst, 85);
    imagedestroy($img); imagedestroy($thumb);
}
?>
