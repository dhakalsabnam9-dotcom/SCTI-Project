<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header('Location: ../index.php'); exit();
}
require_once '../includes/config.php';

$studentId = $_SESSION['user_id'] ?? 0;
$search    = trim($_GET['search'] ?? '');

try {
    $db = getDBConnection();
    $where = []; $params = [];
    if ($search) {
        $where[] = "(m.title LIKE ? OR m.subject LIKE ? OR m.description LIKE ?)";
        $s = '%'.$search.'%';
        $params[] = $s; $params[] = $s; $params[] = $s;
    }
    $sql = "SELECT m.*, t.full_name as teacher_name FROM materials m LEFT JOIN teachers t ON m.teacher_id = t.id"
         . ($where ? " WHERE ".implode(" AND ",$where) : "")
         . " ORDER BY m.created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $materials = $stmt->fetchAll();
    $totalMaterials = count($materials);
    $totalDownloads = array_sum(array_column($materials, 'download_count'));
    $downloaded     = count(array_filter($materials, function($m){ return ($m['download_count']??0) > 0; }));
} catch(Exception $e) {
    $materials = []; $totalMaterials = 0; $totalDownloads = 0; $downloaded = 0;
}

$coverColors = [
    'linear-gradient(135deg,#6f42c1,#e83e8c)',
    'linear-gradient(135deg,#28a745,#20c997)',
    'linear-gradient(135deg,#fd7e14,#ffc107)',
    'linear-gradient(135deg,#004080,#0059b3)',
    'linear-gradient(135deg,#dc3545,#fd7e14)',
    'linear-gradient(135deg,#20c997,#28a745)',
];
$coverIcons = ['fa-book-open','fa-database','fa-code','fa-laptop-code','fa-brain','fa-network-wired','fa-file-alt','fa-graduation-cap'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Library | SCTI Student Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f5f7fa}
    .top-header{background:linear-gradient(135deg,#6f42c1,#e83e8c);color:white;padding:10px 20px;font-size:14px}
    .footer{background:#2c3e50;color:white;text-align:center;padding:20px;margin-top:40px}
    .container{max-width:1400px;margin:0 auto;padding:20px}
    .page-header{background:linear-gradient(135deg,#6f42c1 0%,#e83e8c 100%);color:white;padding:30px;border-radius:10px;margin-bottom:30px;box-shadow:0 4px 15px rgba(111,66,193,.2)}
    .page-header h1{margin:0 0 10px;font-size:28px}
    .breadcrumb{font-size:14px;background:transparent;padding:0}
    .breadcrumb a{color:white;text-decoration:none}
    .search-bar{background:white;padding:20px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.1);margin-bottom:30px;display:flex;gap:10px}
    .search-input{flex:1;padding:12px 20px;border:2px solid #dee2e6;border-radius:6px;font-size:16px}
    .search-input:focus{outline:none;border-color:#6f42c1}
    .search-btn{padding:12px 30px;background:linear-gradient(135deg,#6f42c1,#e83e8c);color:white;border:none;border-radius:6px;cursor:pointer;font-size:16px;transition:.3s;display:flex;align-items:center;gap:8px}
    .search-btn:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(111,66,193,.3)}
    .stats-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:30px}
    .stat-box{background:white;padding:20px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.1);text-align:center}
    .stat-box h3{font-size:36px;color:#6f42c1;margin:10px 0}
    /* BOOK GRID */
    .book-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:25px}
    .book-card{background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.1);transition:.3s}
    .book-card:hover{transform:translateY(-8px);box-shadow:0 8px 25px rgba(111,66,193,.2)}
    .book-cover{height:200px;display:flex;align-items:center;justify-content:center;color:white;font-size:56px}
    .book-body{padding:20px}
    .book-title{font-size:17px;font-weight:700;color:#333;margin-bottom:6px}
    .book-author{color:#666;font-size:14px;margin-bottom:12px}
    .book-meta{display:flex;gap:15px;margin:6px 0;font-size:13px;color:#666}
    .book-meta i{color:#6f42c1;width:14px}
    .book-status{padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;display:inline-block;margin:10px 0}
    .status-available{background:#d4edda;color:#155724}
    .book-actions{display:flex;gap:10px;margin-top:12px}
    .btn{flex:1;padding:10px;border:none;border-radius:6px;cursor:pointer;font-size:14px;font-weight:600;transition:.3s;text-align:center;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:6px}
    .btn-primary{background:linear-gradient(135deg,#6f42c1,#e83e8c);color:white}
    .btn-primary:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(111,66,193,.3)}
    .btn-outline{background:white;color:#6f42c1;border:2px solid #6f42c1}
    .btn-outline:hover{background:#6f42c1;color:white}
    .empty-state{text-align:center;padding:60px 20px;color:#999;grid-column:1/-1}
    .empty-state i{font-size:56px;display:block;margin-bottom:15px;color:#ccc}
    /* MODAL */
    .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;align-items:center;justify-content:center}
    .modal-overlay.open{display:flex}
    .modal-box{background:white;border-radius:14px;width:100%;max-width:480px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.3);animation:mIn .25s ease}
    @keyframes mIn{from{transform:translateY(-20px);opacity:0}to{transform:translateY(0);opacity:1}}
    .modal-head{background:linear-gradient(135deg,#6f42c1,#e83e8c);color:white;padding:20px 24px;display:flex;justify-content:space-between;align-items:center}
    .modal-head h3{margin:0;font-size:17px}
    .modal-close{background:rgba(255,255,255,.2);border:none;color:white;width:30px;height:30px;border-radius:50%;cursor:pointer;font-size:14px}
    .modal-body{padding:24px}
    .modal-row{margin-bottom:12px;font-size:14px;color:#555;line-height:1.6}
    .modal-row strong{color:#333}
    .modal-actions{display:flex;gap:10px;margin-top:18px}
  </style>
</head>
<body>
<div class="top-header">
  <marquee>Library — Browse and download study materials uploaded by your teachers</marquee>
</div>
<div class="container">
  <div class="page-header">
    <h1><i class="fa fa-book"></i> Library</h1>
    <div class="breadcrumb">
      <a href="../dashboards/student-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Library
    </div>
  </div>

  <form method="GET" class="search-bar">
    <input type="text" name="search" class="search-input" placeholder="Search by title, subject, or description..." value="<?=htmlspecialchars($search)?>">
    <button type="submit" class="search-btn"><i class="fa fa-search"></i> Search</button>
    <?php if ($search): ?>
    <a href="student-library.php" class="btn btn-outline" style="flex:none;padding:12px 16px"><i class="fa fa-times"></i></a>
    <?php endif; ?>
  </form>

  <div class="stats-row">
    <div class="stat-box">
      <i class="fa fa-book" style="font-size:32px;color:#6f42c1"></i>
      <h3><?=$totalMaterials?></h3>
      <p>Books Available</p>
    </div>
    <div class="stat-box">
      <i class="fa fa-clock" style="font-size:32px;color:#ffc107"></i>
      <h3><?=$downloaded?></h3>
      <p>Books Downloaded</p>
    </div>
    <div class="stat-box">
      <i class="fa fa-history" style="font-size:32px;color:#28a745"></i>
      <h3><?=$totalDownloads?></h3>
      <p>Total Downloads</p>
    </div>
  </div>

  <h2 style="color:#6f42c1;margin-bottom:20px"><i class="fa fa-star"></i> Available Books</h2>

  <div class="book-grid">
    <?php if (empty($materials)): ?>
    <div class="empty-state">
      <i class="fa fa-book-open"></i>
      <p><?=$search ? 'No materials found for "'.htmlspecialchars($search).'".' : 'No materials uploaded yet. Check back later.'?></p>
    </div>
    <?php else: ?>
    <?php foreach ($materials as $i => $m):
        $color   = $coverColors[$i % count($coverColors)];
        $icon    = $coverIcons[$i % count($coverIcons)];
        $ext     = strtoupper(pathinfo($m['file_path'] ?? '', PATHINFO_EXTENSION)) ?: 'DOC';
        $teacher = htmlspecialchars($m['teacher_name'] ?? 'Teacher');
        $subject = htmlspecialchars($m['subject'] ?? 'General');
        $dlCount = intval($m['download_count'] ?? 0);
        $title   = htmlspecialchars($m['title']);
        $desc    = htmlspecialchars($m['description'] ?? '');
        $date    = !empty($m['created_at']) ? date('M d, Y', strtotime($m['created_at'])) : '—';
        $mid     = intval($m['id']);
    ?>
    <div class="book-card">
      <div class="book-cover" style="background:<?=$color?>">
        <i class="fa <?=$icon?>"></i>
      </div>
      <div class="book-body">
        <div class="book-title"><?=$title?></div>
        <div class="book-author">by <?=$teacher?></div>
        <div class="book-meta"><i class="fa fa-tag"></i> <?=$subject?></div>
        <div class="book-meta"><i class="fa fa-file"></i> <?=$ext?> &nbsp;&nbsp;<i class="fa fa-download"></i> <?=$dlCount?> downloads</div>
        <span class="book-status status-available"><i class="fa fa-check-circle"></i> Available</span>
        <div class="book-actions">
          <a href="material-download.php?id=<?=$mid?>" class="btn btn-primary">
            <i class="fa fa-download"></i> Borrow
          </a>
          <button class="btn btn-outline" onclick="showDetails(<?=$mid?>, '<?=addslashes($title)?>', '<?=addslashes($teacher)?>', '<?=addslashes($subject)?>', '<?=addslashes($desc)?>', <?=$dlCount?>, '<?=$date?>', '<?=$ext?>')">
            <i class="fa fa-eye"></i> Details
          </button>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
<footer class="footer"><p>© 2025 SCTI - Student Portal</p></footer>

<!-- Details Modal -->
<div class="modal-overlay" id="detailModal">
  <div class="modal-box">
    <div class="modal-head">
      <h3 id="dTitle"><i class="fa fa-book"></i> Book Details</h3>
      <button class="modal-close" onclick="closeModal()"><i class="fa fa-times"></i></button>
    </div>
    <div class="modal-body">
      <div class="modal-row"><strong>Title:</strong> <span id="dTitleText"></span></div>
      <div class="modal-row"><strong>Author / Teacher:</strong> <span id="dAuthor"></span></div>
      <div class="modal-row"><strong>Subject:</strong> <span id="dSubject"></span></div>
      <div class="modal-row"><strong>File Type:</strong> <span id="dExt"></span></div>
      <div class="modal-row"><strong>Downloads:</strong> <span id="dDownloads"></span></div>
      <div class="modal-row"><strong>Uploaded:</strong> <span id="dDate"></span></div>
      <div class="modal-row"><strong>Description:</strong><br><span id="dDesc" style="color:#666"></span></div>
      <div class="modal-actions">
        <a id="dBorrowBtn" href="#" class="btn btn-primary"><i class="fa fa-download"></i> Borrow / Download</a>
        <button class="btn btn-outline" onclick="closeModal()"><i class="fa fa-times"></i> Close</button>
      </div>
    </div>
  </div>
</div>

<script>
function showDetails(id, title, author, subject, desc, downloads, date, ext) {
  document.getElementById('dTitle').innerHTML      = '<i class="fa fa-book"></i> ' + title;
  document.getElementById('dTitleText').textContent = title;
  document.getElementById('dAuthor').textContent   = author;
  document.getElementById('dSubject').textContent  = subject;
  document.getElementById('dExt').textContent      = ext;
  document.getElementById('dDownloads').textContent = downloads;
  document.getElementById('dDate').textContent     = date;
  document.getElementById('dDesc').textContent     = desc || 'No description available.';
  document.getElementById('dBorrowBtn').href       = 'material-download.php?id=' + id;
  document.getElementById('detailModal').classList.add('open');
}
function closeModal() {
  document.getElementById('detailModal').classList.remove('open');
}
document.getElementById('detailModal').addEventListener('click', function(e){
  if (e.target === this) closeModal();
});
</script>
</body>
</html>
