<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php'); exit();
}
require_once '../includes/config.php';

$db = getDBConnection();
$results = [];

// 1. Add file_type column if missing
try {
    $db->exec("ALTER TABLE gallery_images ADD COLUMN file_type VARCHAR(20) DEFAULT 'image'");
    $results[] = ['ok', 'Added file_type column'];
} catch (Exception $e) {
    $results[] = ['info', 'file_type column already exists (OK)'];
}

// 2. Update existing rows where file_type is NULL to 'image'
$r = $db->exec("UPDATE gallery_images SET file_type = 'image' WHERE file_type IS NULL OR file_type = ''");
$results[] = ['ok', "Set $r rows to file_type='image'"];

// 3. Auto-detect videos by extension and update
$stmt = $db->query("SELECT id, file_path FROM gallery_images");
$rows = $stmt->fetchAll();
$videoExts = ['mp4','webm','ogv','mov','avi','mkv'];
$audioExts = ['mp3','ogg','wav','m4a','weba','flac'];
$docExts   = ['pdf','doc','docx','ppt','pptx','zip','rar','txt'];
$updated = 0;
foreach ($rows as $row) {
    $ext = strtolower(pathinfo($row['file_path'], PATHINFO_EXTENSION));
    if (in_array($ext, $videoExts)) {
        $db->prepare("UPDATE gallery_images SET file_type='video' WHERE id=?")->execute([$row['id']]);
        $updated++;
    } elseif (in_array($ext, $audioExts)) {
        $db->prepare("UPDATE gallery_images SET file_type='audio' WHERE id=?")->execute([$row['id']]);
        $updated++;
    } elseif (in_array($ext, $docExts)) {
        $db->prepare("UPDATE gallery_images SET file_type='document' WHERE id=?")->execute([$row['id']]);
        $updated++;
    }
}
$results[] = ['ok', "Auto-detected and updated $updated non-image records"];

// 4. Show current state
$stmt = $db->query("SELECT file_type, COUNT(*) as cnt FROM gallery_images GROUP BY file_type");
$summary = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><title>Gallery Fix</title>
<style>body{font-family:sans-serif;padding:30px;background:#f0f4f8;}
.card{background:#fff;border-radius:12px;padding:24px;max-width:600px;margin:0 auto;box-shadow:0 4px 16px rgba(0,0,0,.1);}
h2{color:#17a2b8;margin-bottom:20px;}
.row{padding:10px 14px;border-radius:8px;margin-bottom:8px;font-size:14px;display:flex;align-items:center;gap:10px;}
.ok{background:#d4edda;color:#155724;}
.info{background:#d1ecf1;color:#0c5460;}
.sum{background:#f8f9fa;border:1px solid #dee2e6;border-radius:8px;padding:14px;margin-top:16px;}
.sum table{width:100%;border-collapse:collapse;}
.sum td{padding:6px 10px;font-size:13px;}
.sum tr:not(:last-child) td{border-bottom:1px solid #eee;}
.btn{display:inline-block;margin-top:20px;padding:10px 22px;background:#17a2b8;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;}
</style></head>
<body>
<div class="card">
  <h2>Gallery File Type Fix</h2>
  <?php foreach ($results as [$type, $msg]): ?>
    <div class="row <?= $type ?>"><i>&#10003;</i> <?= htmlspecialchars($msg) ?></div>
  <?php endforeach; ?>
  <div class="sum">
    <strong>Current file_type breakdown:</strong>
    <table>
      <?php foreach ($summary as $s): ?>
        <tr><td><?= htmlspecialchars($s['file_type'] ?? 'NULL') ?></td><td><?= $s['cnt'] ?> records</td></tr>
      <?php endforeach; ?>
    </table>
  </div>
  <a href="manage-gallery.php" class="btn">Go to Gallery Manager</a>
</div>
</body>
</html>
