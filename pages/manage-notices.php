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
  <title>Notice Manager | SCTI Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box;}
    body{font-family:'Segoe UI',sans-serif;background:#f0f4f8;min-height:100vh;}
    .top-bar{background:#00264d;color:white;padding:7px 20px;font-size:13px;}
    /* â”€â”€ HEADER â”€â”€ */
    .pg-header{background:linear-gradient(135deg,#004080,#0059b3);color:#fff;padding:20px 28px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 18px rgba(0,64,128,.25);}
    .pg-header h1{font-size:22px;font-weight:700;display:flex;align-items:center;gap:10px;margin:0 0 3px;}
    .pg-header .bc{font-size:12px;color:rgba(255,255,255,.75);}
    .pg-header .bc a{color:#fff;text-decoration:none;}
    .hdr-btns{display:flex;gap:8px;}
    .btn-hdr{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;border:none;transition:.2s;text-decoration:none;}
    .btn-hdr.ghost{background:rgba(255,255,255,.18);color:#fff;}
    .btn-hdr.ghost:hover{background:rgba(255,255,255,.32);}
    /* â”€â”€ STATS â”€â”€ */
    .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;padding:18px 28px;background:#fff;border-bottom:1px solid #e9ecef;}
    .stat{display:flex;align-items:center;gap:12px;background:#f8f9fa;border-radius:10px;padding:12px 16px;}
    .stat-ico{width:42px;height:42px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:17px;color:#fff;flex-shrink:0;}
    .ico-blue{background:linear-gradient(135deg,#004080,#0059b3);}
    .ico-green{background:linear-gradient(135deg,#28a745,#20c997);}
    .ico-red{background:linear-gradient(135deg,#dc3545,#c82333);}
    .ico-grey{background:linear-gradient(135deg,#6c757d,#495057);}
    .stat-val{font-size:24px;font-weight:700;color:#222;line-height:1;}
    .stat-lbl{font-size:11px;color:#999;margin-top:2px;}
    /* â”€â”€ LAYOUT â”€â”€ */
    .layout{display:grid;grid-template-columns:360px 1fr;min-height:calc(100vh - 170px);}
    .panel{background:#fff;border-right:1px solid #e9ecef;padding:22px;overflow-y:auto;}
    .panel h3{font-size:15px;color:#004080;margin-bottom:16px;padding-bottom:10px;border-bottom:2px solid #e8f0fe;display:flex;align-items:center;gap:8px;}
    .content{padding:22px;overflow-y:auto;}
    /* â”€â”€ FORM â”€â”€ */
    .fg{margin-bottom:13px;}
    .fg label{display:block;font-size:12px;font-weight:600;color:#555;margin-bottom:5px;}
    .fc{width:100%;padding:9px 12px;border:2px solid #dee2e6;border-radius:8px;font-size:13px;font-family:inherit;transition:.2s;}
    .fc:focus{outline:none;border-color:#004080;box-shadow:0 0 0 3px rgba(0,64,128,.1);}
    textarea.fc{resize:vertical;min-height:80px;}
    .frow{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
    .btn-submit{width:100%;padding:12px;background:linear-gradient(135deg,#004080,#0059b3);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:.25s;margin-top:4px;}
    .btn-submit:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,64,128,.35);}
    .btn-reset{width:100%;padding:10px;background:#f0f0f0;color:#555;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;margin-top:8px;}
    .btn-reset:hover{background:#e0e0e0;}
    .alert{padding:10px 13px;border-radius:8px;font-size:13px;margin-bottom:12px;display:none;}
    .alert-ok{background:#d4edda;color:#155724;border:1px solid #c3e6cb;}
    .alert-err{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;}
    /* â”€â”€ TOOLBAR â”€â”€ */
    .toolbar{display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap;align-items:center;}
    .toolbar select,.toolbar input{padding:9px 13px;border:2px solid #dee2e6;border-radius:8px;font-size:13px;font-family:inherit;}
    .toolbar input{flex:1;min-width:160px;}
    .toolbar select:focus,.toolbar input:focus{outline:none;border-color:#004080;}
    .btn-refresh{padding:9px 16px;background:linear-gradient(135deg,#004080,#0059b3);color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;transition:.2s;}
    .btn-refresh:hover{transform:translateY(-1px);}
    /* â”€â”€ NOTICE GRID â”€â”€ */
    .ngrid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;}
    .ncard{background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.07);transition:.25s;border-top:4px solid #004080;}
    .ncard:hover{transform:translateY(-5px);box-shadow:0 10px 28px rgba(0,64,128,.18);}
    .ncard.urgent{border-top-color:#dc3545;}
    .ncard.high{border-top-color:#fd7e14;}
    .ncard.dim{opacity:.55;}
    .ncard-body{padding:14px;}
    .ncard-badges{display:flex;gap:5px;flex-wrap:wrap;margin-bottom:8px;}
    .nbdg{padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;}
    .nbdg-urgent{background:#f8d7da;color:#721c24;}
    .nbdg-high{background:#fff3cd;color:#856404;}
    .nbdg-normal{background:#cce5ff;color:#004085;}
    .nbdg-active{background:#d4edda;color:#155724;}
    .nbdg-inactive{background:#e2e3e5;color:#383d41;}
    .nbdg-cat{background:#e8ecf2;color:#4a5568;}
    .ncard-title{font-weight:700;color:#222;font-size:13px;margin-bottom:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .ncard-desc{color:#aaa;font-size:11px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;margin-bottom:8px;}
    .ncard-meta{font-size:10px;color:#ccc;margin-bottom:9px;display:flex;align-items:center;gap:4px;}
    .ncard-meta i{color:#004080;}
    .ncard-actions{display:flex;gap:7px;}
    .nbtn{flex:1;padding:6px;border:none;border-radius:7px;cursor:pointer;font-size:11px;font-weight:600;transition:.2s;display:flex;align-items:center;justify-content:center;gap:4px;}
    .nbtn-edit{background:#fff3cd;color:#856404;}
    .nbtn-edit:hover{background:#ffc107;color:#fff;}
    .nbtn-tog{background:#d4edda;color:#155724;}
    .nbtn-tog:hover{background:#28a745;color:#fff;}
    .nbtn-del{background:#f8d7da;color:#721c24;}
    .nbtn-del:hover{background:#dc3545;color:#fff;}
    .empty{text-align:center;padding:60px 20px;color:#ccc;grid-column:1/-1;}
    .empty i{font-size:56px;display:block;margin-bottom:12px;}
    /* â”€â”€ AUDIENCE TAGS â”€â”€ */
    .aud-tag{cursor:pointer;display:inline-flex;align-items:center;}
    .aud-tag span{padding:7px 14px;border-radius:20px;font-size:12px;font-weight:700;border:2px solid #dee2e6;background:#f8f9fa;color:#555;transition:.2s;display:flex;align-items:center;gap:5px;}
    .aud-tag:hover span{border-color:#004080;color:#004080;}
    .aud-tag.selected span{background:linear-gradient(135deg,#004080,#0059b3);color:white;border-color:transparent;}
    .aud-tag[data-val="student"].selected span{background:linear-gradient(135deg,#004080,#0059b3);}
    .aud-tag[data-val="teacher"].selected span{background:linear-gradient(135deg,#28a745,#20c997);}
    .aud-tag[data-val="emergency"].selected span{background:linear-gradient(135deg,#dc3545,#c82333);}
    .aud-tag[data-val="all"].selected span{background:linear-gradient(135deg,#6f42c1,#e83e8c);}
    /* audience badge on cards */
    .nbdg-aud-all{background:#ede9fe;color:#5b21b6;}
    .nbdg-aud-student{background:#cce5ff;color:#004085;}
    .nbdg-aud-teacher{background:#d4edda;color:#155724;}
    .nbdg-aud-emergency{background:#f8d7da;color:#721c24;}
    /* â”€â”€ TOAST â”€â”€ */
    .toast{position:fixed;bottom:22px;right:22px;color:white;padding:11px 18px;border-radius:9px;font-size:13px;font-weight:700;z-index:99999;display:none;}
    .toast.ok{background:#28a745;}
    .toast.err{background:#dc3545;}
    footer{background:#00264d;color:white;text-align:center;padding:12px;font-size:13px;}
    @media(max-width:860px){.layout{grid-template-columns:1fr;}.stats{grid-template-columns:repeat(2,1fr);}}
  </style>
</head>
<body>
<div class="top-bar"><marquee>Notice Manager â€” Create and manage notices for the SCTI website</marquee></div>

<!-- HEADER -->
<div class="pg-header">
  <div>
    <h1><i class="fa fa-bullhorn"></i> Notice Manager</h1>
    <div class="bc"><a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Notices</div>
  </div>
  <div class="hdr-btns">
    <a href="../dashboards/admin-dashboard.php" class="btn-hdr ghost"><i class="fa fa-arrow-left"></i> Back</a>
  </div>
</div>

<!-- STATS -->
<div class="stats">
  <div class="stat"><div class="stat-ico ico-blue"><i class="fa fa-bullhorn"></i></div><div><div class="stat-val" id="sTotal">â€”</div><div class="stat-lbl">Total Notices</div></div></div>
  <div class="stat"><div class="stat-ico ico-green"><i class="fa fa-check-circle"></i></div><div><div class="stat-val" id="sActive">â€”</div><div class="stat-lbl">Active</div></div></div>
  <div class="stat"><div class="stat-ico ico-red"><i class="fa fa-exclamation-circle"></i></div><div><div class="stat-val" id="sUrgent">â€”</div><div class="stat-lbl">Urgent</div></div></div>
  <div class="stat"><div class="stat-ico ico-grey"><i class="fa fa-archive"></i></div><div><div class="stat-val" id="sInactive">â€”</div><div class="stat-lbl">Inactive</div></div></div>
</div>

<!-- LAYOUT -->
<div class="layout">

  <!-- LEFT PANEL: CREATE / EDIT FORM -->
  <div class="panel">
    <h3 id="formTitle"><i class="fa fa-plus-circle"></i> Create Notice</h3>
    <div id="formAlert" class="alert"></div>
    <input type="hidden" id="fId" value="0">
    <div class="fg"><label>Title *</label><input type="text" id="fTitle" class="fc" placeholder="Notice title..."></div>
    <div class="fg"><label>Description *</label><textarea id="fDesc" class="fc" placeholder="Notice content..."></textarea></div>
    <div class="frow">
      <div class="fg">
        <label>Category</label>
        <select id="fCat" class="fc">
          <option value="general">General</option>
          <option value="admission">Admission</option>
          <option value="exam">Exam</option>
          <option value="event">Event</option>
          <option value="holiday">Holiday</option>
          <option value="urgent">Urgent</option>
        </select>
      </div>
      <div class="fg">
        <label>Priority</label>
        <select id="fPri" class="fc">
          <option value="normal">Normal</option>
          <option value="high">High</option>
          <option value="urgent">Urgent</option>
        </select>
      </div>
    </div>
    <div class="fg">
      <label>Audience / Tag</label>
      <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:4px" id="audienceTags">
        <label class="aud-tag" data-val="all">
          <input type="radio" name="audience" value="all" checked style="display:none">
          <span><i class="fa fa-globe"></i> For All</span>
        </label>
        <label class="aud-tag" data-val="student">
          <input type="radio" name="audience" value="student" style="display:none">
          <span><i class="fa fa-user-graduate"></i> Students</span>
        </label>
        <label class="aud-tag" data-val="teacher">
          <input type="radio" name="audience" value="teacher" style="display:none">
          <span><i class="fa fa-chalkboard-teacher"></i> Teachers</span>
        </label>
        <label class="aud-tag" data-val="emergency">
          <input type="radio" name="audience" value="emergency" style="display:none">
          <span><i class="fa fa-triangle-exclamation"></i> Emergency</span>
        </label>
      </div>
    </div>
    <div class="frow">
      <div class="fg"><label>Date</label><input type="date" id="fDate" class="fc"></div>
      <div class="fg">
        <label>Status</label>
        <select id="fStat" class="fc">
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>
    </div>
    <button class="btn-submit" onclick="saveNotice()"><i class="fa fa-save"></i> <span id="btnTxt">Save Notice</span></button>
    <button class="btn-reset" onclick="resetForm()"><i class="fa fa-times"></i> Cancel / Reset</button>
  </div>

  <!-- RIGHT CONTENT: NOTICE GRID -->
  <div class="content">
    <div class="toolbar">
      <select id="filterStatus" onchange="loadNotices()">
        <option value="all">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>
      <select id="filterPri" onchange="loadNotices()">
        <option value="">All Priority</option>
        <option value="urgent">Urgent</option>
        <option value="high">High</option>
        <option value="normal">Normal</option>
      </select>
      <input type="text" id="searchInput" placeholder="Search by title..." oninput="filterLocal()">
      <button class="btn-refresh" onclick="loadNotices()"><i class="fa fa-sync"></i> Refresh</button>
    </div>
    <div class="ngrid" id="ngrid">
      <div class="empty"><i class="fa fa-spinner fa-spin"></i><p>Loading...</p></div>
    </div>
  </div>

</div><!-- /layout -->

<div class="toast" id="toast"></div>
<footer>Â© 2025 SCTI â€” Admin Panel</footer>

<script>
var allNotices = [];

/* â”€â”€ LOAD â”€â”€ */
function loadNotices() {
  var status = document.getElementById('filterStatus').value;
  var grid = document.getElementById('ngrid');
  grid.innerHTML = '<div class="empty"><i class="fa fa-spinner fa-spin"></i><p>Loading...</p></div>';

  fetch('notice-list.php?status=' + encodeURIComponent(status))
    .then(function(r){ return r.text(); })
    .then(function(txt){
      var d;
      try { d = JSON.parse(txt); } catch(e) {
        grid.innerHTML = '<div class="empty"><i class="fa fa-exclamation-triangle"></i><p>Parse error: ' + txt.substring(0,80) + '</p></div>';
        return;
      }
      if (!d.success) { grid.innerHTML = '<div class="empty"><i class="fa fa-exclamation-triangle"></i><p>' + (d.message||'Error') + '</p></div>'; return; }
      allNotices = d.notices || [];
      updateStats();
      renderGrid(allNotices);
    })
    .catch(function(e){ grid.innerHTML = '<div class="empty"><i class="fa fa-exclamation-triangle"></i><p>Network error: ' + e.message + '</p></div>'; });
}

function updateStats() {
  document.getElementById('sTotal').textContent    = allNotices.length;
  document.getElementById('sActive').textContent   = allNotices.filter(function(n){return n.status==='active';}).length;
  document.getElementById('sUrgent').textContent   = allNotices.filter(function(n){return n.priority==='urgent';}).length;
  document.getElementById('sInactive').textContent = allNotices.filter(function(n){return n.status==='inactive';}).length;
}

function filterLocal() {
  var q = document.getElementById('searchInput').value.toLowerCase();
  var pri = document.getElementById('filterPri').value;
  var list = allNotices.filter(function(n){
    var matchQ = !q || n.title.toLowerCase().indexOf(q) > -1;
    var matchP = !pri || n.priority === pri;
    return matchQ && matchP;
  });
  renderGrid(list);
}

function renderGrid(list) {
  var grid = document.getElementById('ngrid');
  if (!list.length) { grid.innerHTML = '<div class="empty"><i class="fa fa-bullhorn"></i><p>No notices found</p></div>'; return; }
  grid.innerHTML = list.map(buildCard).join('');
}

function buildCard(n) {
  var pc  = n.priority==='urgent' ? 'urgent' : n.priority==='high' ? 'high' : '';
  var dim = n.status==='inactive' ? 'dim' : '';
  var pl  = cap(n.priority);
  var sl  = cap(n.status);
  var cl  = cap(n.category);
  var dt  = fmtDate(n.notice_date);
  var tl  = n.status==='active' ? 'Deactivate' : 'Activate';
  var ti  = n.status==='active' ? 'fa-eye-slash' : 'fa-eye';
  var id  = n.id;
  var st  = n.status;
  var aud = n.audience || 'all';
  var audLabels = {all:'For All',student:'Students',teacher:'Teachers',emergency:'Emergency'};
  var audIcons  = {all:'fa-globe',student:'fa-user-graduate',teacher:'fa-chalkboard-teacher',emergency:'fa-triangle-exclamation'};

  return '<div class="ncard ' + pc + ' ' + dim + '">'
    + '<div class="ncard-body">'
    +   '<div class="ncard-badges">'
    +     '<span class="nbdg nbdg-' + n.priority + '">' + pl + '</span>'
    +     '<span class="nbdg nbdg-' + n.status + '">' + sl + '</span>'
    +     '<span class="nbdg nbdg-cat">' + cl + '</span>'
    +     '<span class="nbdg nbdg-aud-' + aud + '"><i class="fa ' + (audIcons[aud]||'fa-globe') + '"></i> ' + (audLabels[aud]||aud) + '</span>'
    +   '</div>'
    +   '<div class="ncard-title">' + esc(n.title) + '</div>'
    +   '<div class="ncard-desc">' + esc(n.description) + '</div>'
    +   '<div class="ncard-meta"><i class="fa fa-calendar"></i> ' + dt + '&nbsp;&nbsp;<i class="fa fa-user" style="color:#28a745"></i> ' + esc(n.created_by_name||'Admin') + '</div>'
    +   '<div class="ncard-actions">'
    +     '<button class="nbtn nbtn-edit" onclick="editNotice(' + id + ')"><i class="fa fa-edit"></i> Edit</button>'
    +     '<button class="nbtn nbtn-tog"  onclick="toggleNotice(' + id + ',\'' + st + '\')"><i class="fa ' + ti + '"></i> ' + tl + '</button>'
    +     '<button class="nbtn nbtn-del"  onclick="deleteNotice(' + id + ')"><i class="fa fa-trash"></i> Del</button>'
    +   '</div>'
    + '</div>'
    + '</div>';
}

/* â”€â”€ FORM â”€â”€ */
function resetForm() {
  document.getElementById('fId').value    = '0';
  document.getElementById('fTitle').value = '';
  document.getElementById('fDesc').value  = '';
  document.getElementById('fCat').value   = 'general';
  document.getElementById('fPri').value   = 'normal';
  document.getElementById('fDate').value  = today();
  document.getElementById('fStat').value  = 'active';
  document.querySelectorAll('input[name="audience"]').forEach(function(r){ r.checked = r.value === 'all'; });
  document.querySelectorAll('.aud-tag').forEach(function(t){ t.classList.toggle('selected', t.dataset.val === 'all'); });
  document.getElementById('formTitle').innerHTML = '<i class="fa fa-plus-circle"></i> Create Notice';
  document.getElementById('btnTxt').textContent  = 'Save Notice';
  document.getElementById('formAlert').style.display = 'none';
}

function editNotice(id) {
  var n = allNotices.find(function(x){ return String(x.id)===String(id); });
  if (!n) { toast('Notice not found','err'); return; }
  document.getElementById('fId').value    = n.id;
  document.getElementById('fTitle').value = n.title;
  document.getElementById('fDesc').value  = n.description;
  document.getElementById('fCat').value   = n.category;
  document.getElementById('fPri').value   = n.priority;
  document.getElementById('fDate').value  = (n.notice_date||'').split(' ')[0].split('T')[0] || today();
  document.getElementById('fStat').value  = n.status;
  // Set audience tag
  var aud = n.audience || 'all';
  document.querySelectorAll('input[name="audience"]').forEach(function(r){ r.checked = r.value === aud; });
  document.querySelectorAll('.aud-tag').forEach(function(t){ t.classList.toggle('selected', t.dataset.val === aud); });
  document.getElementById('formTitle').innerHTML = '<i class="fa fa-edit"></i> Edit Notice';
  document.getElementById('btnTxt').textContent  = 'Update Notice';
  document.querySelector('.panel').scrollTop = 0;
}

function saveNotice() {
  var payload = {
    id:          parseInt(document.getElementById('fId').value),
    title:       document.getElementById('fTitle').value.trim(),
    description: document.getElementById('fDesc').value.trim(),
    category:    document.getElementById('fCat').value,
    priority:    document.getElementById('fPri').value,
    notice_date: document.getElementById('fDate').value,
    status:      document.getElementById('fStat').value,
    audience:    document.querySelector('input[name="audience"]:checked')?.value || 'all'
  };
  if (!payload.title || !payload.description) { showAlert('Title and description required','err'); return; }

  fetch('notice-save.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify(payload)
  })
  .then(function(r){ return r.text(); })
  .then(function(txt){
    var d;
    try { d = JSON.parse(txt); } catch(e) { showAlert('Server error: ' + txt.substring(0,60),'err'); return; }
    if (d.success) { showAlert(d.message,'ok'); resetForm(); loadNotices(); }
    else showAlert(d.message||'Save failed','err');
  })
  .catch(function(e){ showAlert('Network error: ' + e.message,'err'); });
}

/* â”€â”€ TOGGLE â”€â”€ */
function toggleNotice(id, curStatus) {
  var newStatus = curStatus==='active' ? 'inactive' : 'active';
  var n = allNotices.find(function(x){ return String(x.id)===String(id); });
  if (!n) { toast('Notice not found','err'); return; }
  var payload = {
    id: parseInt(n.id), title: n.title, description: n.description,
    category: n.category, priority: n.priority,
    notice_date: (n.notice_date||'').split(' ')[0].split('T')[0] || today(),
    status: newStatus
  };
  fetch('notice-save.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload)})
    .then(function(r){ return r.text(); })
    .then(function(txt){
      var d; try { d=JSON.parse(txt); } catch(e){ toast('Server error','err'); return; }
      if (d.success) { toast('Status â†’ ' + newStatus,'ok'); loadNotices(); }
      else toast(d.message||'Failed','err');
    })
    .catch(function(e){ toast('Network error: ' + e.message,'err'); });
}

/* â”€â”€ DELETE â”€â”€ */
function deleteNotice(id) {
  if (!confirm('Delete this notice? This cannot be undone.')) return;
  fetch('notice-delete.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({id:parseInt(id)})})
    .then(function(r){ return r.text(); })
    .then(function(txt){
      var d; try { d=JSON.parse(txt); } catch(e){ toast('Server error','err'); return; }
      if (d.success) { toast('Notice deleted','ok'); loadNotices(); }
      else toast(d.message||'Delete failed','err');
    })
    .catch(function(e){ toast('Network error: ' + e.message,'err'); });
}

/* â”€â”€ HELPERS â”€â”€ */
function showAlert(msg, type) {
  var el = document.getElementById('formAlert');
  el.className = 'alert alert-' + type;
  el.textContent = msg;
  el.style.display = 'block';
  setTimeout(function(){ el.style.display='none'; }, 4000);
}
function toast(msg, type) {
  var t = document.getElementById('toast');
  t.textContent = msg;
  t.className = 'toast ' + (type||'ok');
  t.style.display = 'block';
  setTimeout(function(){ t.style.display='none'; }, 3000);
}
function esc(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function cap(s){ return s ? s.charAt(0).toUpperCase()+s.slice(1) : ''; }
function today(){ return new Date().toISOString().split('T')[0]; }
function fmtDate(d){ try{ return new Date(d).toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric'}); }catch(e){ return d; } }

document.getElementById('filterPri').addEventListener('change', filterLocal);
document.getElementById('fDate').value = today();

// Audience tag click handler
document.querySelectorAll('.aud-tag').forEach(function(tag) {
  tag.addEventListener('click', function() {
    var val = this.dataset.val;
    document.querySelectorAll('.aud-tag').forEach(function(t){ t.classList.remove('selected'); });
    this.classList.add('selected');
    this.querySelector('input[type="radio"]').checked = true;
  });
});
// Set default selected
document.querySelector('.aud-tag[data-val="all"]').classList.add('selected');

loadNotices();
</script>
</body>
</html>
