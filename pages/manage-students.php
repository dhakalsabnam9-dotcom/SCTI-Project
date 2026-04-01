<?php
session_start();
if (!isset(<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php'); exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Students | SCTI Admin</title>
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
    .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;padding:18px 28px;background:#fff;border-bottom:1px solid #e9ecef}
    .stat{display:flex;align-items:center;gap:12px;background:#f8f9fa;border-radius:10px;padding:12px 16px}
    .stat-ico{width:42px;height:42px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:17px;color:#fff;flex-shrink:0}
    .ico-blue{background:linear-gradient(135deg,#004080,#0059b3)}
    .ico-green{background:linear-gradient(135deg,#28a745,#20c997)}
    .ico-orange{background:linear-gradient(135deg,#fd7e14,#ffc107)}
    .ico-grey{background:linear-gradient(135deg,#6c757d,#495057)}
    .stat-val{font-size:24px;font-weight:700;color:#222;line-height:1}
    .stat-lbl{font-size:11px;color:#999;margin-top:2px}
    .layout{display:grid;grid-template-columns:380px 1fr;min-height:calc(100vh - 170px)}
    .panel{background:#fff;border-right:1px solid #e9ecef;padding:22px;overflow-y:auto}
    .panel h3{font-size:15px;color:#004080;margin-bottom:16px;padding-bottom:10px;border-bottom:2px solid #e8f0fe;display:flex;align-items:center;gap:8px}
    .content{padding:22px;overflow-y:auto}
    .fg{margin-bottom:13px}
    .fg label{display:block;font-size:12px;font-weight:600;color:#555;margin-bottom:5px}
    .fc{width:100%;padding:9px 12px;border:2px solid #dee2e6;border-radius:8px;font-size:13px;font-family:inherit;transition:.2s}
    .fc:focus{outline:none;border-color:#004080;box-shadow:0 0 0 3px rgba(0,64,128,.1)}
    .fc[readonly]{background:#f8f9fa;color:#666;cursor:default}
    .frow{display:grid;grid-template-columns:1fr 1fr;gap:10px}
    .input-group{position:relative;display:flex}
    .input-group .fc{border-radius:8px 0 0 8px;flex:1}
    .input-group .ig-btn{padding:0 12px;background:linear-gradient(135deg,#004080,#0059b3);color:#fff;border:none;border-radius:0 8px 8px 0;cursor:pointer;font-size:13px;white-space:nowrap;display:flex;align-items:center;gap:5px;transition:.2s}
    .input-group .ig-btn:hover{opacity:.88}
    .btn-submit{width:100%;padding:12px;background:linear-gradient(135deg,#004080,#0059b3);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:.25s;margin-top:4px}
    .btn-submit:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,64,128,.35)}
    .btn-reset{width:100%;padding:10px;background:#f0f0f0;color:#555;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;margin-top:8px}
    .btn-reset:hover{background:#e0e0e0}
    .alert{padding:10px 13px;border-radius:8px;font-size:13px;margin-bottom:12px;display:none}
    .alert-ok{background:#d4edda;color:#155724;border:1px solid #c3e6cb}
    .alert-err{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb}
    .auto-badge{display:inline-flex;align-items:center;gap:4px;background:#e8f0fe;color:#004080;font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px;margin-left:6px;vertical-align:middle}
    .toolbar{display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap;align-items:center}
    .toolbar input,.toolbar select{padding:9px 13px;border:2px solid #dee2e6;border-radius:8px;font-size:13px;font-family:inherit}
    .toolbar input:focus,.toolbar select:focus{outline:none;border-color:#004080}
    .toolbar input{flex:1;min-width:160px}
    .btn-refresh{padding:9px 16px;background:linear-gradient(135deg,#004080,#0059b3);color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;transition:.2s}
    .btn-refresh:hover{transform:translateY(-1px)}
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
    .student-cell{display:flex;align-items:center}
    .empty-row td{text-align:center;padding:40px;color:#999}
    .divider{border:none;border-top:1px dashed #e0e6ef;margin:14px 0}
    .toast{position:fixed;bottom:22px;right:22px;color:white;padding:11px 18px;border-radius:9px;font-size:13px;font-weight:700;z-index:99999;display:none}
    .toast.ok{background:#28a745}.toast.err{background:#dc3545}
    footer{background:#00264d;color:white;text-align:center;padding:12px;font-size:13px}
    @media(max-width:860px){.layout{grid-template-columns:1fr}.stats{grid-template-columns:repeat(2,1fr)}}
  </style>
</head>
<body>
<div class="top-bar"><marquee>Student Manager  Add and manage all students at SCTI</marquee></div>
<div class="pg-header">
  <div>
    <h1><i class="fa fa-user-graduate"></i> Manage Students</h1>
    <div class="bc"><a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Students</div>
  </div>
  <div class="hdr-btns">
    <a href="../dashboards/admin-dashboard.php" class="btn-hdr ghost"><i class="fa fa-arrow-left"></i> Back</a>
  </div>
</div>

<div class="stats">
  <div class="stat"><div class="stat-ico ico-blue"><i class="fa fa-user-graduate"></i></div><div><div class="stat-val" id="sTotal">-</div><div class="stat-lbl">Total Students</div></div></div>
  <div class="stat"><div class="stat-ico ico-green"><i class="fa fa-check-circle"></i></div><div><div class="stat-val" id="sActive">-</div><div class="stat-lbl">Active</div></div></div>
  <div class="stat"><div class="stat-ico ico-orange"><i class="fa fa-layer-group"></i></div><div><div class="stat-val" id="sPrograms">-</div><div class="stat-lbl">Programs</div></div></div>
  <div class="stat"><div class="stat-ico ico-grey"><i class="fa fa-user-slash"></i></div><div><div class="stat-val" id="sInactive">-</div><div class="stat-lbl">Inactive</div></div></div>
</div>

<div class="layout">
  <div class="panel">
    <h3 id="formTitle"><i class="fa fa-plus-circle"></i> Add Student</h3>
    <div id="formAlert" class="alert"></div>
    <input type="hidden" id="fId" value="0">

    <div class="fg">
      <label>Full Name *</label>
      <input type="text" id="fName" class="fc" placeholder="e.g. Ram Bahadur Thapa" oninput="autoGenerate()">
    </div>

    <div class="frow">
      <div class="fg">
        <label>Student ID <span class="auto-badge"><i class="fa fa-magic"></i> Auto</span></label>
        <div class="input-group">
          <input type="text" id="fStudentId" class="fc" placeholder="STU-2025-001" readonly>
          <button class="ig-btn" onclick="genStudentId()" title="Regenerate"><i class="fa fa-sync"></i></button>
        </div>
      </div>
      <div class="fg">
        <label>Program *</label>
        <select id="fProgram" class="fc" onchange="autoGenerate()">
          <option value="">Select...</option>
          <?php foreach($programs as $p): ?><option><?=htmlspecialchars($p)?></option><?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="frow">
      <div class="fg">
        <label>Username <span class="auto-badge"><i class="fa fa-magic"></i> Auto</span></label>
        <div class="input-group">
          <input type="text" id="fUsername" class="fc" placeholder="student.ram">
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
      <div style="font-size:11px;color:#888;margin-top:6px"><i class="fa fa-info-circle"></i> Share these credentials with the student after saving.</div>
    </div>

    <hr class="divider">

    <div class="fg">
      <label>Email <span class="auto-badge"><i class="fa fa-magic"></i> Auto</span></label>
      <div class="input-group">
        <input type="email" id="fEmail" class="fc" placeholder="student@scti.edu.np">
        <button class="ig-btn" onclick="genEmail()" title="Regenerate Email"><i class="fa fa-sync"></i></button>
      </div>
    </div>
    <div class="frow">
      <div class="fg">
        <label>Phone</label>
        <input type="text" id="fPhone" class="fc" placeholder="98XXXXXXXX">
      </div>
      <div class="fg">
        <label>Qualification</label>
        <input type="text" id="fQual" class="fc" placeholder="e.g. +2 Science">
      </div>
    </div>
    <div class="frow">
      <div class="fg">
        <label>Semester</label>
        <select id="fSemester" class="fc">
          <option value="">Select...</option>
          <option value="Semester 1">Semester 1</option>
          <option value="Semester 2">Semester 2</option>
          <option value="Semester 3">Semester 3</option>
          <option value="Semester 4">Semester 4</option>
          <option value="Semester 5">Semester 5</option>
          <option value="Semester 6">Semester 6</option>
        </select>
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

    <button class="btn-submit" onclick="saveStudent()"><i class="fa fa-save"></i> <span id="btnTxt">Save Student</span></button>
    <button class="btn-reset" onclick="resetForm()"><i class="fa fa-times"></i> Cancel / Reset</button>
  </div>

  <div class="content">
    <div class="toolbar">
      <input type="text" id="searchInput" placeholder="Search students..." oninput="filterLocal()">
      <select id="filterProgram" onchange="filterLocal()">
        <option value="">All Programs</option>
        <?php foreach($programs as $p): ?><option><?=htmlspecialchars($p)?></option><?php endforeach; ?>
      </select>
      <select id="filterStatus" onchange="filterLocal()">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>
      <button class="btn-refresh" onclick="loadStudents()"><i class="fa fa-sync"></i> Refresh</button>
    </div>
    <table>
      <thead>
        <tr><th>#</th><th>Student</th><th>Student ID</th><th>Program</th><th>Semester</th><th>Contact</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody id="tbody">
        <tr class="empty-row"><td colspan="8"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<div class="toast" id="toast"></div>
<footer> 2025 SCTI  Admin Panel</footer>

<script>
var allStudents = [];

function autoGenerate() {
  genStudentId();
  genUsername();
  genEmail();
  if (!document.getElementById('fPassword').value) genPassword();
  updateCredBox();
}

function updateCredBox() {
  var u = document.getElementById('fUsername').value.trim();
  var p = document.getElementById('fPassword').value.trim();
  var box = document.getElementById('credBox');
  if (u || p) {
    document.getElementById('credUser').textContent = u || 'â€”';
    document.getElementById('credPass').textContent = p || 'â€”';
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
  }).catch(function(){ toast('Copy failed', 'err'); });
}

function genStudentId() {
  var year = new Date().getFullYear();
  var num  = String(Math.floor(Math.random() * 900) + 100);
  document.getElementById('fStudentId').value = 'STU-' + year + '-' + num;
}

function genUsername() {
  var name  = document.getElementById('fName').value.trim().toLowerCase();
  var parts = name.split(/\s+/);
  var base  = parts.length >= 2
    ? parts[0] + '.' + parts[parts.length - 1]
    : (parts[0] || 'student') + Math.floor(Math.random() * 99 + 1);
  base = base.replace(/[^a-z0-9.]/g, '');
  document.getElementById('fUsername').value = base || 'student' + Math.floor(Math.random()*999);
  updateCredBox();
}

function genEmail() {
  var username = document.getElementById('fUsername').value.trim();
  if (username) {
    document.getElementById('fEmail').value = username + '@student.scti.edu.np';
  }
}

function genPassword() {
  var upper='ABCDEFGHJKLMNPQRSTUVWXYZ', lower='abcdefghjkmnpqrstuvwxyz', digits='23456789', spec='@#$!';
  var pwd = upper[Math.floor(Math.random()*upper.length)]
          + lower[Math.floor(Math.random()*lower.length)]
          + lower[Math.floor(Math.random()*lower.length)]
          + digits[Math.floor(Math.random()*digits.length)]
          + digits[Math.floor(Math.random()*digits.length)]
          + spec[Math.floor(Math.random()*spec.length)]
          + lower[Math.floor(Math.random()*lower.length)]
          + upper[Math.floor(Math.random()*upper.length)];
  pwd = pwd.split('').sort(function(){return Math.random()-.5;}).join('');
  document.getElementById('fPassword').value = pwd;
  updateCredBox();
}

function saveStudent() {
  var name     = document.getElementById('fName').value.trim();
  var sid      = document.getElementById('fStudentId').value.trim();
  var username = document.getElementById('fUsername').value.trim();
  var password = document.getElementById('fPassword').value.trim();
  var program  = document.getElementById('fProgram').value;
  var id       = parseInt(document.getElementById('fId').value) || 0;

  if (!name || !program) { showAlert('Name and Program are required', 'err'); return; }
  if (!sid || !username)  { showAlert('Please generate Student ID and Username', 'err'); return; }
  if (id === 0 && !password) { showAlert('Please generate a Password', 'err'); return; }

  var payload = {
    id:            id,
    name:          name,
    student_id:    sid,
    username:      username,
    password:      password,
    program:       program,
    email:         document.getElementById('fEmail').value.trim(),
    phone:         document.getElementById('fPhone').value.trim(),
    qualification: document.getElementById('fQual').value.trim(),
    semester:      document.getElementById('fSemester').value || '',
    address:       document.getElementById('fAddress').value.trim(),
    status:        document.getElementById('fStatus').value
  };

  fetch('student-save.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify(payload)
  })
  .then(function(r){ return r.text(); })
  .then(function(txt){
    var d; try { d = JSON.parse(txt); } catch(e) { showAlert('Server error: '+txt.substring(0,100), 'err'); return; }
    if (d.success) {
      var msg = d.message;
      if (id === 0) msg += ' | Username: ' + username + ' | Password: ' + password;
      showAlert(msg, 'ok');
      resetForm();
      loadStudents();
    } else {
      showAlert(d.message || 'Failed', 'err');
    }
  })
  .catch(function(){ showAlert('Network error', 'err'); });
}

function editStudent(id) {
  var s = allStudents.find(function(x){ return String(x.id) === String(id); });
  if (!s) return;
  document.getElementById('fId').value          = s.id;
  document.getElementById('fName').value        = s.full_name || '';
  document.getElementById('fStudentId').value   = s.student_id || '';
  document.getElementById('fUsername').value    = s.username || '';
  document.getElementById('fPassword').value    = '';
  document.getElementById('fPassword').placeholder = 'Leave blank to keep current password';
  document.getElementById('fPassword').readOnly    = false;
  document.getElementById('fProgram').value     = s.program || '';
  document.getElementById('fEmail').value       = s.email || '';
  document.getElementById('fPhone').value       = s.phone || '';
  document.getElementById('fQual').value        = s.qualification || '';
  document.getElementById('fSemester').value    = s.semester || '';
  document.getElementById('fAddress').value     = s.address || '';
  document.getElementById('fStatus').value      = s.status || 'active';
  document.getElementById('credBox').style.display = 'none';
  document.getElementById('formTitle').innerHTML = '<i class="fa fa-edit"></i> Edit Student';
  document.getElementById('btnTxt').textContent  = 'Update Student';
  document.getElementById('formAlert').style.display = 'none';
  window.scrollTo({top:0, behavior:'smooth'});
}

function deleteStudent(id) {
  if (!confirm('Delete this student? This cannot be undone.')) return;
  fetch('student-delete.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({id: parseInt(id)})
  })
  .then(function(r){ return r.text(); })
  .then(function(txt){
    var d; try { d = JSON.parse(txt); } catch(e) { toast('Server error','err'); return; }
    if (d.success) { toast('Deleted','ok'); loadStudents(); }
    else toast(d.message || 'Failed','err');
  })
  .catch(function(){ toast('Network error','err'); });
}

function resetForm() {
  document.getElementById('fId').value = '0';
  ['fName','fStudentId','fUsername','fPassword','fEmail','fPhone','fQual','fAddress'].forEach(function(id){
    document.getElementById(id).value = '';
  });
  document.getElementById('fProgram').value  = '';
  document.getElementById('fSemester').value = '';
  document.getElementById('fStatus').value   = 'active';
  document.getElementById('fPassword').readOnly    = false;
  document.getElementById('fPassword').placeholder = 'Auto123!';
  document.getElementById('credBox').style.display = 'none';
  document.getElementById('formTitle').innerHTML = '<i class="fa fa-plus-circle"></i> Add Student';
  document.getElementById('btnTxt').textContent  = 'Save Student';
  document.getElementById('formAlert').style.display = 'none';
}

function loadStudents() {
  document.getElementById('tbody').innerHTML = '<tr class="empty-row"><td colspan="8"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>';
  fetch('student-list.php')
    .then(function(r){ return r.text(); })
    .then(function(txt){
      var d; try { d = JSON.parse(txt); } catch(e) {
        document.getElementById('tbody').innerHTML = '<tr class="empty-row"><td colspan="8">Parse error</td></tr>'; return;
      }
      if (!d.success) {
        document.getElementById('tbody').innerHTML = '<tr class="empty-row"><td colspan="8">'+(d.message||'Error')+'</td></tr>'; return;
      }
      allStudents = d.students || [];
      updateStats();
      renderTable(allStudents);
    })
    .catch(function(e){
      document.getElementById('tbody').innerHTML = '<tr class="empty-row"><td colspan="8">'+e.message+'</td></tr>';
    });
}

function updateStats() {
  var progs = new Set(allStudents.map(function(s){return s.program;}));
  document.getElementById('sTotal').textContent    = allStudents.length;
  document.getElementById('sActive').textContent   = allStudents.filter(function(s){return s.status==='active';}).length;
  document.getElementById('sPrograms').textContent = progs.size;
  document.getElementById('sInactive').textContent = allStudents.filter(function(s){return s.status==='inactive';}).length;
}

function filterLocal() {
  var q  = document.getElementById('searchInput').value.toLowerCase();
  var pr = document.getElementById('filterProgram').value;
  var st = document.getElementById('filterStatus').value;
  var list = allStudents.filter(function(s){
    var matchQ  = !q  || (s.full_name||'').toLowerCase().includes(q) || (s.student_id||'').toLowerCase().includes(q) || (s.email||'').toLowerCase().includes(q);
    var matchPr = !pr || s.program === pr;
    var matchSt = !st || s.status  === st;
    return matchQ && matchPr && matchSt;
  });
  renderTable(list);
}

function renderTable(list) {
  if (!list.length) {
    document.getElementById('tbody').innerHTML = '<tr class="empty-row"><td colspan="8"><i class="fa fa-user-graduate"></i> No students found</td></tr>';
    return;
  }
  document.getElementById('tbody').innerHTML = list.map(function(s, i){
    var init = (s.full_name||'?').charAt(0).toUpperCase();
    var bdg  = s.status==='active' ? 'badge-active' : 'badge-inactive';
    return '<tr>'
      + '<td>'+(i+1)+'</td>'
      + '<td><div class="student-cell"><span class="avatar-sm">'+init+'</span>'+esc(s.full_name)+'</div></td>'
      + '<td>'+esc(s.student_id||'-')+'</td>'
      + '<td>'+esc(s.program||'-')+'</td>'
      + '<td>'+(s.semester ? (isNaN(s.semester) ? s.semester : 'Semester '+s.semester) : '-')+'</td>'
      + '<td>'+esc(s.email||s.phone||'-')+'</td>'
      + '<td><span class="badge '+bdg+'">'+cap(s.status)+'</span></td>'
      + '<td><div class="action-btns">'
      +   '<button class="btn-icon btn-edit-i" onclick="editStudent('+s.id+')" title="Edit"><i class="fa fa-edit"></i></button>'
      +   '<button class="btn-icon btn-del-i"  onclick="deleteStudent('+s.id+')" title="Delete"><i class="fa fa-trash"></i></button>'
      + '</div></td>'
      + '</tr>';
  }).join('');
}

function showAlert(msg,type){
  var el=document.getElementById('formAlert');
  el.className='alert alert-'+(type==='ok'?'ok':'err');
  el.textContent=msg; el.style.display='block';
  setTimeout(function(){el.style.display='none';},6000);
}
function toast(msg,type){
  var t=document.getElementById('toast');
  t.textContent=msg; t.className='toast '+(type||'ok');
  t.style.display='block';
  setTimeout(function(){t.style.display='none';},3000);
}
function esc(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
function cap(s){return s?s.charAt(0).toUpperCase()+s.slice(1):'';}

loadStudents();
</script>
</body>
</html>
SESSION['user_type']) || <?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php'); exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Students | SCTI Admin</title>
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
    .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;padding:18px 28px;background:#fff;border-bottom:1px solid #e9ecef}
    .stat{display:flex;align-items:center;gap:12px;background:#f8f9fa;border-radius:10px;padding:12px 16px}
    .stat-ico{width:42px;height:42px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:17px;color:#fff;flex-shrink:0}
    .ico-blue{background:linear-gradient(135deg,#004080,#0059b3)}
    .ico-green{background:linear-gradient(135deg,#28a745,#20c997)}
    .ico-orange{background:linear-gradient(135deg,#fd7e14,#ffc107)}
    .ico-grey{background:linear-gradient(135deg,#6c757d,#495057)}
    .stat-val{font-size:24px;font-weight:700;color:#222;line-height:1}
    .stat-lbl{font-size:11px;color:#999;margin-top:2px}
    .layout{display:grid;grid-template-columns:380px 1fr;min-height:calc(100vh - 170px)}
    .panel{background:#fff;border-right:1px solid #e9ecef;padding:22px;overflow-y:auto}
    .panel h3{font-size:15px;color:#004080;margin-bottom:16px;padding-bottom:10px;border-bottom:2px solid #e8f0fe;display:flex;align-items:center;gap:8px}
    .content{padding:22px;overflow-y:auto}
    .fg{margin-bottom:13px}
    .fg label{display:block;font-size:12px;font-weight:600;color:#555;margin-bottom:5px}
    .fc{width:100%;padding:9px 12px;border:2px solid #dee2e6;border-radius:8px;font-size:13px;font-family:inherit;transition:.2s}
    .fc:focus{outline:none;border-color:#004080;box-shadow:0 0 0 3px rgba(0,64,128,.1)}
    .fc[readonly]{background:#f8f9fa;color:#666;cursor:default}
    .frow{display:grid;grid-template-columns:1fr 1fr;gap:10px}
    .input-group{position:relative;display:flex}
    .input-group .fc{border-radius:8px 0 0 8px;flex:1}
    .input-group .ig-btn{padding:0 12px;background:linear-gradient(135deg,#004080,#0059b3);color:#fff;border:none;border-radius:0 8px 8px 0;cursor:pointer;font-size:13px;white-space:nowrap;display:flex;align-items:center;gap:5px;transition:.2s}
    .input-group .ig-btn:hover{opacity:.88}
    .btn-submit{width:100%;padding:12px;background:linear-gradient(135deg,#004080,#0059b3);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:.25s;margin-top:4px}
    .btn-submit:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,64,128,.35)}
    .btn-reset{width:100%;padding:10px;background:#f0f0f0;color:#555;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;margin-top:8px}
    .btn-reset:hover{background:#e0e0e0}
    .alert{padding:10px 13px;border-radius:8px;font-size:13px;margin-bottom:12px;display:none}
    .alert-ok{background:#d4edda;color:#155724;border:1px solid #c3e6cb}
    .alert-err{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb}
    .auto-badge{display:inline-flex;align-items:center;gap:4px;background:#e8f0fe;color:#004080;font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px;margin-left:6px;vertical-align:middle}
    .toolbar{display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap;align-items:center}
    .toolbar input,.toolbar select{padding:9px 13px;border:2px solid #dee2e6;border-radius:8px;font-size:13px;font-family:inherit}
    .toolbar input:focus,.toolbar select:focus{outline:none;border-color:#004080}
    .toolbar input{flex:1;min-width:160px}
    .btn-refresh{padding:9px 16px;background:linear-gradient(135deg,#004080,#0059b3);color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;transition:.2s}
    .btn-refresh:hover{transform:translateY(-1px)}
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
    .student-cell{display:flex;align-items:center}
    .empty-row td{text-align:center;padding:40px;color:#999}
    .divider{border:none;border-top:1px dashed #e0e6ef;margin:14px 0}
    .toast{position:fixed;bottom:22px;right:22px;color:white;padding:11px 18px;border-radius:9px;font-size:13px;font-weight:700;z-index:99999;display:none}
    .toast.ok{background:#28a745}.toast.err{background:#dc3545}
    footer{background:#00264d;color:white;text-align:center;padding:12px;font-size:13px}
    @media(max-width:860px){.layout{grid-template-columns:1fr}.stats{grid-template-columns:repeat(2,1fr)}}
  </style>
</head>
<body>
<div class="top-bar"><marquee>Student Manager  Add and manage all students at SCTI</marquee></div>
<div class="pg-header">
  <div>
    <h1><i class="fa fa-user-graduate"></i> Manage Students</h1>
    <div class="bc"><a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Students</div>
  </div>
  <div class="hdr-btns">
    <a href="../dashboards/admin-dashboard.php" class="btn-hdr ghost"><i class="fa fa-arrow-left"></i> Back</a>
  </div>
</div>

<div class="stats">
  <div class="stat"><div class="stat-ico ico-blue"><i class="fa fa-user-graduate"></i></div><div><div class="stat-val" id="sTotal">-</div><div class="stat-lbl">Total Students</div></div></div>
  <div class="stat"><div class="stat-ico ico-green"><i class="fa fa-check-circle"></i></div><div><div class="stat-val" id="sActive">-</div><div class="stat-lbl">Active</div></div></div>
  <div class="stat"><div class="stat-ico ico-orange"><i class="fa fa-layer-group"></i></div><div><div class="stat-val" id="sPrograms">-</div><div class="stat-lbl">Programs</div></div></div>
  <div class="stat"><div class="stat-ico ico-grey"><i class="fa fa-user-slash"></i></div><div><div class="stat-val" id="sInactive">-</div><div class="stat-lbl">Inactive</div></div></div>
</div>

<div class="layout">
  <div class="panel">
    <h3 id="formTitle"><i class="fa fa-plus-circle"></i> Add Student</h3>
    <div id="formAlert" class="alert"></div>
    <input type="hidden" id="fId" value="0">

    <div class="fg">
      <label>Full Name *</label>
      <input type="text" id="fName" class="fc" placeholder="e.g. Ram Bahadur Thapa" oninput="autoGenerate()">
    </div>

    <div class="frow">
      <div class="fg">
        <label>Student ID <span class="auto-badge"><i class="fa fa-magic"></i> Auto</span></label>
        <div class="input-group">
          <input type="text" id="fStudentId" class="fc" placeholder="STU-2025-001" readonly>
          <button class="ig-btn" onclick="genStudentId()" title="Regenerate"><i class="fa fa-sync"></i></button>
        </div>
      </div>
      <div class="fg">
        <label>Program *</label>
        <select id="fProgram" class="fc" onchange="autoGenerate()">
          <option value="">Select...</option>
          <?php foreach($programs as $p): ?><option><?=htmlspecialchars($p)?></option><?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="frow">
      <div class="fg">
        <label>Username <span class="auto-badge"><i class="fa fa-magic"></i> Auto</span></label>
        <div class="input-group">
          <input type="text" id="fUsername" class="fc" placeholder="student.ram">
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
      <div style="font-size:11px;color:#888;margin-top:6px"><i class="fa fa-info-circle"></i> Share these credentials with the student after saving.</div>
    </div>

    <hr class="divider">

    <div class="fg">
      <label>Email <span class="auto-badge"><i class="fa fa-magic"></i> Auto</span></label>
      <div class="input-group">
        <input type="email" id="fEmail" class="fc" placeholder="student@scti.edu.np">
        <button class="ig-btn" onclick="genEmail()" title="Regenerate Email"><i class="fa fa-sync"></i></button>
      </div>
    </div>
    <div class="frow">
      <div class="fg">
        <label>Phone</label>
        <input type="text" id="fPhone" class="fc" placeholder="98XXXXXXXX">
      </div>
      <div class="fg">
        <label>Qualification</label>
        <input type="text" id="fQual" class="fc" placeholder="e.g. +2 Science">
      </div>
    </div>
    <div class="frow">
      <div class="fg">
        <label>Semester</label>
        <select id="fSemester" class="fc">
          <option value="">Select...</option>
          <option value="Semester 1">Semester 1</option>
          <option value="Semester 2">Semester 2</option>
          <option value="Semester 3">Semester 3</option>
          <option value="Semester 4">Semester 4</option>
          <option value="Semester 5">Semester 5</option>
          <option value="Semester 6">Semester 6</option>
        </select>
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

    <button class="btn-submit" onclick="saveStudent()"><i class="fa fa-save"></i> <span id="btnTxt">Save Student</span></button>
    <button class="btn-reset" onclick="resetForm()"><i class="fa fa-times"></i> Cancel / Reset</button>
  </div>

  <div class="content">
    <div class="toolbar">
      <input type="text" id="searchInput" placeholder="Search students..." oninput="filterLocal()">
      <select id="filterProgram" onchange="filterLocal()">
        <option value="">All Programs</option>
        <?php foreach($programs as $p): ?><option><?=htmlspecialchars($p)?></option><?php endforeach; ?>
      </select>
      <select id="filterStatus" onchange="filterLocal()">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>
      <button class="btn-refresh" onclick="loadStudents()"><i class="fa fa-sync"></i> Refresh</button>
    </div>
    <table>
      <thead>
        <tr><th>#</th><th>Student</th><th>Student ID</th><th>Program</th><th>Semester</th><th>Contact</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody id="tbody">
        <tr class="empty-row"><td colspan="8"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<div class="toast" id="toast"></div>
<footer> 2025 SCTI  Admin Panel</footer>

<script>
var allStudents = [];

function autoGenerate() {
  genStudentId();
  genUsername();
  genEmail();
  if (!document.getElementById('fPassword').value) genPassword();
  updateCredBox();
}

function updateCredBox() {
  var u = document.getElementById('fUsername').value.trim();
  var p = document.getElementById('fPassword').value.trim();
  var box = document.getElementById('credBox');
  if (u || p) {
    document.getElementById('credUser').textContent = u || 'â€”';
    document.getElementById('credPass').textContent = p || 'â€”';
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
  }).catch(function(){ toast('Copy failed', 'err'); });
}

function genStudentId() {
  var year = new Date().getFullYear();
  var num  = String(Math.floor(Math.random() * 900) + 100);
  document.getElementById('fStudentId').value = 'STU-' + year + '-' + num;
}

function genUsername() {
  var name  = document.getElementById('fName').value.trim().toLowerCase();
  var parts = name.split(/\s+/);
  var base  = parts.length >= 2
    ? parts[0] + '.' + parts[parts.length - 1]
    : (parts[0] || 'student') + Math.floor(Math.random() * 99 + 1);
  base = base.replace(/[^a-z0-9.]/g, '');
  document.getElementById('fUsername').value = base || 'student' + Math.floor(Math.random()*999);
  updateCredBox();
}

function genEmail() {
  var username = document.getElementById('fUsername').value.trim();
  if (username) {
    document.getElementById('fEmail').value = username + '@student.scti.edu.np';
  }
}

function genPassword() {
  var upper='ABCDEFGHJKLMNPQRSTUVWXYZ', lower='abcdefghjkmnpqrstuvwxyz', digits='23456789', spec='@#$!';
  var pwd = upper[Math.floor(Math.random()*upper.length)]
          + lower[Math.floor(Math.random()*lower.length)]
          + lower[Math.floor(Math.random()*lower.length)]
          + digits[Math.floor(Math.random()*digits.length)]
          + digits[Math.floor(Math.random()*digits.length)]
          + spec[Math.floor(Math.random()*spec.length)]
          + lower[Math.floor(Math.random()*lower.length)]
          + upper[Math.floor(Math.random()*upper.length)];
  pwd = pwd.split('').sort(function(){return Math.random()-.5;}).join('');
  document.getElementById('fPassword').value = pwd;
  updateCredBox();
}

function saveStudent() {
  var name     = document.getElementById('fName').value.trim();
  var sid      = document.getElementById('fStudentId').value.trim();
  var username = document.getElementById('fUsername').value.trim();
  var password = document.getElementById('fPassword').value.trim();
  var program  = document.getElementById('fProgram').value;
  var id       = parseInt(document.getElementById('fId').value) || 0;

  if (!name || !program) { showAlert('Name and Program are required', 'err'); return; }
  if (!sid || !username)  { showAlert('Please generate Student ID and Username', 'err'); return; }
  if (id === 0 && !password) { showAlert('Please generate a Password', 'err'); return; }

  var payload = {
    id:            id,
    name:          name,
    student_id:    sid,
    username:      username,
    password:      password,
    program:       program,
    email:         document.getElementById('fEmail').value.trim(),
    phone:         document.getElementById('fPhone').value.trim(),
    qualification: document.getElementById('fQual').value.trim(),
    semester:      document.getElementById('fSemester').value || '',
    address:       document.getElementById('fAddress').value.trim(),
    status:        document.getElementById('fStatus').value
  };

  fetch('student-save.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify(payload)
  })
  .then(function(r){ return r.text(); })
  .then(function(txt){
    var d; try { d = JSON.parse(txt); } catch(e) { showAlert('Server error: '+txt.substring(0,100), 'err'); return; }
    if (d.success) {
      var msg = d.message;
      if (id === 0) msg += ' | Username: ' + username + ' | Password: ' + password;
      showAlert(msg, 'ok');
      resetForm();
      loadStudents();
    } else {
      showAlert(d.message || 'Failed', 'err');
    }
  })
  .catch(function(){ showAlert('Network error', 'err'); });
}

function editStudent(id) {
  var s = allStudents.find(function(x){ return String(x.id) === String(id); });
  if (!s) return;
  document.getElementById('fId').value          = s.id;
  document.getElementById('fName').value        = s.full_name || '';
  document.getElementById('fStudentId').value   = s.student_id || '';
  document.getElementById('fUsername').value    = s.username || '';
  document.getElementById('fPassword').value    = '';
  document.getElementById('fPassword').placeholder = 'Leave blank to keep current password';
  document.getElementById('fPassword').readOnly    = false;
  document.getElementById('fProgram').value     = s.program || '';
  document.getElementById('fEmail').value       = s.email || '';
  document.getElementById('fPhone').value       = s.phone || '';
  document.getElementById('fQual').value        = s.qualification || '';
  document.getElementById('fSemester').value    = s.semester || '';
  document.getElementById('fAddress').value     = s.address || '';
  document.getElementById('fStatus').value      = s.status || 'active';
  document.getElementById('credBox').style.display = 'none';
  document.getElementById('formTitle').innerHTML = '<i class="fa fa-edit"></i> Edit Student';
  document.getElementById('btnTxt').textContent  = 'Update Student';
  document.getElementById('formAlert').style.display = 'none';
  window.scrollTo({top:0, behavior:'smooth'});
}

function deleteStudent(id) {
  if (!confirm('Delete this student? This cannot be undone.')) return;
  fetch('student-delete.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({id: parseInt(id)})
  })
  .then(function(r){ return r.text(); })
  .then(function(txt){
    var d; try { d = JSON.parse(txt); } catch(e) { toast('Server error','err'); return; }
    if (d.success) { toast('Deleted','ok'); loadStudents(); }
    else toast(d.message || 'Failed','err');
  })
  .catch(function(){ toast('Network error','err'); });
}

function resetForm() {
  document.getElementById('fId').value = '0';
  ['fName','fStudentId','fUsername','fPassword','fEmail','fPhone','fQual','fAddress'].forEach(function(id){
    document.getElementById(id).value = '';
  });
  document.getElementById('fProgram').value  = '';
  document.getElementById('fSemester').value = '';
  document.getElementById('fStatus').value   = 'active';
  document.getElementById('fPassword').readOnly    = false;
  document.getElementById('fPassword').placeholder = 'Auto123!';
  document.getElementById('credBox').style.display = 'none';
  document.getElementById('formTitle').innerHTML = '<i class="fa fa-plus-circle"></i> Add Student';
  document.getElementById('btnTxt').textContent  = 'Save Student';
  document.getElementById('formAlert').style.display = 'none';
}

function loadStudents() {
  document.getElementById('tbody').innerHTML = '<tr class="empty-row"><td colspan="8"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>';
  fetch('student-list.php')
    .then(function(r){ return r.text(); })
    .then(function(txt){
      var d; try { d = JSON.parse(txt); } catch(e) {
        document.getElementById('tbody').innerHTML = '<tr class="empty-row"><td colspan="8">Parse error</td></tr>'; return;
      }
      if (!d.success) {
        document.getElementById('tbody').innerHTML = '<tr class="empty-row"><td colspan="8">'+(d.message||'Error')+'</td></tr>'; return;
      }
      allStudents = d.students || [];
      updateStats();
      renderTable(allStudents);
    })
    .catch(function(e){
      document.getElementById('tbody').innerHTML = '<tr class="empty-row"><td colspan="8">'+e.message+'</td></tr>';
    });
}

function updateStats() {
  var progs = new Set(allStudents.map(function(s){return s.program;}));
  document.getElementById('sTotal').textContent    = allStudents.length;
  document.getElementById('sActive').textContent   = allStudents.filter(function(s){return s.status==='active';}).length;
  document.getElementById('sPrograms').textContent = progs.size;
  document.getElementById('sInactive').textContent = allStudents.filter(function(s){return s.status==='inactive';}).length;
}

function filterLocal() {
  var q  = document.getElementById('searchInput').value.toLowerCase();
  var pr = document.getElementById('filterProgram').value;
  var st = document.getElementById('filterStatus').value;
  var list = allStudents.filter(function(s){
    var matchQ  = !q  || (s.full_name||'').toLowerCase().includes(q) || (s.student_id||'').toLowerCase().includes(q) || (s.email||'').toLowerCase().includes(q);
    var matchPr = !pr || s.program === pr;
    var matchSt = !st || s.status  === st;
    return matchQ && matchPr && matchSt;
  });
  renderTable(list);
}

function renderTable(list) {
  if (!list.length) {
    document.getElementById('tbody').innerHTML = '<tr class="empty-row"><td colspan="8"><i class="fa fa-user-graduate"></i> No students found</td></tr>';
    return;
  }
  document.getElementById('tbody').innerHTML = list.map(function(s, i){
    var init = (s.full_name||'?').charAt(0).toUpperCase();
    var bdg  = s.status==='active' ? 'badge-active' : 'badge-inactive';
    return '<tr>'
      + '<td>'+(i+1)+'</td>'
      + '<td><div class="student-cell"><span class="avatar-sm">'+init+'</span>'+esc(s.full_name)+'</div></td>'
      + '<td>'+esc(s.student_id||'-')+'</td>'
      + '<td>'+esc(s.program||'-')+'</td>'
      + '<td>'+(s.semester ? (isNaN(s.semester) ? s.semester : 'Semester '+s.semester) : '-')+'</td>'
      + '<td>'+esc(s.email||s.phone||'-')+'</td>'
      + '<td><span class="badge '+bdg+'">'+cap(s.status)+'</span></td>'
      + '<td><div class="action-btns">'
      +   '<button class="btn-icon btn-edit-i" onclick="editStudent('+s.id+')" title="Edit"><i class="fa fa-edit"></i></button>'
      +   '<button class="btn-icon btn-del-i"  onclick="deleteStudent('+s.id+')" title="Delete"><i class="fa fa-trash"></i></button>'
      + '</div></td>'
      + '</tr>';
  }).join('');
}

function showAlert(msg,type){
  var el=document.getElementById('formAlert');
  el.className='alert alert-'+(type==='ok'?'ok':'err');
  el.textContent=msg; el.style.display='block';
  setTimeout(function(){el.style.display='none';},6000);
}
function toast(msg,type){
  var t=document.getElementById('toast');
  t.textContent=msg; t.className='toast '+(type||'ok');
  t.style.display='block';
  setTimeout(function(){t.style.display='none';},3000);
}
function esc(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
function cap(s){return s?s.charAt(0).toUpperCase()+s.slice(1):'';}

loadStudents();
</script>
</body>
</html>
SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php'); exit();
}
require_once '../includes/config.php';
try {
    $db = getDBConnection();
    $progRows = $db->query("SELECT DISTINCT title FROM programs WHERE status='active' ORDER BY title ASC")->fetchAll();
    $programs = array_column($progRows, 'title');
    if (empty($programs)) $programs = ['Animal Husbandry','B.Tech Ed in IT','B.Tech Ed in Civil','Diploma in Civil','Diploma Electrical'];
} catch(Exception $e) {
    $programs = ['Animal Husbandry','B.Tech Ed in IT','B.Tech Ed in Civil','Diploma in Civil','Diploma Electrical'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Students | SCTI Admin</title>
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
    .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;padding:18px 28px;background:#fff;border-bottom:1px solid #e9ecef}
    .stat{display:flex;align-items:center;gap:12px;background:#f8f9fa;border-radius:10px;padding:12px 16px}
    .stat-ico{width:42px;height:42px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:17px;color:#fff;flex-shrink:0}
    .ico-blue{background:linear-gradient(135deg,#004080,#0059b3)}
    .ico-green{background:linear-gradient(135deg,#28a745,#20c997)}
    .ico-orange{background:linear-gradient(135deg,#fd7e14,#ffc107)}
    .ico-grey{background:linear-gradient(135deg,#6c757d,#495057)}
    .stat-val{font-size:24px;font-weight:700;color:#222;line-height:1}
    .stat-lbl{font-size:11px;color:#999;margin-top:2px}
    .layout{display:grid;grid-template-columns:380px 1fr;min-height:calc(100vh - 170px)}
    .panel{background:#fff;border-right:1px solid #e9ecef;padding:22px;overflow-y:auto}
    .panel h3{font-size:15px;color:#004080;margin-bottom:16px;padding-bottom:10px;border-bottom:2px solid #e8f0fe;display:flex;align-items:center;gap:8px}
    .content{padding:22px;overflow-y:auto}
    .fg{margin-bottom:13px}
    .fg label{display:block;font-size:12px;font-weight:600;color:#555;margin-bottom:5px}
    .fc{width:100%;padding:9px 12px;border:2px solid #dee2e6;border-radius:8px;font-size:13px;font-family:inherit;transition:.2s}
    .fc:focus{outline:none;border-color:#004080;box-shadow:0 0 0 3px rgba(0,64,128,.1)}
    .fc[readonly]{background:#f8f9fa;color:#666;cursor:default}
    .frow{display:grid;grid-template-columns:1fr 1fr;gap:10px}
    .input-group{position:relative;display:flex}
    .input-group .fc{border-radius:8px 0 0 8px;flex:1}
    .input-group .ig-btn{padding:0 12px;background:linear-gradient(135deg,#004080,#0059b3);color:#fff;border:none;border-radius:0 8px 8px 0;cursor:pointer;font-size:13px;white-space:nowrap;display:flex;align-items:center;gap:5px;transition:.2s}
    .input-group .ig-btn:hover{opacity:.88}
    .btn-submit{width:100%;padding:12px;background:linear-gradient(135deg,#004080,#0059b3);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:.25s;margin-top:4px}
    .btn-submit:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,64,128,.35)}
    .btn-reset{width:100%;padding:10px;background:#f0f0f0;color:#555;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;margin-top:8px}
    .btn-reset:hover{background:#e0e0e0}
    .alert{padding:10px 13px;border-radius:8px;font-size:13px;margin-bottom:12px;display:none}
    .alert-ok{background:#d4edda;color:#155724;border:1px solid #c3e6cb}
    .alert-err{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb}
    .auto-badge{display:inline-flex;align-items:center;gap:4px;background:#e8f0fe;color:#004080;font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px;margin-left:6px;vertical-align:middle}
    .toolbar{display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap;align-items:center}
    .toolbar input,.toolbar select{padding:9px 13px;border:2px solid #dee2e6;border-radius:8px;font-size:13px;font-family:inherit}
    .toolbar input:focus,.toolbar select:focus{outline:none;border-color:#004080}
    .toolbar input{flex:1;min-width:160px}
    .btn-refresh{padding:9px 16px;background:linear-gradient(135deg,#004080,#0059b3);color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;transition:.2s}
    .btn-refresh:hover{transform:translateY(-1px)}
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
    .student-cell{display:flex;align-items:center}
    .empty-row td{text-align:center;padding:40px;color:#999}
    .divider{border:none;border-top:1px dashed #e0e6ef;margin:14px 0}
    .toast{position:fixed;bottom:22px;right:22px;color:white;padding:11px 18px;border-radius:9px;font-size:13px;font-weight:700;z-index:99999;display:none}
    .toast.ok{background:#28a745}.toast.err{background:#dc3545}
    footer{background:#00264d;color:white;text-align:center;padding:12px;font-size:13px}
    @media(max-width:860px){.layout{grid-template-columns:1fr}.stats{grid-template-columns:repeat(2,1fr)}}
  </style>
</head>
<body>
<div class="top-bar"><marquee>Student Manager  Add and manage all students at SCTI</marquee></div>
<div class="pg-header">
  <div>
    <h1><i class="fa fa-user-graduate"></i> Manage Students</h1>
    <div class="bc"><a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Students</div>
  </div>
  <div class="hdr-btns">
    <a href="../dashboards/admin-dashboard.php" class="btn-hdr ghost"><i class="fa fa-arrow-left"></i> Back</a>
  </div>
</div>

<div class="stats">
  <div class="stat"><div class="stat-ico ico-blue"><i class="fa fa-user-graduate"></i></div><div><div class="stat-val" id="sTotal">-</div><div class="stat-lbl">Total Students</div></div></div>
  <div class="stat"><div class="stat-ico ico-green"><i class="fa fa-check-circle"></i></div><div><div class="stat-val" id="sActive">-</div><div class="stat-lbl">Active</div></div></div>
  <div class="stat"><div class="stat-ico ico-orange"><i class="fa fa-layer-group"></i></div><div><div class="stat-val" id="sPrograms">-</div><div class="stat-lbl">Programs</div></div></div>
  <div class="stat"><div class="stat-ico ico-grey"><i class="fa fa-user-slash"></i></div><div><div class="stat-val" id="sInactive">-</div><div class="stat-lbl">Inactive</div></div></div>
</div>

<div class="layout">
  <div class="panel">
    <h3 id="formTitle"><i class="fa fa-plus-circle"></i> Add Student</h3>
    <div id="formAlert" class="alert"></div>
    <input type="hidden" id="fId" value="0">

    <div class="fg">
      <label>Full Name *</label>
      <input type="text" id="fName" class="fc" placeholder="e.g. Ram Bahadur Thapa" oninput="autoGenerate()">
    </div>

    <div class="frow">
      <div class="fg">
        <label>Student ID <span class="auto-badge"><i class="fa fa-magic"></i> Auto</span></label>
        <div class="input-group">
          <input type="text" id="fStudentId" class="fc" placeholder="STU-2025-001" readonly>
          <button class="ig-btn" onclick="genStudentId()" title="Regenerate"><i class="fa fa-sync"></i></button>
        </div>
      </div>
      <div class="fg">
        <label>Program *</label>
        <select id="fProgram" class="fc" onchange="autoGenerate()">
          <option value="">Select...</option>
          <?php foreach($programs as $p): ?><option><?=htmlspecialchars($p)?></option><?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="frow">
      <div class="fg">
        <label>Username <span class="auto-badge"><i class="fa fa-magic"></i> Auto</span></label>
        <div class="input-group">
          <input type="text" id="fUsername" class="fc" placeholder="student.ram">
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
      <div style="font-size:11px;color:#888;margin-top:6px"><i class="fa fa-info-circle"></i> Share these credentials with the student after saving.</div>
    </div>

    <hr class="divider">

    <div class="fg">
      <label>Email <span class="auto-badge"><i class="fa fa-magic"></i> Auto</span></label>
      <div class="input-group">
        <input type="email" id="fEmail" class="fc" placeholder="student@scti.edu.np">
        <button class="ig-btn" onclick="genEmail()" title="Regenerate Email"><i class="fa fa-sync"></i></button>
      </div>
    </div>
    <div class="frow">
      <div class="fg">
        <label>Phone</label>
        <input type="text" id="fPhone" class="fc" placeholder="98XXXXXXXX">
      </div>
      <div class="fg">
        <label>Qualification</label>
        <input type="text" id="fQual" class="fc" placeholder="e.g. +2 Science">
      </div>
    </div>
    <div class="frow">
      <div class="fg">
        <label>Semester</label>
        <select id="fSemester" class="fc">
          <option value="">Select...</option>
          <option value="Semester 1">Semester 1</option>
          <option value="Semester 2">Semester 2</option>
          <option value="Semester 3">Semester 3</option>
          <option value="Semester 4">Semester 4</option>
          <option value="Semester 5">Semester 5</option>
          <option value="Semester 6">Semester 6</option>
        </select>
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

    <button class="btn-submit" onclick="saveStudent()"><i class="fa fa-save"></i> <span id="btnTxt">Save Student</span></button>
    <button class="btn-reset" onclick="resetForm()"><i class="fa fa-times"></i> Cancel / Reset</button>
  </div>

  <div class="content">
    <div class="toolbar">
      <input type="text" id="searchInput" placeholder="Search students..." oninput="filterLocal()">
      <select id="filterProgram" onchange="filterLocal()">
        <option value="">All Programs</option>
        <?php foreach($programs as $p): ?><option><?=htmlspecialchars($p)?></option><?php endforeach; ?>
      </select>
      <select id="filterStatus" onchange="filterLocal()">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>
      <button class="btn-refresh" onclick="loadStudents()"><i class="fa fa-sync"></i> Refresh</button>
    </div>
    <table>
      <thead>
        <tr><th>#</th><th>Student</th><th>Student ID</th><th>Program</th><th>Semester</th><th>Contact</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody id="tbody">
        <tr class="empty-row"><td colspan="8"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<div class="toast" id="toast"></div>
<footer> 2025 SCTI  Admin Panel</footer>

<script>
var allStudents = [];

function autoGenerate() {
  genStudentId();
  genUsername();
  genEmail();
  if (!document.getElementById('fPassword').value) genPassword();
  updateCredBox();
}

function updateCredBox() {
  var u = document.getElementById('fUsername').value.trim();
  var p = document.getElementById('fPassword').value.trim();
  var box = document.getElementById('credBox');
  if (u || p) {
    document.getElementById('credUser').textContent = u || 'â€”';
    document.getElementById('credPass').textContent = p || 'â€”';
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
  }).catch(function(){ toast('Copy failed', 'err'); });
}

function genStudentId() {
  var year = new Date().getFullYear();
  var num  = String(Math.floor(Math.random() * 900) + 100);
  document.getElementById('fStudentId').value = 'STU-' + year + '-' + num;
}

function genUsername() {
  var name  = document.getElementById('fName').value.trim().toLowerCase();
  var parts = name.split(/\s+/);
  var base  = parts.length >= 2
    ? parts[0] + '.' + parts[parts.length - 1]
    : (parts[0] || 'student') + Math.floor(Math.random() * 99 + 1);
  base = base.replace(/[^a-z0-9.]/g, '');
  document.getElementById('fUsername').value = base || 'student' + Math.floor(Math.random()*999);
  updateCredBox();
}

function genEmail() {
  var username = document.getElementById('fUsername').value.trim();
  if (username) {
    document.getElementById('fEmail').value = username + '@student.scti.edu.np';
  }
}

function genPassword() {
  var upper='ABCDEFGHJKLMNPQRSTUVWXYZ', lower='abcdefghjkmnpqrstuvwxyz', digits='23456789', spec='@#$!';
  var pwd = upper[Math.floor(Math.random()*upper.length)]
          + lower[Math.floor(Math.random()*lower.length)]
          + lower[Math.floor(Math.random()*lower.length)]
          + digits[Math.floor(Math.random()*digits.length)]
          + digits[Math.floor(Math.random()*digits.length)]
          + spec[Math.floor(Math.random()*spec.length)]
          + lower[Math.floor(Math.random()*lower.length)]
          + upper[Math.floor(Math.random()*upper.length)];
  pwd = pwd.split('').sort(function(){return Math.random()-.5;}).join('');
  document.getElementById('fPassword').value = pwd;
  updateCredBox();
}

function saveStudent() {
  var name     = document.getElementById('fName').value.trim();
  var sid      = document.getElementById('fStudentId').value.trim();
  var username = document.getElementById('fUsername').value.trim();
  var password = document.getElementById('fPassword').value.trim();
  var program  = document.getElementById('fProgram').value;
  var id       = parseInt(document.getElementById('fId').value) || 0;

  if (!name || !program) { showAlert('Name and Program are required', 'err'); return; }
  if (!sid || !username)  { showAlert('Please generate Student ID and Username', 'err'); return; }
  if (id === 0 && !password) { showAlert('Please generate a Password', 'err'); return; }

  var payload = {
    id:            id,
    name:          name,
    student_id:    sid,
    username:      username,
    password:      password,
    program:       program,
    email:         document.getElementById('fEmail').value.trim(),
    phone:         document.getElementById('fPhone').value.trim(),
    qualification: document.getElementById('fQual').value.trim(),
    semester:      document.getElementById('fSemester').value || '',
    address:       document.getElementById('fAddress').value.trim(),
    status:        document.getElementById('fStatus').value
  };

  fetch('student-save.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify(payload)
  })
  .then(function(r){ return r.text(); })
  .then(function(txt){
    var d; try { d = JSON.parse(txt); } catch(e) { showAlert('Server error: '+txt.substring(0,100), 'err'); return; }
    if (d.success) {
      var msg = d.message;
      if (id === 0) msg += ' | Username: ' + username + ' | Password: ' + password;
      showAlert(msg, 'ok');
      resetForm();
      loadStudents();
    } else {
      showAlert(d.message || 'Failed', 'err');
    }
  })
  .catch(function(){ showAlert('Network error', 'err'); });
}

function editStudent(id) {
  var s = allStudents.find(function(x){ return String(x.id) === String(id); });
  if (!s) return;
  document.getElementById('fId').value          = s.id;
  document.getElementById('fName').value        = s.full_name || '';
  document.getElementById('fStudentId').value   = s.student_id || '';
  document.getElementById('fUsername').value    = s.username || '';
  document.getElementById('fPassword').value    = '';
  document.getElementById('fPassword').placeholder = 'Leave blank to keep current password';
  document.getElementById('fPassword').readOnly    = false;
  document.getElementById('fProgram').value     = s.program || '';
  document.getElementById('fEmail').value       = s.email || '';
  document.getElementById('fPhone').value       = s.phone || '';
  document.getElementById('fQual').value        = s.qualification || '';
  document.getElementById('fSemester').value    = s.semester || '';
  document.getElementById('fAddress').value     = s.address || '';
  document.getElementById('fStatus').value      = s.status || 'active';
  document.getElementById('credBox').style.display = 'none';
  document.getElementById('formTitle').innerHTML = '<i class="fa fa-edit"></i> Edit Student';
  document.getElementById('btnTxt').textContent  = 'Update Student';
  document.getElementById('formAlert').style.display = 'none';
  window.scrollTo({top:0, behavior:'smooth'});
}

function deleteStudent(id) {
  if (!confirm('Delete this student? This cannot be undone.')) return;
  fetch('student-delete.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({id: parseInt(id)})
  })
  .then(function(r){ return r.text(); })
  .then(function(txt){
    var d; try { d = JSON.parse(txt); } catch(e) { toast('Server error','err'); return; }
    if (d.success) { toast('Deleted','ok'); loadStudents(); }
    else toast(d.message || 'Failed','err');
  })
  .catch(function(){ toast('Network error','err'); });
}

function resetForm() {
  document.getElementById('fId').value = '0';
  ['fName','fStudentId','fUsername','fPassword','fEmail','fPhone','fQual','fAddress'].forEach(function(id){
    document.getElementById(id).value = '';
  });
  document.getElementById('fProgram').value  = '';
  document.getElementById('fSemester').value = '';
  document.getElementById('fStatus').value   = 'active';
  document.getElementById('fPassword').readOnly    = false;
  document.getElementById('fPassword').placeholder = 'Auto123!';
  document.getElementById('credBox').style.display = 'none';
  document.getElementById('formTitle').innerHTML = '<i class="fa fa-plus-circle"></i> Add Student';
  document.getElementById('btnTxt').textContent  = 'Save Student';
  document.getElementById('formAlert').style.display = 'none';
}

function loadStudents() {
  document.getElementById('tbody').innerHTML = '<tr class="empty-row"><td colspan="8"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>';
  fetch('student-list.php')
    .then(function(r){ return r.text(); })
    .then(function(txt){
      var d; try { d = JSON.parse(txt); } catch(e) {
        document.getElementById('tbody').innerHTML = '<tr class="empty-row"><td colspan="8">Parse error</td></tr>'; return;
      }
      if (!d.success) {
        document.getElementById('tbody').innerHTML = '<tr class="empty-row"><td colspan="8">'+(d.message||'Error')+'</td></tr>'; return;
      }
      allStudents = d.students || [];
      updateStats();
      renderTable(allStudents);
    })
    .catch(function(e){
      document.getElementById('tbody').innerHTML = '<tr class="empty-row"><td colspan="8">'+e.message+'</td></tr>';
    });
}

function updateStats() {
  var progs = new Set(allStudents.map(function(s){return s.program;}));
  document.getElementById('sTotal').textContent    = allStudents.length;
  document.getElementById('sActive').textContent   = allStudents.filter(function(s){return s.status==='active';}).length;
  document.getElementById('sPrograms').textContent = progs.size;
  document.getElementById('sInactive').textContent = allStudents.filter(function(s){return s.status==='inactive';}).length;
}

function filterLocal() {
  var q  = document.getElementById('searchInput').value.toLowerCase();
  var pr = document.getElementById('filterProgram').value;
  var st = document.getElementById('filterStatus').value;
  var list = allStudents.filter(function(s){
    var matchQ  = !q  || (s.full_name||'').toLowerCase().includes(q) || (s.student_id||'').toLowerCase().includes(q) || (s.email||'').toLowerCase().includes(q);
    var matchPr = !pr || s.program === pr;
    var matchSt = !st || s.status  === st;
    return matchQ && matchPr && matchSt;
  });
  renderTable(list);
}

function renderTable(list) {
  if (!list.length) {
    document.getElementById('tbody').innerHTML = '<tr class="empty-row"><td colspan="8"><i class="fa fa-user-graduate"></i> No students found</td></tr>';
    return;
  }
  document.getElementById('tbody').innerHTML = list.map(function(s, i){
    var init = (s.full_name||'?').charAt(0).toUpperCase();
    var bdg  = s.status==='active' ? 'badge-active' : 'badge-inactive';
    return '<tr>'
      + '<td>'+(i+1)+'</td>'
      + '<td><div class="student-cell"><span class="avatar-sm">'+init+'</span>'+esc(s.full_name)+'</div></td>'
      + '<td>'+esc(s.student_id||'-')+'</td>'
      + '<td>'+esc(s.program||'-')+'</td>'
      + '<td>'+(s.semester ? (isNaN(s.semester) ? s.semester : 'Semester '+s.semester) : '-')+'</td>'
      + '<td>'+esc(s.email||s.phone||'-')+'</td>'
      + '<td><span class="badge '+bdg+'">'+cap(s.status)+'</span></td>'
      + '<td><div class="action-btns">'
      +   '<button class="btn-icon btn-edit-i" onclick="editStudent('+s.id+')" title="Edit"><i class="fa fa-edit"></i></button>'
      +   '<button class="btn-icon btn-del-i"  onclick="deleteStudent('+s.id+')" title="Delete"><i class="fa fa-trash"></i></button>'
      + '</div></td>'
      + '</tr>';
  }).join('');
}

function showAlert(msg,type){
  var el=document.getElementById('formAlert');
  el.className='alert alert-'+(type==='ok'?'ok':'err');
  el.textContent=msg; el.style.display='block';
  setTimeout(function(){el.style.display='none';},6000);
}
function toast(msg,type){
  var t=document.getElementById('toast');
  t.textContent=msg; t.className='toast '+(type||'ok');
  t.style.display='block';
  setTimeout(function(){t.style.display='none';},3000);
}
function esc(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
function cap(s){return s?s.charAt(0).toUpperCase()+s.slice(1):'';}

loadStudents();
</script>
</body>
</html>
