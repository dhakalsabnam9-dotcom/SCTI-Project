<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header('Location: ../index.php'); exit();
}
require_once '../includes/config.php';
$studentId = intval($_SESSION['user_id'] ?? 0);

try {
    $db = getDBConnection();
    $stmt = $db->prepare("
        SELECT a.*, t.full_name as teacher_name,
               s.id as sub_id, s.file_path, s.file_name, s.notes as sub_notes,
               s.grade, s.feedback, s.status as sub_status, s.submitted_at, s.graded_at
        FROM assignments a
        LEFT JOIN teachers t ON a.created_by = t.id
        LEFT JOIN assignment_submissions s ON s.assignment_id = a.id AND s.student_id = ?
        ORDER BY a.due_date ASC
    ");
    $stmt->execute([$studentId]);
    $assignments = $stmt->fetchAll();
    $now = time();
    $total=count($assignments); $pending=0; $overdue=0; $submitted=0; $graded=0;
    foreach ($assignments as $a) {
        if ($a['sub_id']) { $submitted++; if ($a['sub_status']==='graded') $graded++; }
        elseif (strtotime($a['due_date']) < $now) $overdue++;
        else $pending++;
    }
} catch(Exception $e) { $assignments=[]; $total=0; $pending=0; $overdue=0; $submitted=0; $graded=0; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Assignments | SCTI Student</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',sans-serif;background:#f0f4f8;min-height:100vh}
.top-bar{background:#00264d;color:white;padding:7px 20px;font-size:13px}
.pg-header{background:linear-gradient(135deg,#6f42c1,#e83e8c);color:#fff;padding:20px 28px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 18px rgba(111,66,193,.25)}
.pg-header h1{font-size:22px;font-weight:700;display:flex;align-items:center;gap:10px;margin:0 0 3px}
.pg-header .bc{font-size:12px;color:rgba(255,255,255,.75)}
.pg-header .bc a{color:#fff;text-decoration:none}
.btn-back{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;border:none;transition:.2s;text-decoration:none;background:rgba(255,255,255,.18);color:#fff}
.btn-back:hover{background:rgba(255,255,255,.32)}
.wrap{max-width:1000px;margin:24px auto;padding:0 20px 60px}
.stats-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;margin-bottom:20px}
.stat-pill{background:white;border-radius:12px;padding:14px 16px;box-shadow:0 2px 10px rgba(0,0,0,.07);display:flex;align-items:center;gap:10px;cursor:pointer;border:2px solid transparent;transition:.2s}
.stat-pill:hover,.stat-pill.active{border-color:#6f42c1;transform:translateY(-2px)}
.sp-ico{width:40px;height:40px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:17px;color:white;flex-shrink:0}
.si-purple{background:linear-gradient(135deg,#6f42c1,#e83e8c)}
.si-yellow{background:linear-gradient(135deg,#fd7e14,#ffc107)}
.si-red{background:linear-gradient(135deg,#dc3545,#fd7e14)}
.si-green{background:linear-gradient(135deg,#28a745,#20c997)}
.si-blue{background:linear-gradient(135deg,#007bff,#17a2b8)}
.sp-val{font-size:22px;font-weight:800;color:#1a202c;line-height:1}
.sp-lbl{font-size:11px;color:#888;margin-top:2px;font-weight:600}
.filter-tabs{display:flex;gap:8px;margin-bottom:18px;flex-wrap:wrap}
.ftab{padding:8px 18px;border:2px solid #6f42c1;border-radius:8px;background:white;color:#6f42c1;cursor:pointer;font-weight:700;font-size:13px;transition:.2s}
.ftab:hover{background:#f3eeff}
.ftab.active{background:linear-gradient(135deg,#6f42c1,#e83e8c);color:white;border-color:transparent}
.grid{display:grid;gap:16px}
.acard{background:white;border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,.08);border-left:6px solid #6f42c1;overflow:hidden;transition:.25s}
.acard:hover{transform:translateY(-3px);box-shadow:0 10px 28px rgba(111,66,193,.15)}
.acard.overdue{border-left-color:#dc3545}
.acard.submitted{border-left-color:#28a745}
.acard.graded{border-left-color:#007bff}
.acard.closed{border-left-color:#aaa}
.acard-top{padding:18px 20px 14px}
.acard-header{display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:10px}
.acard-title{font-size:16px;font-weight:800;color:#1a202c}
.acard-sub{font-size:12px;color:#888;margin-top:3px;display:flex;align-items:center;gap:5px}
.badge{padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap}
.badge-upcoming{background:#fef3c7;color:#92400e}
.badge-overdue{background:#fee2e2;color:#991b1b}
.badge-today{background:#dbeafe;color:#1e40af}
.badge-submitted{background:#d1fae5;color:#065f46}
.badge-graded{background:#dbeafe;color:#1e40af}
.badge-late{background:#fee2e2;color:#991b1b}
.badge-closed{background:#f0f0f0;color:#888}
.acard-meta{display:flex;gap:14px;flex-wrap:wrap;font-size:12px;color:#777;margin-bottom:10px}
.acard-meta span{display:flex;align-items:center;gap:5px}
.acard-meta i{color:#6f42c1}
.deadline-warn{background:#fff7ed;border-left:3px solid #fd7e14;border-radius:8px;padding:8px 12px;font-size:12px;color:#9a3412;margin-bottom:10px;display:flex;align-items:center;gap:6px}
.deadline-closed{background:#fee2e2;border-left:3px solid #dc3545;border-radius:8px;padding:8px 12px;font-size:12px;color:#991b1b;margin-bottom:10px;display:flex;align-items:center;gap:6px}
.acard-desc{font-size:13px;color:#666;background:#f8f4ff;border-radius:8px;padding:10px 12px;margin-bottom:10px;line-height:1.6;border-left:3px solid #c4b5fd}
.acard-submit{border-top:1px solid #f0f0f0;padding:16px 20px;background:#fafbff}
.submit-head{font-size:13px;font-weight:700;color:#6f42c1;margin-bottom:10px;display:flex;align-items:center;gap:6px}
.upload-area{border:2px dashed #c4b5fd;border-radius:10px;padding:16px;text-align:center;cursor:pointer;transition:.2s;background:#fff;margin-bottom:10px}
.upload-area:hover{border-color:#6f42c1;background:#f8f4ff}
.upload-area i{font-size:24px;color:#c4b5fd;display:block;margin-bottom:6px}
.upload-area p{font-size:12px;color:#999;margin:0}
.upload-area input[type=file]{display:none}
.file-preview{display:none;align-items:center;gap:8px;background:#ede9fe;border-radius:8px;padding:8px 12px;font-size:12px;color:#5b21b6;margin-bottom:10px}
.notes-input{width:100%;padding:9px 12px;border:2px solid #e0e6ef;border-radius:8px;font-size:13px;font-family:inherit;resize:vertical;min-height:60px;transition:.2s}
.notes-input:focus{outline:none;border-color:#6f42c1}
.btn-submit-work{width:100%;padding:11px;background:linear-gradient(135deg,#6f42c1,#e83e8c);color:white;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:.25s;margin-top:10px}
.btn-submit-work:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(111,66,193,.35)}
.btn-submit-work:disabled{opacity:.6;cursor:not-allowed;transform:none}
.btn-delete-sub{width:100%;padding:9px;background:#fee2e2;color:#991b1b;border:2px solid #fca5a5;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px;transition:.2s;margin-top:8px}
.btn-delete-sub:hover{background:#dc3545;color:white;border-color:#dc3545}
.submitted-info{background:#d1fae5;border-radius:10px;padding:12px 14px;margin-bottom:10px;font-size:13px;color:#065f46}
.si-row{display:flex;align-items:center;gap:8px;margin-bottom:4px}
.si-row:last-child{margin-bottom:0}
.feedback-box{background:linear-gradient(135deg,#e8f4fd,#f0f8ff);border:1px solid #bee5eb;border-radius:10px;padding:14px 16px;margin-top:10px}
.feedback-box h5{font-size:13px;font-weight:700;color:#004080;margin-bottom:10px;display:flex;align-items:center;gap:6px}
.grade-display{display:inline-flex;align-items:center;gap:6px;background:linear-gradient(135deg,#6f42c1,#e83e8c);color:white;padding:6px 16px;border-radius:20px;font-size:15px;font-weight:800;margin-bottom:8px}
.feedback-text{font-size:13px;color:#555;line-height:1.6;background:white;border-radius:8px;padding:10px 12px}
.empty{text-align:center;padding:60px 20px;color:#aaa;background:white;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.06)}
.empty i{font-size:56px;display:block;margin-bottom:14px;color:#c4b5fd}
.toast{position:fixed;bottom:22px;right:22px;color:white;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:700;z-index:99999;display:none;align-items:center;gap:8px;box-shadow:0 6px 20px rgba(0,0,0,.2)}
.toast.ok{background:linear-gradient(135deg,#6f42c1,#e83e8c)}
.toast.err{background:#dc3545}
footer{background:#00264d;color:white;text-align:center;padding:12px;font-size:13px;margin-top:40px}
@media(max-width:600px){.acard-meta{gap:8px}.stats-row{grid-template-columns:repeat(2,1fr)}}
</style>
</head>
<body>
<div class="top-bar"><marquee>SCTI Student Portal — Assignments</marquee></div>
<div class="pg-header">
  <div>
    <h1><i class="fa fa-tasks"></i> My Assignments</h1>
    <div class="bc"><a href="../dashboards/student-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Assignments</div>
  </div>
  <a href="../dashboards/student-dashboard.php" class="btn-back"><i class="fa fa-arrow-left"></i> Back</a>
</div>

<div class="wrap">
  <!-- Stats -->
  <div class="stats-row">
    <div class="stat-pill active" id="sc-all" onclick="setFilter('all')"><div class="sp-ico si-purple"><i class="fa fa-list"></i></div><div><div class="sp-val"><?=$total?></div><div class="sp-lbl">Total</div></div></div>
    <div class="stat-pill" id="sc-pending" onclick="setFilter('pending')"><div class="sp-ico si-yellow"><i class="fa fa-clock"></i></div><div><div class="sp-val"><?=$pending?></div><div class="sp-lbl">Pending</div></div></div>
    <div class="stat-pill" id="sc-overdue" onclick="setFilter('overdue')"><div class="sp-ico si-red"><i class="fa fa-exclamation-circle"></i></div><div><div class="sp-val"><?=$overdue?></div><div class="sp-lbl">Overdue</div></div></div>
    <div class="stat-pill" id="sc-submitted" onclick="setFilter('submitted')"><div class="sp-ico si-green"><i class="fa fa-paper-plane"></i></div><div><div class="sp-val"><?=$submitted?></div><div class="sp-lbl">Submitted</div></div></div>
    <div class="stat-pill" id="sc-graded" onclick="setFilter('graded')"><div class="sp-ico si-blue"><i class="fa fa-star"></i></div><div><div class="sp-val"><?=$graded?></div><div class="sp-lbl">Graded</div></div></div>
  </div>

  <!-- Filter tabs -->
  <div class="filter-tabs">
    <button class="ftab active" id="tab-all"       onclick="setFilter('all')">All (<?=$total?>)</button>
    <button class="ftab"        id="tab-pending"   onclick="setFilter('pending')">Pending (<?=$pending?>)</button>
    <button class="ftab"        id="tab-overdue"   onclick="setFilter('overdue')">Overdue (<?=$overdue?>)</button>
    <button class="ftab"        id="tab-submitted" onclick="setFilter('submitted')">Submitted (<?=$submitted?>)</button>
    <button class="ftab"        id="tab-graded"    onclick="setFilter('graded')">Graded (<?=$graded?>)</button>
  </div>

  <div class="grid" id="grid">

<?php if (empty($assignments)): ?>
    <div class="empty"><i class="fa fa-tasks"></i><p>No assignments yet.</p></div>
<?php else: ?>
<?php foreach ($assignments as $a):
    $dueTs    = strtotime($a['due_date']);
    $isOver   = $dueTs < $now;
    $isToday  = date('Y-m-d',$dueTs) === date('Y-m-d');
    $hasSub   = !empty($a['sub_id']);
    $isGraded = $hasSub && $a['sub_status']==='graded';
    $aid      = intval($a['id']);
    $dueStr   = date('M d, Y g:i A', $dueTs);
    $hoursLeft= ($dueTs - $now) / 3600;

    if ($isGraded)   $cardCls='graded';
    elseif ($hasSub) $cardCls='submitted';
    elseif ($isOver) $cardCls='overdue';
    else             $cardCls='pending';

    $filterTag = $isGraded?'graded':($hasSub?'submitted':($isOver?'overdue':'pending'));

    if ($isGraded)       { $bdg='badge-graded';    $bdgTxt='Graded'; }
    elseif ($hasSub)     { $bdg='badge-submitted'; $bdgTxt='Submitted'; }
    elseif ($isOver)     { $bdg='badge-overdue';   $bdgTxt='Overdue'; }
    elseif ($isToday)    { $bdg='badge-today';     $bdgTxt='Due Today'; }
    else                 { $bdg='badge-upcoming';  $bdgTxt='Upcoming'; }

    $diff = $dueTs - $now;
    if ($diff < 0)       $dlTxt = abs(floor($diff/86400)).' days overdue';
    elseif ($diff < 3600) $dlTxt = 'Less than 1 hour left!';
    elseif ($diff < 86400) $dlTxt = floor($diff/3600).' hours left';
    else                  $dlTxt = floor($diff/86400).' days left';
?>
<div class="acard <?=$cardCls?>" data-filter="<?=$filterTag?>">
  <div class="acard-top">
    <div class="acard-header">
      <div>
        <div class="acard-title"><?=htmlspecialchars($a['title'])?></div>
        <div class="acard-sub">
          <i class="fa fa-chalkboard"></i><?=htmlspecialchars($a['class_name']??'General')?>
          &nbsp;|&nbsp;<i class="fa fa-user"></i><?=htmlspecialchars($a['teacher_name']??'Teacher')?>
        </div>
      </div>
      <span class="badge <?=$bdg?>"><?=$bdgTxt?></span>
    </div>
    <div class="acard-meta">
      <span><i class="fa fa-calendar-xmark"></i> Deadline: <?=$dueStr?></span>
      <span><i class="fa fa-hourglass-half"></i> <?=$dlTxt?></span>
      <span><i class="fa fa-star"></i> <?=intval($a['total_points'])?> pts</span>
    </div>
    <?php if (!$isOver && !$hasSub && $hoursLeft < 24 && $hoursLeft > 0): ?>
    <div class="deadline-warn"><i class="fa fa-triangle-exclamation"></i> Deadline approaching — submit soon!</div>
    <?php endif; ?>
    <?php if ($isOver && !$hasSub): ?>
    <div class="deadline-closed"><i class="fa fa-lock"></i> Deadline passed — submissions are closed.</div>
    <?php endif; ?>
    <?php if (!empty($a['description'])): ?>
    <div class="acard-desc"><?=nl2br(htmlspecialchars($a['description']))?></div>
    <?php endif; ?>
  </div>

  <div class="acard-submit">
    <?php if ($isGraded): ?>
      <!-- GRADED -->
      <div class="submitted-info">
        <div class="si-row"><i class="fa fa-check-circle"></i> Submitted: <?=date('M d, Y g:i A', strtotime($a['submitted_at']))?></div>
        <?php if ($a['file_name']): ?><div class="si-row"><i class="fa fa-paperclip"></i> <?=htmlspecialchars($a['file_name'])?></div><?php endif; ?>
        <?php if ($a['sub_notes']): ?><div class="si-row"><i class="fa fa-sticky-note"></i> <?=htmlspecialchars($a['sub_notes'])?></div><?php endif; ?>
      </div>
      <div class="feedback-box">
        <h5><i class="fa fa-comment-dots"></i> Teacher Feedback</h5>
        <?php if ($a['grade']): ?><div class="grade-display"><i class="fa fa-star"></i> Grade: <?=htmlspecialchars($a['grade'])?> / <?=intval($a['total_points'])?></div><?php endif; ?>
        <div class="feedback-text"><?=nl2br(htmlspecialchars($a['feedback']??'No feedback yet.'))?></div>
      </div>

    <?php elseif ($hasSub): ?>
      <!-- SUBMITTED — can resubmit or delete before deadline -->
      <div class="submitted-info">
        <div class="si-row"><i class="fa fa-check-circle"></i> Submitted: <?=date('M d, Y g:i A', strtotime($a['submitted_at']))?></div>
        <?php if ($a['file_name']): ?><div class="si-row"><i class="fa fa-paperclip"></i> <?=htmlspecialchars($a['file_name'])?></div><?php endif; ?>
        <?php if ($a['sub_notes']): ?><div class="si-row"><i class="fa fa-sticky-note"></i> <?=htmlspecialchars($a['sub_notes'])?></div><?php endif; ?>
      </div>
      <div style="font-size:12px;color:#888;display:flex;align-items:center;gap:6px;margin-bottom:10px">
        <i class="fa fa-clock" style="color:#fd7e14"></i> Waiting for teacher to grade...
      </div>
      <?php if (!$isOver): ?>
      <!-- Resubmit / Delete only before deadline -->
      <button class="btn-submit-work" style="background:linear-gradient(135deg,#fd7e14,#ffc107)" onclick="toggleResubmit(<?=$aid?>)">
        <i class="fa fa-redo"></i> Resubmit (Replace)
      </button>
      <div id="resub-<?=$aid?>" style="display:none;margin-top:10px">
        <form id="form-<?=$aid?>" onsubmit="event.preventDefault();submitWork(<?=$aid?>)">
          <input type="hidden" name="assignment_id" value="<?=$aid?>">
          <div class="upload-area" onclick="document.getElementById('file-<?=$aid?>').click()">
            <i class="fa fa-cloud-upload-alt"></i>
            <p>Click to upload new file (PDF, DOC, DOCX, TXT, ZIP, Image — max 10MB)</p>
            <input type="file" id="file-<?=$aid?>" name="file" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.zip,.rar" onchange="previewFile(this,<?=$aid?>)">
          </div>
          <div class="file-preview" id="fp-<?=$aid?>"><i class="fa fa-paperclip"></i><span class="fp-name"></span><span style="margin-left:auto;cursor:pointer;color:#dc3545" onclick="clearFile(<?=$aid?>)"><i class="fa fa-times"></i></span></div>
          <textarea class="notes-input" name="notes" placeholder="Add a note (optional)..."></textarea>
          <button type="submit" class="btn-submit-work"><i class="fa fa-paper-plane"></i> Confirm Resubmit</button>
        </form>
      </div>
      <button class="btn-delete-sub" onclick="deleteSubmission(<?=$aid?>)"><i class="fa fa-trash"></i> Delete Submission</button>
      <?php else: ?>
      <div class="deadline-closed"><i class="fa fa-lock"></i> Deadline passed — no changes allowed.</div>
      <?php endif; ?>

    <?php elseif (!$isOver): ?>
      <!-- NOT submitted, deadline open -->
      <div class="submit-head"><i class="fa fa-upload"></i> Submit Your Work</div>
      <form id="form-<?=$aid?>" onsubmit="event.preventDefault();submitWork(<?=$aid?>)">
        <input type="hidden" name="assignment_id" value="<?=$aid?>">
        <div class="upload-area" onclick="document.getElementById('file-<?=$aid?>').click()">
          <i class="fa fa-cloud-upload-alt"></i>
          <p>Click to upload file (PDF, DOC, DOCX, TXT, ZIP, Image — max 10MB)</p>
          <input type="file" id="file-<?=$aid?>" name="file" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.zip,.rar" onchange="previewFile(this,<?=$aid?>)">
        </div>
        <div class="file-preview" id="fp-<?=$aid?>"><i class="fa fa-paperclip"></i><span class="fp-name"></span><span style="margin-left:auto;cursor:pointer;color:#dc3545" onclick="clearFile(<?=$aid?>)"><i class="fa fa-times"></i></span></div>
        <textarea class="notes-input" name="notes" placeholder="Add a note to your teacher (optional)..."></textarea>
        <button type="submit" class="btn-submit-work"><i class="fa fa-paper-plane"></i> Submit Assignment</button>
      </form>

    <?php else: ?>
      <!-- NOT submitted, deadline CLOSED -->
      <div class="deadline-closed"><i class="fa fa-lock"></i> Deadline passed — you did not submit this assignment.</div>
    <?php endif; ?>
  </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

  </div><!-- /grid -->
</div><!-- /wrap -->

<footer>© 2025 SCTI — Student Portal</footer>
<div class="toast" id="toast"></div>

<script>
function setFilter(f) {
  ['all','pending','overdue','submitted','graded'].forEach(function(k){
    document.getElementById('tab-'+k).classList.toggle('active', k===f);
    var sc = document.getElementById('sc-'+k);
    if (sc) sc.classList.toggle('active', k===f);
  });
  var cards = document.querySelectorAll('.acard');
  var shown = 0;
  cards.forEach(function(c){
    var match = f==='all' || c.dataset.filter===f;
    c.style.display = match ? '' : 'none';
    if (match) shown++;
  });
  var grid = document.getElementById('grid');
  var ex = grid.querySelector('.js-empty');
  if (shown===0 && !ex) {
    var d = document.createElement('div');
    d.className = 'empty js-empty';
    d.innerHTML = '<i class="fa fa-check"></i><p>No '+f+' assignments.</p>';
    grid.appendChild(d);
  } else if (shown>0 && ex) ex.remove();
}

function toggleResubmit(id) {
  var el = document.getElementById('resub-'+id);
  if (el) el.style.display = el.style.display==='none' ? 'block' : 'none';
}

function previewFile(input, id) {
  var prev = document.getElementById('fp-'+id);
  if (input.files && input.files[0]) {
    prev.style.display = 'flex';
    prev.querySelector('.fp-name').textContent = input.files[0].name;
  }
}

function clearFile(id) {
  document.getElementById('file-'+id).value = '';
  document.getElementById('fp-'+id).style.display = 'none';
}

function submitWork(aid) {
  var form = document.getElementById('form-'+aid);
  var fd   = new FormData(form);
  var btn  = form.querySelector('.btn-submit-work');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Submitting...';
  fetch('assignment-submit.php', {method:'POST', body:fd})
    .then(function(r){ return r.json(); })
    .then(function(d){
      if (d.success) { showToast(d.message,'ok'); setTimeout(function(){ location.reload(); }, 1400); }
      else { showToast(d.message||'Failed','err'); btn.disabled=false; btn.innerHTML='<i class="fa fa-paper-plane"></i> Submit Assignment'; }
    })
    .catch(function(){ showToast('Network error','err'); btn.disabled=false; });
}

function deleteSubmission(aid) {
  if (!confirm('Delete your submission? You can resubmit before the deadline.')) return;
  fetch('assignment-submission-delete.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({assignment_id:aid})})
    .then(function(r){ return r.json(); })
    .then(function(d){
      if (d.success) { showToast(d.message,'ok'); setTimeout(function(){ location.reload(); }, 1400); }
      else showToast(d.message||'Failed','err');
    })
    .catch(function(){ showToast('Network error','err'); });
}

function showToast(msg, type) {
  var t = document.getElementById('toast');
  t.innerHTML = '<i class="fa fa-'+(type==='ok'?'check':'times')+'-circle"></i> '+msg;
  t.className = 'toast '+(type||'ok');
  t.style.display = 'flex';
  setTimeout(function(){ t.style.display='none'; }, 3500);
}
</script>
</body>
</html>
