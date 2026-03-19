<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header('Location: ../index.php'); exit();
}
require_once '../includes/config.php';

$studentId = $_SESSION['user_id'] ?? 0;

try {
    $db = getDBConnection();
    $assignments = $db->query(
        "SELECT a.*, t.full_name as teacher_name
         FROM assignments a
         LEFT JOIN teachers t ON a.created_by = t.id
         ORDER BY a.due_date ASC"
    )->fetchAll();

    $now = time();
    $total = count($assignments);
    $pending = 0; $overdue = 0;
    foreach ($assignments as $a) {
        if (strtotime($a['due_date']) < $now) $overdue++;
        else $pending++;
    }
} catch(Exception $e) {
    $assignments = []; $total = 0; $pending = 0; $overdue = 0;
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
    .container{max-width:1200px;margin:0 auto;padding:20px}
    .page-header{background:linear-gradient(135deg,#6f42c1,#e83e8c);color:white;padding:30px;border-radius:12px;margin-bottom:30px;box-shadow:0 4px 15px rgba(111,66,193,.2)}
    .page-header h1{margin:0 0 8px;font-size:28px}
    .breadcrumb{background:transparent;padding:0;font-size:14px;opacity:.9}
    .breadcrumb a{color:white;text-decoration:none}
    .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;margin-bottom:28px}
    .stat-card{background:white;padding:25px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.08);display:flex;align-items:center;gap:20px;transition:all .3s cubic-bezier(.25,.8,.25,1);cursor:pointer;position:relative;overflow:hidden;border:2px solid transparent}
    .stat-card::before{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(111,66,193,.07),transparent);transition:left .5s}
    .stat-card:hover::before{left:100%}
    .stat-card:hover,.stat-card.active-filter{transform:translateY(-4px);box-shadow:0 12px 30px rgba(111,66,193,.2);border-color:#6f42c1}
    .stat-card:active{transform:translateY(-1px) scale(.98)}
    .stat-card .card-arrow{position:absolute;right:14px;top:50%;transform:translateY(-50%);color:#ccc;font-size:13px;transition:all .3s;opacity:0}
    .stat-card:hover .card-arrow,.stat-card.active-filter .card-arrow{opacity:1;color:#6f42c1;right:10px}
    .stat-icon{width:60px;height:60px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:24px;color:white;flex-shrink:0}
    .stat-icon.purple{background:linear-gradient(135deg,#6f42c1,#e83e8c)}
    .stat-icon.yellow{background:linear-gradient(135deg,#fd7e14,#ffc107)}
    .stat-icon.red{background:linear-gradient(135deg,#dc3545,#fd7e14)}
    .stat-info h3{margin:0;font-size:32px;color:#6f42c1;font-weight:800}
    .stat-info p{margin:5px 0 0;color:#666;font-size:14px}
    .filter-tabs{display:flex;gap:10px;margin-bottom:25px;flex-wrap:wrap}
    .filter-tab{padding:10px 22px;border:2px solid #6f42c1;border-radius:8px;background:white;color:#6f42c1;cursor:pointer;font-weight:600;font-size:14px;transition:all .22s}
    .filter-tab:hover{background:#f3eeff;transform:translateY(-2px);box-shadow:0 4px 12px rgba(111,66,193,.15)}
    .filter-tab.active{background:linear-gradient(135deg,#6f42c1,#e83e8c);color:white;border-color:transparent;box-shadow:0 4px 14px rgba(111,66,193,.35)}
    .assignment-grid{display:grid;gap:18px}
    .assignment-card{background:white;border-radius:12px;padding:22px 24px;box-shadow:0 2px 10px rgba(0,0,0,.08);border-left:5px solid #6f42c1;transition:all .25s cubic-bezier(.25,.8,.25,1);cursor:pointer}
    .assignment-card.overdue{border-left-color:#dc3545}
    .assignment-card.upcoming{border-left-color:#ffc107}
    .assignment-card:hover{transform:translateX(6px);box-shadow:0 8px 24px rgba(111,66,193,.18)}
    .assignment-card:active{transform:translateX(3px) scale(.99)}
    .assignment-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px;gap:12px}
    .assignment-title{font-size:17px;font-weight:700;color:#1a202c;margin-bottom:4px}
    .assignment-course{color:#666;font-size:13px;display:flex;align-items:center;gap:6px;flex-wrap:wrap}
    .status-badge{padding:5px 14px;border-radius:20px;font-size:12px;font-weight:700;white-space:nowrap;flex-shrink:0}
    .status-upcoming{background:#fff3cd;color:#856404}
    .status-overdue{background:#f8d7da;color:#721c24}
    .status-today{background:#cce5ff;color:#004085}
    .assignment-meta{display:flex;gap:18px;margin:12px 0;flex-wrap:wrap}
    .meta-item{display:flex;align-items:center;gap:6px;color:#666;font-size:13px}
    .meta-item i{color:#6f42c1;width:14px}
    .assignment-desc{color:#777;line-height:1.6;font-size:14px;margin:10px 0;padding:10px 14px;background:#f8f9fa;border-radius:8px}
    .assignment-actions{display:flex;gap:10px;margin-top:14px}
    .btn{padding:9px 18px;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;transition:all .22s;text-decoration:none;display:inline-flex;align-items:center;gap:6px}
    .btn-primary{background:linear-gradient(135deg,#6f42c1,#e83e8c);color:white}
    .btn-primary:hover{transform:translateY(-2px);box-shadow:0 6px 16px rgba(111,66,193,.35)}
    .btn-outline{background:white;color:#6f42c1;border:2px solid #6f42c1}
    .btn-outline:hover{background:#6f42c1;color:white;transform:translateY(-2px)}
    .progress-bar-wrap{background:#f0f0f0;border-radius:20px;height:6px;margin-top:12px;overflow:hidden}
    .progress-bar{height:100%;border-radius:20px;transition:width .6s}
    .empty-state{text-align:center;padding:70px 20px;color:#999;background:white;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.06)}
    .empty-icon{width:72px;height:72px;border-radius:50%;background:#f0f0f0;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;color:#bbb}
    .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;align-items:center;justify-content:center;padding:20px}
    .modal-overlay.open{display:flex}
    .modal-box{background:white;border-radius:16px;width:100%;max-width:520px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.3);animation:mIn .25s ease}
    @keyframes mIn{from{transform:translateY(-20px);opacity:0}to{transform:translateY(0);opacity:1}}
    .modal-head{background:linear-gradient(135deg,#6f42c1,#e83e8c);color:white;padding:20px 24px;display:flex;justify-content:space-between;align-items:center}
    .modal-head h3{margin:0;font-size:17px}
    .modal-close{background:rgba(255,255,255,.2);border:none;color:white;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:14px;transition:.2s}
    .modal-close:hover{background:rgba(255,255,255,.35)}
    .modal-body{padding:24px}
    .modal-row{margin-bottom:12px;font-size:14px;color:#555;display:flex;gap:10px;align-items:flex-start}
    .modal-row i{color:#6f42c1;width:16px;margin-top:2px;flex-shrink:0}
    .modal-row strong{color:#333;min-width:80px;flex-shrink:0}
    .modal-desc{background:#f8f9fa;border-radius:8px;padding:12px 14px;font-size:14px;color:#555;line-height:1.7;margin-top:4px;width:100%}
    .modal-actions{display:flex;gap:10px;margin-top:20px}
    .submit-note{border-radius:8px;padding:10px 14px;font-size:13px;margin-top:14px;display:flex;align-items:center;gap:8px}
  </style>
</head>
<body>
<div class="top-header"><marquee>Assignments - Stay on top of your coursework and submit on time</marquee></div>
<div class="container">
  <div class="page-header">
    <h1><i class="fa fa-tasks"></i> My Assignments</h1>
    <div class="breadcrumb"><a href="../dashboards/student-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Assignments</div>
  </div>

  <div class="stats-grid">
    <div class="stat-card active-filter" id="sc-all" onclick="setFilter('all')">
      <div class="stat-icon purple"><i class="fa fa-list"></i></div>
      <div class="stat-info"><h3><?=$total?></h3><p>Total Assignments</p></div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>
    <div class="stat-card" id="sc-upcoming" onclick="setFilter('upcoming')">
      <div class="stat-icon yellow"><i class="fa fa-clock"></i></div>
      <div class="stat-info"><h3><?=$pending?></h3><p>Upcoming</p></div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>
    <div class="stat-card" id="sc-overdue" onclick="setFilter('overdue')">
      <div class="stat-icon red"><i class="fa fa-exclamation-circle"></i></div>
      <div class="stat-info"><h3><?=$overdue?></h3><p>Overdue</p></div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>
  </div>

  <div class="filter-tabs">
    <button class="filter-tab active" id="tab-all" onclick="setFilter('all')">All (<?=$total?>)</button>
    <button class="filter-tab" id="tab-upcoming" onclick="setFilter('upcoming')">Upcoming (<?=$pending?>)</button>
    <button class="filter-tab" id="tab-overdue" onclick="setFilter('overdue')">Overdue (<?=$overdue?>)</button>
  </div>

  <div class="assignment-grid" id="assignmentGrid">
    <?php if (empty($assignments)): ?>
    <div class="empty-state">
      <div class="empty-icon"><i class="fa fa-check"></i></div>
      <p>No assignments found.</p>
    </div>
    <?php else: ?>
    <?php foreach ($assignments as $a):
        $over    = isOverdue($a['due_date']);
        $dueTs   = strtotime($a['due_date']);
        $isToday = date('Y-m-d', $dueTs) === date('Y-m-d');
        $cardCls = $over ? 'overdue' : 'upcoming';
        $badgeCls = $over ? 'status-overdue' : ($isToday ? 'status-today' : 'status-upcoming');
        $badgeTxt = $over ? 'Overdue' : ($isToday ? 'Due Today' : 'Upcoming');
        $dl       = daysLeft($a['due_date']);
        $points   = intval($a['total_points'] ?? 100);
        $class    = htmlspecialchars($a['class_name'] ?? 'General');
        $teacher  = htmlspecialchars($a['teacher_name'] ?? 'Teacher');
        $title    = htmlspecialchars($a['title']);
        $desc     = htmlspecialchars($a['description'] ?? '');
        $dueStr   = date('M d, Y', $dueTs);
        $aid      = intval($a['id']);
        $remaining = max(0, $dueTs - time());
        $progress  = $over ? 100 : max(5, round((1 - $remaining/(30*86400))*100));
        $pbColor   = $over ? 'linear-gradient(90deg,#dc3545,#fd7e14)' : 'linear-gradient(90deg,#6f42c1,#e83e8c)';
        $jsArgs    = "$aid,'".addslashes($a['title'])."','".addslashes($a['class_name']??'General')."','".addslashes($a['teacher_name']??'Teacher')."','".addslashes($a['description']??'')."','$dueStr',$points,'$dl','$cardCls'";
    ?>
    <div class="assignment-card <?=$cardCls?>" data-filter="<?=$cardCls?>" onclick="openModal(<?=$jsArgs?>)">
      <div class="assignment-header">
        <div>
          <div class="assignment-title"><?=$title?></div>
          <div class="assignment-course">
            <i class="fa fa-chalkboard-teacher" style="color:#6f42c1"></i> <?=$class?>
            &nbsp;|&nbsp;
            <i class="fa fa-user" style="color:#6f42c1"></i> <?=$teacher?>
          </div>
        </div>
        <span class="status-badge <?=$badgeCls?>"><?=$badgeTxt?></span>
      </div>
      <div class="assignment-meta">
        <div class="meta-item"><i class="fa fa-calendar"></i><span>Due: <?=$dueStr?></span></div>
        <div class="meta-item"><i class="fa fa-hourglass-half"></i><span><?=$dl?></span></div>
        <div class="meta-item"><i class="fa fa-star"></i><span><?=$points?> pts</span></div>
      </div>
      <?php if ($desc): ?>
      <div class="assignment-desc"><?=nl2br($desc)?></div>
      <?php endif; ?>
      <div class="progress-bar-wrap">
        <div class="progress-bar" style="width:<?=$progress?>%;background:<?=$pbColor?>"></div>
      </div>
      <div class="assignment-actions" onclick="event.stopPropagation()">
        <button class="btn btn-primary" onclick="openModal(<?=$jsArgs?>)"><i class="fa fa-eye"></i> View Details</button>
        <button class="btn btn-outline" onclick="markDone(this)"><i class="fa fa-check"></i> Mark Done</button>
      </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
<footer class="footer"><p>© 2025 SCTI - Student Portal</p></footer>

<div class="modal-overlay" id="detailModal">
  <div class="modal-box">
    <div class="modal-head">
      <h3 id="mTitle"><i class="fa fa-tasks"></i> Assignment Details</h3>
      <button class="modal-close" onclick="closeModal()"><i class="fa fa-times"></i></button>
    </div>
    <div class="modal-body">
      <div class="modal-row"><i class="fa fa-heading"></i><strong>Title:</strong><span id="mTitleText"></span></div>
      <div class="modal-row"><i class="fa fa-chalkboard-teacher"></i><strong>Class:</strong><span id="mClass"></span></div>
      <div class="modal-row"><i class="fa fa-user"></i><strong>Teacher:</strong><span id="mTeacher"></span></div>
      <div class="modal-row"><i class="fa fa-calendar"></i><strong>Due Date:</strong><span id="mDue"></span></div>
      <div class="modal-row"><i class="fa fa-hourglass-half"></i><strong>Time Left:</strong><span id="mDl"></span></div>
      <div class="modal-row"><i class="fa fa-star"></i><strong>Points:</strong><span id="mPoints"></span></div>
      <div class="modal-row" style="flex-direction:column">
        <div style="display:flex;gap:8px;align-items:center;margin-bottom:6px"><i class="fa fa-align-left" style="color:#6f42c1"></i><strong style="color:#333">Description:</strong></div>
        <div class="modal-desc" id="mDesc"></div>
      </div>
      <div class="submit-note" id="mNote"><i class="fa fa-info-circle"></i> Submit your work directly to your teacher or via the class portal.</div>
      <div class="modal-actions">
        <button class="btn btn-primary" onclick="markDoneModal()"><i class="fa fa-check"></i> Mark as Done</button>
        <button class="btn btn-outline" onclick="closeModal()"><i class="fa fa-times"></i> Close</button>
      </div>
    </div>
  </div>
</div>

<script>
function setFilter(f) {
  ['all','upcoming','overdue'].forEach(function(k){
    document.getElementById('tab-'+k).classList.toggle('active', k===f);
    document.getElementById('sc-'+k).classList.toggle('active-filter', k===f);
  });
  var cards = document.querySelectorAll('.assignment-card');
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

function openModal(id,title,cls,teacher,desc,due,points,dl,status) {
  document.getElementById('mTitle').innerHTML = '<i class="fa fa-tasks"></i> '+title;
  document.getElementById('mTitleText').textContent = title;
  document.getElementById('mClass').textContent = cls;
  document.getElementById('mTeacher').textContent = teacher;
  document.getElementById('mDue').textContent = due;
  document.getElementById('mDl').textContent = dl;
  document.getElementById('mPoints').textContent = points+' points';
  document.getElementById('mDesc').textContent = desc||'No description provided.';
  var note = document.getElementById('mNote');
  if (status==='overdue') {
    note.style.cssText = 'background:#f8d7da;color:#721c24;border-radius:8px;padding:10px 14px;font-size:13px;margin-top:14px;display:flex;align-items:center;gap:8px';
    note.innerHTML = '<i class="fa fa-exclamation-circle"></i> This assignment is overdue. Contact your teacher if you still need to submit.';
  } else {
    note.style.cssText = 'background:#d4edda;color:#155724;border-radius:8px;padding:10px 14px;font-size:13px;margin-top:14px;display:flex;align-items:center;gap:8px';
    note.innerHTML = '<i class="fa fa-info-circle"></i> Submit your work directly to your teacher or via the class portal.';
  }
  document.getElementById('detailModal').classList.add('open');
}

function closeModal() { document.getElementById('detailModal').classList.remove('open'); }

function markDone(btn) {
  var card = btn.closest('.assignment-card');
  card.style.opacity = '.45';
  card.style.pointerEvents = 'none';
  btn.innerHTML = '<i class="fa fa-check-circle"></i> Done';
  btn.style.cssText += ';background:#28a745;color:white;border:none';
  showToast('Marked as done!');
}
function markDoneModal() { closeModal(); showToast('Assignment marked as done!'); }

function showToast(msg) {
  var t = document.getElementById('toast');
  if (!t) {
    t = document.createElement('div'); t.id='toast';
    t.style.cssText='position:fixed;bottom:24px;right:24px;background:linear-gradient(135deg,#6f42c1,#e83e8c);color:white;padding:12px 22px;border-radius:10px;font-size:14px;font-weight:600;z-index:99999;box-shadow:0 6px 20px rgba(111,66,193,.4);display:none';
    document.body.appendChild(t);
  }
  t.textContent = msg; t.style.display='block';
  setTimeout(function(){ t.style.display='none'; }, 3000);
}

document.getElementById('detailModal').addEventListener('click', function(e){ if(e.target===this) closeModal(); });
</script>
</body>
</html>
