<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php'); exit();
}
require_once '../includes/config.php';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Gallery | SCTI Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; }
    .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
    .page-header {
      background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
      color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;
      display: flex; justify-content: space-between; align-items: center;
      box-shadow: 0 4px 15px rgba(23,162,184,0.2);
    }
    .page-header h1 { margin: 0 0 6px 0; font-size: 28px; }
    .breadcrumb { background: transparent !important; padding: 0; font-size: 14px; }
    .breadcrumb a { color: white; text-decoration: none; }
    .back-btn {
      background: rgba(255,255,255,0.2); color: white; padding: 10px 20px;
      border-radius: 5px; text-decoration: none; transition: all 0.3s;
    }
    .back-btn:hover { background: rgba(255,255,255,0.3); }
    .card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 25px; }
    .card h2 { margin: 0 0 20px 0; color: #17a2b8; font-size: 20px; }
    .form-group { margin-bottom: 18px; }
    .form-group label { display: block; margin-bottom: 6px; color: #333; font-weight: 600; font-size: 14px; }
    .form-control {
      width: 100%; padding: 11px 14px; border: 2px solid #dee2e6;
      border-radius: 6px; font-size: 14px; transition: border-color 0.2s;
    }
    .form-control:focus { outline: none; border-color: #17a2b8; }
    textarea.form-control { resize: vertical; min-height: 80px; }

    /* Category select row */
    .cat-select-row { display: flex; gap: 8px; align-items: center; }
    .cat-select-row select { flex: 1; }
    .btn-icon-sm {
      width: 40px; height: 40px; border: none; border-radius: 6px;
      cursor: pointer; font-size: 16px; display: flex; align-items: center;
      justify-content: center; transition: all 0.2s; flex-shrink: 0;
    }
    .btn-add-cat { background: #17a2b8; color: white; }
    .btn-add-cat:hover { background: #138496; }

    /* Upload area */
    .upload-area {
      border: 3px dashed #17a2b8; border-radius: 12px; padding: 40px 20px;
      text-align: center; background: #f8f9fa; cursor: pointer; transition: all 0.3s;
      position: relative;
    }
    .upload-area:hover { background: #e8f7fa; border-color: #138496; }
    .upload-area.dragover { background: #d1ecf1; border-color: #0c5460; transform: scale(1.01); }
    .upload-area.has-file { border-style: solid; border-color: #17a2b8; background: #e8f7fa; padding: 20px; }
    .upload-area .upload-icon { font-size: 48px; color: #17a2b8; margin-bottom: 12px; display: block; transition: all 0.3s; }
    .upload-area:hover .upload-icon { transform: translateY(-4px); }
    .upload-area p { color: #666; margin: 6px 0; font-size: 14px; }
    #fileInput { display: none; }

    /* File preview card */
    .file-preview-card {
      display: none; margin-top: 16px;
      background: white; border-radius: 10px;
      border: 2px solid #17a2b8;
      overflow: hidden; box-shadow: 0 4px 15px rgba(23,162,184,0.15);
      position: relative;
    }
    .file-preview-card.visible { display: flex; align-items: stretch; }
    .preview-img-wrap {
      width: 120px; min-height: 100px; flex-shrink: 0;
      background: #e9ecef; overflow: hidden;
    }
    .preview-img-wrap img {
      width: 100%; height: 100%; object-fit: cover; display: block;
    }
    .preview-info {
      flex: 1; padding: 14px 16px; display: flex; flex-direction: column; justify-content: center; gap: 4px;
    }
    .preview-name { font-weight: 700; color: #333; font-size: 14px; word-break: break-all; }
    .preview-size { color: #888; font-size: 12px; }
    .preview-type { color: #17a2b8; font-size: 12px; font-weight: 600; }
    .preview-remove {
      position: absolute; top: 8px; right: 8px;
      width: 28px; height: 28px; border-radius: 50%;
      background: #dc3545; color: white; border: none;
      cursor: pointer; font-size: 14px; display: flex;
      align-items: center; justify-content: center;
      transition: all 0.2s; box-shadow: 0 2px 8px rgba(220,53,69,0.4);
    }
    .preview-remove:hover { background: #c82333; transform: scale(1.1); }

    .btn {
      padding: 11px 28px; border: none; border-radius: 6px;
      cursor: pointer; font-size: 15px; transition: all 0.3s;
      display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-primary { background: linear-gradient(135deg, #17a2b8, #138496); color: white; }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(23,162,184,0.3); }
    .btn-danger { background: linear-gradient(135deg, #dc3545, #c82333); color: white; }
    .btn-danger:hover { transform: translateY(-2px); }

    /* Filter bar */
    .filter-bar { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; align-items: center; }
    .filter-bar select, .filter-bar input {
      padding: 10px 14px; border: 2px solid #dee2e6; border-radius: 6px; font-size: 14px;
    }
    .filter-bar input { flex: 1; min-width: 200px; }

    /* Gallery grid */
    .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px; }
    .gallery-item {
      background: white; border-radius: 12px; overflow: hidden;
      box-shadow: 0 3px 12px rgba(0,0,0,0.1); transition: all 0.3s;
    }
    .gallery-item:hover { transform: translateY(-6px); box-shadow: 0 10px 30px rgba(23,162,184,0.25); }
    .img-wrap { position: relative; height: 200px; overflow: hidden; background: #e9ecef; }
    .img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s; }
    .gallery-item:hover .img-wrap img { transform: scale(1.08); }
    .img-overlay {
      position: absolute; inset: 0;
      background: linear-gradient(to bottom, transparent 40%, rgba(0,0,0,0.7));
      opacity: 0; transition: opacity 0.3s; display: flex; align-items: flex-end; padding: 12px;
    }
    .gallery-item:hover .img-overlay { opacity: 1; }
    .img-overlay span { color: white; font-size: 13px; font-weight: 600; }
    .cat-badge {
      position: absolute; top: 10px; left: 10px;
      background: rgba(23,162,184,0.9); color: white;
      padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600;
    }
    .quick-view {
      position: absolute; top: 50%; left: 50%;
      transform: translate(-50%,-50%) scale(0);
      background: white; color: #17a2b8; border: none;
      padding: 10px 18px; border-radius: 20px; font-size: 13px;
      font-weight: 600; cursor: pointer; transition: all 0.3s;
      box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }
    .gallery-item:hover .quick-view { transform: translate(-50%,-50%) scale(1); }
    .item-body { padding: 15px; }
    .item-title { font-weight: 700; color: #333; font-size: 15px; margin-bottom: 4px; }
    .item-desc { color: #888; font-size: 12px; margin-bottom: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .item-meta { font-size: 11px; color: #aaa; margin-bottom: 12px; }
    .item-actions { display: flex; gap: 8px; }
    .btn-sm {
      flex: 1; padding: 8px; border: none; border-radius: 5px;
      cursor: pointer; font-size: 12px; font-weight: 600; transition: all 0.2s;
      display: flex; align-items: center; justify-content: center; gap: 4px;
    }
    .btn-edit-sm { background: #fff3cd; color: #856404; }
    .btn-edit-sm:hover { background: #ffc107; color: white; }
    .btn-del-sm { background: #f8d7da; color: #721c24; }
    .btn-del-sm:hover { background: #dc3545; color: white; }

    .empty-state { text-align: center; padding: 60px 20px; color: #aaa; }
    .empty-state i { font-size: 60px; display: block; margin-bottom: 15px; opacity: 0.3; }

    /* Alert */
    .alert { padding: 12px 16px; border-radius: 6px; margin-bottom: 15px; font-size: 14px; display: none; }
    .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-error   { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

    /* Modal */
    .modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
    .modal.active { display: flex; }
    .modal-box { background: white; padding: 28px; border-radius: 10px; width: 90%; max-width: 480px; max-height: 90vh; overflow-y: auto; }
    .modal-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-head h3 { margin: 0; color: #17a2b8; font-size: 18px; }
    .close-modal { background: none; border: none; font-size: 22px; cursor: pointer; color: #999; line-height: 1; }
    .close-modal:hover { color: #333; }

    /* Category manager modal */
    .cat-list { list-style: none; padding: 0; margin: 0 0 15px 0; max-height: 220px; overflow-y: auto; }
    .cat-list li {
      display: flex; justify-content: space-between; align-items: center;
      padding: 10px 12px; border-bottom: 1px solid #f0f0f0; font-size: 14px;
    }
    .cat-list li:last-child { border-bottom: none; }
    .cat-list li:hover { background: #f8f9fa; }
    .btn-del-cat {
      background: #f8d7da; color: #dc3545; border: none;
      width: 28px; height: 28px; border-radius: 5px; cursor: pointer;
      font-size: 12px; transition: all 0.2s;
    }
    .btn-del-cat:hover { background: #dc3545; color: white; }
    .add-cat-row { display: flex; gap: 8px; }
    .add-cat-row input { flex: 1; padding: 10px 12px; border: 2px solid #dee2e6; border-radius: 6px; font-size: 14px; }
    .add-cat-row input:focus { outline: none; border-color: #17a2b8; }
    .add-cat-row button { padding: 10px 18px; background: #17a2b8; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; }
    .add-cat-row button:hover { background: #138496; }
  </style>
</head>
<body>
<div class="top-header"><marquee>Manage Gallery - Upload and manage images for the school website</marquee></div>
<div class="container">

  <div class="page-header">
    <div>
      <h1><i class="fa fa-images"></i> Manage Gallery</h1>
      <div class="breadcrumb">
        <a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Manage Gallery
      </div>
    </div>
    <a href="../dashboards/admin-dashboard.php" class="back-btn"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
  </div>

  <!-- Upload Section -->
  <div class="card">
    <h2><i class="fa fa-cloud-upload-alt"></i> Upload New Image</h2>
    <div id="uploadAlert" class="alert"></div>

    <form id="uploadForm" enctype="multipart/form-data">
      <div class="upload-area" id="uploadArea">
        <input type="file" id="fileInput" name="image" accept="image/*" required>
        <i class="fa fa-cloud-upload-alt upload-icon"></i>
        <p><strong>Drag & Drop image here</strong> or click to browse</p>
        <p style="font-size:12px;color:#aaa;">JPG, PNG, GIF, WEBP — max 10MB</p>
      </div>

      <!-- File preview card shown after picking -->
      <div class="file-preview-card" id="filePreviewCard">
        <div class="preview-img-wrap">
          <img id="previewImg" src="" alt="preview">
        </div>
        <div class="preview-info">
          <div class="preview-name" id="previewName">—</div>
          <div class="preview-size" id="previewSize">—</div>
          <div class="preview-type" id="previewType">—</div>
        </div>
        <button type="button" class="preview-remove" id="removeFileBtn" title="Remove file">
          <i class="fa fa-times"></i>
        </button>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-top:18px;">
        <div class="form-group">
          <label>Image Title *</label>
          <input type="text" name="title" class="form-control" placeholder="Enter image title" required>
        </div>
        <div class="form-group">
          <label>Category
            <button type="button" onclick="openCatModal()" style="margin-left:8px;background:#e8f7fa;color:#17a2b8;border:none;padding:3px 10px;border-radius:10px;font-size:12px;cursor:pointer;"><i class="fa fa-cog"></i> Manage</button>
          </label>
          <select name="category" id="uploadCatSelect" class="form-control">
            <option value="">— Select category —</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control" placeholder="Optional description"></textarea>
      </div>

      <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> Upload Image</button>
    </form>
  </div>

  <!-- Gallery List -->
  <div class="card">
    <h2><i class="fa fa-th"></i> Gallery Images</h2>

    <div class="filter-bar">
      <select id="filterCat">
        <option value="">All Categories</option>
      </select>
      <input type="text" id="searchInput" placeholder="Search images...">
      <button class="btn btn-primary" onclick="loadGallery()"><i class="fa fa-sync"></i> Refresh</button>
    </div>

    <div class="gallery-grid" id="galleryGrid">
      <div class="empty-state"><i class="fa fa-images"></i><p>Loading images...</p></div>
    </div>
  </div>

</div>

<!-- Category Manager Modal -->
<div class="modal" id="catModal">
  <div class="modal-box">
    <div class="modal-head">
      <h3><i class="fa fa-tags"></i> Manage Categories</h3>
      <button class="close-modal" onclick="closeCatModal()">×</button>
    </div>
    <ul class="cat-list" id="catList"><li style="color:#aaa;text-align:center;padding:20px;">Loading...</li></ul>
    <div class="add-cat-row">
      <input type="text" id="newCatName" placeholder="New category name..." maxlength="100">
      <button onclick="addCategory()"><i class="fa fa-plus"></i> Add</button>
    </div>
  </div>
</div>

<!-- Edit Image Modal -->
<div class="modal" id="editModal">
  <div class="modal-box">
    <div class="modal-head">
      <h3><i class="fa fa-edit"></i> Edit Image</h3>
      <button class="close-modal" onclick="document.getElementById('editModal').classList.remove('active')">×</button>
    </div>
    <div id="editAlert" class="alert"></div>
    <form id="editForm">
      <input type="hidden" id="editId" name="id">
      <div class="form-group"><label>Title *</label><input type="text" id="editTitle" name="title" class="form-control" required></div>
      <div class="form-group">
        <label>Category</label>
        <select id="editCatSelect" name="category" class="form-control"><option value="">— None —</option></select>
      </div>
      <div class="form-group"><label>Description</label><textarea id="editDesc" name="description" class="form-control"></textarea></div>
      <div style="display:flex;gap:10px;">
        <button type="submit" class="btn btn-primary" style="flex:1;"><i class="fa fa-save"></i> Save</button>
        <button type="button" class="btn" style="background:#6c757d;color:white;flex:1;" onclick="document.getElementById('editModal').classList.remove('active')">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script>
let categories = [];

// ── Category helpers ──────────────────────────────────────────
async function loadCategories() {
  const res  = await fetch('gallery-categories.php');
  const data = await res.json();
  if (!data.success) return;
  categories = data.categories;
  populateCatSelects();
  renderCatList();
}

function populateCatSelects() {
  const opts = categories.map(c => `<option value="${c.name}">${c.name}</option>`).join('');
  document.getElementById('uploadCatSelect').innerHTML = '<option value="">— Select category —</option>' + opts;
  document.getElementById('filterCat').innerHTML       = '<option value="">All Categories</option>' + opts;
  document.getElementById('editCatSelect').innerHTML   = '<option value="">— None —</option>' + opts;
}

function renderCatList() {
  const ul = document.getElementById('catList');
  if (!categories.length) { ul.innerHTML = '<li style="color:#aaa;text-align:center;padding:20px;">No categories yet</li>'; return; }
  ul.innerHTML = categories.map(c => `
    <li>
      <span><i class="fa fa-tag" style="color:#17a2b8;margin-right:8px;"></i>${c.name}</span>
      <button class="btn-del-cat" onclick="deleteCategory(${c.id},'${c.name}')" title="Delete"><i class="fa fa-trash"></i></button>
    </li>`).join('');
}

async function addCategory() {
  const input = document.getElementById('newCatName');
  const name  = input.value.trim();
  if (!name) { alert('Enter a category name'); return; }
  const fd = new FormData(); fd.append('action','add'); fd.append('name', name);
  const res  = await fetch('gallery-categories.php', { method:'POST', body: fd });
  const data = await res.json();
  if (data.success) { input.value = ''; loadCategories(); }
  else alert(data.message || 'Failed to add category');
}

async function deleteCategory(id, name) {
  if (!confirm(`Delete category "${name}"? Images in this category won't be deleted.`)) return;
  const fd = new FormData(); fd.append('action','delete'); fd.append('id', id);
  const res  = await fetch('gallery-categories.php', { method:'POST', body: fd });
  const data = await res.json();
  if (data.success) loadCategories();
  else alert(data.message || 'Failed to delete');
}

function openCatModal()  { loadCategories(); document.getElementById('catModal').classList.add('active'); }
function closeCatModal() { document.getElementById('catModal').classList.remove('active'); }

// ── Upload area ───────────────────────────────────────────────
const uploadArea     = document.getElementById('uploadArea');
const fileInput      = document.getElementById('fileInput');
const previewCard    = document.getElementById('filePreviewCard');
const previewImg     = document.getElementById('previewImg');
const previewName    = document.getElementById('previewName');
const previewSize    = document.getElementById('previewSize');
const previewType    = document.getElementById('previewType');
const removeFileBtn  = document.getElementById('removeFileBtn');

uploadArea.addEventListener('click', () => fileInput.click());
uploadArea.addEventListener('dragover', e => { e.preventDefault(); uploadArea.classList.add('dragover'); });
uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('dragover'));
uploadArea.addEventListener('drop', e => {
  e.preventDefault(); uploadArea.classList.remove('dragover');
  if (e.dataTransfer.files.length) { fileInput.files = e.dataTransfer.files; showPreview(e.dataTransfer.files[0]); }
});
fileInput.addEventListener('change', e => { if (e.target.files[0]) showPreview(e.target.files[0]); });

function showPreview(file) {
  const reader = new FileReader();
  reader.onload = e => {
    previewImg.src = e.target.result;
    previewName.textContent = file.name;
    previewSize.textContent = formatBytes(file.size);
    previewType.textContent = file.type || 'image';
    previewCard.classList.add('visible');
    uploadArea.classList.add('has-file');
    // Hide the default upload prompt text
    uploadArea.querySelectorAll('i, p').forEach(el => el.style.display = 'none');
  };
  reader.readAsDataURL(file);
}

function clearFile() {
  fileInput.value = '';
  previewImg.src = '';
  previewCard.classList.remove('visible');
  uploadArea.classList.remove('has-file');
  uploadArea.querySelectorAll('i, p').forEach(el => el.style.display = '');
}

removeFileBtn.addEventListener('click', e => { e.stopPropagation(); clearFile(); });

function formatBytes(bytes) {
  if (bytes < 1024) return bytes + ' B';
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
  return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

document.getElementById('uploadForm').addEventListener('submit', async e => {
  e.preventDefault();
  const alertEl = document.getElementById('uploadAlert');
  alertEl.style.display = 'none';
  const btn = e.target.querySelector('button[type=submit]');
  btn.disabled = true; btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Uploading...';

  try {
    const res  = await fetch('gallery-upload.php', { method:'POST', body: new FormData(e.target) });
    const data = await res.json();
    if (data.success) {
      showAlert(alertEl, 'Image uploaded successfully!', 'success');
      e.target.reset();
      clearFile();
      loadGallery();
    } else {
      showAlert(alertEl, data.message || 'Upload failed', 'error');
    }
  } catch(err) {
    showAlert(alertEl, 'Network error: ' + err.message, 'error');
  }
  btn.disabled = false; btn.innerHTML = '<i class="fa fa-upload"></i> Upload Image';
});

// ── Gallery list ──────────────────────────────────────────────
async function loadGallery() {
  const cat    = document.getElementById('filterCat').value;
  const search = document.getElementById('searchInput').value;
  const grid   = document.getElementById('galleryGrid');
  grid.innerHTML = '<div class="empty-state"><i class="fa fa-spinner fa-spin"></i><p>Loading...</p></div>';

  try {
    const res  = await fetch(`gallery-list.php?category=${encodeURIComponent(cat)}&search=${encodeURIComponent(search)}`);
    const data = await res.json();

    if (!data.success) { grid.innerHTML = `<div class="empty-state"><i class="fa fa-exclamation-circle"></i><p>${data.message}</p></div>`; return; }
    if (!data.images.length) { grid.innerHTML = '<div class="empty-state"><i class="fa fa-images"></i><p>No images found</p></div>'; return; }

    grid.innerHTML = data.images.map(img => {
      const date = new Date(img.created_at).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'});
      const thumb = img.thumbnail_path || img.file_path;
      return `
      <div class="gallery-item">
        <div class="img-wrap">
          <img src="../${thumb}" alt="${img.title}" loading="lazy" onerror="this.src='../assets/images/img1.jpg'">
          ${img.category ? `<span class="cat-badge">${img.category}</span>` : ''}
          <button class="quick-view" onclick="quickView('../${img.file_path}','${img.title}')"><i class="fa fa-eye"></i> View</button>
          <div class="img-overlay"><span>${img.title}</span></div>
        </div>
        <div class="item-body">
          <div class="item-title">${img.title}</div>
          <div class="item-desc">${img.description || '<em style="color:#ccc">No description</em>'}</div>
          <div class="item-meta"><i class="fa fa-calendar" style="color:#17a2b8"></i> ${date}</div>
          <div class="item-actions">
            <button class="btn-sm btn-edit-sm" onclick="editImage(${img.id})"><i class="fa fa-edit"></i> Edit</button>
            <button class="btn-sm btn-del-sm"  onclick="deleteImage(${img.id})"><i class="fa fa-trash"></i> Delete</button>
          </div>
        </div>
      </div>`;
    }).join('');
  } catch(err) {
    grid.innerHTML = `<div class="empty-state"><i class="fa fa-exclamation-circle"></i><p>Error: ${err.message}</p></div>`;
  }
}

// ── Quick view ────────────────────────────────────────────────
function quickView(path, title) {
  const m = document.createElement('div');
  m.className = 'modal active'; m.style.zIndex = '2000';
  m.innerHTML = `<div class="modal-box" style="max-width:90%;padding:0;overflow:hidden;">
    <div style="position:relative;">
      <button onclick="this.closest('.modal').remove()" style="position:absolute;top:12px;right:12px;background:rgba(0,0,0,0.6);color:white;border:none;width:36px;height:36px;border-radius:50%;cursor:pointer;font-size:18px;z-index:10;">×</button>
      <img src="${path}" alt="${title}" style="width:100%;max-height:80vh;object-fit:contain;display:block;">
      <div style="padding:15px;background:white;"><strong style="color:#17a2b8;">${title}</strong></div>
    </div>
  </div>`;
  document.body.appendChild(m);
  m.addEventListener('click', e => { if (e.target === m) m.remove(); });
}

// ── Edit ──────────────────────────────────────────────────────
async function editImage(id) {
  try {
    const res  = await fetch(`gallery-get.php?id=${id}`);
    const data = await res.json();
    if (!data.success) { alert(data.message); return; }
    const img = data.image;
    document.getElementById('editId').value    = img.id;
    document.getElementById('editTitle').value = img.title;
    document.getElementById('editDesc').value  = img.description || '';
    // Set category select
    const sel = document.getElementById('editCatSelect');
    sel.innerHTML = '<option value="">— None —</option>' + categories.map(c => `<option value="${c.name}" ${c.name===img.category?'selected':''}>${c.name}</option>`).join('');
    document.getElementById('editModal').classList.add('active');
  } catch(err) { alert('Error: ' + err.message); }
}

document.getElementById('editForm').addEventListener('submit', async e => {
  e.preventDefault();
  const alertEl = document.getElementById('editAlert');
  const res  = await fetch('gallery-update.php', { method:'POST', body: new FormData(e.target) });
  const data = await res.json();
  if (data.success) {
    showAlert(alertEl, 'Updated successfully!', 'success');
    setTimeout(() => { document.getElementById('editModal').classList.remove('active'); loadGallery(); }, 800);
  } else {
    showAlert(alertEl, data.message || 'Update failed', 'error');
  }
});

// ── Delete ────────────────────────────────────────────────────
async function deleteImage(id) {
  if (!confirm('Delete this image? This cannot be undone.')) return;
  const res  = await fetch('gallery-delete.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({id}) });
  const data = await res.json();
  if (data.success) loadGallery();
  else alert(data.message || 'Delete failed');
}

// ── Helpers ───────────────────────────────────────────────────
function showAlert(el, msg, type) {
  el.className = 'alert alert-' + type;
  el.textContent = msg;
  el.style.display = 'block';
  setTimeout(() => { el.style.display = 'none'; }, 4000);
}

document.getElementById('filterCat').addEventListener('change', loadGallery);
document.getElementById('searchInput').addEventListener('input', debounce(loadGallery, 400));
document.getElementById('newCatName').addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); addCategory(); } });

function debounce(fn, ms) {
  let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); };
}

// Init
loadCategories();
loadGallery();
</script>

<footer class="footer" style="margin-top:30px;"><p>© 2025 SCTI - Admin Panel</p></footer>
</body>
</html>
