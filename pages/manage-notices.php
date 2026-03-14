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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Segoe UI',sans-serif;background:#f5f7fa}
    .container{max-width:1200px;margin:0 auto;padding:20px}
    .page-header{background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:28px 30px;border-radius:12px;margin-bottom:28px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 15px rgba(0,64,128,.2)}
    .page-header h1{margin:0 0 6px;font-size:26px}
    .breadcrumb{background:transparent!important;padding:0;font-size:13px}
    .breadcrumb a{color:rgba(255,255,255,.85);text-decoration:none}
    .btn-add{background:rgba(255,255,255,.2);color:white;padding:11px 22px;border:2px solid white;border-radius:8px;cursor:pointer;font-size:14px;font-weight:600;transition:all .2s;display:inline-flex;align-items:center;gap:8px}
    .btn-add:hover{background:white;color:#004080}
    .stats-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:14px;margin-bottom:24px}
    .stat-box{background:white;border-radius:10px;padding:16px;text-align:center;box-shadow:0 2px 10px rgba(0,0,0,.08);border-top:4px solid #004080}
    .stat-box .num{font-size:26px;font-weight:700;color:#004080}
    .stat-box .lbl{color:#666;font-size:12px;margin-top:4px}
    .filter-bar{background:white;border-radius:10px;padding:14px 20px;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,0,0,.07);display:flex;gap:8px;flex-wrap:wrap}
    .filter-btn{padding:7px 16px;border:2px solid #dde3ed;background:#f8fafc;color:#5a6a80;border-radius:20px;cursor:pointer;font-size:13px;font-weight:600;transition:all .2s}
    .filter-btn.active,.filter-btn:hover{background:#004080;border-color:#004080;color:white}
    .notice-list{display:flex;flex-direction:column;gap:14px}
    .notice-card{background:white;border-radius:12px;padding:22px 24px;box-shadow:0 2px 10px rgba(0,0,0,.08);border-left:5px solid #004080;transition:all .25s}
    .notice-card:hover{transform:translateX(4px);box-shadow:0 5px 20px rgba(0,64,128,.15)}
    .notice-card.urgent{border-left-color:#dc3545}
    .notice-card.high{border-left-color:#fd7e14}
    .notice-card.info{border-left-color:#17a2b8}
    .notice-card.inactive{opacity:.6}
    .notice-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;gap:12px}
    .notice-title{font-size:17px;font-weight:700;color:#222}
    .notice-badges{display:flex;gap:6px;flex-wrap:wrap;flex-shrink:0}
    .badge{padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700}
    .badge-active{background:#d4edda;color:#155724}
    .badge-inactive{background:#e2e3e5;color:#383d41}
    .badge-urgent{background:#f8d7da;color:#721c24}
    .badge-high{background:#fff3cd;color:#856404}
    .badge-normal{background:#cce5ff;color:#004085}
    .badge-cat{background:#e8ecf2;color:#4a5568}
    .notice-meta{display:flex;gap:16px;margin-bottom:10px;flex-wrap:wrap}
    .meta-item{display:flex;align-items:center;gap:5px;color:#666;font-size:12px}
    .meta-item i{color:#004080}
    .notice-body{color:#555;font-size:14px;line-height:1.6;margin-bottom:14px}
    .notice-actions{display:flex;gap:8px}
    .btn-sm{padding:7px 14px;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600;transition:all .2s;display:inline-flex;align-items:center;gap:5px}
    .btn-edit{background:#cce5ff;color:#004085}
    .btn-edit:hover{background:#004080;color:white}
    .btn-delete{background:#f8d7da;color:#dc3545}
    .btn-delete:hover{background:#dc3545;color:white}
    .btn-toggle{background:#d4edda;color:#155724}
    .btn-toggle:hover{background:#28a745;color:white}
    .empty-state{text-align:center;padding:60px 20px;color:#8a9ab5}
    .empty-state i{font-size:56px;margin-bottom:16px;display:block}

    /* MODAL */
    .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:1000;display:none;align-items:center;justify-content:center;backdrop-filter:blur(3px)}
    .modal-overlay.open{display:flex}
    .modal{background:white;border-radius:16px;width:100%;max-width:580px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.3);animation:modalIn .25s ease}
    @keyframes modalIn{from{transform:scale(.9);opacity:0}to{transform:scale(1);opacity:1}}
    .modal-head{background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:20px 24px;border-radius:16px 16px 0 0;display:flex;justify-content:space-between;align-items:center}
    .modal-head h3{margin:0;font-size:18px}
    .modal-close{background:rgba(255,255,255,.2);border:none;color:white;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;transition:all .2s}
    .modal-close:hover{background:#dc3545;transform:rotate(90deg)}
    .modal-body{padding:24px}
    .form-group{margin-bottom:18px}
    .form-group label{display:block;font-size:13px;font-weight:600;color:#444;margin-bottom:6px}
    .form-group input,.form-group textarea,.form-group select{width:100%;padding:10px 14px;border:2px solid #dde3ed;border-radius:8px;font-size:14px;font-family:inherit;transition:border-color .2s;outline:none}
    .form-group input:focus,.form-group textarea:focus,.form-group select:focus{border-color:#004080}
    .form-group textarea{resize:vertical;min-height:100px}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .modal-footer{padding:16px 24px;border-top:1px solid #eee;display:flex;justify-content:flex-end;gap:10px}
    .btn-cancel{padding:10px 20px;border:2px solid #dde3ed;background:white;color:#666;border-radius:8px;cursor:pointer;font-size:14px;font-weight:600;transition:all .2s}
    .btn-cancel:hover{border-color:#999;color:#333}
    .btn-save{padding:10px 24px;background:linear-gradient(135deg,#004080,#0059b3);color:white;border:none;border-radius:8px;cursor:pointer;font-size:14px;font-weight:600;transition:all .2s}
    .btn-save:hover{opacity:.9;transform:translateY(-1px)}
    .toast{position:fixed;bottom:24px;right:24px;background:#004080;color:white;padding:12px 20px;border-radius:10px;font-size:14px;font-weight:600;z-index:9999;display:none;animation:toastIn .3s ease}
    @keyframes toastIn{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}
    .toast.success{background:#28a745}
    .toast.error{background:#dc3545}
  </style>
</head>
<body>
<div class="top-header"><marquee>Manage Notices — SCTI Admin Panel</marquee></div>
<div class="container">

  <div class="page-header">
    <div>
      <h1><i class="fa fa-bullhorn"></i> Manage Notices</h1>
      <div class="breadcrumb">
        <a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Manage Notices
      </div>
    </div>
    <button class="btn-add" onclick="openModal()"><i class="fa fa-plus"></i> Create Notice</button>
  </div>

  <div class="stats-row">
    <div class="stat-box"><div class="num" id="statTotal">0</div><div class="lbl">Total</div></div>
    <div class="stat-box" style="border-top-color:#28a745"><div class="num" id="statActive" style="color:#28a745">0</div><div class="lbl">Active</div></div>
    <div class="stat-box" style="border-top-color:#dc3545"><div class="num" id="statUrgent" style="color:#dc3545">0</div><div class="lbl">Urgent</div></div>
    <div class="stat-box" style="border-top-color:#6c757d"><div class="num" id="statInactive" style="color:#6c757d">0</div><div class="lbl">Inactive</div></div>
  </div>

  <div class="filter-bar">
    <button class="filter-btn active" data-filter="all" onclick="setFilter('all',this)"><i class="fa fa-border-all"></i> All</button>
    <button class="filter-btn" data-filter="active" onclick="setFilter('active',this)"><i class="fa fa-check-circle"></i> Active</button>
    <button class="filter-btn" data-filter="inactive" onclick="setFilter('inactive',this)"><i class="fa fa-archive"></i> Inactive</button>
    <button class="filter-btn" data-filter="urgent" onclick="setFilter('urgent',this)"><i class="fa fa-exclamation-circle"></i> Urgent</button>
  </div>

  <div class="notice-list" id="noticeList">
    <div class="empty-state"><i class="fa fa-spinner fa-spin"></i><p>Loading notices...</p></div>
  </div>
</div>

<!-- MODAL -->
<div class="modal-overlay" id="modalOverlay" onclick="bgClose(event)">
  <div class="modal">
    <div class="modal-head">
      <h3 id="modalTitle"><i class="fa fa-plus"></i> Create Notice</h3>
      <button class="modal-close" onclick="closeModal()"><i class="fa fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="noticeId" value="0">
      <div class="form-group">
        <label>Title *</label>
        <input type="text" id="fTitle" placeholder="Notice title...">
      </div>
      <div class="form-group">
        <label>Description *</label>
        <textarea id="fDesc" placeholder="Notice content..."></textarea>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Category</label>
          <select id="fCategory">
            <option value="general">General</option>
            <option value="admission">Admission</option>
            <option value="exam">Exam</option>
            <option value="event">Event</option>
            <option value="holiday">Holiday</option>
            <option value="urgent">Urgent</option>
          </select>
        </div>
        <div class="form-group">
          <label>Priority</label>
          <select id="fPriority">
            <option value="normal">Normal</option>
            <option value="high">High</option>
            <option value="urgent">Urgent</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Notice Date</label>
          <input type="date" id="fDate">
        </div>
        <div class="form-group">
          <label>Status</label>
          <select id="fStatus">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel" onclick="closeModal()">Cancel</button>
      <button class="btn-save" onclick="saveNotice()"><i class="fa fa-save"></i> Save Notice</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<footer class="footer" style="margin-top:30px"><p>© 2025 SCTI - Admin Panel</p></footer>

<script>
let allNotices = [];
let currentFilter = 'all';

// ── LOAD ──────────────────────────────────────
async function loadNotices() {
  try {
    const res  = await fetch('notice-list.php?status=all');
    const data = await res.json();
    if (data.success) {
      allNotices = data.notices;
      updateStats();
      renderNotices();
    }
  } catch(e) {
    document.getElementById('noticeList').innerHTML = '<div class="empty-state"><i class="fa fa-exclamation-triangle"></i><p>Failed to load notices</p></div>';
  }
}

function updateStats() {
  document.getElementById('statTotal').textContent   = allNotices.length;
  document.getElementById('statActive').textContent  = allNotices.filter(n => n.status === 'active').length;
  document.getElementById('statUrgent').textContent  = allNotices.filter(n => n.priority === 'urgent').length;
  document.getElementById('statInactive').textContent= allNotices.filter(n => n.status === 'inactive').length;
}

function setFilter(f, btn) {
  currentFilter = f;
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  renderNotices();
}

function renderNotices() {
  let list = allNotices;
  if (currentFilter === 'active')   list = list.filter(n => n.status === 'active');
  if (currentFilter === 'inactive') list = list.filter(n => n.status === 'inactive');
  if (currentFilter === 'urgent')   list = list.filter(n => n.priority === 'urgent');

  const el = document.getElementById('noticeList');
  if (!list.length) {
    el.innerHTML = '<div class="empty-state"><i class="fa fa-bullhorn"></i><p>No notices found</p></div>';
    return;
  }
  el.innerHTML = list.map(n => buildCard(n)).join('');
}

function buildCard(n) {
  const priClass = n.priority === 'urgent' ? 'urgent' : n.priority === 'high' ? 'high' : '';
  const stClass  = n.status === 'inactive' ? 'inactive' : '';
  const priLabel = n.priority.charAt(0).toUpperCase() + n.priority.slice(1);
  const stLabel  = n.status.charAt(0).toUpperCase() + n.status.slice(1);
  const catLabel = n.category.charAt(0).toUpperCase() + n.category.slice(1);
  const date     = new Date(n.notice_date).toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric'});
  const toggleLbl = n.status === 'active' ? 'Deactivate' : 'Activate';
  const toggleIcon= n.status === 'active' ? 'fa-eye-slash' : 'fa-eye';

  return `
    <div class="notice-card ${priClass} ${stClass}" id="nc-${n.id}">
      <div class="notice-header">
        <div class="notice-title">${escHtml(n.title)}</div>
        <div class="notice-badges">
          <span class="badge badge-${n.priority}">${priLabel}</span>
          <span class="badge badge-${n.status}">${stLabel}</span>
          <span class="badge badge-cat">${catLabel}</span>
        </div>
      </div>
      <div class="notice-meta">
        <span class="meta-item"><i class="fa fa-calendar"></i> ${date}</span>
        <span class="meta-item"><i class="fa fa-tag"></i> ${catLabel}</span>
      </div>
      <div class="notice-body">${escHtml(n.description)}</div>
      <div class="notice-actions">
        <button class="btn-sm btn-edit" onclick="editNotice(${n.id})"><i class="fa fa-edit"></i> Edit</button>
        <button class="btn-sm btn-toggle" onclick="toggleNotice(${n.id},'${n.status}')"><i class="fa ${toggleIcon}"></i> ${toggleLbl}</button>
        <button class="btn-sm btn-delete" onclick="deleteNotice(${n.id})"><i class="fa fa-trash"></i> Delete</button>
      </div>
    </div>`;
}

// ── MODAL ─────────────────────────────────────
function openModal(n = null) {
  document.getElementById('noticeId').value = n ? n.id : 0;
  document.getElementById('fTitle').value    = n ? n.title : '';
  document.getElementById('fDesc').value     = n ? n.description : '';
  document.getElementById('fCategory').value = n ? n.category : 'general';
  document.getElementById('fPriority').value = n ? n.priority : 'normal';
  document.getElementById('fDate').value     = n ? n.notice_date : new Date().toISOString().split('T')[0];
  document.getElementById('fStatus').value   = n ? n.status : 'active';
  document.getElementById('modalTitle').innerHTML = n
    ? '<i class="fa fa-edit"></i> Edit Notice'
    : '<i class="fa fa-plus"></i> Create Notice';
  document.getElementById('modalOverlay').classList.add('open');
}

function closeModal() { document.getElementById('modalOverlay').classList.remove('open'); }
function bgClose(e)   { if (e.target.id === 'modalOverlay') closeModal(); }

function editNotice(id) {
  const n = allNotices.find(x => x.id == id);
  if (n) openModal(n);
}

// ── SAVE ──────────────────────────────────────
async function saveNotice() {
  const payload = {
    id:          parseInt(document.getElementById('noticeId').value),
    title:       document.getElementById('fTitle').value.trim(),
    description: document.getElementById('fDesc').value.trim(),
    category:    document.getElementById('fCategory').value,
    priority:    document.getElementById('fPriority').value,
    notice_date: document.getElementById('fDate').value,
    status:      document.getElementById('fStatus').value,
  };
  if (!payload.title || !payload.description) { showToast('Title and description required','error'); return; }

  try {
    const res  = await fetch('notice-save.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload) });
    const data = await res.json();
    if (data.success) { showToast(data.message,'success'); closeModal(); loadNotices(); }
    else showToast(data.message,'error');
  } catch(e) { showToast('Save failed','error'); }
}

// ── TOGGLE ────────────────────────────────────
async function toggleNotice(id, currentStatus) {
  const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
  const n = allNotices.find(x => x.id == id);
  if (!n) return;
  const payload = {
    id:          parseInt(n.id),
    title:       n.title,
    description: n.description,
    category:    n.category,
    priority:    n.priority,
    notice_date: n.notice_date,
    status:      newStatus
  };
  try {
    const res  = await fetch('notice-save.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload) });
    const data = await res.json();
    if (data.success) { showToast('Status updated to ' + newStatus,'success'); loadNotices(); }
    else showToast(data.message || 'Update failed','error');
  } catch(e) { showToast('Update failed: ' + e.message,'error'); }
}

// ── DELETE ────────────────────────────────────
async function deleteNotice(id) {
  if (!confirm('Delete this notice? This cannot be undone.')) return;
  try {
    const res  = await fetch('notice-delete.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({id: parseInt(id)}) });
    const data = await res.json();
    if (data.success) { showToast('Notice deleted','success'); loadNotices(); }
    else showToast(data.message || 'Delete failed','error');
  } catch(e) { showToast('Delete failed: ' + e.message,'error'); }
}

// ── HELPERS ───────────────────────────────────
function showToast(msg, type='success') {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className   = `toast ${type}`;
  t.style.display = 'block';
  setTimeout(() => { t.style.display = 'none'; }, 3000);
}

function escHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

loadNotices();
</script>
</body>
</html>
