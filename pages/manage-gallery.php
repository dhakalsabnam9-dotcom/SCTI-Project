<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php'); exit();
}
require_once '../includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Gallery Manager | SCTI Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box;}
    body{font-family:'Segoe UI',sans-serif;background:#f0f4f8;min-height:100vh;}
    .pg-header{background:linear-gradient(135deg,#17a2b8,#0d6efd);color:#fff;padding:20px 28px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 18px rgba(13,110,253,.25);}
    .pg-header h1{font-size:22px;font-weight:700;display:flex;align-items:center;gap:10px;margin:0 0 3px;}
    .pg-header .bc{font-size:12px;color:rgba(255,255,255,.75);}
    .pg-header .bc a{color:#fff;text-decoration:none;}
    .hdr-btns{display:flex;gap:8px;}
    .btn-hdr{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;border:none;transition:.2s;text-decoration:none;}
    .btn-hdr.white{background:#fff;color:#17a2b8;}
    .btn-hdr.white:hover{background:#e0f7fa;}
    .btn-hdr.ghost{background:rgba(255,255,255,.18);color:#fff;}
    .btn-hdr.ghost:hover{background:rgba(255,255,255,.32);}
    .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;padding:18px 28px;background:#fff;border-bottom:1px solid #e9ecef;}
    .stat{display:flex;align-items:center;gap:12px;background:#f8f9fa;border-radius:10px;padding:12px 16px;}
    .stat-ico{width:42px;height:42px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:17px;color:#fff;flex-shrink:0;}
    .ico-teal{background:linear-gradient(135deg,#17a2b8,#138496);}
    .ico-blue{background:linear-gradient(135deg,#0d6efd,#0a58ca);}
    .ico-green{background:linear-gradient(135deg,#28a745,#20c997);}
    .ico-orange{background:linear-gradient(135deg,#fd7e14,#ffc107);}
    .stat-val{font-size:24px;font-weight:700;color:#222;line-height:1;}
    .stat-lbl{font-size:11px;color:#999;margin-top:2px;}
    .layout{display:grid;grid-template-columns:360px 1fr;min-height:calc(100vh - 170px);}
    .panel{background:#fff;border-right:1px solid #e9ecef;padding:22px;overflow-y:auto;}
    .panel h3{font-size:15px;color:#17a2b8;margin-bottom:16px;padding-bottom:10px;border-bottom:2px solid #e0f7fa;display:flex;align-items:center;gap:8px;}
    .content{padding:22px;overflow-y:auto;}
    .dz{border:2.5px dashed #17a2b8;border-radius:12px;padding:28px 16px;text-align:center;background:#f8fdfe;cursor:pointer;transition:.25s;margin-bottom:14px;}
    .dz:hover,.dz.over{background:#d1ecf1;border-color:#0c5460;}
    .dz i{font-size:38px;color:#17a2b8;display:block;margin-bottom:8px;transition:.25s;}
    .dz:hover i{transform:translateY(-4px);}
    .dz p{color:#888;font-size:12px;margin:3px 0;}
    .dz strong{color:#333;font-size:13px;}
    #fileInput{display:none;}
    .preview-card{display:none;border:2px solid #17a2b8;border-radius:10px;overflow:hidden;margin-bottom:14px;position:relative;background:#fff;}
    .preview-card .pc-inner{display:flex;align-items:stretch;}
    .preview-card img{width:100px;height:80px;object-fit:cover;flex-shrink:0;}
    .pc-info{padding:10px 12px;flex:1;display:flex;flex-direction:column;justify-content:center;gap:3px;}
    .pc-name{font-weight:700;font-size:13px;color:#333;word-break:break-all;}
    .pc-meta{font-size:11px;color:#888;}
    .pc-type{font-size:11px;color:#17a2b8;font-weight:600;}
    .btn-rm{position:absolute;top:6px;right:6px;width:22px;height:22px;border-radius:50%;background:#dc3545;color:#fff;border:none;cursor:pointer;font-size:11px;display:flex;align-items:center;justify-content:center;}
    .btn-rm:hover{background:#b02a37;}
    .fg{margin-bottom:13px;}
    .fg label{display:block;font-size:12px;font-weight:600;color:#555;margin-bottom:5px;}
    .fc{width:100%;padding:9px 12px;border:2px solid #dee2e6;border-radius:8px;font-size:13px;font-family:inherit;transition:.2s;}
    .fc:focus{outline:none;border-color:#17a2b8;box-shadow:0 0 0 3px rgba(23,162,184,.1);}
    textarea.fc{resize:vertical;min-height:65px;}
    .cat-row{display:flex;gap:8px;}
    .cat-row select{flex:1;}
    .btn-sm-cat{background:#e0f7fa;color:#17a2b8;border:2px solid #17a2b8;padding:0 12px;border-radius:8px;cursor:pointer;font-size:12px;font-weight:600;white-space:nowrap;transition:.2s;}
    .btn-sm-cat:hover{background:#17a2b8;color:#fff;}
    .btn-upload{width:100%;padding:12px;background:linear-gradient(135deg,#17a2b8,#0d6efd);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:.25s;margin-top:4px;}
    .btn-upload:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(23,162,184,.4);}
    .btn-upload:disabled{opacity:.55;cursor:not-allowed;transform:none;}
    .alert{padding:10px 13px;border-radius:8px;font-size:13px;margin-bottom:12px;display:none;}
    .alert-ok{background:#d4edda;color:#155724;border:1px solid #c3e6cb;}
    .alert-err{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;}
    .toolbar{display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap;align-items:center;}
    .toolbar select,.toolbar input{padding:9px 13px;border:2px solid #dee2e6;border-radius:8px;font-size:13px;font-family:inherit;}
    .toolbar input{flex:1;min-width:160px;}
    .toolbar select:focus,.toolbar input:focus{outline:none;border-color:#17a2b8;}
    .btn-refresh{padding:9px 16px;background:linear-gradient(135deg,#17a2b8,#138496);color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;transition:.2s;}
    .btn-refresh:hover{transform:translateY(-1px);}
    .ggrid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;}
    .gcard{background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.07);transition:.25s;}
    .gcard:hover{transform:translateY(-5px);box-shadow:0 10px 28px rgba(23,162,184,.18);}
    .gimg{position:relative;height:160px;overflow:hidden;background:#e9ecef;}
    .gimg img{width:100%;height:100%;object-fit:cover;transition:transform .35s;}
    .gcard:hover .gimg img{transform:scale(1.07);}
    .gcat{position:absolute;top:8px;left:8px;background:rgba(23,162,184,.9);color:#fff;padding:2px 9px;border-radius:20px;font-size:10px;font-weight:700;}
    .gview{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%) scale(0);background:#fff;color:#17a2b8;border:none;padding:7px 14px;border-radius:20px;font-size:12px;font-weight:700;cursor:pointer;transition:.25s;box-shadow:0 4px 14px rgba(0,0,0,.2);}
    .gcard:hover .gview{transform:translate(-50%,-50%) scale(1);}
    .govl{position:absolute;inset:0;background:linear-gradient(to bottom,transparent 40%,rgba(0,0,0,.7));opacity:0;transition:.25s;display:flex;align-items:flex-end;padding:10px;}
    .gcard:hover .govl{opacity:1;}
    .govl span{color:#fff;font-size:12px;font-weight:600;}
    .gbody{padding:12px;}
    .gtitle{font-weight:700;color:#222;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:2px;}
    .gdesc{color:#aaa;font-size:11px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:7px;}
    .gmeta{font-size:10px;color:#ccc;margin-bottom:9px;display:flex;align-items:center;gap:4px;}
    .gactions{display:flex;gap:7px;}
    .gbtn{flex:1;padding:6px;border:none;border-radius:7px;cursor:pointer;font-size:11px;font-weight:600;transition:.2s;display:flex;align-items:center;justify-content:center;gap:4px;}
    .gbtn-edit{background:#fff3cd;color:#856404;}
    .gbtn-edit:hover{background:#ffc107;color:#fff;}
    .gbtn-del{background:#f8d7da;color:#721c24;}
    .gbtn-del:hover{background:#dc3545;color:#fff;}
    .empty{text-align:center;padding:60px 20px;color:#ccc;grid-column:1/-1;}
    .empty i{font-size:56px;display:block;margin-bottom:12px;}
    .modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;}
    .modal.on{display:flex;}
    .mbox{background:#fff;padding:26px;border-radius:14px;width:90%;max-width:460px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 50px rgba(0,0,0,.2);}
    .mhead{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;padding-bottom:12px;border-bottom:2px solid #f0f0f0;}
    .mhead h3{margin:0;color:#17a2b8;font-size:16px;display:flex;align-items:center;gap:8px;}
    .mclose{background:none;border:none;font-size:22px;cursor:pointer;color:#bbb;line-height:1;}
    .mclose:hover{color:#333;}
    .cat-ul{list-style:none;padding:0;margin:0 0 12px;max-height:200px;overflow-y:auto;}
    .cat-ul li{display:flex;justify-content:space-between;align-items:center;padding:9px 10px;border-bottom:1px solid #f5f5f5;font-size:13px;border-radius:6px;cursor:pointer;transition:.15s;}
    .cat-ul li:hover{background:#e0f7fa;color:#17a2b8;}
    .btn-delcat{background:#f8d7da;color:#dc3545;border:none;width:26px;height:26px;border-radius:6px;cursor:pointer;font-size:11px;}
    .btn-delcat:hover{background:#dc3545;color:#fff;}
    .addcat{display:flex;gap:8px;}
    .addcat input{flex:1;padding:9px 11px;border:2px solid #dee2e6;border-radius:8px;font-size:13px;font-family:inherit;}
    .addcat input:focus{outline:none;border-color:#17a2b8;}
    .addcat button{padding:9px 14px;background:#17a2b8;color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;}
    .addcat button:hover{background:#138496;}
    .btn-save{width:100%;padding:11px;background:linear-gradient(135deg,#17a2b8,#0d6efd);color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:.2s;}
    .btn-save:hover{transform:translateY(-1px);box-shadow:0 5px 14px rgba(23,162,184,.3);}
    .btn-cancel{width:100%;padding:11px;background:#f0f0f0;color:#555;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;margin-top:8px;}
    .btn-cancel:hover{background:#e0e0e0;}
    @media(max-width:860px){.layout{grid-template-columns:1fr;}.stats{grid-template-columns:repeat(2,1fr);}}
  </style>
</head>
<body>
<div class="top-header"><marquee>Gallery Manager � Upload and manage images for the SCTI website</marquee></div>
<div class="pg-header">
  <div>
    <h1><i class="fa fa-images"></i> Gallery Manager</h1>
    <div class="bc"><a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Gallery</div>
  </div>
  <div class="hdr-btns">
    <button class="btn-hdr white" onclick="openCatModal()"><i class="fa fa-tags"></i> Categories</button>
    <a href="../dashboards/admin-dashboard.php" class="btn-hdr ghost"><i class="fa fa-arrow-left"></i> Back</a>
  </div>
</div>
<div class="stats">
  <div class="stat"><div class="stat-ico ico-teal"><i class="fa fa-images"></i></div><div><div class="stat-val" id="sTotal">�</div><div class="stat-lbl">Total Images</div></div></div>
  <div class="stat"><div class="stat-ico ico-blue"><i class="fa fa-tags"></i></div><div><div class="stat-val" id="sCats">�</div><div class="stat-lbl">Categories</div></div></div>
  <div class="stat"><div class="stat-ico ico-green"><i class="fa fa-calendar"></i></div><div><div class="stat-val" id="sToday">�</div><div class="stat-lbl">Added Today</div></div></div>
  <div class="stat"><div class="stat-ico ico-orange"><i class="fa fa-filter"></i></div><div><div class="stat-val" id="sShowing">�</div><div class="stat-lbl">Showing</div></div></div>
</div>
<div class="layout">
  <div class="panel">
    <h3><i class="fa fa-cloud-upload-alt"></i> Upload Media</h3>
    <div id="upAlert" class="alert"></div>
    <form id="upForm" enctype="multipart/form-data">
      <div class="dz" id="dz">
        <input type="file" id="fileInput" name="image" accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.ppt,.pptx,.zip,.rar,.txt" required>
        <i class="fa fa-cloud-upload-alt"></i>
        <p><strong>Drag &amp; drop</strong> or click to browse</p>
        <p>JPG, PNG, GIF, WEBP � max 10MB</p>
      </div>
      <div class="preview-card" id="previewCard">
        <div class="pc-inner">
          <img id="previewImg" src="" alt="preview">
          <div class="pc-info">
            <div class="pc-name" id="previewName"></div>
            <div class="pc-meta" id="previewSize"></div>
            <div class="pc-type" id="previewType"></div>
          </div>
        </div>
        <button type="button" class="btn-rm" id="btnRm"><i class="fa fa-times"></i></button>
      </div>
      <div class="fg"><label>Image Title *</label><input type="text" name="title" class="fc" placeholder="Enter image title" required></div>
      <div class="fg"><label>Category</label>
        <div class="cat-row">
          <select name="category" id="upCat" class="fc"><option value="">� None �</option></select>
          <button type="button" class="btn-sm-cat" onclick="openCatModal()"><i class="fa fa-cog"></i> Manage</button>
        </div>
      </div>
      <div class="fg"><label>Description</label><textarea name="description" class="fc" placeholder="Optional..."></textarea></div>
      <button type="submit" class="btn-upload" id="upBtn"><i class="fa fa-upload"></i> Upload File</button>
    </form>
  </div>
  <div class="content">
    <div class="toolbar">
      <select id="filterCat"><option value="">All Categories</option></select>
      <input type="text" id="searchInput" placeholder="Search by title...">
      <button class="btn-refresh" onclick="loadGallery()"><i class="fa fa-sync"></i> Refresh</button>
    </div>
    <div class="ggrid" id="ggrid"><div class="empty"><i class="fa fa-spinner fa-spin"></i><p>Loading...</p></div></div>
  </div>
</div>

<!-- Category Modal -->
<div class="modal" id="catModal">
  <div class="mbox">
    <div class="mhead"><h3><i class="fa fa-tags"></i> Manage Categories</h3><button class="mclose" onclick="closeMod('catModal')">�</button></div>
    <ul class="cat-ul" id="catList"><li style="color:#aaa;text-align:center;padding:20px">Loading...</li></ul>
    <div class="addcat">
      <input type="text" id="newCat" placeholder="New category name..." maxlength="100">
      <button onclick="addCat()"><i class="fa fa-plus"></i> Add</button>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal" id="editModal">
  <div class="mbox">
    <div class="mhead"><h3><i class="fa fa-edit"></i> Edit Image</h3><button class="mclose" onclick="closeMod('editModal')">�</button></div>
    <div id="editAlert" class="alert"></div>
    <form id="editForm">
      <input type="hidden" id="editId" name="id">
      <div class="fg"><label>Title *</label><input type="text" id="editTitle" name="title" class="fc" required></div>
      <div class="fg"><label>Category</label><select id="editCat" name="category" class="fc"><option value="">� None �</option></select></div>
      <div class="fg"><label>Description</label><textarea id="editDesc" name="description" class="fc"></textarea></div>
      <button type="submit" class="btn-save"><i class="fa fa-save"></i> Save Changes</button>
      <button type="button" class="btn-cancel" onclick="closeMod('editModal')">Cancel</button>
    </form>
  </div>
</div>

<footer class="footer" style="margin-top:0"><p>� 2025 SCTI � Admin Panel</p></footer>

<script>
let cats = [];

function openMod(id)  { document.getElementById(id).classList.add('on'); }
function closeMod(id) { document.getElementById(id).classList.remove('on'); }

// -- Categories ----------------------------------------------
async function loadCats() {
  try {
    const r = await fetch('gallery-categories.php');
    const d = await r.json();
    if (!d.success) return;
    cats = d.categories;
    fillCatSelects();
    renderCatList();
    document.getElementById('sCats').textContent = cats.length;
  } catch(e) {}
}

function fillCatSelects() {
  const opts = cats.map(c => `<option value="${c.name}">${c.name}</option>`).join('');
  document.getElementById('upCat').innerHTML    = '<option value="">� None �</option>' + opts;
  document.getElementById('filterCat').innerHTML = '<option value="">All Categories</option>' + opts;
  document.getElementById('editCat').innerHTML  = '<option value="">� None �</option>' + opts;
}

function renderCatList() {
  const ul = document.getElementById('catList');
  if (!cats.length) { ul.innerHTML = '<li style="color:#aaa;text-align:center;padding:20px">No categories yet</li>'; return; }
  ul.innerHTML = cats.map(c => `
    <li style="cursor:pointer;" onclick="selectCat('${c.name.replace(/'/g,"\\'")}')">
      <span><i class="fa fa-tag" style="color:#17a2b8;margin-right:8px"></i>${c.name}</span>
      <button class="btn-delcat" onclick="event.stopPropagation();delCat(${c.id},'${c.name.replace(/'/g,"\\'")}')"><i class="fa fa-trash"></i></button>
    </li>`).join('');
}

function selectCat(name) {
  // Set category in upload form
  const upCat = document.getElementById('upCat');
  for (let i = 0; i < upCat.options.length; i++) {
    if (upCat.options[i].value === name) { upCat.selectedIndex = i; break; }
  }
  // Visual feedback
  document.querySelectorAll('#catList li').forEach(li => li.style.background = '');
  event.currentTarget.style.background = '#e0f7fa';
  // Close modal after short delay
  setTimeout(() => closeMod('catModal'), 300);
}

async function addCat() {
  const inp = document.getElementById('newCat');
  const name = inp.value.trim();
  if (!name) return;
  const fd = new FormData(); fd.append('action','add'); fd.append('name',name);
  const r = await fetch('gallery-categories.php',{method:'POST',body:fd});
  const d = await r.json();
  if (d.success) { inp.value=''; loadCats(); } else alert(d.message||'Failed');
}

async function delCat(id, name) {
  if (!confirm(`Delete "${name}"?`)) return;
  const fd = new FormData(); fd.append('action','delete'); fd.append('id',id);
  const r = await fetch('gallery-categories.php',{method:'POST',body:fd});
  const d = await r.json();
  if (d.success) loadCats();
}

function openCatModal() { loadCats(); openMod('catModal'); }

// -- File picker ---------------------------------------------
const dz = document.getElementById('dz');
const fi = document.getElementById('fileInput');

dz.addEventListener('click', () => fi.click());
dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('over'); });
dz.addEventListener('dragleave', () => dz.classList.remove('over'));
dz.addEventListener('drop', e => {
  e.preventDefault(); dz.classList.remove('over');
  if (e.dataTransfer.files[0]) { fi.files = e.dataTransfer.files; showPreview(e.dataTransfer.files[0]); }
});
fi.addEventListener('change', e => { if (e.target.files[0]) showPreview(e.target.files[0]); });

function showPreview(file) {
  const reader = new FileReader();
  reader.onload = ev => {
    document.getElementById('previewImg').src  = ev.target.result;
    document.getElementById('previewName').textContent = file.name;
    document.getElementById('previewSize').textContent = fmtBytes(file.size);
    document.getElementById('previewType').textContent = file.type || 'image';
    document.getElementById('previewCard').style.display = 'block';
    dz.style.display = 'none';
  };
  reader.readAsDataURL(file);
}

function clearFile() {
  fi.value = '';
  document.getElementById('previewCard').style.display = 'none';
  dz.style.display = 'block';
}

document.getElementById('btnRm').addEventListener('click', e => { e.stopPropagation(); clearFile(); });

function fmtBytes(b) {
  if (b < 1024) return b + ' B';
  if (b < 1048576) return (b/1024).toFixed(1) + ' KB';
  return (b/1048576).toFixed(1) + ' MB';
}

// -- Upload --------------------------------------------------
document.getElementById('upForm').addEventListener('submit', async e => {
  e.preventDefault();
  const al = document.getElementById('upAlert');
  const btn = document.getElementById('upBtn');
  btn.disabled = true; btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Uploading...';
  try {
    const r = await fetch('gallery-upload.php', {method:'POST', body: new FormData(e.target)});
    const text = await r.text();
    let d;
    try { d = JSON.parse(text); } catch(pe) { throw new Error('Server error: ' + text.substring(0,200)); }
    if (d.success) {
      showAlert(al, 'Image uploaded successfully!', 'ok');
      e.target.reset(); clearFile(); loadGallery();
    } else {
      showAlert(al, d.message || 'Upload failed', 'err');
    }
  } catch(err) { showAlert(al, err.message, 'err'); }
  btn.disabled = false; btn.innerHTML = '<i class="fa fa-upload"></i> Upload File';
});

// -- Gallery -------------------------------------------------
async function loadGallery() {
  const cat    = document.getElementById('filterCat').value;
  const search = document.getElementById('searchInput').value;
  const grid   = document.getElementById('ggrid');
  grid.innerHTML = '<div class="empty"><i class="fa fa-spinner fa-spin"></i><p>Loading...</p></div>';
  try {
    const r = await fetch(`gallery-list.php?category=${encodeURIComponent(cat)}&search=${encodeURIComponent(search)}`);
    const text = await r.text();
    let d;
    try { d = JSON.parse(text); } catch(pe) { throw new Error('Server error: ' + text.substring(0,200)); }
    if (!d.success) { grid.innerHTML = `<div class="empty"><i class="fa fa-exclamation-circle"></i><p>${d.message}</p></div>`; return; }
    document.getElementById('sTotal').textContent   = d.count;
    document.getElementById('sShowing').textContent = d.images.length;
    if (!d.images.length) { grid.innerHTML = '<div class="empty"><i class="fa fa-images"></i><p>No images found</p></div>'; return; }
    const today = new Date().toDateString();
    let todayCnt = 0;
    grid.innerHTML = d.images.map(img => {
      const dt = new Date(img.created_at);
      if (dt.toDateString() === today) todayCnt++;
      const date  = dt.toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'});
      const thumb = img.thumbnail_path || img.file_path;
      return `<div class="gcard">
        <div class="gimg">
          <img src="../${thumb}" alt="${img.title}" loading="lazy" onerror="this.src='../assets/images/img1.jpg'">
          ${img.category ? `<span class="gcat">${img.category}</span>` : ''}
          <button class="gview" onclick="quickView('../${img.file_path}','${img.title.replace(/'/g,"\\'")}')"><i class="fa fa-eye"></i> View</button>
          <div class="govl"><span>${img.title}</span></div>
        </div>
        <div class="gbody">
          <div class="gtitle">${img.title}</div>
          <div class="gdesc">${img.description || '<em style="color:#ddd">No description</em>'}</div>
          <div class="gmeta"><i class="fa fa-calendar" style="color:#17a2b8"></i> ${date}</div>
          <div class="gactions">
            <button class="gbtn gbtn-edit" onclick="editImg(${img.id})"><i class="fa fa-edit"></i> Edit</button>
            <button class="gbtn gbtn-del"  onclick="delImg(${img.id})"><i class="fa fa-trash"></i> Delete</button>
          </div>
        </div>
      </div>`;
    }).join('');
    document.getElementById('sToday').textContent = todayCnt;
  } catch(err) {
    grid.innerHTML = `<div class="empty"><i class="fa fa-exclamation-circle"></i><p style="color:#dc3545">${err.message}</p></div>`;
  }
}

// -- Quick view ----------------------------------------------
function quickView(path, title) {
  const m = document.createElement('div');
  m.className = 'modal on'; m.style.zIndex = '2000';
  m.innerHTML = `<div class="mbox" style="max-width:90%;padding:0;overflow:hidden;">
    <div style="position:relative;">
      <button onclick="this.closest('.modal').remove()" style="position:absolute;top:10px;right:10px;background:rgba(0,0,0,.6);color:#fff;border:none;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:16px;z-index:10">�</button>
      <img src="${path}" alt="${title}" style="width:100%;max-height:80vh;object-fit:contain;display:block">
      <div style="padding:12px;background:#fff"><strong style="color:#17a2b8">${title}</strong></div>
    </div>
  </div>`;
  document.body.appendChild(m);
  m.addEventListener('click', e => { if (e.target === m) m.remove(); });
}

// -- Edit ----------------------------------------------------
async function editImg(id) {
  try {
    const r = await fetch(`gallery-get.php?id=${id}`);
    const d = await r.json();
    if (!d.success) { alert(d.message); return; }
    const img = d.image;
    document.getElementById('editId').value    = img.id;
    document.getElementById('editTitle').value = img.title;
    document.getElementById('editDesc').value  = img.description || '';
    document.getElementById('editCat').innerHTML = '<option value="">� None �</option>' +
      cats.map(c => `<option value="${c.name}" ${c.name===img.category?'selected':''}>${c.name}</option>`).join('');
    openMod('editModal');
  } catch(err) { alert('Error: ' + err.message); }
}

document.getElementById('editForm').addEventListener('submit', async e => {
  e.preventDefault();
  const al  = document.getElementById('editAlert');
  const btn = e.target.querySelector('button[type=submit]');
  btn.disabled = true; btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
  try {
    const r = await fetch('gallery-update.php', {method:'POST', body: new FormData(e.target)});
    const text = await r.text();
    let d;
    try { d = JSON.parse(text); } catch(pe) { throw new Error('Server error: ' + text.substring(0,200)); }
    if (d.success) {
      showAlert(al, 'Saved successfully!', 'ok');
      setTimeout(() => { closeMod('editModal'); loadGallery(); }, 800);
    } else {
      showAlert(al, d.message || 'Update failed', 'err');
    }
  } catch(err) { showAlert(al, err.message, 'err'); }
  btn.disabled = false; btn.innerHTML = '<i class="fa fa-save"></i> Save Changes';
});

// -- Delete --------------------------------------------------
async function delImg(id) {
  if (!confirm('Delete this image?')) return;
  const r = await fetch('gallery-delete.php', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({id})});
  const d = await r.json();
  if (d.success) loadGallery();
  else alert(d.message || 'Delete failed');
}

// -- Helpers -------------------------------------------------
function showAlert(el, msg, type) {
  el.className = 'alert alert-' + type;
  el.textContent = msg;
  el.style.display = 'block';
  setTimeout(() => el.style.display = 'none', 4000);
}

function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(()=>fn(...a), ms); }; }

document.getElementById('filterCat').addEventListener('change', loadGallery);
document.getElementById('searchInput').addEventListener('input', debounce(loadGallery, 400));
document.getElementById('newCat').addEventListener('keydown', e => { if (e.key==='Enter') { e.preventDefault(); addCat(); } });

loadCats();
loadGallery();
</script>
</body>
</html>
