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
        $where[] = "(m.title LIKE ? OR m.subject LIKE ?)";
        $s = '%'.$search.'%';
        $params[] = $s; $params[] = $s;
    }
    $sql = "SELECT m.*, t.full_name as teacher_name FROM materials m LEFT JOIN teachers t ON m.uploaded_by = t.id"
         . ($where ? " WHERE ".implode(" AND ",$where) : "")
         . " ORDER BY m.created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $materials = $stmt->fetchAll();
    $totalMaterials = count($materials);
    $totalDownloads = array_sum(array_column($materials, 'downloads'));
    $downloaded     = count(array_filter($materials, function($m){ return ($m['downloads']??0) > 0; }));
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

    /* PAGE HEADER */
    .page-header{background:linear-gradient(135deg,#6f42c1 0%,#e83e8c 100%);color:white;padding:30px;border-radius:12px;margin-bottom:30px;box-shadow:0 4px 15px rgba(111,66,193,.25)}
    .page-header h1{margin:0 0 8px;font-size:28px}
    .breadcrumb{font-size:14px;opacity:.9}
    .breadcrumb a{color:white;text-decoration:none}

    /* SEARCH */
    .search-bar{background:white;padding:20px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.08);margin-bottom:30px;display:flex;gap:10px}
    .search-input{flex:1;padding:12px 20px;border:2px solid #dee2e6;border-radius:8px;font-size:15px;transition:.3s}
    .search-input:focus{outline:none;border-color:#6f42c1;box-shadow:0 0 0 3px rgba(111,66,193,.1)}
    .search-btn{padding:12px 28px;background:linear-gradient(135deg,#6f42c1,#e83e8c);color:white;border:none;border-radius:8px;cursor:pointer;font-size:15px;font-weight:600;transition:.3s;display:flex;align-items:center;gap:8px}
    .search-btn:hover{transform:translateY(-2px);box-shadow:0 6px 16px rgba(111,66,193,.35)}
    .search-btn:active{transform:translateY(0)}

    /* STAT CARDS — same style as dashboard */
    .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;margin-bottom:30px}
    .stat-card{background:white;padding:25px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.08);display:flex;align-items:center;gap:20px;transition:all .3s cubic-bezier(.25,.8,.25,1);cursor:pointer;position:relative;overflow:hidden;border:2px solid transparent}
    .stat-card::before{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(111,66,193,.07),transparent);transition:left .5s}
    .stat-card:hover::before{left:100%}
    .stat-card:hover{transform:translateY(-6px);box-shadow:0 12px 30px rgba(111,66,193,.2);border-color:#6f42c1}
    .stat-card:active{transform:translateY(-2px) scale(.98)}
    .stat-card .card-arrow{position:absolute;right:14px;top:50%;transform:translateY(-50%);color:#ccc;font-size:13px;transition:all .3s;opacity:0}
    .stat-card:hover .card-arrow{opacity:1;color:#6f42c1;right:10px}
    .stat-icon{width:60px;height:60px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:24px;color:white;flex-shrink:0}
    .stat-icon.purple{background:linear-gradient(135deg,#6f42c1,#e83e8c)}
    .stat-icon.yellow{background:linear-gradient(135deg,#fd7e14,#ffc107)}
    .stat-icon.green{background:linear-gradient(135deg,#28a745,#20c997)}
    .stat-info h3{margin:0;font-size:32px;color:#6f42c1;font-weight:800}
    .stat-info p{margin:5px 0 0;color:#666;font-size:14px}

    /* SECTION TITLE */
    .section-title{color:#6f42c1;margin-bottom:20px;font-size:20px;font-weight:800;display:flex;align-items:center;gap:8px}

    /* BOOK GRID */
    .book-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:25px}
    .book-card{background:white;border-radius:14px;overflow:hidden;box-shadow:0 3px 12px rgba(0,0,0,.09);transition:all .3s cubic-bezier(.25,.8,.25,1);cursor:pointer;border:2px solid transparent;position:relative}
    .book-card:hover{transform:translateY(-8px);box-shadow:0 16px 36px rgba(111,66,193,.22);border-color:#6f42c1}
    .book-card:active{transform:translateY(-3px) scale(.98)}
    .book-cover{height:190px;display:flex;align-items:center;justify-content:center;color:white;font-size:58px;position:relative;overflow:hidden}
    .book-cover::after{content:'';position:absolute;inset:0;background:rgba(0,0,0,0);transition:background .3s}
    .book-card:hover .book-cover::after{background:rgba(0,0,0,.08)}
    .book-cover i{position:relative;z-index:1;transition:transform .3s}
    .book-card:hover .book-cover i{transform:scale(1.15)}
    .book-body{padding:18px 20px 20px}
    .book-title{font-size:16px;font-weight:700;color:#1a202c;margin-bottom:5px;line-height:1.35}
    .book-author{color:#888;font-size:13px;margin-bottom:10px;display:flex;align-items:center;gap:5px}
    .book-meta{display:flex;align-items:center;gap:6px;margin:5px 0;font-size:13px;color:#666}
    .book-meta i{color:#6f42c1;width:14px}
    .book-status{padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;display:inline-flex;align-items:center;gap:5px;margin:10px 0 14px}
    .status-available{background:#d4edda;color:#155724}
    .book-actions{display:flex;gap:10px}
    .btn{flex:1;padding:10px;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:700;transition:all .25s;text-align:center;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:6px;position:relative;overflow:hidden}
    .btn-primary{background:linear-gradient(135deg,#6f42c1,#e83e8c);color:white}
    .btn-primary::before{content:'';position:absolute;top:50%;left:50%;width:0;height:0;border-radius:50%;background:rgba(255,255,255,.25);transform:translate(-50%,-50%);transition:width .5s,height .5s}
    .btn-primary:hover::before{width:200px;height:200px}
    .btn-primary:hover{transform:translateY(-2px);box-shadow:0 6px 16px rgba(111,66,193,.35)}
    .btn-primary:active{transform:translateY(0) scale(.97)}
    .btn-outline{background:white;color:#6f42c1;border:2px solid #6f42c1}
    .btn-outline:hover{background:#6f42c1;color:white;transform:translateY(-2px)}
    .btn-outline:active{transform:translateY(0) scale(.97)}

    /* EMPTY STATE */
    .empty-state{text-align:center;padding:60px 20px;color:#999;grid-column:1/-1}
    .empty-state i{font-size:56px;display:block;margin-bottom:15px;color:#ccc}

    /* MODAL */
    .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;align-items:center;justify-content:center}
    .modal-overlay.open{display:flex}
    .modal-box{background:white;border-radius:16px;width:100%;max-width:480px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.3);animation:mIn .25s ease}
    @keyframes mIn{from{transform:translateY(-20px);opacity:0}to{transform:translateY(0);opacity:1}}
    .modal-head{background:linear-gradient(135deg,#6f42c1,#e83e8c);color:white;padding:20px 24px;display:flex;justify-content:space-between;align-items:center}
    .modal-head h3{margin:0;font-size:17px}
    .modal-close{background:rgba(255,255,255,.2);border:none;color:white;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:14px;transition:.2s}
    .modal-close:hover{background:rgba(255,255,255,.35)}
    .modal-body{padding:24px}
    .modal-row{margin-bottom:12px;font-size:14px;color:#555;line-height:1.6}
    .modal-row strong{color:#333}
    .modal-actions{display:flex;gap:10px;margin-top:18px}
  </style>
</head>
<body>
<div class="top-header">
  <marquee>Library — Browse, borrow, and explore our extensive collection of books</marquee>
</div>
<div class="container">

  <div class="page-header">
    <h1><i class="fa fa-book-open"></i> Library</h1>
    <div class="breadcrumb">
      <a href="../dashboards/student-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Library
    </div>
  </div>

  <form method="GET" class="search-bar">
    <input type="text" name="search" class="search-input" placeholder="Search books by title, author, or ISBN..." value="<?=htmlspecialchars($search)?>">
    <button type="submit" class="search-btn"><i class="fa fa-search"></i> Search</button>
    <?php if ($search): ?>
    <a href="student-library.php" class="btn btn-outline" style="flex:none;padding:12px 16px;border-radius:8px"><i class="fa fa-times"></i></a>
    <?php endif; ?>
  </form>

  <!-- STAT CARDS -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon purple"><i class="fa fa-book"></i></div>
      <div class="stat-info"><h3><?=$totalMaterials?></h3><p>Books Available</p></div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>
    <div class="stat-card">
      <div class="stat-icon yellow"><i class="fa fa-clock"></i></div>
      <div class="stat-info"><h3><?=$downloaded?></h3><p>Books Borrowed</p></div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>
    <div class="stat-card">
      <div class="stat-icon green"><i class="fa fa-history"></i></div>
      <div class="stat-info"><h3><?=$totalDownloads?></h3><p>Total Downloads</p></div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>
  </div>

  <div class="section-title"><i class="fa fa-star"></i> Available Books</div>

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
        $ext     = strtoupper($m['file_type'] ?? pathinfo($m['file_name'] ?? '', PATHINFO_EXTENSION)) ?: 'DOC';
        $teacher = htmlspecialchars($m['teacher_name'] ?? 'Teacher');
        $subject = htmlspecialchars($m['subject'] ?? 'General');
        $dlCount = intval($m['downloads'] ?? 0);
        $title   = htmlspecialchars($m['title']);
        $desc    = htmlspecialchars($m['file_size'] ?? '');
        $date    = !empty($m['created_at']) ? date('M d, Y', strtotime($m['created_at'])) : '—';
        $mid     = intval($m['id']);
        $jsArgs  = "$mid, '".addslashes($m['title'])."', '".addslashes($m['teacher_name'] ?? 'Teacher')."', '".addslashes($m['subject'] ?? 'General')."', '".addslashes($m['file_size'] ?? '')."', $dlCount, '$date', '$ext'";
    ?>
    <div class="book-card" onclick="showDetails(<?=$jsArgs?>)">
      <div class="book-cover" style="background:<?=$color?>">
        <i class="fa <?=$icon?>"></i>
      </div>
      <div class="book-body">
        <div class="book-title"><?=$title?></div>
        <div class="book-author"><i class="fa fa-user-tie" style="color:#6f42c1"></i> <?=$teacher?></div>
        <div class="book-meta"><i class="fa fa-tag"></i> <?=$subject?></div>
        <div class="book-meta"><i class="fa fa-file"></i> <?=$ext?> &nbsp;&nbsp;<i class="fa fa-download"></i> <?=$dlCount?> downloads</div>
        <span class="book-status status-available"><i class="fa fa-check-circle"></i> Available</span>
        <div class="book-actions">
          <a href="material-download.php?id=<?=$mid?>" class="btn btn-primary" onclick="event.stopPropagation()">
            <i class="fa fa-download"></i> Borrow
          </a>
          <button class="btn btn-outline" onclick="event.stopPropagation();showDetails(<?=$jsArgs?>)">
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
      <div class="modal-row"><strong>File Size:</strong> <span id="dDesc" style="color:#666"></span></div>
      <div class="modal-row"><strong>Downloads:</strong> <span id="dDownloads"></span></div>
      <div class="modal-row"><strong>Uploaded:</strong> <span id="dDate"></span></div>
      <div class="modal-actions">
        <a id="dBorrowBtn" href="#" class="btn btn-primary"><i class="fa fa-download"></i> Borrow / Download</a>
        <button class="btn btn-outline" onclick="closeModal()"><i class="fa fa-times"></i> Close</button>
      </div>
    </div>
  </div>
</div>

<script>
function showDetails(id, title, author, subject, desc, downloads, date, ext) {
  document.getElementById('dTitle').innerHTML       = '<i class="fa fa-book"></i> ' + title;
  document.getElementById('dTitleText').textContent = title;
  document.getElementById('dAuthor').textContent    = author;
  document.getElementById('dSubject').textContent   = subject;
  document.getElementById('dExt').textContent       = ext;
  document.getElementById('dDownloads').textContent = downloads;
  document.getElementById('dDate').textContent      = date;
  document.getElementById('dDesc').textContent      = desc || 'No description available.';
  document.getElementById('dBorrowBtn').href        = 'material-download.php?id=' + id;
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
