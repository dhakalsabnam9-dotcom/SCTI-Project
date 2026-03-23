<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php'); exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Teachers | SCTI Admin</title>
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
    .hdr-btns{display:flex;gap:8px}
    .btn-hdr{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;border:none;transition:.2s;text-decoration:none}
    .btn-hdr.ghost{background:rgba(255,255,255,.18);color:#fff}
    .btn-hdr.ghost:hover{background:rgba(255,255,255,.32)}
    /* Stats bar */
    .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;padding:18px 28px;background:#fff;border-bottom:1px solid #e9ecef}
    .stat{display:flex;align-items:center;gap:12px;background:#f8f9fa;border-radius:10px;padding:12px 16px;transition:.2s;cursor:default}
    .stat:hover{background:#e8f0fe;transform:translateY(-2px)}
    .stat-ico{width:42px;height:42px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:17px;color:#fff;flex-shrink:0}
    .ico-blue{background:linear-gradient(135deg,#004080,#0059b3)}
    .ico-green{background:linear-gradient(135deg,#28a745,#20c997)}
    .ico-orange{background:linear-gradient(135deg,#fd7e14,#ffc107)}
    .ico-grey{background:linear-gradient(135deg,#6c757d,#495057)}
    .stat-val{font-size:24px;font-weight:700;color:#222;line-height:1}
    .stat-lbl{font-size:11px;color:#999;margin-top:2px}
    /* Layout */
    .layout{display:grid;grid-template-columns:390px 1fr;min-height:calc(100vh - 175px)}
    .panel{background:#fff;border-right:1px solid #e9ecef;padding:22px;overflow-y:auto}
    .panel h3{font-size:15px;color:#004080;margin-bottom:16px;padding-bottom:10px;border-bottom:2px solid #e8f0fe;display:flex;align-items:center;gap:8px}
    .content{padding:22px;overflow-y:auto}
    /* Form */
    .fg{margin-bottom:13px}
    .fg label{display:block;font-size:12px;font-weight:600;color:#555;margin-bottom:5px}
    .fc{width:100%;padding:9px 12px;border:2px solid #dee2e6;border-radius:8px;font-size:13px;font-family:inherit;transition:.2s;background:#fff}
    .fc:focus{outline:none;border-color:#004080;box-shadow:0 0 0 3px rgba(0,64,128,.1)}
    .fc[readonly]{background:#f8f9fa;color:#666;cursor:default}
    .frow{display:grid;grid-template-columns:1fr 1fr;gap:10px}
    .input-group{display:flex}
    .input-group .fc{border-radius:8px 0 0 8px;flex:1}
    .input-group .ig-btn{padding:0 12px;background:linear-gradient(135deg,#004080,#0059b3);color:#fff;border:none;border-radius:0 8px 8px 0;cursor:pointer;font-size:13px;display:flex;align-items:center;gap:5px;transition:.2s;white-space:nowrap}
    .input-group .ig-btn:hover{opacity:.85}
    .auto-badge{display:inline-flex;align-items:center;gap:4px;background:#e8f0fe;color:#004080;font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px;margin-left:6px;vertical-align:middle}
    .divider{border:none;border-top:1px dashed #e0e6ef;margin:14px 0}
    .btn-submit{width:100%;padding:12px;background:linear-gradient(135deg,#004080,#0059b3);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:.25s;margin-top:4px}
    .btn-submit:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,64,128,.35)}
    .btn-reset{width:100%;padding:10px;background:#f0f0f0;color:#555;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;margin-top:8px;transition:.2s}
    .btn-reset:hover{background:#e0e0e0}
    .alert{padding:10px 13px;border-radius:8px;font-size:13px;margin-bottom:12px;display:none}
    .alert-ok{background:#d4edda;color:#155724;border:1px solid #c3e6cb}
    .alert-err{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb}
    /* Toolbar */
    .toolbar{display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap;align-items:center}
    .toolbar input,.toolbar select{padding:9px 13px;border:2px solid #dee2e6;border-radius:8px;font-size:13px;font-family:inherit;background:#fff;transition:.2s}
    .toolbar input{flex:1;min-width:160px}
    .toolbar input:focus,.toolbar select:focus{outline:none;border-color:#004080}
    .btn-refresh{padding:9px 16px;background:linear-gradient(135deg,#004080,#0059b3);color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;transition:.2s}
    .btn-refresh:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,64,128,.3)}
    /* Cards → replaced with table */
    table{width:100%;border-collapse:collapse;background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.07)}
    th{background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:12px 14px;text-align:left;font-size:13px}
    td{padding:11px 14px;border-bottom:1px solid #f0f0f0;font-size:13px}
    tr:hover td{background:#f0f4ff}
    .badge{padding:3px 9px;border-radius:10px;font-size:11px;font-weight:700}
    .badge-active{background:#d4edda;color:#155724}
    .badge-inactive{background:#f8d7da;color:#721c24}
    .action-btns{display:flex;gap:5px}
    .btn-icon{width:30px;height:30px;border:none;border-radius:5px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px;transition:.2s}
    .btn-edit-i{background:#d4edda;color:#155724}
    .btn-del-i{background:#f8d7da;color:#721c24}
    .btn-icon:hover{transform:scale(1.1)}
    .avatar-sm{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#004080,#0059b3);display:inline-flex;align-items:center;justify-content:center;color:white;font-size:12px;font-weight:700;margin-right:8px;flex-shrink:0}
    .teacher-cell{display:flex;align-items:center}
    .empty-row td{text-align:center;padding:40px;color:#999}
    /* Empty / loading */
    .empty{text-align:center;padding:60px 20px;color:#ccc;grid-column:1/-1}
    .empty i{font-size:56px;display:block;margin-bottom:12px}
    /* Toast */
    .toast{position:fixed;bottom:22px;right:22px;color:white;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:700;z-index:99999;display:none;box-shadow:0 4px 15px rgba(0,0,0,.2);animation:slideUp .3s ease}
    @keyframes slideUp{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}
    .toast.ok{background:linear-gradient(135deg,#28a745,#20c997)}
    .toast.err{background:linear-gradient(135deg,#dc3545,#c82333)}
    /* Modal */
    .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9000;display:none;align-items:center;justify-content:center;backdrop-filter:blur(3px)}
    .modal-overlay.show{display:flex}
    .modal{background:#fff;border-radius:14px;padding:28px;max-width:420px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,.3);animation:popIn .25s ease}
    @keyframes popIn{from{transform:scale(.9);opacity:0}to{transform:scale(1);opacity:1}}
    .modal h3{margin:0 0 10px;color:#333;font-size:17px;display:flex;align-items:center;gap:8px}
    .modal p{color:#666;font-size:14px;margin-bottom:20px}
    .modal-btns{display:flex;gap:10px;justify-content:flex-end}
    .mbtn{padding:9px 22px;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;transition:.2s}
    .mbtn-cancel{background:#f0f0f0;color:#555}
    .mbtn-cancel:hover{background:#e0e0e0}
    .mbtn-del{background:linear-gradient(135deg,#dc3545,#c82333);color:#fff}
    .mbtn-del:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(220,53,69,.4)}
    footer{background:#00264d;color:white;text-align:center;padding:12px;font-size:13px}
    @media(max-width:860px){.layout{grid-template-columns:1fr}.stats{grid-template-columns:repeat(2,1fr)}}
  </style>
</head>
<body>

<div class="top-bar"><marquee>Teacher Manager — Add, edit and manage all teaching staff at SCTI</marquee></div>

<div class="pg-header">
  <div>
    <h1><i class="fa fa-chalkboard-teacher"></i> Manage Teachers</h1>
    <div class="bc"><a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Teachers</div>
  </div>
  <div class="hdr-btns">
    <a href="add-teacher.php" class="btn-hdr" style="background:#28a745;color:#fff"><i class="fa fa-user-plus"></i> Add Teacher</a>
    <a href="../dashboards/admin-dashboard.php" class="btn-hdr ghost"><i class="fa fa-arrow-left"></i> Back</a>
  </div>
</div>

<!-- Stats -->
<div class="stats">
  <div class="stat"><div class="stat-ico ico-blue"><i class="fa fa-chalkboard-teacher"></i></div><div><div class="stat-val" id="sTotal">—</div><div class="stat-lbl">Total Teachers</div></div></div>
  <div class="stat"><div class="stat-ico ico-green"><i class="fa fa-check-circle"></i></div><div><div class="stat-val" id="sActive">—</div><div class="stat-lbl">Active</div></div></div>
  <div class="stat"><div class="stat-ico ico-orange"><i class="fa fa-layer-group"></i></div><div><div class="stat-val" id="sDepts">—</div><div class="stat-lbl">Departments</div></div></div>
  <div class="stat"><div class="stat-ico ico-grey"><i class="fa fa-user-slash"></i></div><div><div class="stat-val" id="sInactive">—</div><div class="stat-lbl">Inactive</div></div></div>
</div>

<div class="layout">
  <!-- LEFT: FORM PANEL -->
  <div class="panel">
    <h3 id="formTitle"><i class="fa fa-plus-circle"></i> Add New Teacher</h3>
    <div id="formAlert" class="alert"></div>
    <input type="hidden" id="fId" value="0">

    <div class="fg">
      <label>Full Name *</label>
      <input type="text" id="fName" class="fc" placeholder="e.g. Ram Bahadur Thapa" oninput="autoGenerate()">
    </div>

    <div class="frow">
      <div class="fg">
        <label>Teacher ID <span class="auto-badge"><i class="fa fa-magic"></i> Auto</span></label>
        <div class="input-group">
          <input type="text" id="fTeacherId" class="fc" placeholder="TCH-2025-001" readonly>
          <button class="ig-btn" onclick="genTeacherId()" title="Regenerate"><i class="fa fa-sync"></i></button>
        </div>
      </div>
      <div class="fg">
        <label>Department *</label>
        <select id="fDept" class="fc" onchange="autoGenerate()">
          <option value="">Select...</option>
          <option>Computer Science</option>
          <option>Civil Engineering</option>
          <option>Electrical Engineering</option>
          <option>Animal Husbandry</option>
          <option>General</option>
        </select>
      </div>
    </div>

    <div class="frow">
      <div class="fg">
        <label>Username <span class="auto-badge"><i class="fa fa-magic"></i> Auto</span></label>
        <div class="input-group">
          <input type="text" id="fUsername" class="fc" placeholder="teacher.ram">
          <button class="ig-btn" onclick="genUsername()" title="Regenerate"><i class="fa fa-sync"></i></button>
        </div>
      </div>
      <div class="fg">
        <label>Password <span class="auto-badge"><i class="fa fa-magic"></i> Auto</span></label>
        <div class="input-group">
          <input type="text" id="fPassword" class="fc" placeholder="Auto123!">
          <button class="ig-btn" onclick="genPassword()" title="Regenerate"><i class="fa fa-sync"></i></button>
          <button class="ig-btn" onclick="copyPwd()" title="Copy password" style="border-radius:0;border-left:1px solid rgba(255,255,255,.3)"><i class="fa fa-copy" id="copyIcon"></i></button>
        </div>
      </div>
    </div>

    <!-- Credentials preview box -->
    <div id="credBox" style="display:none;background:linear-gradient(135deg,#e8f0fe,#f0f4ff);border:2px solid #004080;border-radius:10px;padding:12px 16px;margin-bottom:12px;font-size:13px">
      <div style="font-weight:700;color:#004080;margin-bottom:6px"><i class="fa fa-key"></i> Generated Credentials</div>
      <div style="display:flex;gap:20px;flex-wrap:wrap">
        <span><i class="fa fa-user" style="color:#004080"></i> <strong>User:</strong> <code id="credUser" style="background:#fff;padding:2px 8px;border-radius:5px;color:#004080"></code></span>
        <span><i class="fa fa-lock" style="color:#004080"></i> <strong>Pass:</strong> <code id="credPass" style="background:#fff;padding:2px 8px;border-radius:5px;color:#dc3545"></code></span>
      </div>
      <div style="font-size:11px;color:#888;margin-top:6px"><i class="fa fa-info-circle"></i> Share these credentials with the teacher after saving.</div>
    </div>

    <hr class="divider">

    <div class="fg">
      <label>Email</label>
      <input type="email" id="fEmail" class="fc" placeholder="teacher@scti.edu.np">
    </div>
    <div class="frow">
      <div class="fg">
        <label>Phone</label>
        <input type="text" id="fPhone" class="fc" placeholder="98XXXXXXXX">
      </div>
      <div class="fg">
        <label>Qualification</label>
        <input type="text" id="fQual" class="fc" placeholder="e.g. M.Sc. IT">
      </div>
    </div>
    <div class="frow">
      <div class="fg">
        <label>Experience</label>
        <input type="text" id="fExp" class="fc" placeholder="e.g. 5 Years">
      </div>
      <div class="fg">
        <label>Designation</label>
        <input type="text" id="fDesignation" class="fc" placeholder="e.g. Lecturer">
      </div>
    </div>
    <div class="frow">
      <div class="fg">
        <label>Address</label>
        <input type="text" id="fAddress" class="fc" placeholder="e.g. Sindhuli">
      </div>
      <div class="fg">
        <label>Status</label>
        <select id="fStatus" class="fc">
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>
    </div>
    <div class="fg">
      <label>Subjects <small style="color:#999">(comma separated)</small></label>
      <input type="text" id="fSubjects" class="fc" placeholder="e.g. Programming, Database, Web Dev">
    </div>

    <button class="btn-submit" onclick="saveTeacher()">
      <i class="fa fa-save"></i> <span id="btnTxt">Save Teacher</span>
    </button>
    <button class="btn-reset" onclick="resetForm()">
      <i class="fa fa-times"></i> Cancel / Reset
    </button>
  </div>

  <!-- RIGHT: CARD GRID -->
  <div class="content">
    <div class="toolbar">
      <input type="text" id="searchInput" placeholder="Search by name, ID, department..." oninput="filterLocal()">
      <select id="filterStatus" onchange="filterLocal()">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>
      <select id="filterDept" onchange="filterLocal()">
        <option value="">All Departments</option>
        <option>Computer Science</option>
        <option>Civil Engineering</option>
        <option>Electrical Engineering</option>
        <option>Animal Husbandry</option>
        <option>General</option>
      </select>
      <button class="btn-refresh" onclick="loadTeachers()"><i class="fa fa-sync"></i> Refresh</button>
    </div>
    <table>
      <thead>
        <tr><th>#</th><th>Teacher</th><th>Teacher ID</th><th>Department</th><th>Contact</th><th>Qualification</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody id="tbody">
        <tr class="empty-row"><td colspan="8"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<!-- Delete Confirm Modal -->
<div class="modal-overlay" id="delModal">
  <div class="modal">
    <h3><i class="fa fa-trash" style="color:#dc3545"></i> Delete Teacher</h3>
    <p>Are you sure you want to delete <strong id="delName"></strong>? This action cannot be undone.</p>
    <div class="modal-btns">
      <button class="mbtn mbtn-cancel" onclick="closeModal()">Cancel</button>
      <button class="mbtn mbtn-del" onclick="confirmDelete()"><i class="fa fa-trash"></i> Delete</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>
<footer>© 2025 SCTI — Admin Panel</footer>


<script>
var allTeachers = [];
var deleteTargetId = 0;

// ── AUTO-GENERATE ──────────────────────────────────────────────
function autoGenerate() {
  genTeacherId();
  genUsername();
  if (!document.getElementById('fPassword').value) genPassword();
  updateCredBox();
}

function updateCredBox() {
  var u = document.getElementById('fUsername').value.trim();
  var p = document.getElementById('fPassword').value.trim();
  var box = document.getElementById('credBox');
  if (u || p) {
    document.getElementById('credUser').textContent = u || '—';
    document.getElementById('credPass').textContent = p || '—';
    box.style.display = 'block';
  } else {
    box.style.display = 'none';
  }
}

function copyPwd() {
  var pwd = document.getElementById('fPassword').value;
  if (!pwd) { toast('No password to copy', 'err'); return; }
  navigator.clipboard.writeText(pwd).then(function(){
    var icon = document.getElementById('copyIcon');
    icon.className = 'fa fa-check';
    toast('Password copied!', 'ok');
    setTimeout(function(){ icon.className = 'fa fa-copy'; }, 2000);
  }).catch(function(){
    toast('Copy failed', 'err');
  });
}

function genTeacherId() {
  var year = new Date().getFullYear();
  var num  = String(Math.floor(Math.random() * 900) + 100);
  document.getElementById('fTeacherId').value = 'TCH-' + year + '-' + num;
}

function genUsername() {
  var name  = document.getElementById('fName').value.trim().toLowerCase();
  var parts = name.split(/\s+/);
  var base  = parts.length >= 2
    ? parts[0] + '.' + parts[parts.length - 1]
    : (parts[0] || 'teacher') + Math.floor(Math.random() * 99 + 1);
  base = base.replace(/[^a-z0-9.]/g, '');
  document.getElementById('fUsername').value = base || 'teacher' + Math.floor(Math.random()*999);
  updateCredBox();
}

function genPassword() {
  var u='ABCDEFGHJKLMNPQRSTUVWXYZ', l='abcdefghjkmnpqrstuvwxyz', d='23456789', s='@#$!';
  var pwd = u[~~(Math.random()*u.length)] + l[~~(Math.random()*l.length)]
          + l[~~(Math.random()*l.length)] + d[~~(Math.random()*d.length)]
          + d[~~(Math.random()*d.length)] + s[~~(Math.random()*s.length)]
          + l[~~(Math.random()*l.length)] + u[~~(Math.random()*u.length)];
  pwd = pwd.split('').sort(function(){return Math.random()-.5;}).join('');
  document.getElementById('fPassword').value = pwd;
  updateCredBox();
}

// ── SAVE (Create / Update) ─────────────────────────────────────
function saveTeacher() {
  var name     = document.getElementById('fName').value.trim();
  var tid      = document.getElementById('fTeacherId').value.trim();
  var username = document.getElementById('fUsername').value.trim();
  var password = document.getElementById('fPassword').value.trim();
  var dept     = document.getElementById('fDept').value;
  var id       = parseInt(document.getElementById('fId').value) || 0;

  if (!name || !dept)       { showAlert('Name and Department are required.', 'err'); return; }
  if (!tid || !username)    { showAlert('Please generate Teacher ID and Username.', 'err'); return; }
  if (id === 0 && !password){ showAlert('Please generate a Password for new teacher.', 'err'); return; }

  var payload = {
    id: id, name: name, teacher_id: tid, username: username, password: password,
    department: dept,
    email:         document.getElementById('fEmail').value.trim(),
    phone:         document.getElementById('fPhone').value.trim(),
    qualification: document.getElementById('fQual').value.trim(),
    experience:    document.getElementById('fExp').value.trim(),
    designation:   document.getElementById('fDesignation').value.trim(),
    address:       document.getElementById('fAddress').value.trim(),
    subjects:      document.getElementById('fSubjects').value.trim(),
    status:        document.getElementById('fStatus').value
  };

  var btn = document.querySelector('.btn-submit');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';

  fetch('teacher-save.php', {
    method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload)
  })
  .then(function(r){ return r.text(); })
  .then(function(txt){
    btn.disabled = false;
    btn.innerHTML = '<i class="fa fa-save"></i> <span id="btnTxt">' + (id===0?'Save Teacher':'Update Teacher') + '</span>';
    var d; try { d = JSON.parse(txt); } catch(e){ showAlert('Server error','err'); return; }
    if (d.success) {
      var msg = d.message;
      if (id === 0) msg += ' | Login: ' + username + ' / ' + password;
      showAlert(msg, 'ok');
      resetForm();
      loadTeachers();
    } else {
      showAlert(d.message || 'Failed to save.', 'err');
    }
  })
  .catch(function(){ btn.disabled=false; showAlert('Network error.','err'); });
}

// ── EDIT ──────────────────────────────────────────────────────
function editTeacher(id) {
  var t = allTeachers.find(function(x){ return String(x.id) === String(id); });
  if (!t) return;
  document.getElementById('fId').value        = t.id;
  document.getElementById('fName').value      = t.name || '';
  document.getElementById('fTeacherId').value = t.teacher_id || '';
  document.getElementById('fUsername').value  = t.username || '';
  document.getElementById('fPassword').value  = '';
  document.getElementById('fPassword').placeholder = 'Leave blank to keep current';
  document.getElementById('fPassword').readOnly    = false;
  var ds = document.getElementById('fDept');
  ds.value = t.department || '';
  if (t.department && ds.value !== t.department) {
    var o = document.createElement('option'); o.value = t.department; o.text = t.department;
    ds.add(o); ds.value = t.department;
  }
  document.getElementById('fEmail').value    = t.email || '';
  document.getElementById('fPhone').value    = t.phone || '';
  document.getElementById('fQual').value     = t.qualification || '';
  document.getElementById('fExp').value      = t.experience || '';
  document.getElementById('fDesignation').value = t.designation || '';
  document.getElementById('fAddress').value  = t.address || '';
  document.getElementById('fSubjects').value = t.subjects || '';
  document.getElementById('fStatus').value   = t.status || 'active';
  document.getElementById('formTitle').innerHTML = '<i class="fa fa-edit"></i> Edit Teacher';
  document.getElementById('btnTxt').textContent  = 'Update Teacher';
  document.getElementById('formAlert').style.display = 'none';
  document.querySelector('.panel').scrollTop = 0;
  window.scrollTo({top:0, behavior:'smooth'});
}

// ── TOGGLE STATUS ─────────────────────────────────────────────
function toggleStatus(id, currentStatus) {
  var newStatus = currentStatus === 'active' ? 'inactive' : 'active';
  var t = allTeachers.find(function(x){ return String(x.id) === String(id); });
  if (!t) return;
  var payload = {
    id: parseInt(id), name: t.name, teacher_id: t.teacher_id,
    username: t.username, password: '', department: t.department,
    email: t.email||'', phone: t.phone||'', qualification: t.qualification||'',
    experience: t.experience||'', subjects: t.subjects||'', status: newStatus
  };
  fetch('teacher-save.php', {
    method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload)
  })
  .then(function(r){ return r.json(); })
  .then(function(d){
    if (d.success) { toast('Status updated to ' + newStatus, 'ok'); loadTeachers(); }
    else toast(d.message || 'Failed', 'err');
  })
  .catch(function(){ toast('Network error','err'); });
}

// ── DELETE ────────────────────────────────────────────────────
function askDelete(id, name) {
  deleteTargetId = id;
  document.getElementById('delName').textContent = name;
  document.getElementById('delModal').classList.add('show');
}
function closeModal() {
  document.getElementById('delModal').classList.remove('show');
  deleteTargetId = 0;
}
function confirmDelete() {
  if (!deleteTargetId) return;
  closeModal();
  fetch('teacher-delete.php', {
    method:'POST', headers:{'Content-Type':'application/json'},
    body:JSON.stringify({id: parseInt(deleteTargetId)})
  })
  .then(function(r){ return r.json(); })
  .then(function(d){
    if (d.success) { toast('Teacher deleted successfully.', 'ok'); loadTeachers(); }
    else toast(d.message || 'Delete failed.', 'err');
  })
  .catch(function(){ toast('Network error.','err'); });
}

// ── RESET FORM ────────────────────────────────────────────────
function resetForm() {
  document.getElementById('fId').value = '0';
  ['fName','fTeacherId','fUsername','fPassword','fEmail','fPhone','fQual','fExp','fDesignation','fAddress','fSubjects']
    .forEach(function(id){ document.getElementById(id).value = ''; });
  document.getElementById('fDept').value   = '';
  document.getElementById('fStatus').value = 'active';
  document.getElementById('fPassword').placeholder = 'Auto123!';
  document.getElementById('fPassword').readOnly    = true;
  document.getElementById('credBox').style.display = 'none';
  document.getElementById('formTitle').innerHTML = '<i class="fa fa-plus-circle"></i> Add New Teacher';
  document.getElementById('btnTxt').textContent  = 'Save Teacher';
  document.getElementById('formAlert').style.display = 'none';
}

// ── LOAD & RENDER ─────────────────────────────────────────────
function loadTeachers() {
  document.getElementById('tbody').innerHTML = '<tr class="empty-row"><td colspan="8"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>';
  fetch('teacher-list.php')
    .then(function(r){ return r.text(); })
    .then(function(txt){
      var d; try { d = JSON.parse(txt); } catch(e){
        document.getElementById('tbody').innerHTML = '<tr class="empty-row"><td colspan="8">Parse error</td></tr>'; return;
      }
      if (!d.success) {
        document.getElementById('tbody').innerHTML = '<tr class="empty-row"><td colspan="8">'+(d.message||'Error loading data')+'</td></tr>'; return;
      }
      allTeachers = d.teachers || [];
      updateStats();
      filterLocal();
    })
    .catch(function(e){
      document.getElementById('tbody').innerHTML = '<tr class="empty-row"><td colspan="8">'+e.message+'</td></tr>';
    });
}

function updateStats() {
  var depts = new Set(allTeachers.map(function(t){ return t.department; }));
  document.getElementById('sTotal').textContent    = allTeachers.length;
  document.getElementById('sActive').textContent   = allTeachers.filter(function(t){ return t.status==='active'; }).length;
  document.getElementById('sDepts').textContent    = depts.size;
  document.getElementById('sInactive').textContent = allTeachers.filter(function(t){ return t.status==='inactive'; }).length;
}

function filterLocal() {
  var q    = document.getElementById('searchInput').value.toLowerCase();
  var st   = document.getElementById('filterStatus').value;
  var dept = document.getElementById('filterDept').value;
  var list = allTeachers.filter(function(t){
    var matchQ    = !q    || (t.name||'').toLowerCase().includes(q) || (t.department||'').toLowerCase().includes(q) || (t.teacher_id||'').toLowerCase().includes(q) || (t.username||'').toLowerCase().includes(q);
    var matchSt   = !st   || t.status === st;
    var matchDept = !dept || t.department === dept;
    return matchQ && matchSt && matchDept;
  });
  renderTable(list);
}

function renderTable(list) {
  if (!list.length) {
    document.getElementById('tbody').innerHTML = '<tr class="empty-row"><td colspan="8"><i class="fa fa-chalkboard-teacher"></i> No teachers found</td></tr>';
    return;
  }
  document.getElementById('tbody').innerHTML = list.map(function(t, i){
    var init = (t.name||'?').charAt(0).toUpperCase();
    var bdg  = t.status==='active' ? 'badge-active' : 'badge-inactive';
    return '<tr>'
      + '<td>'+(i+1)+'</td>'
      + '<td><div class="teacher-cell"><span class="avatar-sm">'+init+'</span>'+esc(t.name)+'</div></td>'
      + '<td>'+esc(t.teacher_id||'-')+'</td>'
      + '<td>'+esc(t.department||'-')+'</td>'
      + '<td>'+esc(t.email||t.phone||'-')+'</td>'
      + '<td>'+esc(t.qualification||'-')+'</td>'
      + '<td><span class="badge '+bdg+'">'+cap(t.status)+'</span></td>'
      + '<td><div class="action-btns">'
      +   '<button class="btn-icon btn-edit-i" onclick="editTeacher('+t.id+')" title="Edit"><i class="fa fa-edit"></i></button>'
      +   '<button class="btn-icon btn-del-i"  onclick="askDelete('+t.id+',\''+esc(t.name)+'\')" title="Delete"><i class="fa fa-trash"></i></button>'
      + '</div></td>'
      + '</tr>';
  }).join('');
}

// ── HELPERS ───────────────────────────────────────────────────
function showAlert(msg, type) {
  var el = document.getElementById('formAlert');
  el.className = 'alert alert-' + (type==='ok' ? 'ok' : 'err');
  el.textContent = msg; el.style.display = 'block';
  setTimeout(function(){ el.style.display='none'; }, 6000);
}
function toast(msg, type) {
  var t = document.getElementById('toast');
  t.textContent = msg; t.className = 'toast ' + (type||'ok');
  t.style.display = 'block';
  setTimeout(function(){ t.style.display='none'; }, 3500);
}
function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/'/g,'&#39;'); }
function cap(s){ return s ? s.charAt(0).toUpperCase()+s.slice(1) : ''; }

// Close modal on overlay click
document.getElementById('delModal').addEventListener('click', function(e){
  if (e.target === this) closeModal();
});

loadTeachers();
</script>
</body>
</html>
