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
  <title>Manage Notices | SCTI Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <!-- NO style.css — admin page uses only its own styles below -->
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Segoe UI',Tahoma,sans-serif;background:#f0f2f5;min-height:100vh}
    a{text-decoration:none;color:inherit}

    /* TOP BAR */
    .top-bar{background:#00264d;color:white;padding:7px 20px;font-size:13px}

    /* CONTAINER */
    .wrap{max-width:1100px;margin:0 auto;padding:24px 16px}

    /* PAGE HEADER */
    .ph{background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:24px 28px;border-radius:12px;margin-bottom:24px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 16px rgba(0,64,128,.25)}
    .ph h1{font-size:24px;margin:0 0 4px}
    .ph-bc{font-size:12px;opacity:.85}
    .ph-bc a{color:white}
    .btn-add{background:rgba(255,255,255,.2);color:white;padding:10px 20px;border:2px solid white;border-radius:8px;cursor:pointer;font-size:13px;font-weight:700;display:inline-flex;align-items:center;gap:7px}
    .btn-add:hover{background:white;color:#004080}

    /* STATS */
    .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px}
    .sbox{background:white;border-radius:10px;padding:14px;text-align:center;box-shadow:0 2px 8px rgba(0,0,0,.07);border-top:4px solid #004080}
    .sbox .n{font-size:28px;font-weight:800;color:#004080}
    .sbox .l{color:#777;font-size:11px;margin-top:3px}

    /* FILTER BAR */
    .fbar{background:white;border-radius:10px;padding:12px 18px;margin-bottom:18px;box-shadow:0 2px 8px rgba(0,0,0,.06);display:flex;gap:8px;flex-wrap:wrap}
    .fbtn{padding:6px 15px;border:2px solid #dde3ed;background:#f8fafc;color:#5a6a80;border-radius:20px;cursor:pointer;font-size:12px;font-weight:700}
    .fbtn.on,.fbtn:hover{background:#004080;border-color:#004080;color:white}

    /* NOTICE LIST */
    .nlist{display:flex;flex-direction:column;gap:12px}

    /* NOTICE CARD — no overflow, no transform, no ::before tricks */
    .nc{background:white;border-radius:10px;padding:20px 22px;box-shadow:0 2px 8px rgba(0,0,0,.08);border-left:5px solid #004080}
    .nc.urgent{border-left-color:#dc3545}
    .nc.high{border-left-color:#fd7e14}
    .nc.dim{opacity:.55}

    .nc-head{display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:8px}
    .nc-title{font-size:16px;font-weight:700;color:#1a1a2e}
    .nc-badges{display:flex;gap:5px;flex-wrap:wrap;flex-shrink:0}
    .bdg{padding:2px 9px;border-radius:10px;font-size:10px;font-weight:700}
    .bdg-active{background:#d4edda;color:#155724}
    .bdg-inactive{background:#e2e3e5;color:#383d41}
    .bdg-urgent{background:#f8d7da;color:#721c24}
    .bdg-high{background:#fff3cd;color:#856404}
    .bdg-normal{background:#cce5ff;color:#004085}
    .bdg-cat{background:#e8ecf2;color:#4a5568}

    .nc-meta{display:flex;gap:14px;margin-bottom:8px;flex-wrap:wrap}
    .nc-meta span{color:#888;font-size:11px;display:flex;align-items:center;gap:4px}
    .nc-meta i{color:#004080}
    .nc-body{color:#555;font-size:13px;line-height:1.6;margin-bottom:14px}

    /* ACTION BUTTONS — plain, no z-index games needed */
    .nc-actions{display:flex;gap:8px}
    .abtn{padding:7px 14px;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:700;display:inline-flex;align-items:center;gap:5px}
    .abtn-edit{background:#cce5ff;color:#004085}
    .abtn-edit:hover{background:#004080;color:white}
    .abtn-tog{background:#d4edda;color:#155724}
    .abtn-tog:hover{background:#28a745;color:white}
    .abtn-del{background:#f8d7da;color:#dc3545}
    .abtn-del:hover{background:#dc3545;color:white}

    /* EMPTY */
    .empty{text-align:center;padding:50px 20px;color:#aaa}
    .empty i{font-size:48px;display:block;margin-bottom:12px}

    /* MODAL */
    .mo{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9000;display:none;align-items:center;justify-content:center}
    .mo.open{display:flex}
    .md{background:white;border-radius:14px;width:100%;max-width:560px;max-height:92vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.3)}
    .md-head{background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:18px 22px;border-radius:14px 14px 0 0;display:flex;justify-content:space-between;align-items:center}
    .md-head h3{margin:0;font-size:17px}
    .md-x{background:rgba(255,255,255,.2);border:none;color:white;width:30px;height:30px;border-radius:50%;cursor:pointer;font-size:15px;display:flex;align-items:center;justify-content:center}
    .md-x:hover{background:#dc3545}
    .md-body{padding:22px}
    .fg{margin-bottom:16px}
    .fg label{display:block;font-size:12px;font-weight:700;color:#444;margin-bottom:5px}
    .fg input,.fg textarea,.fg select{width:100%;padding:9px 12px;border:2px solid #dde3ed;border-radius:7px;font-size:13px;font-family:inherit;outline:none}
    .fg input:focus,.fg textarea:focus,.fg select:focus{border-color:#004080}
    .fg textarea{resize:vertical;min-height:90px}
    .frow{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    .md-foot{padding:14px 22px;border-top:1px solid #eee;display:flex;justify-content:flex-end;gap:10px}
    .btn-cancel{padding:9px 18px;border:2px solid #dde3ed;background:white;color:#666;border-radius:7px;cursor:pointer;font-size:13px;font-weight:700}
    .btn-cancel:hover{border-color:#999}
    .btn-save{padding:9px 22px;background:linear-gradient(135deg,#004080,#0059b3);color:white;border:none;border-radius:7px;cursor:pointer;font-size:13px;font-weight:700}
    .btn-save:hover{opacity:.88}

    /* TOAST */
    .toast{position:fixed;bottom:22px;right:22px;color:white;padding:11px 18px;border-radius:9px;font-size:13px;font-weight:700;z-index:99999;display:none}
    .toast.ok{background:#28a745}
    .toast.err{background:#dc3545}

    /* FOOTER */
    footer{background:#00264d;color:white;text-align:center;padding:12px;margin-top:30px;font-size:13px}

    @media(max-width:600px){
      .stats{grid-template-columns:repeat(2,1fr)}
      .frow{grid-template-columns:1fr}
    }
  </style>
</head>
<body>

<div class="top-bar"><marquee>Manage Notices — SCTI Admin Panel</marquee></div>

<div class="wrap">

  <!-- PAGE HEADER -->
  <div class="ph">
    <div>
      <h1><i class="fa fa-bullhorn"></i> Manage Notices</h1>
      <div class="ph-bc"><a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Manage Notices</div>
    </div>
    <button class="btn-add" onclick="openModal()"><i class="fa fa-plus"></i> Create Notice</button>
  </div>

  <!-- STATS -->
  <div class="stats">
    <div class="sbox"><div class="n" id="sTotal">0</div><div class="l">Total</div></div>
    <div class="sbox" style="border-top-color:#28a745"><div class="n" id="sActive" style="color:#28a745">0</div><div class="l">Active</div></div>
    <div class="sbox" style="border-top-color:#dc3545"><div class="n" id="sUrgent" style="color:#dc3545">0</div><div class="l">Urgent</div></div>
    <div class="sbox" style="border-top-color:#6c757d"><div class="n" id="sInactive" style="color:#6c757d">0</div><div class="l">Inactive</div></div>
  </div>

  <!-- FILTER BAR -->
  <div class="fbar">
    <button class="fbtn on" onclick="setFilter('all',this)">All</button>
    <button class="fbtn" onclick="setFilter('active',this)">Active</button>
    <button class="fbtn" onclick="setFilter('inactive',this)">Inactive</button>
    <button class="fbtn" onclick="setFilter('urgent',this)">Urgent</button>
  </div>

  <!-- NOTICE LIST -->
  <div class="nlist" id="nlist">
    <div class="empty"><i class="fa fa-spinner fa-spin"></i><p>Loading...</p></div>
  </div>

</div><!-- /wrap -->

<!-- MODAL -->
<div class="mo" id="mo" onclick="bgClose(event)">
  <div class="md">
    <div class="md-head">
      <h3 id="mdTitle"><i class="fa fa-plus"></i> Create Notice</h3>
      <button class="md-x" onclick="closeModal()"><i class="fa fa-xmark"></i></button>
    </div>
    <div class="md-body">
      <input type="hidden" id="fId" value="0">
      <div class="fg"><label>Title *</label><input type="text" id="fTitle" placeholder="Notice title..."></div>
      <div class="fg"><label>Description *</label><textarea id="fDesc" placeholder="Notice content..."></textarea></div>
      <div class="frow">
        <div class="fg">
          <label>Category</label>
          <select id="fCat">
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
          <select id="fPri">
            <option value="normal">Normal</option>
            <option value="high">High</option>
            <option value="urgent">Urgent</option>
          </select>
        </div>
      </div>
      <div class="frow">
        <div class="fg"><label>Notice Date</label><input type="date" id="fDate"></div>
        <div class="fg">
          <label>Status</label>
          <select id="fStat">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
      </div>
    </div>
    <div class="md-foot">
      <button class="btn-cancel" onclick="closeModal()">Cancel</button>
      <button class="btn-save" onclick="saveNotice()"><i class="fa fa-save"></i> Save</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<footer>© 2025 SCTI - Admin Panel</footer>

<script>
var notices = [];
var curFilter = 'all';

/* ── LOAD ── */
function loadNotices() {
  fetch('notice-list.php?status=all')
    .then(function(r){ return r.text(); })
    .then(function(txt){
      var d;
      try { d = JSON.parse(txt); } catch(e) {
        document.getElementById('nlist').innerHTML = '<div class="empty"><i class="fa fa-exclamation-triangle"></i><p>Parse error: ' + txt.substring(0,100) + '</p></div>';
        return;
      }
      if (d.success) {
        notices = d.notices;
        updateStats();
        renderList();
      } else {
        document.getElementById('nlist').innerHTML = '<div class="empty"><i class="fa fa-exclamation-triangle"></i><p>' + (d.message||'Error') + '</p></div>';
      }
    })
    .catch(function(e){
      document.getElementById('nlist').innerHTML = '<div class="empty"><i class="fa fa-exclamation-triangle"></i><p>Network error: ' + e.message + '</p></div>';
    });
}

function updateStats() {
  document.getElementById('sTotal').textContent    = notices.length;
  document.getElementById('sActive').textContent   = notices.filter(function(n){return n.status==='active';}).length;
  document.getElementById('sUrgent').textContent   = notices.filter(function(n){return n.priority==='urgent';}).length;
  document.getElementById('sInactive').textContent = notices.filter(function(n){return n.status==='inactive';}).length;
}

function setFilter(f, btn) {
  curFilter = f;
  document.querySelectorAll('.fbtn').forEach(function(b){ b.classList.remove('on'); });
  btn.classList.add('on');
  renderList();
}

function renderList() {
  var list = notices.slice();
  if (curFilter === 'active')   list = list.filter(function(n){return n.status==='active';});
  if (curFilter === 'inactive') list = list.filter(function(n){return n.status==='inactive';});
  if (curFilter === 'urgent')   list = list.filter(function(n){return n.priority==='urgent';});

  var el = document.getElementById('nlist');
  if (!list.length) {
    el.innerHTML = '<div class="empty"><i class="fa fa-bullhorn"></i><p>No notices found</p></div>';
    return;
  }
  el.innerHTML = list.map(buildCard).join('');
}

function buildCard(n) {
  var pc  = n.priority==='urgent' ? 'urgent' : n.priority==='high' ? 'high' : '';
  var sc  = n.status==='inactive' ? 'dim' : '';
  var pl  = cap(n.priority);
  var sl  = cap(n.status);
  var cl  = cap(n.category);
  var dt  = fmtDate(n.notice_date);
  var tl  = n.status==='active' ? 'Deactivate' : 'Activate';
  var ti  = n.status==='active' ? 'fa-eye-slash' : 'fa-eye';
  var id  = n.id;
  var st  = n.status;

  return '<div class="nc ' + pc + ' ' + sc + '">'
    + '<div class="nc-head">'
    +   '<div class="nc-title">' + esc(n.title) + '</div>'
    +   '<div class="nc-badges">'
    +     '<span class="bdg bdg-' + n.priority + '">' + pl + '</span>'
    +     '<span class="bdg bdg-' + n.status + '">' + sl + '</span>'
    +     '<span class="bdg bdg-cat">' + cl + '</span>'
    +   '</div>'
    + '</div>'
    + '<div class="nc-meta">'
    +   '<span><i class="fa fa-calendar"></i> ' + dt + '</span>'
    +   '<span><i class="fa fa-tag"></i> ' + cl + '</span>'
    + '</div>'
    + '<div class="nc-body">' + esc(n.description) + '</div>'
    + '<div class="nc-actions">'
    +   '<button class="abtn abtn-edit" onclick="editNotice(' + id + ')"><i class="fa fa-edit"></i> Edit</button>'
    +   '<button class="abtn abtn-tog"  onclick="toggleNotice(' + id + ',\'' + st + '\')"><i class="fa ' + ti + '"></i> ' + tl + '</button>'
    +   '<button class="abtn abtn-del"  onclick="deleteNotice(' + id + ')"><i class="fa fa-trash"></i> Delete</button>'
    + '</div>'
    + '</div>';
}

/* ── MODAL ── */
function openModal(n) {
  document.getElementById('fId').value    = n ? n.id : 0;
  document.getElementById('fTitle').value = n ? n.title : '';
  document.getElementById('fDesc').value  = n ? n.description : '';
  document.getElementById('fCat').value   = n ? n.category : 'general';
  document.getElementById('fPri').value   = n ? n.priority : 'normal';
  document.getElementById('fDate').value  = n ? (n.notice_date||'').split(' ')[0].split('T')[0] : today();
  document.getElementById('fStat').value  = n ? n.status : 'active';
  document.getElementById('mdTitle').innerHTML = n
    ? '<i class="fa fa-edit"></i> Edit Notice'
    : '<i class="fa fa-plus"></i> Create Notice';
  document.getElementById('mo').classList.add('open');
}
function closeModal() { document.getElementById('mo').classList.remove('open'); }
function bgClose(e)   { if (e.target.id === 'mo') closeModal(); }

function editNotice(id) {
  var n = notices.find(function(x){ return String(x.id)===String(id); });
  if (n) openModal(n);
  else toast('Notice not found','err');
}

/* ── SAVE ── */
function saveNotice() {
  var payload = {
    id:          parseInt(document.getElementById('fId').value),
    title:       document.getElementById('fTitle').value.trim(),
    description: document.getElementById('fDesc').value.trim(),
    category:    document.getElementById('fCat').value,
    priority:    document.getElementById('fPri').value,
    notice_date: document.getElementById('fDate').value,
    status:      document.getElementById('fStat').value
  };
  if (!payload.title || !payload.description) { toast('Title and description required','err'); return; }

  fetch('notice-save.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify(payload)
  })
  .then(function(r){ return r.text(); })
  .then(function(txt){
    var d;
    try { d = JSON.parse(txt); } catch(e) { toast('Server error: ' + txt.substring(0,60),'err'); return; }
    if (d.success) { toast(d.message,'ok'); closeModal(); loadNotices(); }
    else toast(d.message||'Save failed','err');
  })
  .catch(function(e){ toast('Network error: ' + e.message,'err'); });
}

/* ── TOGGLE ── */
function toggleNotice(id, curStatus) {
  var newStatus = curStatus === 'active' ? 'inactive' : 'active';
  var n = notices.find(function(x){ return String(x.id)===String(id); });
  if (!n) { toast('Notice not found','err'); return; }
  var payload = {
    id:          parseInt(n.id),
    title:       n.title,
    description: n.description,
    category:    n.category,
    priority:    n.priority,
    notice_date: (n.notice_date||'').split(' ')[0].split('T')[0] || today(),
    status:      newStatus
  };
  fetch('notice-save.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify(payload)
  })
  .then(function(r){ return r.text(); })
  .then(function(txt){
    var d;
    try { d = JSON.parse(txt); } catch(e) { toast('Server error: ' + txt.substring(0,60),'err'); return; }
    if (d.success) { toast('Status changed to ' + newStatus,'ok'); loadNotices(); }
    else toast(d.message||'Update failed','err');
  })
  .catch(function(e){ toast('Network error: ' + e.message,'err'); });
}

/* ── DELETE ── */
function deleteNotice(id) {
  if (!confirm('Delete this notice? This cannot be undone.')) return;
  fetch('notice-delete.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({id: parseInt(id)})
  })
  .then(function(r){ return r.text(); })
  .then(function(txt){
    var d;
    try { d = JSON.parse(txt); } catch(e) { toast('Server error: ' + txt.substring(0,60),'err'); return; }
    if (d.success) { toast('Notice deleted','ok'); loadNotices(); }
    else toast(d.message||'Delete failed','err');
  })
  .catch(function(e){ toast('Network error: ' + e.message,'err'); });
}

/* ── HELPERS ── */
function toast(msg, type) {
  var t = document.getElementById('toast');
  t.textContent = msg;
  t.className = 'toast ' + (type||'ok');
  t.style.display = 'block';
  setTimeout(function(){ t.style.display='none'; }, 3000);
}
function esc(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function cap(s) { return s ? s.charAt(0).toUpperCase()+s.slice(1) : ''; }
function today() { return new Date().toISOString().split('T')[0]; }
function fmtDate(d) {
  try { return new Date(d).toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric'}); }
  catch(e) { return d; }
}

loadNotices();
</script>
</body>
</html>
