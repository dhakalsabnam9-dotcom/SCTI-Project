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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
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
    .toolbar input{flex:1;min-width:160px;padding:9px 13px;border:2px solid #dee2e6;border-radius:8px;font-size:13px;font-family:inherit}
    .toolbar input:focus{outline:none;border-color:#004080}
    .btn-refresh{padding:9px 16px;background:linear-gradient(135deg,#004080,#0059b3);color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;transition:.2s}
    .btn-refresh:hover{transform:translateY(-1px)}
    .tgrid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px}
    .tcard{background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.07);transition:.25s}
    .tcard:hover{transform:translateY(-5px);box-shadow:0 10px 28px rgba(0,64,128,.18)}
    .tcard-head{background:linear-gradient(135deg,#004080,#0059b3);padding:20px;text-align:center;color:white}
    .tcard-avatar{width:60px;height:60px;border-radius:50%;background:rgba(255,255,255,.2);border:2px solid rgba(255,255,255,.4);display:flex;align-items:center;justify-content:center;font-size:24px;margin:0 auto 10px}
    .tcard-name{font-size:14px;font-weight:700;margin-bottom:2px}
    .tcard-dept{font-size:11px;opacity:.8}
    .tcard-body{padding:14px}
    .tcard-row{display:flex;align-items:center;gap:7px;font-size:12px;color:#666;padding:4px 0;border-bottom:1px solid #f5f5f5}
    .tcard-row:last-of-type{border-bottom:none}
    .tcard-row i{color:#004080;width:14px;font-size:11px}
    .tbdg{padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;display:inline-block;margin:8px 0}
    .tbdg-active{background:#d4edda;color:#155724}
    .tbdg-inactive{background:#e2e3e5;color:#383d41}
    .tcard-actions{display:flex;gap:7px;margin-top:10px}
    .tbtn{flex:1;padding:6px;border:none;border-radius:7px;cursor:pointer;font-size:11px;font-weight:600;transition:.2s;display:flex;align-items:center;justify-content:center;gap:4px}
    .tbtn-edit{background:#fff3cd;color:#856404}
    .tbtn-edit:hover{background:#ffc107;color:#fff}
    .tbtn-del{background:#f8d7da;color:#721c24}
    .tbtn-del:hover{background:#dc3545;color:#fff}
    .empty{text-align:center;padding:60px 20px;color:#ccc;grid-column:1/-1}
    .empty i{font-size:56px;display:block;margin-bottom:12px}
    .toast{position:fixed;bottom:22px;right:22px;color:white;padding:11px 18px;border-radius:9px;font-size:13px;font-weight:700;z-index:99999;display:none}
    .toast.ok{background:#28a745}
    .toast.err{background:#dc3545}
    .divider{border:none;border-top:1px dashed #e0e6ef;margin:14px 0}
    footer{background:#00264d;color:white;text-align:center;padding:12px;font-size:13px}
    @media(max-width:860px){.layout{grid-template-columns:1fr}.stats{grid-template-columns:repeat(2,1fr)}}
  </style>
</head>
<body>
<div class="top-bar"><marquee>Teacher Manager — Add and manage all teaching staff at SCTI</marquee></div>
<div class="pg-header">
  <div>
    <h1><i class="fa fa-chalkboard-teacher"></i> Manage Teachers</h1>
    <div class="bc"><a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Teachers</div>
  </div>
  <div class="hdr-btns">
    <a href="../dashboards/admin-dashboard.php" class="btn-hdr ghost"><i class="fa fa-arrow-left"></i> Back</a>
  </div>
</div>

<div class="stats">
  <div class="stat"><div class="stat-ico ico-blue"><i class="fa fa-chalkboard-teacher"></i></div><div><div class="stat-val" id="sTotal">—</div><div class="stat-lbl">Total Teachers</div></div></div>
  <div class="stat"><div class="stat-ico ico-green"><i class="fa fa-check-circle"></i></div><div><div class="stat-val" id="sActive">—</div><div class="stat-lbl">Active</div></div></div>
  <div class="stat"><div class="stat-ico ico-orange"><i class="fa fa-layer-group"></i></div><div><div class="stat-val" id="sDepts">—</div><div class="stat-lbl">Departments</div></div></div>
  <div class="stat"><div class="stat-ico ico-grey"><i class="fa fa-user-slash"></i></div><div><div class="stat-val" id="sInactive">—</div><div class="stat-lbl">Inactive</div></div></div>
</div>

<div class="layout">
  <!-- LEFT PANEL: FORM -->
  <div class="panel">
    <h3 id="formTitle"><i class="fa fa-plus-circle"></i> Add Teacher</h3>
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
          <input type="text" id="fUsername" class="fc" placeholder="teacher.ram" readonly>
          <button class="ig-btn" onclick="genUsername()" title="Regenerate"><i class="fa fa-sync"></i></button>
        </div>
      </div>
      <div class="fg">
        <label>Password <span class="auto-badge"><i class="fa fa-magic"></i> Auto</span></label>
        <div class="input-group">
          <input type="text" id="fPassword" class="fc" placeholder="Auto123!" readonly>
          <button class="ig-btn" onclick="genPassword()" title="Regenerate"><i class="fa fa-sync"></i></button>
        </div>
      </div>
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
        <label>Status</label>
        <select id="fStatus" class="fc">
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>
    </div>
    <div class="fg">
      <label>Subjects (comma separated)</label>
      <input type="text" id="fSubjects" class="fc" placeholder="e.g. Programming, Database, Web Dev">
    </div>

    <button class="btn-submit" onclick="saveTeacher()"><i class="fa fa-save"></i> <span id="btnTxt">Save Teacher</span></button>
    <button class="btn-reset" onclick="resetForm()"><i class="fa fa-times"></i> Cancel / Reset</button>
  </div>

  <!-- RIGHT: GRID -->
  <div class="content">
    <div class="toolbar">
      <input type="text" id="searchInput" placeholder="Search teachers..." oninput="filterLocal()">
      <button class="btn-refresh" onclick="loadTeachers()"><i class="fa fa-sync"></i> Refresh</button>
    </div>
    <div class="tgrid" id="tgrid">
      <div class="empty"><i class="fa fa-spinner fa-spin"></i><p>Loading...</p></div>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>
<footer>© 2025 SCTI — Admin Panel</footer>

<script>
var allTeachers = [];

// ── AUTO-GENERATE ──────────────────────────────
function autoGenerate() {
  genTeacherId();
  genUsername();
  if (!document.getElementById('fPassword').value) genPassword();
}

function genTeacherId() {
  var year = new Date().getFullYear();
  var num  = String(Math.floor(Math.random() * 900) + 100);
  document.getElementById('fTeacherId').value = 'TCH-' + year + '-' + num;
}

function genUsername() {
  var name = document.getElementById('fName').value.trim().toLowerCase();
  var parts = name.split(/\s+/);
  var base  = parts.length >= 2
    ? parts[0] + '.' + parts[parts.length - 1]
    : (parts[0] || 'teacher') + Math.floor(Math.random() * 99 + 1);
  base = base.replace(/[^a-z0-9.]/g, '');
  document.getElementById('fUsername').value = base || 'teacher' + Math.floor(Math.random()*999);
}

function genPassword() {
  var upper  = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
  var lower  = 'abcdefghjkmnpqrstuvwxyz';
  var digits = '23456789';
  var spec   = '@#$!';
  var pwd = upper[Math.floor(Math.random()*upper.length)]
          + lower[Math.floor(Math.random()*lower.length)]
          + lower[Math.floor(Math.random()*lower.length)]
          + digits[Math.floor(Math.random()*digits.length)]
          + digits[Math.floor(Math.random()*digits.length)]
          + spec[Math.floor(Math.random()*spec.length)]
          + lower[Math.floor(Math.random()*lower.length)]
          + upper[Math.floor(Math.random()*upper.length)];
  // shuffle
  pwd = pwd.split('').sort(function(){return Math.random()-.5;}).join('');
  document.getElementById('fPassword').value = pwd;
}

// ── SAVE ──────────────────────────────────────
function saveTeacher() {
  var name     = document.getElementById('fName').value.trim();
  var tid      = document.getElementById('fTeacherId').value.trim();
  var username = document.getElementById('fUsername').value.trim();
  var password = document.getElementById('fPassword').value.trim();
  var dept     = document.getElementById('fDept').value;
  var id       = parseInt(document.getElementById('fId').value) || 0;

  if (!name || !dept) { showAlert('Name and Department are required', 'err'); return; }
  if (!tid || !username)  { showAlert('Please generate Teacher ID and Username', 'err'); return; }
  if (id === 0 && !password) { showAlert('Please generate a Password', 'err'); return; }

  var payload = {
    id:            id,
    name:          name,
    teacher_id:    tid,
    username:      username,
    password:      password,   // empty string on edit = no change
    department:    dept,
    email:         document.getElementById('fEmail').value.trim(),
    phone:         document.getElementById('fPhone').value.trim(),
    qualification: document.getElementById('fQual').value.trim(),
    experience:    document.getElementById('fExp').value.trim(),
    subjects:      document.getElementById('fSubjects').value.trim(),
    status:        document.getElementById('fStatus').value
  };

  fetch('teacher-save.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify(payload)
  })
  .then(function(r){ return r.text(); })
  .then(function(txt){
    var d; try { d = JSON.parse(txt); } catch(e) { showAlert('Server error', 'err'); return; }
    if (d.success) {
      var msg = d.message;
      if (id === 0) msg += ' | Username: ' + username + ' | Password: ' + password;
      showAlert(msg, 'ok');
      resetForm();
      loadTeachers();
    } else {
      showAlert(d.message || 'Failed', 'err');
    }
  })
  .catch(function(){ showAlert('Network error', 'err'); });
}

// ── EDIT ──────────────────────────────────────
function editTeacher(id) {
  var t = allTeachers.find(function(x){ return String(x.id) === String(id); });
  if (!t) return;
  document.getElementById('fId').value         = t.id;
  document.getElementById('fName').value       = t.full_name || t.name || '';
  document.getElementById('fTeacherId').value  = t.teacher_id || '';
  document.getElementById('fUsername').value   = t.username || '';
  document.getElementById('fPassword').value   = '';  // never show hashed password
  document.getElementById('fDept').value       = t.department || '';
  document.getElementById('fEmail').value      = t.email || '';
  document.getElementById('fPhone').value      = t.phone || '';
  document.getElementById('fQual').value       = t.qualification || '';
  document.getElementById('fExp').value        = t.experience || '';
  document.getElementById('fSubjects').value   = t.subjects || '';
  document.getElementById('fStatus').value     = t.status || 'active';
  document.getElementById('fPassword').placeholder = 'Leave blank to keep current password';
  document.getElementById('fPassword').readOnly    = false;
  document.getElementById('formTitle').innerHTML = '<i class="fa fa-edit"></i> Edit Teacher';
  document.getElementById('btnTxt').textContent  = 'Update Teacher';
  document.getElementById('formAlert').style.display = 'none';
  window.scrollTo({top:0, behavior:'smooth'});
}

// ── DELETE ────────────────────────────────────
function deleteTeacher(id) {
  if (!confirm('Delete this teacher? This cannot be undone.')) return;
  fetch('teacher-delete.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({id: parseInt(id)})
  })
  .then(function(r){ return r.text(); })
  .then(function(txt){
    var d; try { d = JSON.parse(txt); } catch(e) { toast('Server error','err'); return; }
    if (d.success) { toast('Deleted','ok'); loadTeachers(); }
    else toast(d.message || 'Failed','err');
  })
  .catch(function(){ toast('Network error','err'); });
}

// ── RESET ─────────────────────────────────────
function resetForm() {
  document.getElementById('fId').value = '0';
  ['fName','fTeacherId','fUsername','fPassword','fEmail','fPhone','fQual','fExp','fSubjects'].forEach(function(id){
    document.getElementById(id).value = '';
  });
  document.getElementById('fDept').value   = '';
  document.getElementById('fStatus').value = 'active';
  document.getElementById('formTitle').innerHTML = '<i class="fa fa-plus-circle"></i> Add Teacher';
  document.getElementById('btnTxt').textContent  = 'Save Teacher';
  document.getElementById('formAlert').style.display = 'none';
}

// ── LOAD / RENDER ─────────────────────────────
function loadTeachers() {
  var grid = document.getElementById('tgrid');
  grid.innerHTML = '<div class="empty"><i class="fa fa-spinner fa-spin"></i><p>Loading...</p></div>';
  fetch('teacher-list.php')
    .then(function(r){ return r.text(); })
    .then(function(txt){
      var d; try { d = JSON.parse(txt); } catch(e) {
        grid.innerHTML = '<div class="empty"><i class="fa fa-exclamation-triangle"></i><p>Parse error</p></div>'; return;
      }
      if (!d.success) {
        grid.innerHTML = '<div class="empty"><i class="fa fa-exclamation-triangle"></i><p>'+(d.message||'Error')+'</p></div>'; return;
      }
      allTeachers = d.teachers || [];
      updateStats();
      renderGrid(allTeachers);
    })
    .catch(function(e){
      grid.innerHTML = '<div class="empty"><i class="fa fa-exclamation-triangle"></i><p>'+e.message+'</p></div>';
    });
}

function updateStats() {
  var depts = new Set(allTeachers.map(function(t){return t.department;}));
  document.getElementById('sTotal').textContent    = allTeachers.length;
  document.getElementById('sActive').textContent   = allTeachers.filter(function(t){return t.status==='active';}).length;
  document.getElementById('sDepts').textContent    = depts.size;
  document.getElementById('sInactive').textContent = allTeachers.filter(function(t){return t.status==='inactive';}).length;
}

function filterLocal() {
  var q = document.getElementById('searchInput').value.toLowerCase();
  var list = allTeachers.filter(function(t){
    return !q || (t.name||'').toLowerCase().includes(q) || (t.department||'').toLowerCase().includes(q) || (t.teacher_id||'').toLowerCase().includes(q);
  });
  renderGrid(list);
}

function renderGrid(list) {
  var grid = document.getElementById('tgrid');
  if (!list.length) {
    grid.innerHTML = '<div class="empty"><i class="fa fa-chalkboard-teacher"></i><p>No teachers found</p></div>';
    return;
  }
  grid.innerHTML = list.map(buildCard).join('');
}

function buildCard(t) {
  var bdg = t.status === 'active' ? 'tbdg-active' : 'tbdg-inactive';
  var tags = (t.subjects || '').split(',').filter(function(s){return s.trim();}).slice(0,3)
    .map(function(s){return '<span style="background:#e8f0fe;color:#004080;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:600;display:inline-block;margin:2px">'+esc(s.trim())+'</span>';}).join('');
  return '<div class="tcard">'
    + '<div class="tcard-head">'
    +   '<div class="tcard-avatar"><i class="fa fa-user-tie"></i></div>'
    +   '<div class="tcard-name">'+esc(t.name)+'</div>'
    +   '<div class="tcard-dept">'+esc(t.department)+'</div>'
    + '</div>'
    + '<div class="tcard-body">'
    +   '<div class="tcard-row"><i class="fa fa-id-badge"></i>'+esc(t.teacher_id)+'</div>'
    +   '<div class="tcard-row"><i class="fa fa-user"></i>'+esc(t.username)+'</div>'
    +   '<div class="tcard-row"><i class="fa fa-envelope"></i>'+esc(t.email||'—')+'</div>'
    +   '<div class="tcard-row"><i class="fa fa-graduation-cap"></i>'+esc(t.qualification||'—')+'</div>'
    +   '<div class="tcard-row"><i class="fa fa-clock"></i>'+esc(t.experience||'—')+'</div>'
    +   '<span class="tbdg '+bdg+'">'+cap(t.status)+'</span>'
    +   (tags ? '<div style="margin-bottom:8px">'+tags+'</div>' : '')
    +   '<div class="tcard-actions">'
    +     '<button class="tbtn tbtn-edit" onclick="editTeacher('+t.id+')"><i class="fa fa-edit"></i> Edit</button>'
    +     '<button class="tbtn tbtn-del"  onclick="deleteTeacher('+t.id+')"><i class="fa fa-trash"></i> Del</button>'
    +   '</div>'
    + '</div></div>';
}

function showAlert(msg,type){
  var el=document.getElementById('formAlert');
  el.className='alert alert-'+(type==='ok'?'ok':'err');
  el.textContent=msg; el.style.display='block';
  setTimeout(function(){el.style.display='none';},5000);
}
function toast(msg,type){
  var t=document.getElementById('toast');
  t.textContent=msg; t.className='toast '+(type||'ok');
  t.style.display='block';
  setTimeout(function(){t.style.display='none';},3000);
}
function esc(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
function cap(s){return s?s.charAt(0).toUpperCase()+s.slice(1):'';}

loadTeachers();
</script>
</body>
</html>
