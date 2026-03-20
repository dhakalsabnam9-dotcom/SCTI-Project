<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header('Location: ../index.php'); exit();
}
require_once '../includes/config.php';

$studentId = intval($_SESSION['user_id'] ?? 0);

try {
    $db = getDBConnection();
    $assignments = $db->prepare(
        "SELECT a.*, t.full_name as teacher_name,
                s.id as sub_id, s.file_path, s.file_name, s.notes as sub_notes,
                s.grade, s.feedback, s.status as sub_status, s.submitted_at, s.graded_at
         FROM assignments a
         LEFT JOIN teachers t ON a.created_by = t.id
         LEFT JOIN assignment_submissions s ON s.assignment_id = a.id AND s.student_id = ?
         ORDER BY a.due_date ASC"
    );
    $assignments->execute([$studentId]);
    $assignments = $assignments->fetchAll();

    $now = time();
    $total = count($assignments);
    $pending = 0; $overdue = 0; $submitted = 0; $graded = 0;
    foreach ($assignments as $a) {
        if ($a['sub_id']) {
            $submitted++;
            if ($a['sub_status'] === 'graded') $graded++;
        } elseif (strtotime($a['due_date']) < $now) $overdue++;
        else $pending++;
    }
} catch(Exception $e) {
    $assignments = []; $total = 0; $pending = 0; $overdue = 0; $submitted = 0; $graded = 0;
}

function daysLeft($due) {
    $diff = strtotime($due) - time();
    if ($diff < 0) return abs(floor($diff/86400)).' days overdue';
    if ($diff < 86400) return 'Due today';
    return floor($diff/86400).' days left';
}
function isOverdue($due) { return strtotime($due) < time(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Assignments | SCTI Student Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Segoe UI',sans-serif;background:#f5f7fa}
    .top-header{background:linear-gradient(135deg,#6f42c1,#e83e8c);color:white;padding:10px 20px;font-size:14px}
    .footer{background:#2c3e50;color:white;text-align:center;padding:20px;margin-top:40px}
    .container{max-width:1100px;margin:0 auto;padding:20px}
    .page-header{background:linear-gradient(135deg,#6f42c1,#e83e8c);color:white;padding:28px 30px;border-radius:14px;margin-bottom:26px;box-shadow:0 4px 18px rgba(111,66,193,.25)}
    .page-header h1{margin:0 0 6px;font-size:26px;display:flex;align-items:center;gap:10px}
    .breadcrumb{font-size:13px;opacity:.85}
    .breadcrumb a{color:white;text-decoration:none}
    .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:24px}
    .stat-card{background:white;padding:18px 20px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.07);display:flex;align-items:center;gap:14px;cursor:pointer;border:2px solid transparent;transition:.25s}
    .stat-card:hover,.stat-card.active-filter{border-color:#6f42c1;transform:translateY(-3px);box-shadow:0 8px 22px rgba(111,66,193,.18)}
    .stat-icon{width:48px;height:48px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;color:white;flex-shrink:0}
    .si-purple{background:linear-gradient(135deg,#6f42c1,#e83e8c)}
    .si-yellow{background:linear-gradient(135deg,#fd7e14,#ffc107)}
    .si-red{background:linear-gradient(135deg,#dc3545,#fd7e14)}
    .si-green{background:linear-gradient(135deg,#28a745,#20c997)}
    .si-blue{background:linear-gradient(135deg,#007bff,#17a2b8)}
    .stat-info h3{font-size:26px;font-weight:800;color:#6f42c1;line-height:1}
    .stat-info p{font-size:12px;color:#888;margin-top:3px}
    .filter-tabs{display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap}
    .filter-tab{padding:8px 18px;border:2px solid #6f42c1;border-radius:8px;background:white;color:#6f42c1;cursor:pointer;font-weight:600;font-size:13px;transition:.2s}
    .filter-tab:hover{background:#f3eeff}
    .filter-tab.active{background:linear-gradient(135deg,#6f42c1,#e83e8c);color:white;border-color:transparent}
    .assignment-grid{display:grid;gap:16px}
    .acard{background:white;border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,.08);border-left:5px solid #6f42c1;overflow:hidden;transition:.25s}
    .acard.overdue{border-left-color:#dc3545}
    .acard.submitted{border-left-color:#28a745}
    .acard.graded{border-left-color:#007bff}
    .acard:hover{transform:translateY(-3px);box-shadow:0 10px 28px rgba(111,66,193,.15)}
    .acard-top{padding:18px 20px 14px}
    .acard-header{display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:10px}
    .acard-title{font-size:16px;font-weight:700;color:#1a202c}
    .acard-sub{font-size:12px;color:#888;margin-top:3px;display:flex;align-items:center;gap:6px}
    .badge{padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap}
    .badge-upcoming{background:#fff3cd;color:#856404}
    .badge-overdue{background:#f8d7da;color:#721c24}
    .badge-today{background:#cce5ff;color:#004085}
    .badge-submitted{background:#d4edda;color:#155724}
    .badge-graded{background:#cce5ff;color:#004085}
    .badge-late{background:#f8d7da;color:#721c24}
    .acard-meta{display:flex;gap:14px;flex-wrap:wrap;font-size:12px;color:#777;margin-bottom:10px}
    .acard-meta span{display:flex;align-items:center;gap:5px}
    .acard-meta i{color:#6f42c1}
    .acard-desc{font-size:13px;color:#666;background:#f8f9fa;border-radius:8px;padding:10px 12px;margin-bottom:12px;line-height:1.6}
    .progress-wrap{background:#f0f0f0;border-radius:20px;height:5px;margin-bottom:14px}
    .progress-bar{height:100%;border-radius:20px}
    /* Submission section */
    .acard-submit{border-top:1px solid #f0f0f0;padding:16px 20px;background:#fafbff}
    .submit-section h4{font-size:13px;font-weight:700;color:#6f42c1;margin-bottom:10px;display:flex;align-items:center;gap:6px}
    .upload-area{border:2px dashed #c5b3e6;border-radius:10px;padding:16px;text-align:center;cursor:pointer;transition:.2s;background:#fff;margin-bottom:10px}
    .upload-area:hover{border-color:#6f42c1;background:#f8f4ff}
    .upload-area i{font-size:24px;color:#c5b3e6;display:block;margin-bottom:6px}
    .upload-area p{font-size:12px;color:#999;margin:0}
    .upload-area input[type=file]{display:none}
    .file-preview{display:none;align-items:center;gap:8px;background:#e8f0fe;border-radius:8px;padding:8px 12px;font-size:12px;color:#004080;margin-bottom:10px}
    .file-preview i{color:#004080}
    .notes-input{width:100%;padding:9px 12px;border:2px solid #dee2e6;border-radius:8px;font-size:13px;font-family:inherit;resize:vertical;min-height:60px;transition:.2s}
    .notes-input:focus{outline:none;border-color:#6f42c1}
    .btn-submit-work{width:100%;padding:11px;background:linear-gradient(135deg,#6f42c1,#e83e8c);color:white;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:.25s;margin-top:10px}
    .btn-submit-work:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(111,66,193,.35)}
    /* Submitted state */
    .submitted-info{background:#d4edda;border-radius:10px;padding:12px 14px;margin-bottom:10px;font-size:13px;color:#155724}
    .submitted-info .si-row{display:flex;align-items:center;gap:8px;margin-bottom:4px}
    .submitted-info .si-row:last-child{margin-bottom:0}
    /* Feedback section */
    .feedback-box{background:linear-gradient(135deg,#e8f4fd,#f0f8ff);border:1px solid #bee5eb;border-radius:10px;padding:14px 16px;margin-top:10px}
    .feedback-box h5{font-size:13px;font-weight:700;color:#004080;margin-bottom:10px;display:flex;align-items:center;gap:6px}
    .grade-display{display:inline-flex;align-items:center;gap:6px;background:linear-gradient(135deg,#6f42c1,#e83e8c);color:white;padding:6px 16px;border-radius:20px;font-size:15px;font-weight:800;margin-bottom:8px}
    .feedback-text{font-size:13px;color:#555;line-height:1.6;background:white;border-radius:8px;padding:10px 12px}
    .empty-state{text-align:center;padding:60px 20px;color:#999;background:white;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.06)}
    .empty-icon{width:64px;height:64px;border-radius:50%;background:#f0f0f0;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:26px;color:#bbb}
    .toast{position:fixed;bottom:22px;right:22px;background:linear-gradient(135deg,#6f42c1,#e83e8c);color:white;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:700;z-index:99999;display:none;box-shadow:0 6px 20px rgba(111,66,193,.4)}
  </style>
</head>
<body>
<div class="top-header"><marquee>Assignments — Upload your work and view teacher feedback</marquee></div>
<div class="container">
  <div class="page-header">
    <h1><i class="fa fa-tasks"></i> My Assignments</h1>
    <div class="breadcrumb"><a href="../dashboards/student-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Assignments</div>
  </div>

  <div class="stats-grid">
    <div class="stat-card active-filter" id="sc-all" onclick="setFilter('all')">
      <div class="stat-icon si-purple"><i class="fa fa-list"></i></div>
      <div class="stat-info"><h3><?=$total?></h3><p>Total</p></div>
    </div>
    <div class="stat-card" id="sc-pending" onclick="setFilter('pending')">
      <div class="stat-icon si-yellow"><i class="fa fa-clock"></i></div>
      <div class="stat-info"><h3><?=$pending?></h3><p>Pending</p></div>
    </div>
    <div class="stat-card" id="sc-overdue" onclick="setFilter('overdue')">
      <div class="stat-icon si-red"><i class="fa fa-exclamation-circle"></i></div>
      <div class="stat-info"><h3><?=$overdue?></h3><p>Overdue</p></div>
    </div>
    <div class="stat-card" id="sc-submitted" onclick="setFilter('submitted')">
      <div class="stat-icon si-green"><i class="fa fa-paper-plane"></i></div>
      <div class="stat-info"><h3><?=$submitted?></h3><p>Submitted</p></div>
    </div>
    <div class="stat-card" id="sc-graded" onclick="setFilter('graded')">
      <div class="stat-icon si-blue"><i class="fa fa-star"></i></div>
      <div class="stat-info"><h3><?=$graded?></h3><p>Graded</p></div>
    </div>
  </div>

  <div class="filter-tabs">
    <button class="filter-tab active" id="tab-all"       onclick="setFilter('all')">All (<?=$total?>)</button>
    <button class="filter-tab"        id="tab-pending"   onclick="setFilter('pending')">Pending (<?=$pending?>)</button>
    <button class="filter-tab"        id="tab-overdue"   onclick="setFilter('overdue')">Overdue (<?=$overdue?>)</button>
    <button class="filter-tab"        id="tab-submitted" onclick="setFilter('submitted')">Submitted (<?=$submitted?>)</button>
    <button class="filter-tab"        id="tab-graded"    onclick="setFilter('graded')">Graded (<?=$graded?>)</button>
  </div>

  <div class="assignment-grid" id="assignmentGrid">
    <?php if (empty($assignments)): ?>
    <div class="empty-state">
      <div class="empty-icon"><i class="fa fa-check"></i></div>
      <p>No assignments found.</p>
    </div>
    <?php else: ?>
    <?php foreach ($assignments as $a):
        $over     = isOverdue($a['due_date']);
        $dueTs    = strtotime($a['due_date']);
        $isToday  = date('Y-m-d', $dueTs) === date('Y-m-d');
        $hasSub   = !empty($a['sub_id']);
        $isGraded = $hasSub && $a['sub_status'] === 'graded';
        $isLate   = $hasSub && $a['sub_status'] === 'late';

        // Card class
        if ($isGraded)       $cardCls = 'graded';
        elseif ($hasSub)     $cardCls = 'submitted';
        elseif ($over)       $cardCls = 'overdue';
        else                 $cardCls = 'pending';

        // Filter tag
        $filterTag = $isGraded ? 'graded' : ($hasSub ? 'submitted' : ($over ? 'overdue' : 'pending'));

        // Badge
        if ($isGraded)       { $bdg = 'badge-graded';    $bdgTxt = 'Graded'; }
        elseif ($isLate)     { $bdg = 'badge-late';      $bdgTxt = 'Late Submission'; }
        elseif ($hasSub)     { $bdg = 'badge-submitted'; $bdgTxt = 'Submitted'; }
        elseif ($over)       { $bdg = 'badge-overdue';   $bdgTxt = 'Overdue'; }
        elseif ($isToday)    { $bdg = 'badge-today';     $bdgTxt = 'Due Today'; }
        else                 { $bdg = 'badge-upcoming';  $bdgTxt = 'Upcoming'; }

        $dl      = daysLeft($a['due_date']);
        $points  = intval($a['total_points'] ?? 100);
        $dueStr  = date('M d, Y', $dueTs);
        $aid     = intval($a['id']);
        $remaining = max(0, $dueTs - time());
        $progress  = $over ? 100 : max(5, round((1 - $remaining/(30*86400))*100));
        $pbColor   = $over ? 'linear-gradient(90deg,#dc3545,#fd7e14)' : ($hasSub ? 'linear-gradient(90deg,#28a745,#20c997)' : 'linear-gradient(90deg,#6f42c1,#e83e8c)');
    ?>
    <div class="acard <?=$cardCls?>" data-filter="<?=$filterTag?>">
      <div class="acard-top">
        <div class="acard-header">
          <div>
            <div class="acard-title"><?=htmlspecialchars($a['title'])?></div>
            <div class="acard-sub">
              <i class="fa fa-chalkboard-teacher" style="color:#6f42c1"></i>
              <?=htmlspecialchars($a['class_name'] ?? 'General')?>
              &nbsp;|&nbsp;
              <i class="fa fa-user" style="color:#6f42c1"></i>
              <?=htmlspecialchars($a['teacher_name'] ?? 'Teacher')?>
            </div>
          </div>
          <span class="badge <?=$bdg?>"><?=$bdgTxt?></span>
        </div>
        <div class="acard-meta">
          <span><i class="fa fa-calendar"></i> Due: <?=$dueStr?></span>
          <span><i class="fa fa-hourglass-half"></i> <?=$dl?></span>
          <span><i class="fa fa-star"></i> <?=$points?> pts</span>
        </div>
        <?php if (!empty($a['description'])): ?>
        <div class="acard-desc"><?=nl2br(htmlspecialchars($a['description']))?></div>
        <?php endif; ?>
        <div class="progress-wrap">
          <div class="progress-bar" style="width:<?=$progress?>%;background:<?=$pbColor?>"></div>
        </div>
      </div>

      <!-- SUBMISSION SECTION -->
      <div class="acard-submit">
        <?php if ($isGraded): ?>
          <!-- GRADED: show submission + feedback -->
          <div class="submitted-info">
            <div class="si-row"><i class="fa fa-check-circle"></i> Submitted on <?=date('M d, Y h:i A', strtotime($a['submitted_at']))?></div>
            <?php if ($a['file_name']): ?>
            <div class="si-row"><i class="fa fa-paperclip"></i> <?=htmlspecialchars($a['file_name'])?></div>
            <?php endif; ?>
            <?php if ($a['sub_notes']): ?>
            <div class="si-row"><i class="fa fa-sticky-note"></i> <?=htmlspecialchars($a['sub_notes'])?></div>
            <?php endif; ?>
          </div>
          <div class="feedback-box">
            <h5><i class="fa fa-comment-dots"></i> Teacher Feedback</h5>
            <?php if ($a['grade']): ?>
            <div class="grade-display"><i class="fa fa-star"></i> Grade: <?=htmlspecialchars($a['grade'])?> / <?=$points?></div>
            <?php endif; ?>
            <div class="feedback-text"><?=nl2br(htmlspecialchars($a['feedback'] ?? 'No feedback provided yet.'))?></div>
          </div>

        <?php elseif ($hasSub): ?>
          <!-- SUBMITTED but not graded yet -->
          <div class="submitted-info">
            <div class="si-row"><i class="fa fa-check-circle"></i> Submitted on <?=date('M d, Y h:i A', strtotime($a['submitted_at']))?></div>
            <?php if ($a['file_name']): ?>
            <div class="si-row"><i class="fa fa-paperclip"></i> <?=htmlspecialchars($a['file_name'])?></div>
            <?php endif; ?>
            <?php if ($a['sub_notes']): ?>
            <div class="si-row"><i class="fa fa-sticky-note"></i> <?=htmlspecialchars($a['sub_notes'])?></div>
            <?php endif; ?>
          </div>
          <div style="font-size:12px;color:#888;display:flex;align-items:center;gap:6px;margin-top:6px">
            <i class="fa fa-clock" style="color:#fd7e14"></i> Waiting for teacher to grade...
          </div>
          <!-- Allow resubmit -->
          <div style="margin-top:10px">
            <button class="btn-submit-work" style="background:linear-gradient(135deg,#fd7e14,#ffc107)" onclick="toggleUpload(<?=$aid?>)">
              <i class="fa fa-redo"></i> Resubmit
            </button>
          </div>
          <div id="upload-<?=$aid?>" style="display:none;margin-top:10px">
            <?php include_once 'upload-form-partial.php'; // inline below ?>
            <?= renderUploadForm($aid) ?>
          </div>

        <?php else: ?>
          <!-- NOT submitted yet -->
          <div class="submit-section">
            <h4><i class="fa fa-upload"></i> Submit Your Work</h4>
            <?= renderUploadForm($aid) ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
<footer class="footer"><p>&copy; 2025 SCTI - Student Portal</p></footer>
<div class="toast" id="toast"></div>

<script>
function setFilter(f) {
  ['all','pending','overdue','submitted','graded'].forEach(function(k){
    document.getElementById('tab-'+k).classList.toggle('active', k===f);
    var sc = document.getElementById('sc-'+k);
    if (sc) sc.classList.toggle('active-filter', k===f);
  });
  var cards = document.querySelectorAll('.acard');
  var shown = 0;
  cards.forEach(function(c){
    var match = f==='all' || c.dataset.filter===f;
    c.style.display = match ? '' : 'none';
    if (match) shown++;
  });
  var grid = document.getElementById('assignmentGrid');
  var ex = grid.querySelector('.js-empty');
  if (shown===0 && !ex) {
    var d = document.createElement('div');
    d.className = 'empty-state js-empty';
    d.innerHTML = '<div class="empty-icon"><i class="fa fa-check"></i></div><p>No '+f+' assignments.</p>';
    grid.appendChild(d);
  } else if (shown>0 && ex) { ex.remove(); }
}

function toggleUpload(id) {
  var el = document.getElementById('upload-'+id);
  if (el) el.style.display = el.style.display === 'none' ? 'block' : 'none';
}

function previewFile(input, id) {
  var prev = document.getElementById('fp-'+id);
  if (input.files && input.files[0]) {
    prev.style.display = 'flex';
    prev.querySelector('.fp-name').textContent = input.files[0].name;
  } else {
    prev.style.display = 'none';
  }
}

function submitWork(aid) {
  var form = document.getElementById('form-'+aid);
  var fd = new FormData(form);
  var btn = form.querySelector('.btn-submit-work');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Submitting...';

  fetch('assignment-submit.php', { method:'POST', body: fd })
    .then(function(r){ return r.json(); })
    .then(function(d){
      if (d.success) {
        showToast(d.message);
        setTimeout(function(){ location.reload(); }, 1200);
      } else {
        showToast(d.message || 'Failed', true);
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-paper-plane"></i> Submit';
      }
    })
    .catch(function(){ showToast('Network error', true); btn.disabled=false; });
}

function showToast(msg, err) {
  var t = document.getElementById('toast');
  t.textContent = msg;
  t.style.background = err ? 'linear-gradient(135deg,#dc3545,#c82333)' : 'linear-gradient(135deg,#6f42c1,#e83e8c)';
  t.style.display = 'block';
  setTimeout(function(){ t.style.display='none'; }, 3000);
}
</script>
</body>
</html>
<?php
function renderUploadForm($aid) {
    ob_start(); ?>
    <form id="form-<?=$aid?>" onsubmit="event.preventDefault(); submitWork(<?=$aid?>)">
      <input type="hidden" name="assignment_id" value="<?=$aid?>">
      <div class="upload-area" onclick="document.getElementById('file-<?=$aid?>').click()">
        <i class="fa fa-cloud-upload-alt"></i>
        <p>Click to upload file (PDF, DOC, DOCX, TXT, ZIP, Image)</p>
        <p style="font-size:11px;margin-top:4px">Max 10MB</p>
        <input type="file" id="file-<?=$aid?>" name="file" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.zip,.rar" onchange="previewFile(this, <?=$aid?>)">
      </div>
      <div class="file-preview" id="fp-<?=$aid?>">
        <i class="fa fa-paperclip"></i>
        <span class="fp-name"></span>
        <span style="margin-left:auto;cursor:pointer;color:#dc3545" onclick="document.getElementById('file-<?=$aid?>').value='';document.getElementById('fp-<?=$aid?>').style.display='none'"><i class="fa fa-times"></i></span>
      </div>
      <textarea class="notes-input" name="notes" placeholder="Add a note to your teacher (optional)..."></textarea>
      <button type="submit" class="btn-submit-work">
        <i class="fa fa-paper-plane"></i> Submit Assignment
      </button>
    </form>
    <?php return ob_get_clean();
}
?>
