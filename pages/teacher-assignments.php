<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../index.php'); exit();
}
$teacherId   = intval($_SESSION['user_id'] ?? 0);
$teacherName = $_SESSION['full_name'] ?? 'Teacher';
require_once '../includes/config.php';
try {
    $db = getDBConnection();
    $totalA = $db->prepare("SELECT COUNT(*) FROM assignments WHERE created_by=?");
    $totalA->execute([$teacherId]);
    $totalAssign = $totalA->fetchColumn();
    $totalSubs = $db->prepare("SELECT COUNT(*) FROM assignment_submissions s JOIN assignments a ON a.id=s.assignment_id WHERE a.created_by=?");
    $totalSubs->execute([$teacherId]);
    $totalSubmissions = $totalSubs->fetchColumn();
} catch(Exception $e) { $totalAssign=0; $totalSubmissions=0; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Assignments | SCTI Teacher</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',sans-serif;background:#f0f4f8;min-height:100vh}
.top-bar{background:#00264d;color:white;padding:7px 20px;font-size:13px}
.pg-header{background:linear-gradient(135deg,#28a745,#20c997);color:#fff;padding:20px 28px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 18px rgba(40,167,69,.25)}
.pg-header h1{font-size:22px;font-weight:700;display:flex;align-items:center;gap:10px;margin:0 0 3px}
.pg-header .bc{font-size:12px;color:rgba(255,255,255,.75)}
.pg-header .bc a{color:#fff;text-decoration:none}
.hdr-btns{display:flex;gap:8px}
.btn-hdr{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;border:none;transition:.2s;text-decoration:none}
.btn-hdr.ghost{background:rgba(255,255,255,.18);color:#fff}.btn-hdr.ghost:hover{background:rgba(255,255,255,.32)}
.btn-hdr.create{background:white;color:#28a745}.btn-hdr.create:hover{background:#f0fff4}
.wrap{max-width:1200px;margin:24px auto;padding:0 20px 60px}
.stats-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:22px}
.stat-pill{background:white;border-radius:12px;padding:16px 18px;box-shadow:0 2px 10px rgba(0,0,0,.07);display:flex;align-items:center;gap:12px}
.sp-ico{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;color:white;flex-shrink:0}
.sp-ico.green{background:linear-gradient(135deg,#28a745,#20c997)}
.sp-ico.blue{background:linear-gradient(135deg,#004080,#0059b3)}
.sp-ico.orange{background:linear-gradient(135deg,#fd7e14,#ffc107)}
.sp-ico.red{background:linear-gradient(135deg,#dc3545,#c82333)}
.sp-val{font-size:26px;font-weight:800;color:#1a202c;line-height:1}
.sp-lbl{font-size:11px;color:#888;margin-top:2px;font-weight:600}
.toolbar{background:white;border-radius:12px;padding:16px 20px;box-shadow:0 2px 10px rgba(0,0,0,.07);margin-bottom:20px;display:flex;gap:12px;flex-wrap:wrap;align-items:center}
.toolbar select,.toolbar input{padding:9px 13px;border:2px solid #e0e6ef;border-radius:9px;font-size:13px;font-family:inherit;transition:.2s}
.toolbar select:focus,.toolbar input:focus{outline:none;border-color:#28a745}
.toolbar input{flex:1;min-width:180px}
.grid{display:grid;gap:18px}
.acard{background:white;border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,.08);border-left:6px solid #28a745;overflow:hidden;transition:.25s}
.acard:hover{transform:translateY(-3px);box-shadow:0 10px 28px rgba(40,167,69,.15)}
.acard.overdue{border-left-color:#dc3545}
.acard-top{padding:20px 22px 14px}
.acard-header{display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:10px}
.acard-title{font-size:17px;font-weight:800;color:#1a202c}
.acard-class{font-size:12px;color:#888;margin-top:3px;display:flex;align-items:center;gap:5px}
.badge{padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700}
.badge-active{background:#d1fae5;color:#065f46}
.badge-upcoming{background:#fef3c7;color:#92400e}
.badge-graded{background:#dbeafe;color:#1e40af}
.badge-pending{background:#fee2e2;color:#991b1b}
.badge-overdue{background:#fee2e2;color:#991b1b}
.acard-meta{display:flex;gap:16px;flex-wrap:wrap;font-size:12px;color:#777;margin-bottom:12px}
.acard-meta span{display:flex;align-items:center;gap:5px}
.acard-meta i{color:#28a745}
.acard-desc{font-size:13px;color:#666;background:#f8fffe;border-radius:8px;padding:10px 12px;margin-bottom:12px;line-height:1.6;border-left:3px solid #a7f3d0}
.sub-bar{background:#f8fffe;border-top:1px solid #e8f5e9;padding:14px 22px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px}
.sub-progress{display:flex;align-items:center;gap:10px;flex:1}
.sub-progress-bar{flex:1;height:8px;background:#e0e6ef;border-radius:4px;overflow:hidden;min-width:80px}
.sub-progress-fill{height:100%;border-radius:4px;background:linear-gradient(90deg,#28a745,#20c997);transition:.4s}
.sub-count{font-size:13px;font-weight:700;color:#28a745;white-space:nowrap}
.acard-actions{display:flex;gap:8px;flex-wrap:wrap}
.abtn{padding:8px 16px;border:none;border-radius:8px;cursor:pointer;font-size:12px;font-weight:700;display:flex;align-items:center;gap:5px;transition:.2s}
.abtn-edit{background:#fef3c7;color:#92400e}.abtn-edit:hover{background:#f59e0b;color:#fff}
.abtn-del{background:#fee2e2;color:#991b1b}.abtn-del:hover{background:#dc3545;color:#fff}
.abtn-subs{background:#dbeafe;color:#1e40af}.abtn-subs:hover{background:#3b82f6;color:#fff}
.empty{text-align:center;padding:60px 20px;color:#aaa;background:white;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.06)}
.empty i{font-size:56px;display:block;margin-bottom:14px;color:#a7f3d0}
/* MODAL */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;align-items:center;justify-content:center;backdrop-filter:blur(4px)}
.modal-overlay.open{display:flex}
.modal-box{background:white;border-radius:16px;width:100%;max-width:620px;max-height:92vh;overflow-y:auto;box-shadow:0 24px 64px rgba(0,0,0,.3);animation:mIn .25s ease}
@keyframes mIn{from{transform:translateY(-28px) scale(.97);opacity:0}to{transform:translateY(0) scale(1);opacity:1}}
.modal-head{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:22px 28px;display:flex;justify-content:space-between;align-items:center;border-radius:16px 16px 0 0}
.modal-head h2{margin:0;font-size:18px;font-weight:800;display:flex;align-items:center;gap:10px}
.modal-close{background:rgba(255,255,255,.2);border:none;color:white;width:34px;height:34px;border-radius:50%;cursor:pointer;font-size:15px;transition:.2s}
.modal-close:hover{background:rgba(255,255,255,.35)}
.modal-body{padding:28px}
.fg{margin-bottom:16px}
.fg label{display:block;font-size:12px;font-weight:700;color:#555;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px}
.fg label .req{color:#dc3545}
.fc{width:100%;padding:11px 14px;border:2px solid #e0e6ef;border-radius:10px;font-size:14px;font-family:inherit;transition:.2s}
.fc:focus{outline:none;border-color:#28a745;box-shadow:0 0 0 3px rgba(40,167,69,.1)}
textarea.fc{resize:vertical;min-height:90px}
.frow{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.modal-foot{padding:18px 28px;border-top:1px solid #f0f0f0;display:flex;gap:10px;justify-content:flex-end;background:#fafafa;border-radius:0 0 16px 16px}
.mbtn{padding:11px 26px;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;transition:.2s;display:flex;align-items:center;gap:7px}
.mbtn-save{background:linear-gradient(135deg,#28a745,#20c997);color:white}.mbtn-save:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(40,167,69,.35)}
.mbtn-cancel{background:#f0f0f0;color:#555}.mbtn-cancel:hover{background:#e0e0e0}
.modal-alert{padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:14px;display:none}
.modal-alert.ok{background:#d1fae5;color:#065f46;border:1px solid #a7f3d0}
.modal-alert.err{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5}
/* SUBMISSIONS MODAL */
.subs-modal-box{max-width:900px}
.subs-table{width:100%;border-collapse:collapse;font-size:13px}
.subs-table th{background:#f8fffe;padding:10px 14px;text-align:left;font-size:11px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e8f5e9}
.subs-table td{padding:11px 14px;border-bottom:1px solid #f0f0f0;color:#333;vertical-align:middle}
.subs-table tr:last-child td{border-bottom:none}
.subs-table tr:hover td{background:#f8fffe}
.grade-input{width:80px;padding:6px 10px;border:2px solid #e0e6ef;border-radius:8px;font-size:13px;text-align:center;transition:.2s}
.grade-input:focus{outline:none;border-color:#28a745}
.btn-grade{padding:6px 14px;background:linear-gradient(135deg,#28a745,#20c997);color:white;border:none;border-radius:8px;cursor:pointer;font-size:12px;font-weight:700;transition:.2s}
.btn-grade:hover{opacity:.88}
.no-sub-row td{color:#bbb;font-style:italic}
/* TOAST */
.toast{position:fixed;bottom:22px;right:22px;color:white;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:700;z-index:99999;display:none;align-items:center;gap:8px;box-shadow:0 6px 20px rgba(0,0,0,.2)}
.toast.ok{background:#28a745}.toast.err{background:#dc3545}
footer{background:#00264d;color:white;text-align:center;padding:12px;font-size:13px;margin-top:40px}
@media(max-width:700px){.frow{grid-template-columns:1fr}.acard-actions{flex-direction:column}.sub-bar{flex-direction:column}}
</style>
</head>
<body>
<div class="top-bar"><marquee>SCTI Teacher Portal — Assignment Management</marquee></div>
<div class="pg-header">
  <div>
    <h1><i class="fa fa-tasks"></i> Assignments</h1>
    <div class="bc"><a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Assignments</div>
  </div>
  <div class="hdr-btns">
    <button class="btn-hdr create" onclick="openCreate()"><i class="fa fa-plus"></i> New Assignment</button>
    <a href="../dashboards/teacher-dashboard.php" class="btn-hdr ghost"><i class="fa fa-arrow-left"></i> Back</a>
  </div>
</div>

<div class="wrap">
  <!-- Stats -->
  <div class="stats-row">
    <div class="stat-pill" onclick="filterByPill('all')" style="cursor:pointer;transition:.2s" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''"><div class="sp-ico green"><i class="fa fa-tasks"></i></div><div><div class="sp-val" id="stTotal">0</div><div class="sp-lbl">Total Assignments</div></div></div>
    <div class="stat-pill" onclick="filterByPill('subs')" style="cursor:pointer;transition:.2s" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''"><div class="sp-ico blue"><i class="fa fa-paper-plane"></i></div><div><div class="sp-val" id="stSubs">0</div><div class="sp-lbl">Total Submissions</div></div></div>
    <div class="stat-pill" onclick="filterByPill('active')" style="cursor:pointer;transition:.2s" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''"><div class="sp-ico orange"><i class="fa fa-clock"></i></div><div><div class="sp-val" id="stActive">0</div><div class="sp-lbl">Active</div></div></div>
    <div class="stat-pill" onclick="filterByPill('overdue')" style="cursor:pointer;transition:.2s" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''"><div class="sp-ico red"><i class="fa fa-calendar-xmark"></i></div><div><div class="sp-val" id="stOverdue">0</div><div class="sp-lbl">Overdue</div></div></div>
  </div>

  <!-- Toolbar -->
  <div class="toolbar">
    <select id="filterStatus" onchange="applyFilter()">
      <option value="">All Status</option>
      <option value="active">Active</option>
      <option value="upcoming">Upcoming</option>
      <option value="graded">Graded</option>
      <option value="pending">Pending</option>
    </select>
    <input type="text" id="searchInput" placeholder="Search by title or class..." oninput="applyFilter()">
  </div>

  <div id="alertBox" style="padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:16px;display:none"></div>
  <div class="grid" id="grid"><div class="empty"><i class="fa fa-spinner fa-spin" style="color:#28a745"></i><p>Loading...</p></div></div>
</div>

<footer>© 2025 SCTI — Teacher Portal</footer>

<!-- CREATE / EDIT MODAL -->
<div class="modal-overlay" id="asgModal">
  <div class="modal-box">
    <div class="modal-head">
      <h2 id="modalTitle"><i class="fa fa-plus-circle"></i> Create Assignment</h2>
      <button class="modal-close" onclick="closeModal('asgModal')"><i class="fa fa-times"></i></button>
    </div>
    <div class="modal-body">
      <div class="modal-alert" id="formAlert"></div>
      <input type="hidden" id="asgId">
      <div class="fg"><label>Title <span class="req">*</span></label><input type="text" id="asgTitle" class="fc" placeholder="e.g. Database Design Project"></div>
      <div class="frow">
        <div class="fg">
          <label>Program / Semester <span class="req">*</span></label>
          <select id="asgClass" class="fc">
            <option value="">— Loading classes... —</option>
          </select>
        </div>
        <div class="fg"><label>Total Points</label><input type="number" id="asgPoints" class="fc" min="1" value="100"></div>
      </div>
      <div class="frow">
        <div class="fg"><label>Assign Date <span class="req">*</span></label><input type="datetime-local" id="asgAssignDate" class="fc"></div>
        <div class="fg"><label>Submission Deadline <span class="req">*</span></label><input type="datetime-local" id="asgDue" class="fc"></div>
      </div>
      <div class="fg"><label>Description / Instructions</label><textarea id="asgDesc" class="fc" placeholder="Describe the assignment requirements..."></textarea></div>
    </div>
    <div class="modal-foot">
      <button class="mbtn mbtn-cancel" onclick="closeModal('asgModal')"><i class="fa fa-times"></i> Cancel</button>
      <button class="mbtn mbtn-save" onclick="saveAssignment()"><i class="fa fa-save"></i> <span id="saveTxt">Create Assignment</span></button>
    </div>
  </div>
</div>

<!-- SUBMISSIONS MODAL -->
<div class="modal-overlay" id="subsModal">
  <div class="modal-box subs-modal-box">
    <div class="modal-head">
      <h2 id="subsModalTitle"><i class="fa fa-inbox"></i> Submissions</h2>
      <button class="modal-close" onclick="closeModal('subsModal')"><i class="fa fa-times"></i></button>
    </div>
    <div class="modal-body" id="subsModalBody" style="padding:0">
      <div style="padding:40px;text-align:center;color:#aaa"><i class="fa fa-spinner fa-spin" style="font-size:32px"></i></div>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
var allAssignments = [];

// Load class dropdown from DB
fetch('assignment-classes.php')
  .then(function(r){ return r.json(); })
  .then(function(res){
    var sel = document.getElementById('asgClass');
    sel.innerHTML = '<option value="">— Select Program/Semester —</option>';
    if (res.success) {
      res.classes.forEach(function(c){
        var opt = document.createElement('option');
        opt.value = c; opt.textContent = c;
        sel.appendChild(opt);
      });
    }
  });

function loadAssignments() {
  fetch('assignment-list.php')
    .then(function(r){ return r.json(); })
    .then(function(res){
      if (!res.success) { showGrid('<div class="empty"><i class="fa fa-exclamation-circle"></i><p>'+esc(res.message)+'</p></div>'); return; }
      allAssignments = res.assignments || [];
      updateStats();
      applyFilter();
    })
    .catch(function(){ showGrid('<div class="empty"><i class="fa fa-exclamation-circle"></i><p>Failed to load.</p></div>'); });
}

function updateStats() {
  var now = new Date();
  document.getElementById('stTotal').textContent   = allAssignments.length;
  document.getElementById('stSubs').textContent    = allAssignments.reduce(function(s,a){ return s + (parseInt(a.sub_count)||0); }, 0);
  document.getElementById('stActive').textContent  = allAssignments.filter(function(a){ return a.status==='active'; }).length;
  document.getElementById('stOverdue').textContent = allAssignments.filter(function(a){ return new Date(a.due_date) < now; }).length;
}

function applyFilter() {
  var status = document.getElementById('filterStatus').value;
  var q      = document.getElementById('searchInput').value.toLowerCase();
  var list   = allAssignments.filter(function(a){
    return (!status || a.status===status)
        && (!q || a.title.toLowerCase().includes(q) || a.class_name.toLowerCase().includes(q));
  });
  renderGrid(list);
}

function filterByPill(type) {
  var now = new Date();
  var list;
  if (type === 'all') {
    document.getElementById('filterStatus').value = '';
    list = allAssignments;
  } else if (type === 'active') {
    document.getElementById('filterStatus').value = 'active';
    list = allAssignments.filter(function(a){ return a.status === 'active'; });
  } else if (type === 'overdue') {
    document.getElementById('filterStatus').value = '';
    list = allAssignments.filter(function(a){ return new Date(a.due_date) < now; });
  } else if (type === 'subs') {
    document.getElementById('filterStatus').value = '';
    list = allAssignments.filter(function(a){ return parseInt(a.sub_count) > 0; });
  }
  renderGrid(list);
}

function renderGrid(list) {
  if (!list.length) { showGrid('<div class="empty"><i class="fa fa-tasks"></i><p>No assignments found.</p></div>'); return; }
  var grid = document.getElementById('grid');
  grid.innerHTML = '';
  list.forEach(function(a){ grid.appendChild(buildCard(a)); });
}

function showGrid(html) { document.getElementById('grid').innerHTML = html; }

function buildCard(a) {
  var now      = new Date();
  var dueDate  = new Date(a.due_date);
  var isOver   = dueDate < now;
  var dueStr   = dueDate.toLocaleString('en-US',{year:'numeric',month:'short',day:'numeric',hour:'2-digit',minute:'2-digit'});
  var total    = parseInt(a.total_students) || 0;
  var subCount = parseInt(a.sub_count) || 0;
  var pct      = total > 0 ? Math.round(subCount/total*100) : 0;
  var badgeMap = {active:'badge-active',upcoming:'badge-upcoming',graded:'badge-graded',pending:'badge-pending'};
  var labelMap = {active:'Active',upcoming:'Upcoming',graded:'Graded',pending:'Pending'};
  var overBadge = isOver ? '<span class="badge badge-overdue" style="margin-left:6px">Overdue</span>' : '';

  var el = document.createElement('div');
  el.className = 'acard' + (isOver ? ' overdue' : '');
  el.innerHTML =
    '<div class="acard-top">'
    + '<div class="acard-header">'
    +   '<div><div class="acard-title">'+esc(a.title)+'</div>'
    +   '<div class="acard-class"><i class="fa fa-chalkboard"></i>'+esc(a.class_name)+'</div></div>'
    +   '<div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center"><span class="badge '+(badgeMap[a.status]||'badge-active')+'">'+(labelMap[a.status]||a.status)+'</span>'+overBadge+'</div>'
    + '</div>'
    + '<div class="acard-meta">'
    +   '<span><i class="fa fa-calendar-plus"></i> Assigned: '+(a.assign_date ? new Date(a.assign_date).toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric'}) : 'N/A')+'</span>'
    +   '<span><i class="fa fa-calendar-xmark"></i> Deadline: '+dueStr+'</span>'
    +   '<span><i class="fa fa-star"></i> '+esc(String(a.total_points))+' pts</span>'
    +   '<span><i class="fa fa-users"></i> '+subCount+' / '+total+' submitted</span>'
    + '</div>'
    + (a.description ? '<div class="acard-desc">'+esc(a.description)+'</div>' : '')
    + '</div>'
    + '<div class="sub-bar">'
    +   '<div class="sub-progress">'
    +     '<div class="sub-progress-bar"><div class="sub-progress-fill" style="width:'+pct+'%"></div></div>'
    +     '<span class="sub-count">'+pct+'% submitted</span>'
    +   '</div>'
    +   '<div class="acard-actions">'
    +     '<button class="abtn abtn-subs"><i class="fa fa-inbox"></i> View Submissions</button>'
    +     '<button class="abtn abtn-edit"><i class="fa fa-pen"></i> Edit</button>'
    +     '<button class="abtn abtn-del"><i class="fa fa-trash"></i> Delete</button>'
    +   '</div>'
    + '</div>';

  el.querySelector('.abtn-edit').addEventListener('click', function(){ openEdit(a); });
  el.querySelector('.abtn-del').addEventListener('click', function(){ deleteAssignment(a.id); });
  el.querySelector('.abtn-subs').addEventListener('click', function(){ openSubmissions(a); });
  return el;
}

function openCreate() {
  document.getElementById('asgId').value          = '';
  document.getElementById('asgTitle').value       = '';
  document.getElementById('asgClass').value       = '';
  document.getElementById('asgPoints').value      = '100';
  document.getElementById('asgDue').value         = '';
  document.getElementById('asgAssignDate').value  = '';
  document.getElementById('asgDesc').value        = '';
  document.getElementById('asgStatus').value      = 'active';
  document.getElementById('modalTitle').innerHTML = '<i class="fa fa-plus-circle"></i> Create Assignment';
  document.getElementById('saveTxt').textContent  = 'Create Assignment';
  document.getElementById('formAlert').style.display = 'none';
  document.getElementById('asgModal').classList.add('open');
}

function openEdit(a) {
  document.getElementById('asgId').value          = a.id;
  document.getElementById('asgTitle').value       = a.title;
  document.getElementById('asgClass').value       = a.class_name;
  document.getElementById('asgPoints').value      = a.total_points;
  var d = a.due_date ? a.due_date.replace(' ','T').substring(0,16) : '';
  document.getElementById('asgDue').value         = d;
  var ad = a.assign_date ? a.assign_date.replace(' ','T').substring(0,16) : '';
  document.getElementById('asgAssignDate').value  = ad;
  document.getElementById('asgDesc').value        = a.description || '';
  document.getElementById('modalTitle').innerHTML = '<i class="fa fa-pen"></i> Edit Assignment';
  document.getElementById('saveTxt').textContent  = 'Update Assignment';
  document.getElementById('formAlert').style.display = 'none';
  document.getElementById('asgModal').classList.add('open');
}

function closeModal(id) { document.getElementById(id).classList.remove('open'); }

function saveAssignment() {
  var id     = document.getElementById('asgId').value;
  var title  = document.getElementById('asgTitle').value.trim();
  var cls    = document.getElementById('asgClass').value.trim();
  var points = document.getElementById('asgPoints').value.trim();
  var due    = document.getElementById('asgDue').value;
  var alert  = document.getElementById('formAlert');
  if (!title||!cls||!points||!due) {
    alert.textContent='Please fill all required fields.'; alert.className='modal-alert err'; alert.style.display='block'; return;
  }
  var payload = {id:id?parseInt(id):0,title:title,class_name:cls,total_points:parseInt(points),due_date:due,assign_date:document.getElementById('asgAssignDate').value,status:'active',description:document.getElementById('asgDesc').value.trim()};
  fetch('assignment-save.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)})
    .then(function(r){ return r.json(); })
    .then(function(res){
      if (res.success) { closeModal('asgModal'); showToast(id?'Assignment updated!':'Assignment created!','ok'); loadAssignments(); }
      else { alert.textContent=res.message||'Save failed'; alert.className='modal-alert err'; alert.style.display='block'; }
    })
    .catch(function(){ alert.textContent='Network error'; alert.className='modal-alert err'; alert.style.display='block'; });
}

function deleteAssignment(id) {
  if (!confirm('Delete this assignment and ALL its submissions? This cannot be undone.')) return;
  fetch('assignment-delete.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id:id})})
    .then(function(r){ return r.json(); })
    .then(function(res){
      if (res.success) { showToast('Assignment deleted.','ok'); loadAssignments(); }
      else showToast(res.message||'Delete failed','err');
    })
    .catch(function(){ showToast('Network error','err'); });
}

function openSubmissions(a) {
  document.getElementById('subsModalTitle').innerHTML = '<i class="fa fa-inbox"></i> Submissions — '+esc(a.title);
  document.getElementById('subsModalBody').innerHTML  = '<div style="padding:40px;text-align:center;color:#aaa"><i class="fa fa-spinner fa-spin" style="font-size:32px;color:#28a745"></i></div>';
  document.getElementById('subsModal').classList.add('open');

  fetch('assignment-submissions-list.php?assignment_id='+a.id)
    .then(function(r){ return r.json(); })
    .then(function(res){
      if (!res.success) { document.getElementById('subsModalBody').innerHTML='<div style="padding:30px;color:#dc3545">'+esc(res.message)+'</div>'; return; }
      renderSubmissions(res, a);
    })
    .catch(function(e){ document.getElementById('subsModalBody').innerHTML='<div style="padding:30px;color:#dc3545">Network error: '+esc(e.message)+'</div>'; });
}

function renderSubmissions(res, a) {
  var subs    = res.submissions || [];
  var all     = res.all_students || [];
  var subMap  = {};
  subs.forEach(function(s){ subMap[s.student_id] = s; });
  var dueDate = new Date(a.due_date);
  var now     = new Date();
  var isOver  = dueDate < now;

  var html = '<div style="padding:16px 20px;background:#f8fffe;border-bottom:1px solid #e8f5e9;display:flex;gap:16px;flex-wrap:wrap">'
    + '<span style="font-size:13px;font-weight:700;color:#28a745"><i class="fa fa-check-circle"></i> '+subs.length+' submitted</span>'
    + '<span style="font-size:13px;font-weight:700;color:#dc3545"><i class="fa fa-times-circle"></i> '+(all.length-subs.length)+' not submitted</span>'
    + '<span style="font-size:13px;color:#888"><i class="fa fa-users"></i> '+all.length+' total students</span>'
    + '<span style="font-size:13px;color:'+(isOver?'#dc3545':'#28a745')+'"><i class="fa fa-calendar"></i> Deadline: '+dueDate.toLocaleString('en-US',{month:'short',day:'numeric',year:'numeric',hour:'2-digit',minute:'2-digit'})+'</span>'
    + '</div>'
    + '<div style="overflow-x:auto"><table class="subs-table"><thead><tr>'
    + '<th>#</th><th>Student</th><th>Status</th><th>Submitted At</th><th>File</th><th>Notes</th><th>Grade</th><th>Action</th>'
    + '</tr></thead><tbody>';

  all.forEach(function(st, i) {
    var sub = subMap[st.id];
    if (sub) {
      var isLate = new Date(sub.submitted_at) > dueDate;
      var stBadge = isLate ? '<span class="badge badge-overdue">Late</span>' : '<span class="badge badge-active">On Time</span>';
      if (sub.status==='graded') stBadge = '<span class="badge badge-graded">Graded</span>';
      var fileLink = sub.file_path ? '<a href="../'+esc(sub.file_path)+'" target="_blank" style="color:#004080;font-size:12px;display:flex;align-items:center;gap:4px"><i class="fa fa-paperclip"></i>'+esc(sub.file_name||'File')+'</a>' : '<span style="color:#bbb;font-size:12px">No file</span>';
      html += '<tr>'
        + '<td style="color:#aaa;font-size:12px">'+(i+1)+'</td>'
        + '<td><div style="font-weight:700;font-size:13px">'+esc(st.full_name)+'</div><div style="font-size:11px;color:#888">'+esc(st.student_id)+'</div></td>'
        + '<td>'+stBadge+'</td>'
        + '<td style="font-size:12px;color:#666">'+new Date(sub.submitted_at).toLocaleString('en-US',{month:'short',day:'numeric',hour:'2-digit',minute:'2-digit'})+'</td>'
        + '<td>'+fileLink+'</td>'
        + '<td style="font-size:12px;color:#666;max-width:140px">'+esc(sub.notes||'—')+'</td>'
        + '<td><input type="text" class="grade-input" id="grade_'+sub.id+'" value="'+esc(sub.grade||'')+'" placeholder="e.g. 85"></td>'
        + '<td><button class="btn-grade" onclick="gradeSubmission('+sub.id+','+a.id+')"><i class="fa fa-check"></i> Save</button></td>'
        + '</tr>';
    } else {
      html += '<tr class="no-sub-row">'
        + '<td style="color:#aaa;font-size:12px">'+(i+1)+'</td>'
        + '<td><div style="font-weight:700;font-size:13px">'+esc(st.full_name)+'</div><div style="font-size:11px;color:#888">'+esc(st.student_id)+'</div></td>'
        + '<td><span class="badge" style="background:#f0f0f0;color:#aaa">Not Submitted</span></td>'
        + '<td style="color:#bbb;font-size:12px">—</td>'
        + '<td style="color:#bbb;font-size:12px">—</td>'
        + '<td style="color:#bbb;font-size:12px">—</td>'
        + '<td style="color:#bbb;font-size:12px">—</td>'
        + '<td style="color:#bbb;font-size:12px">—</td>'
        + '</tr>';
    }
  });
  html += '</tbody></table></div>';
  document.getElementById('subsModalBody').innerHTML = html;
}

function gradeSubmission(subId, asgId) {
  var grade = document.getElementById('grade_'+subId).value.trim();
  if (!grade) { showToast('Enter a grade first','err'); return; }
  fetch('assignment-grade.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({submission_id:subId,grade:grade})})
    .then(function(r){ return r.json(); })
    .then(function(res){
      if (res.success) showToast('Grade saved!','ok');
      else showToast(res.message||'Failed','err');
    })
    .catch(function(){ showToast('Network error','err'); });
}

function showToast(msg, type) {
  var t = document.getElementById('toast');
  t.innerHTML = '<i class="fa fa-'+(type==='ok'?'check':'times')+'-circle"></i> '+msg;
  t.className = 'toast '+(type||'ok');
  t.style.display = 'flex';
  setTimeout(function(){ t.style.display='none'; }, 3000);
}

function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

document.getElementById('asgModal').addEventListener('click', function(e){ if(e.target===this) closeModal('asgModal'); });
document.getElementById('subsModal').addEventListener('click', function(e){ if(e.target===this) closeModal('subsModal'); });
loadAssignments();
</script>
</body>
</html>
