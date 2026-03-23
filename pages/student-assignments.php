<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header('Location: ../index.php'); exit();
}
require_once '../includes/config.php';
$studentId = intval($_SESSION['user_id']);
$studentName = $_SESSION['full_name'] ?? 'Student';
try {
    $db = getDBConnection();
    $stmt = $db->prepare("
        SELECT a.*,
               s.id AS sub_id, s.status AS sub_status, s.submitted_at,
               s.file_name, s.grade, s.feedback, s.notes AS sub_notes
        FROM assignments a
        LEFT JOIN assignment_submissions s ON s.assignment_id = a.id AND s.student_id = ?
        WHERE a.status = 'active'
        ORDER BY a.due_date ASC
    ");
    $stmt->execute([$studentId]);
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) { $assignments = []; }

$total     = count($assignments);
$submitted = count(array_filter($assignments, fn($a) => !empty($a['sub_id'])));
$pending   = count(array_filter($assignments, fn($a) => empty($a['sub_id']) && strtotime($a['due_date']) > time()));
$overdue   = count(array_filter($assignments, fn($a) => empty($a['sub_id']) && strtotime($a['due_date']) <= time()));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Assignments | SCTI Student</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',sans-serif;background:#f0f4f8;min-height:100vh}
.top-bar{background:#00264d;color:white;padding:7px 20px;font-size:13px}
.pg-header{background:linear-gradient(135deg,#004080,#0059b3);color:#fff;padding:20px 28px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 18px rgba(0,64,128,.25)}
.pg-header h1{font-size:22px;font-weight:700;display:flex;align-items:center;gap:10px;margin:0 0 3px}
.pg-header .bc{font-size:12px;color:rgba(255,255,255,.75)}
.pg-header .bc a{color:#fff;text-decoration:none}
.btn-hdr{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;border:none;transition:.2s;text-decoration:none;background:rgba(255,255,255,.18);color:#fff}
.btn-hdr:hover{background:rgba(255,255,255,.32)}
.wrap{max-width:1000px;margin:28px auto;padding:0 20px 60px}
/* STATS */
.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px}
.stat-card{background:white;border-radius:14px;padding:20px;display:flex;align-items:center;gap:14px;box-shadow:0 3px 14px rgba(0,0,0,.08);border-left:5px solid #004080;transition:.2s;cursor:pointer}
.stat-card:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,.13)}
.stat-card.blue{border-left-color:#004080}.stat-card.green{border-left-color:#10b981}.stat-card.orange{border-left-color:#f97316}.stat-card.red{border-left-color:#dc2626}
.stat-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;color:white;flex-shrink:0}
.stat-card.blue .stat-icon{background:linear-gradient(135deg,#004080,#0059b3)}
.stat-card.green .stat-icon{background:linear-gradient(135deg,#059669,#10b981)}
.stat-card.orange .stat-icon{background:linear-gradient(135deg,#ea580c,#f97316)}
.stat-card.red .stat-icon{background:linear-gradient(135deg,#b91c1c,#dc2626)}
.stat-info h3{font-size:26px;font-weight:800;color:#1a202c;margin:0 0 2px}
.stat-info p{font-size:12px;color:#888;margin:0;font-weight:500}
/* FILTER */
.filter-bar{background:white;border-radius:12px;padding:14px 20px;margin-bottom:20px;display:flex;gap:8px;flex-wrap:wrap;align-items:center;box-shadow:0 2px 10px rgba(0,0,0,.07)}
.filter-label{font-size:11px;font-weight:700;color:#aaa;text-transform:uppercase;letter-spacing:1px;margin-right:4px}
.fbtn{padding:8px 18px;border:2px solid #e0e6ef;background:#f8fafc;color:#5a6a80;border-radius:20px;cursor:pointer;font-size:13px;font-weight:600;transition:.2s;display:inline-flex;align-items:center;gap:6px}
.fbtn:hover{border-color:#004080;color:#004080}
.fbtn.active{background:linear-gradient(135deg,#004080,#0059b3);border-color:transparent;color:white;box-shadow:0 4px 14px rgba(0,64,128,.3)}
.search-box{margin-left:auto;display:flex;gap:0;border:2px solid #e0e6ef;border-radius:20px;overflow:hidden}
.search-box input{padding:8px 14px;border:none;outline:none;font-size:13px;width:200px}
.search-box button{padding:8px 14px;background:#004080;color:white;border:none;cursor:pointer;font-size:13px}
/* CARDS */
.asg-card{background:white;border-radius:16px;box-shadow:0 3px 14px rgba(0,0,0,.08);margin-bottom:18px;overflow:hidden;transition:.25s;border-left:5px solid #004080}
.asg-card:hover{transform:translateY(-3px);box-shadow:0 10px 28px rgba(0,0,0,.13)}
.asg-card.submitted{border-left-color:#10b981}
.asg-card.overdue{border-left-color:#dc2626}
.asg-card.pending{border-left-color:#f97316}
.asg-card.graded{border-left-color:#7c3aed}
.asg-head{padding:20px 24px 14px;display:flex;align-items:flex-start;gap:16px}
.asg-icon{width:52px;height:52px;border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:22px;color:white;flex-shrink:0}
.asg-icon.submitted{background:linear-gradient(135deg,#059669,#10b981)}
.asg-icon.overdue{background:linear-gradient(135deg,#b91c1c,#dc2626)}
.asg-icon.pending{background:linear-gradient(135deg,#ea580c,#f97316)}
.asg-icon.graded{background:linear-gradient(135deg,#5b21b6,#7c3aed)}
.asg-info{flex:1;min-width:0}
.asg-title{font-size:18px;font-weight:800;color:#1a202c;margin:0 0 6px}
.asg-meta{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.asg-badge{padding:3px 11px;border-radius:14px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:4px}
.badge-submitted{background:#d1fae5;color:#065f46}
.badge-pending{background:#fef3c7;color:#92400e}
.badge-overdue{background:#fee2e2;color:#991b1b}
.badge-graded{background:#ede9fe;color:#5b21b6}
.asg-due{font-size:12px;color:#888;display:flex;align-items:center;gap:5px}
.asg-due.overdue-text{color:#dc2626;font-weight:700}
.asg-desc{padding:0 24px 14px;font-size:13px;color:#4a5568;line-height:1.7;border-bottom:1px solid #f0f4f8}
.asg-footer{padding:14px 24px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;background:#fafbff}
.asg-class{font-size:12px;color:#888;display:flex;align-items:center;gap:5px}
.btn-submit{margin-left:auto;padding:9px 22px;background:linear-gradient(135deg,#004080,#0059b3);color:white;border:none;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:7px;transition:.2s}
.btn-submit:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,64,128,.35)}
.btn-resubmit{background:linear-gradient(135deg,#059669,#10b981)}
.btn-resubmit:hover{box-shadow:0 6px 18px rgba(16,185,129,.35)}
.btn-view-grade{background:linear-gradient(135deg,#5b21b6,#7c3aed)}
.btn-view-grade:hover{box-shadow:0 6px 18px rgba(124,58,237,.35)}
.grade-pill{padding:6px 16px;border-radius:20px;font-size:13px;font-weight:800;background:#ede9fe;color:#5b21b6;display:inline-flex;align-items:center;gap:6px}
/* SUBMIT MODAL */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;display:none;align-items:center;justify-content:center;backdrop-filter:blur(4px)}
.modal-overlay.open{display:flex}
.modal-box{background:white;border-radius:20px;width:100%;max-width:520px;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.3);animation:modalIn .25s ease}
@keyframes modalIn{from{transform:translateY(-20px) scale(.97);opacity:0}to{transform:translateY(0) scale(1);opacity:1}}
.modal-head{background:linear-gradient(135deg,#004080,#0059b3);padding:22px 28px;color:white;display:flex;justify-content:space-between;align-items:center}
.modal-head h3{margin:0;font-size:17px;font-weight:800;display:flex;align-items:center;gap:9px}
.modal-close{background:rgba(255,255,255,.2);border:none;color:white;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:14px;transition:.2s}
.modal-close:hover{background:rgba(255,255,255,.35)}
.modal-body{padding:24px 28px}
.fg{margin-bottom:16px}
.fg label{display:block;font-size:12px;font-weight:700;color:#555;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px}
.fc{width:100%;padding:11px 14px;border:2px solid #e0e6ef;border-radius:10px;font-size:14px;font-family:inherit;transition:.2s;background:#fff;color:#333}
.fc:focus{outline:none;border-color:#004080;box-shadow:0 0 0 3px rgba(0,64,128,.1)}
textarea.fc{resize:vertical;min-height:80px}
.file-drop{border:2px dashed #c0cfe0;border-radius:10px;padding:24px;text-align:center;cursor:pointer;transition:.2s;background:#f8fafc}
.file-drop:hover{border-color:#004080;background:#f0f4ff}
.file-drop i{font-size:32px;color:#004080;margin-bottom:8px}
.file-drop p{font-size:13px;color:#888;margin:0}
.file-drop .file-name{font-size:13px;color:#004080;font-weight:700;margin-top:6px}
.modal-alert{padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:14px;display:none}
.modal-alert.ok{background:#d1fae5;color:#065f46;border:1px solid #a7f3d0}
.modal-alert.err{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5}
.modal-foot{padding:16px 28px;border-top:1px solid #f0f0f0;display:flex;gap:10px;justify-content:flex-end;background:#fafafa}
.mbtn{padding:11px 24px;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;transition:.2s;display:flex;align-items:center;gap:7px}
.mbtn-save{background:linear-gradient(135deg,#004080,#0059b3);color:white}
.mbtn-save:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,64,128,.35)}
.mbtn-cancel{background:#f0f0f0;color:#555}
.mbtn-cancel:hover{background:#e0e0e0}
/* GRADE MODAL */
.grade-modal-box{max-width:460px}
.grade-display{text-align:center;padding:20px 0}
.grade-circle{width:100px;height:100px;border-radius:50%;background:linear-gradient(135deg,#5b21b6,#7c3aed);color:white;font-size:36px;font-weight:900;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;box-shadow:0 8px 24px rgba(124,58,237,.35)}
.grade-feedback{background:#f8f7ff;border-radius:10px;padding:14px;font-size:14px;color:#374151;line-height:1.7;margin-top:14px;border-left:4px solid #7c3aed}
.empty-state{text-align:center;padding:80px 20px}
.empty-state i{font-size:64px;color:#c5cfe0;margin-bottom:16px}
.empty-state h3{color:#4a5568;font-size:20px;margin:0 0 8px}
.empty-state p{color:#888;font-size:14px}
.toast{position:fixed;bottom:22px;right:22px;color:white;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:700;z-index:99999;display:none;box-shadow:0 4px 15px rgba(0,0,0,.2);animation:slideUp .3s ease}
@keyframes slideUp{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}
.toast.ok{background:linear-gradient(135deg,#28a745,#20c997)}
.toast.err{background:linear-gradient(135deg,#dc3545,#c82333)}
footer{background:#00264d;color:white;text-align:center;padding:12px;font-size:13px}
@media(max-width:640px){.stats-row{grid-template-columns:1fr 1fr}.asg-head{flex-direction:column}.btn-submit{margin-left:0}}
</style>
</head>
<body>
<div class="top-bar"><marquee>SCTI Student Portal — My Assignments</marquee></div>
<div class="pg-header">
  <div>
    <h1><i class="fa fa-tasks"></i> My Assignments</h1>
    <div class="bc">
      <a href="../dashboards/student-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Assignments
    </div>
  </div>
  <a href="../dashboards/student-dashboard.php" class="btn-hdr"><i class="fa fa-arrow-left"></i> Back</a>
</div>

<div class="wrap">
  <!-- STATS -->
  <div class="stats-row">
    <div class="stat-card blue" onclick="filterCards('all')">
      <div class="stat-icon"><i class="fa fa-tasks"></i></div>
      <div class="stat-info"><h3><?=$total?></h3><p>Total Assignments</p></div>
    </div>
    <div class="stat-card green" onclick="filterCards('submitted')">
      <div class="stat-icon"><i class="fa fa-check-circle"></i></div>
      <div class="stat-info"><h3><?=$submitted?></h3><p>Submitted</p></div>
    </div>
    <div class="stat-card orange" onclick="filterCards('pending')">
      <div class="stat-icon"><i class="fa fa-clock"></i></div>
      <div class="stat-info"><h3><?=$pending?></h3><p>Pending</p></div>
    </div>
    <div class="stat-card red" onclick="filterCards('overdue')">
      <div class="stat-icon"><i class="fa fa-exclamation-circle"></i></div>
      <div class="stat-info"><h3><?=$overdue?></h3><p>Overdue</p></div>
    </div>
  </div>

  <!-- FILTER BAR -->
  <div class="filter-bar">
    <span class="filter-label">Filter:</span>
    <button class="fbtn active" id="fbAll" onclick="filterCards('all')"><i class="fa fa-border-all"></i> All</button>
    <button class="fbtn" id="fbPending" onclick="filterCards('pending')"><i class="fa fa-clock"></i> Pending</button>
    <button class="fbtn" id="fbSubmitted" onclick="filterCards('submitted')"><i class="fa fa-check"></i> Submitted</button>
    <button class="fbtn" id="fbOverdue" onclick="filterCards('overdue')"><i class="fa fa-exclamation-circle"></i> Overdue</button>
    <button class="fbtn" id="fbGraded" onclick="filterCards('graded')"><i class="fa fa-star"></i> Graded</button>
    <div class="search-box">
      <input type="text" id="searchInput" placeholder="Search assignments..." oninput="filterCards(currentFilter)">
      <button><i class="fa fa-search"></i></button>
    </div>
  </div>

  <!-- ASSIGNMENT CARDS -->
  <div id="asgList">
<?php if (empty($assignments)): ?>
    <div class="empty-state">
      <i class="fa fa-tasks"></i>
      <h3>No Assignments Yet</h3>
      <p>Your teacher hasn't posted any assignments yet. Check back later.</p>
    </div>
<?php else: ?>
<?php foreach ($assignments as $a):
    $isSubmitted = !empty($a['sub_id']);
    $isGraded    = $isSubmitted && !empty($a['grade']);
    $isOverdue   = !$isSubmitted && strtotime($a['due_date']) <= time();
    $isPending   = !$isSubmitted && !$isOverdue;
    $dueTs       = strtotime($a['due_date']);
    $dueStr      = date('M d, Y h:i A', $dueTs);
    $daysLeft    = ceil(($dueTs - time()) / 86400);

    if ($isGraded)       $cardClass = 'graded';
    elseif ($isSubmitted) $cardClass = 'submitted';
    elseif ($isOverdue)   $cardClass = 'overdue';
    else                  $cardClass = 'pending';

    $iconClass = $cardClass;
    $iconFa    = $isGraded ? 'fa-star' : ($isSubmitted ? 'fa-check-circle' : ($isOverdue ? 'fa-times-circle' : 'fa-hourglass-half'));

    $dataAttr = "data-status=\"$cardClass\"";
?>
    <div class="asg-card <?=$cardClass?>" <?=$dataAttr?> id="asgCard<?=$a['id']?>">
      <div class="asg-head">
        <div class="asg-icon <?=$iconClass?>"><i class="fa <?=$iconFa?>"></i></div>
        <div class="asg-info">
          <div class="asg-title"><?=htmlspecialchars($a['title'])?></div>
          <div class="asg-meta">
            <?php if ($isGraded): ?>
              <span class="asg-badge badge-graded"><i class="fa fa-star"></i> Graded: <?=htmlspecialchars($a['grade'])?></span>
            <?php elseif ($isSubmitted): ?>
              <span class="asg-badge badge-submitted"><i class="fa fa-check"></i> Submitted</span>
            <?php elseif ($isOverdue): ?>
              <span class="asg-badge badge-overdue"><i class="fa fa-times-circle"></i> Overdue</span>
            <?php else: ?>
              <span class="asg-badge badge-pending"><i class="fa fa-clock"></i> Pending</span>
            <?php endif; ?>
            <span class="asg-due <?=$isOverdue ? 'overdue-text' : ''?>">
              <i class="fa fa-calendar"></i>
              Due: <?=$dueStr?>
              <?php if ($isPending && $daysLeft >= 0): ?>
                &nbsp;(<?=$daysLeft?> day<?=$daysLeft!=1?'s':''?> left)
              <?php endif; ?>
            </span>
          </div>
        </div>
      </div>
      <?php if (!empty($a['description'])): ?>
      <div class="asg-desc"><?=nl2br(htmlspecialchars($a['description']))?></div>
      <?php endif; ?>
      <div class="asg-footer">
        <span class="asg-class"><i class="fa fa-chalkboard-teacher"></i> <?=htmlspecialchars($a['class_name'] ?? 'General')?></span>
        <?php if ($isSubmitted && !empty($a['submitted_at'])): ?>
          <span class="asg-class"><i class="fa fa-upload"></i> Submitted: <?=date('M d, Y', strtotime($a['submitted_at']))?></span>
        <?php endif; ?>
        <?php if ($isGraded): ?>
          <div class="grade-pill"><i class="fa fa-star"></i> Grade: <?=htmlspecialchars($a['grade'])?></div>
          <button class="btn-submit btn-view-grade" onclick="openGrade(<?=$a['id']?>)"><i class="fa fa-eye"></i> View Feedback</button>
        <?php elseif ($isSubmitted): ?>
          <button class="btn-submit btn-resubmit" onclick="openSubmit(<?=$a['id']?>, '<?=htmlspecialchars(addslashes($a['title']))?>', true)"><i class="fa fa-redo"></i> Resubmit</button>
        <?php elseif (!$isOverdue): ?>
          <button class="btn-submit" onclick="openSubmit(<?=$a['id']?>, '<?=htmlspecialchars(addslashes($a['title']))?>', false)"><i class="fa fa-paper-plane"></i> Submit Assignment</button>
        <?php else: ?>
          <span style="margin-left:auto;font-size:12px;color:#dc2626;font-weight:700"><i class="fa fa-lock"></i> Submission Closed</span>
        <?php endif; ?>
      </div>
    </div>
<?php endforeach; ?>
<?php endif; ?>
  </div>
</div>

<!-- SUBMIT MODAL -->
<div class="modal-overlay" id="submitModal">
  <div class="modal-box">
    <div class="modal-head">
      <h3><i class="fa fa-paper-plane"></i> <span id="modalTitleTxt">Submit Assignment</span></h3>
      <button class="modal-close" onclick="closeModal('submitModal')"><i class="fa fa-times"></i></button>
    </div>
    <div class="modal-body">
      <div class="modal-alert" id="submitAlert"></div>
      <input type="hidden" id="submitAsgId">
      <div class="fg">
        <label>Assignment</label>
        <input type="text" id="submitAsgTitle" class="fc" readonly>
      </div>
      <div class="fg">
        <label>Upload File <small style="color:#999;font-weight:400">(PDF, DOC, DOCX, ZIP, Image — max 10MB)</small></label>
        <div class="file-drop" id="fileDrop" onclick="document.getElementById('fileInput').click()">
          <i class="fa fa-cloud-upload-alt"></i>
          <p>Click to choose file or drag & drop here</p>
          <div class="file-name" id="fileNameDisplay"></div>
        </div>
        <input type="file" id="fileInput" style="display:none" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.zip,.rar" onchange="onFileSelect(this)">
      </div>
      <div class="fg">
        <label>Notes / Comments <small style="color:#999;font-weight:400">(Optional)</small></label>
        <textarea id="submitNotes" class="fc" placeholder="Any notes for your teacher..."></textarea>
      </div>
    </div>
    <div class="modal-foot">
      <button class="mbtn mbtn-cancel" onclick="closeModal('submitModal')"><i class="fa fa-times"></i> Cancel</button>
      <button class="mbtn mbtn-save" id="submitBtn" onclick="doSubmit()"><i class="fa fa-paper-plane"></i> <span id="submitBtnTxt">Submit</span></button>
    </div>
  </div>
</div>

<!-- GRADE MODAL -->
<div class="modal-overlay" id="gradeModal">
  <div class="modal-box grade-modal-box">
    <div class="modal-head" style="background:linear-gradient(135deg,#5b21b6,#7c3aed)">
      <h3><i class="fa fa-star"></i> Grade & Feedback</h3>
      <button class="modal-close" onclick="closeModal('gradeModal')"><i class="fa fa-times"></i></button>
    </div>
    <div class="modal-body">
      <div class="grade-display">
        <div class="grade-circle" id="gradeCircle">—</div>
        <div style="font-size:14px;color:#888;font-weight:600" id="gradeAsgTitle"></div>
      </div>
      <div class="grade-feedback" id="gradeFeedback" style="display:none"></div>
    </div>
    <div class="modal-foot">
      <button class="mbtn mbtn-cancel" onclick="closeModal('gradeModal')"><i class="fa fa-check"></i> Close</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>
<footer>© 2025 SCTI — Student Portal</footer>

<script>
var currentFilter = 'all';

// Grade data from PHP
var gradeData = <?php
  $gd = [];
  foreach ($assignments as $a) {
    if (!empty($a['sub_id'])) {
      $gd[$a['id']] = ['grade' => $a['grade'] ?? '', 'feedback' => $a['feedback'] ?? '', 'title' => $a['title']];
    }
  }
  echo json_encode($gd);
?>;

function filterCards(status) {
  currentFilter = status;
  var q = (document.getElementById('searchInput').value || '').toLowerCase();
  // update filter buttons
  ['fbAll','fbPending','fbSubmitted','fbOverdue','fbGraded'].forEach(function(id) {
    document.getElementById(id).classList.remove('active');
  });
  var map = {all:'fbAll',pending:'fbPending',submitted:'fbSubmitted',overdue:'fbOverdue',graded:'fbGraded'};
  if (map[status]) document.getElementById(map[status]).classList.add('active');

  document.querySelectorAll('.asg-card').forEach(function(card) {
    var s = card.getAttribute('data-status');
    var title = card.querySelector('.asg-title').textContent.toLowerCase();
    var matchStatus = (status === 'all') || (s === status);
    var matchSearch = !q || title.includes(q);
    card.style.display = (matchStatus && matchSearch) ? 'block' : 'none';
  });
}

function openSubmit(id, title, isResubmit) {
  document.getElementById('submitAsgId').value = id;
  document.getElementById('submitAsgTitle').value = title;
  document.getElementById('submitNotes').value = '';
  document.getElementById('fileNameDisplay').textContent = '';
  document.getElementById('fileInput').value = '';
  document.getElementById('submitAlert').style.display = 'none';
  document.getElementById('modalTitleTxt').textContent = isResubmit ? 'Resubmit Assignment' : 'Submit Assignment';
  document.getElementById('submitBtnTxt').textContent = isResubmit ? 'Resubmit' : 'Submit';
  document.getElementById('submitModal').classList.add('open');
}

function onFileSelect(input) {
  var name = input.files[0] ? input.files[0].name : '';
  document.getElementById('fileNameDisplay').textContent = name ? '📎 ' + name : '';
}

function doSubmit() {
  var id    = document.getElementById('submitAsgId').value;
  var notes = document.getElementById('submitNotes').value.trim();
  var file  = document.getElementById('fileInput').files[0];
  var alert = document.getElementById('submitAlert');
  alert.style.display = 'none';

  var fd = new FormData();
  fd.append('assignment_id', id);
  fd.append('notes', notes);
  if (file) fd.append('file', file);

  var btn = document.getElementById('submitBtn');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Submitting...';

  fetch('assignment-submit.php', { method: 'POST', body: fd })
    .then(function(r){ return r.json(); })
    .then(function(res) {
      btn.disabled = false;
      btn.innerHTML = '<i class="fa fa-paper-plane"></i> <span id="submitBtnTxt">Submit</span>';
      if (res.success) {
        closeModal('submitModal');
        showToast(res.message || 'Submitted!', 'ok');
        setTimeout(function(){ location.reload(); }, 1500);
      } else {
        alert.textContent = res.message || 'Submission failed.';
        alert.className = 'modal-alert err';
        alert.style.display = 'block';
      }
    })
    .catch(function() {
      btn.disabled = false;
      btn.innerHTML = '<i class="fa fa-paper-plane"></i> <span id="submitBtnTxt">Submit</span>';
      alert.textContent = 'Network error. Please try again.';
      alert.className = 'modal-alert err';
      alert.style.display = 'block';
    });
}

function openGrade(id) {
  var d = gradeData[id];
  if (!d) return;
  document.getElementById('gradeCircle').textContent = d.grade || '—';
  document.getElementById('gradeAsgTitle').textContent = d.title || '';
  var fb = document.getElementById('gradeFeedback');
  if (d.feedback) {
    fb.textContent = '💬 ' + d.feedback;
    fb.style.display = 'block';
  } else {
    fb.style.display = 'none';
  }
  document.getElementById('gradeModal').classList.add('open');
}

function closeModal(id) {
  document.getElementById(id).classList.remove('open');
}

function showToast(msg, type) {
  var t = document.getElementById('toast');
  t.textContent = msg; t.className = 'toast ' + (type||'ok');
  t.style.display = 'block';
  setTimeout(function(){ t.style.display='none'; }, 3500);
}

// Drag & drop
var drop = document.getElementById('fileDrop');
drop.addEventListener('dragover', function(e){ e.preventDefault(); drop.style.borderColor='#004080'; });
drop.addEventListener('dragleave', function(){ drop.style.borderColor=''; });
drop.addEventListener('drop', function(e){
  e.preventDefault(); drop.style.borderColor='';
  var f = e.dataTransfer.files[0];
  if (f) {
    var dt = new DataTransfer(); dt.items.add(f);
    document.getElementById('fileInput').files = dt.files;
    document.getElementById('fileNameDisplay').textContent = '📎 ' + f.name;
  }
});
</script>
</body>
</html>
