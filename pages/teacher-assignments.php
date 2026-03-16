<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../index.php'); exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Assignments | SCTI Teacher Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Segoe UI',sans-serif;background:#f5f7fa}
    .top-header{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:10px 0;text-align:center}
    .top-header marquee{font-size:14px;font-weight:500}
    .container{max-width:1400px;margin:0 auto;padding:20px}
    .page-header{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:30px;border-radius:10px;margin-bottom:30px;box-shadow:0 4px 15px rgba(40,167,69,.2);display:flex;justify-content:space-between;align-items:center}
    .page-header h1{margin:0 0 10px;font-size:28px}
    .breadcrumb{font-size:14px;background:transparent;padding:0}
    .breadcrumb a{color:white;text-decoration:none}
    .btn-create{background:rgba(255,255,255,.2);color:white;padding:12px 24px;border:2px solid white;border-radius:6px;cursor:pointer;font-size:14px;transition:.3s;display:inline-flex;align-items:center;gap:8px}
    .btn-create:hover{background:white;color:#28a745}
    .filter-bar{background:white;padding:20px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.1);margin-bottom:30px;display:flex;gap:15px;flex-wrap:wrap;align-items:center}
    .filter-select{padding:10px 15px;border:2px solid #dee2e6;border-radius:6px;font-size:14px}
    .search-input{flex:1;padding:10px 15px;border:2px solid #dee2e6;border-radius:6px;font-size:14px;min-width:250px}
    .assignments-grid{display:grid;gap:20px}
    .assignment-card{background:white;border-radius:10px;padding:25px;box-shadow:0 2px 10px rgba(0,0,0,.1);border-left:5px solid #28a745;transition:.3s}
    .assignment-card:hover{transform:translateY(-5px);box-shadow:0 8px 25px rgba(40,167,69,.3)}
    .assignment-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:15px}
    .assignment-title{font-size:20px;font-weight:600;color:#333;margin-bottom:5px}
    .assignment-class{color:#666;font-size:14px}
    .status-badge{padding:6px 12px;border-radius:20px;font-size:12px;font-weight:600}
    .status-active{background:#d4edda;color:#155724}
    .status-graded{background:#cce5ff;color:#004085}
    .status-upcoming{background:#fff3cd;color:#856404}
    .status-pending{background:#f8d7da;color:#721c24}
    .assignment-meta{display:flex;gap:20px;margin:15px 0;padding:15px;background:#f8f9fa;border-radius:6px;flex-wrap:wrap}
    .meta-item{display:flex;align-items:center;gap:8px;color:#666;font-size:14px}
    .meta-item i{color:#28a745}
    .assignment-desc{font-size:13px;color:#666;line-height:1.6;margin:10px 0;padding:12px;background:#f8f9fa;border-radius:6px}
    .assignment-actions{display:flex;gap:10px;margin-top:15px}
    .action-btn{flex:1;padding:10px;border:none;border-radius:6px;cursor:pointer;font-size:14px;transition:.3s;display:flex;align-items:center;justify-content:center;gap:8px}
    .btn-edit{background:#004080;color:white}
    .btn-edit:hover{background:#0059b3}
    .btn-delete{background:#dc3545;color:white}
    .btn-delete:hover{background:#c82333}
    .empty-state{text-align:center;padding:60px 20px;color:#aaa}
    .empty-state i{font-size:60px;margin-bottom:16px;display:block}
    .empty-state p{font-size:16px}
    .footer{background:#2c3e50;color:white;text-align:center;padding:20px;border-radius:10px;margin-top:40px}
    .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;align-items:center;justify-content:center}
    .modal-overlay.open{display:flex}
    .modal-box{background:white;border-radius:14px;width:100%;max-width:600px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.3);animation:modalIn .25s ease}
    @keyframes modalIn{from{transform:translateY(-30px);opacity:0}to{transform:translateY(0);opacity:1}}
    .modal-head{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:22px 28px;display:flex;justify-content:space-between;align-items:center;border-radius:14px 14px 0 0}
    .modal-head h2{margin:0;font-size:20px}
    .modal-close{background:rgba(255,255,255,.2);border:none;color:white;width:34px;height:34px;border-radius:50%;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center}
    .modal-close:hover{background:rgba(255,255,255,.35)}
    .modal-body{padding:28px}
    .form-group{margin-bottom:20px}
    .form-group label{display:block;font-size:13px;font-weight:700;color:#444;margin-bottom:7px}
    .form-group label span{color:#dc3545}
    .form-control{width:100%;padding:11px 14px;border:2px solid #dee2e6;border-radius:8px;font-size:14px;transition:border-color .2s;font-family:inherit}
    .form-control:focus{outline:none;border-color:#28a745}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
    .modal-foot{padding:18px 28px;border-top:1px solid #eee;display:flex;gap:12px;justify-content:flex-end}
    .btn-cancel{padding:11px 24px;border:2px solid #dee2e6;background:white;color:#666;border-radius:8px;cursor:pointer;font-size:14px;font-weight:600}
    .btn-cancel:hover{border-color:#aaa;color:#333}
    .btn-save{padding:11px 28px;background:linear-gradient(135deg,#28a745,#20c997);color:white;border:none;border-radius:8px;cursor:pointer;font-size:14px;font-weight:700;display:flex;align-items:center;gap:8px}
    .btn-save:hover{opacity:.88}
    .toast{display:none;position:fixed;bottom:30px;right:30px;padding:14px 22px;border-radius:10px;font-size:14px;font-weight:600;box-shadow:0 6px 20px rgba(0,0,0,.2);z-index:99999;align-items:center;gap:10px}
    .toast.show{display:flex;animation:toastIn .3s ease}
    .toast-success{background:#28a745;color:white}
    .toast-error{background:#dc3545;color:white}
    @keyframes toastIn{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}
  </style>
</head>
<body>
<div class="top-header"><marquee>Assignment Management — Create, edit and delete assignments</marquee></div>
<div class="container">
  <div class="page-header">
    <div>
      <h1><i class="fa fa-tasks"></i> Assignments</h1>
      <div class="breadcrumb"><a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Assignments</div>
    </div>
    <button class="btn-create" onclick="openCreate()"><i class="fa fa-plus"></i> Create Assignment</button>
  </div>
  <div class="filter-bar">
    <select class="filter-select" id="filterClass" onchange="applyFilter()">
      <option value="">All Classes</option>
    </select>
    <select class="filter-select" id="filterStatus" onchange="applyFilter()">
      <option value="">All Status</option>
      <option value="active">Active</option>
      <option value="upcoming">Upcoming</option>
      <option value="graded">Graded</option>
      <option value="pending">Pending</option>
    </select>
    <input type="text" class="search-input" id="searchInput" placeholder="Search assignments..." oninput="applyFilter()">
  </div>
  <div class="assignments-grid" id="assignmentsGrid">
    <div class="empty-state"><i class="fa fa-spinner fa-spin"></i><p>Loading assignments...</p></div>
  </div>
</div>
<footer class="footer"><p>© 2025 SCTI - Teacher Portal</p></footer>

<!-- MODAL -->
<div class="modal-overlay" id="asgModal">
  <div class="modal-box">
    <div class="modal-head">
      <h2 id="modalTitle"><i class="fa fa-plus-circle"></i> Create Assignment</h2>
      <button class="modal-close" onclick="closeModal()"><i class="fa fa-times"></i></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="asgId">
      <div class="form-group">
        <label>Assignment Title <span>*</span></label>
        <input type="text" class="form-control" id="asgTitle" placeholder="e.g. Database Design Project">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Class / Course <span>*</span></label>
          <input type="text" class="form-control" id="asgClass" placeholder="e.g. Database Management">
        </div>
        <div class="form-group">
          <label>Total Points <span>*</span></label>
          <input type="number" class="form-control" id="asgPoints" placeholder="100" min="1">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Due Date <span>*</span></label>
          <input type="date" class="form-control" id="asgDue">
        </div>
        <div class="form-group">
          <label>Status</label>
          <select class="form-control" id="asgStatus">
            <option value="active">Active</option>
            <option value="upcoming">Upcoming</option>
            <option value="graded">Graded</option>
            <option value="pending">Pending</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>Description / Instructions</label>
        <textarea class="form-control" id="asgDesc" rows="4" placeholder="Describe the assignment requirements..."></textarea>
      </div>
    </div>
    <div class="modal-foot">
      <button class="btn-cancel" onclick="closeModal()">Cancel</button>
      <button class="btn-save" onclick="saveAssignment()"><i class="fa fa-check"></i> <span id="saveBtnTxt">Create Assignment</span></button>
    </div>
  </div>
</div>

<div class="toast toast-success" id="toastSuccess"><i class="fa fa-check-circle"></i><span id="toastMsg">Done</span></div>
<div class="toast toast-error" id="toastError"><i class="fa fa-times-circle"></i><span id="toastErrMsg">Error</span></div>

<script>
var allAssignments = [];

function loadAssignments() {
  fetch('assignment-list.php')
    .then(function(r){ return r.json(); })
    .then(function(res){
      if (res.success) {
        allAssignments = res.assignments;
        populateClassFilter();
        renderGrid(allAssignments);
      } else {
        document.getElementById('assignmentsGrid').innerHTML = '<div class="empty-state"><i class="fa fa-exclamation-circle"></i><p>' + escHtml(res.message) + '</p></div>';
      }
    })
    .catch(function(){ document.getElementById('assignmentsGrid').innerHTML = '<div class="empty-state"><i class="fa fa-exclamation-circle"></i><p>Failed to load assignments.</p></div>'; });
}

function populateClassFilter() {
  var sel = document.getElementById('filterClass');
  var classes = [...new Set(allAssignments.map(function(a){ return a.class_name; }))];
  sel.innerHTML = '<option value="">All Classes</option>';
  classes.forEach(function(c){ sel.innerHTML += '<option value="' + escHtml(c) + '">' + escHtml(c) + '</option>'; });
}

function applyFilter() {
  var cls    = document.getElementById('filterClass').value.toLowerCase();
  var status = document.getElementById('filterStatus').value;
  var search = document.getElementById('searchInput').value.toLowerCase();
  var filtered = allAssignments.filter(function(a){
    return (!cls    || a.class_name.toLowerCase() === cls)
        && (!status || a.status === status)
        && (!search || a.title.toLowerCase().includes(search) || a.class_name.toLowerCase().includes(search));
  });
  renderGrid(filtered);
}

function renderGrid(list) {
  var grid = document.getElementById('assignmentsGrid');
  grid.innerHTML = '';
  if (!list.length) {
    grid.innerHTML = '<div class="empty-state"><i class="fa fa-tasks"></i><p>No assignments found.</p></div>';
    return;
  }
  list.forEach(function(a){ grid.appendChild(buildCard(a)); });
}

function buildCard(a) {
  var badgeMap = { active:'status-active', upcoming:'status-upcoming', graded:'status-graded', pending:'status-pending' };
  var labelMap = { active:'Active', upcoming:'Upcoming', graded:'Graded', pending:'Pending Review' };
  var badge = badgeMap[a.status] || 'status-active';
  var label = labelMap[a.status] || a.status;
  var due   = new Date(a.due_date).toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric'});
  var desc  = a.description ? '<div class="assignment-desc">' + escHtml(a.description) + '</div>' : '';
  // Store JSON in data attribute to avoid inline quote escaping issues
  var el = document.createElement('div');
  el.className = 'assignment-card';
  el.innerHTML = '<div class="assignment-header">'
    +   '<div><div class="assignment-title">' + escHtml(a.title) + '</div><div class="assignment-class">' + escHtml(a.class_name) + '</div></div>'
    +   '<span class="status-badge ' + badge + '">' + label + '</span>'
    + '</div>'
    + '<div class="assignment-meta">'
    +   '<div class="meta-item"><i class="fa fa-calendar"></i><span>Due: ' + due + '</span></div>'
    +   '<div class="meta-item"><i class="fa fa-star"></i><span>' + escHtml(String(a.total_points)) + ' points</span></div>'
    + '</div>'
    + desc
    + '<div class="assignment-actions">'
    +   '<button class="action-btn btn-edit" data-id="' + a.id + '"><i class="fa fa-edit"></i> Edit</button>'
    +   '<button class="action-btn btn-delete" data-id="' + a.id + '"><i class="fa fa-trash"></i> Delete</button>'
    + '</div>';
  // Attach click handlers directly (no inline JSON, no escaping issues)
  el.querySelector('.btn-edit').addEventListener('click', function(){ openEdit(a); });
  el.querySelector('.btn-delete').addEventListener('click', function(){ deleteAssignment(a.id); });
  return el;
}

function openCreate() {
  document.getElementById('asgId').value    = '';
  document.getElementById('asgTitle').value = '';
  document.getElementById('asgClass').value = '';
  document.getElementById('asgPoints').value= '';
  document.getElementById('asgDue').value   = '';
  document.getElementById('asgDesc').value  = '';
  document.getElementById('asgStatus').value= 'active';
  document.getElementById('modalTitle').innerHTML = '<i class="fa fa-plus-circle"></i> Create Assignment';
  document.getElementById('saveBtnTxt').textContent = 'Create Assignment';
  document.getElementById('asgModal').classList.add('open');
}

function openEdit(a) {
  document.getElementById('asgId').value     = a.id;
  document.getElementById('asgTitle').value  = a.title;
  document.getElementById('asgClass').value  = a.class_name;
  document.getElementById('asgPoints').value = a.total_points;
  document.getElementById('asgDue').value    = a.due_date;
  document.getElementById('asgDesc').value   = a.description || '';
  document.getElementById('asgStatus').value = a.status;
  document.getElementById('modalTitle').innerHTML = '<i class="fa fa-edit"></i> Edit Assignment';
  document.getElementById('saveBtnTxt').textContent = 'Update Assignment';
  document.getElementById('asgModal').classList.add('open');
}

function closeModal() { document.getElementById('asgModal').classList.remove('open'); }

function saveAssignment() {
  var id     = document.getElementById('asgId').value;
  var title  = document.getElementById('asgTitle').value.trim();
  var cls    = document.getElementById('asgClass').value.trim();
  var points = document.getElementById('asgPoints').value.trim();
  var due    = document.getElementById('asgDue').value;
  if (!title || !cls || !points || !due) { showToast('error','Please fill all required fields.'); return; }
  var payload = { id:id?parseInt(id):0, title:title, class_name:cls, total_points:parseInt(points), due_date:due, status:document.getElementById('asgStatus').value, description:document.getElementById('asgDesc').value.trim() };
  fetch('assignment-save.php', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload) })
    .then(function(r){ return r.json(); })
    .then(function(res){
      if (res.success) {
        closeModal();
        showToast('success', id ? 'Assignment updated!' : 'Assignment created!');
        loadAssignments();
      } else { showToast('error', res.message || 'Save failed.'); }
    })
    .catch(function(){ showToast('error','Network error.'); });
}

function deleteAssignment(id) {
  if (!confirm('Delete this assignment? This cannot be undone.')) return;
  fetch('assignment-delete.php', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({id:id}) })
    .then(function(r){ return r.json(); })
    .then(function(res){
      if (res.success) { showToast('success','Assignment deleted.'); loadAssignments(); }
      else { showToast('error', res.message || 'Delete failed.'); }
    })
    .catch(function(){ showToast('error','Network error.'); });
}

function showToast(type, msg) {
  if (type === 'success') {
    document.getElementById('toastMsg').textContent = msg;
    var t = document.getElementById('toastSuccess');
    t.classList.add('show');
    setTimeout(function(){ t.classList.remove('show'); }, 3000);
  } else {
    document.getElementById('toastErrMsg').textContent = msg;
    var t = document.getElementById('toastError');
    t.classList.add('show');
    setTimeout(function(){ t.classList.remove('show'); }, 3500);
  }
}

function escHtml(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

document.getElementById('asgModal').addEventListener('click', function(e){ if(e.target===this) closeModal(); });
loadAssignments();
</script>
</body>
</html>
