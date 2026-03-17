<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../index.php'); exit();
}
require_once '../includes/config.php';

$subjects = ['Programming Fundamentals','Database Management','Web Development','Data Structures','Algorithms'];

try {
    $db = getDBConnection();
    $db->exec("CREATE TABLE IF NOT EXISTS materials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        subject VARCHAR(100) NOT NULL,
        file_type VARCHAR(20) NOT NULL,
        file_name VARCHAR(255) NOT NULL,
        file_path VARCHAR(500) NOT NULL,
        file_size VARCHAR(30),
        downloads INT DEFAULT 0,
        uploaded_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    $materials = $db->query("SELECT * FROM materials ORDER BY created_at DESC")->fetchAll();
} catch(Exception $e) { $materials = []; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Course Materials | SCTI Teacher Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Segoe UI',sans-serif;background:#f5f7fa}
    .top-header{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:10px 20px;font-size:14px}
    .container{max-width:1400px;margin:0 auto;padding:20px}
    .page-header{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:30px;border-radius:10px;margin-bottom:25px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 15px rgba(40,167,69,.2)}
    .page-header h1{margin:0 0 8px;font-size:28px}
    .breadcrumb{font-size:14px}.breadcrumb a{color:white;text-decoration:none}
    .btn-upload{background:rgba(255,255,255,.2);color:white;padding:12px 24px;border:2px solid white;border-radius:6px;cursor:pointer;font-size:14px;transition:.3s;display:inline-flex;align-items:center;gap:8px}
    .btn-upload:hover{background:white;color:#28a745}
    .filter-bar{background:white;padding:18px 22px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.1);margin-bottom:22px;display:flex;gap:12px;flex-wrap:wrap;align-items:center}
    .filter-select,.search-input{padding:10px 14px;border:2px solid #dee2e6;border-radius:6px;font-size:14px}
    .filter-select:focus,.search-input:focus{outline:none;border-color:#28a745}
    .search-input{flex:1;min-width:220px}
    .materials-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px}
    .material-card{background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.1);transition:.3s}
    .material-card:hover{transform:translateY(-4px);box-shadow:0 8px 25px rgba(40,167,69,.2)}
    .material-header{padding:18px 20px;display:flex;align-items:center;gap:14px;border-bottom:1px solid #f0f0f0}
    .file-icon{width:50px;height:50px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:22px;color:white;flex-shrink:0}
    .icon-PDF{background:linear-gradient(135deg,#dc3545,#c82333)}
    .icon-DOC{background:linear-gradient(135deg,#004080,#0059b3)}
    .icon-PPT{background:linear-gradient(135deg,#fd7e14,#e55a00)}
    .icon-ZIP{background:linear-gradient(135deg,#6f42c1,#5a32a3)}
    .icon-MP4{background:linear-gradient(135deg,#e83e8c,#c2185b)}
    .icon-XLS{background:linear-gradient(135deg,#28a745,#20c997)}
    .icon-TXT{background:linear-gradient(135deg,#6c757d,#495057)}
    .material-title{font-weight:600;color:#333;font-size:15px;margin-bottom:4px}
    .material-subject{color:#666;font-size:13px}
    .material-body{padding:14px 20px}
    .material-meta{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:12px}
    .meta-tag{display:flex;align-items:center;gap:5px;color:#666;font-size:12px}
    .meta-tag i{color:#28a745}
    .material-actions{display:flex;gap:8px}
    .btn-sm{flex:1;padding:9px;border:none;border-radius:5px;cursor:pointer;font-size:13px;font-weight:600;transition:.2s;display:flex;align-items:center;justify-content:center;gap:5px}
    .btn-download{background:#28a745;color:white}
    .btn-download:hover{background:#20c997}
    .btn-delete{background:#f8d7da;color:#dc3545}
    .btn-delete:hover{background:#dc3545;color:white}
    .empty-state{text-align:center;padding:60px 20px;color:#aaa;grid-column:1/-1}
    .empty-state i{font-size:52px;display:block;margin-bottom:14px;opacity:.3}
    .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;align-items:center;justify-content:center}
    .modal-overlay.open{display:flex}
    .modal-box{background:white;border-radius:14px;width:100%;max-width:500px;box-shadow:0 20px 60px rgba(0,0,0,.3);animation:mIn .25s ease}
    @keyframes mIn{from{transform:translateY(-24px);opacity:0}to{transform:translateY(0);opacity:1}}
    .modal-head{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:20px 26px;display:flex;justify-content:space-between;align-items:center;border-radius:14px 14px 0 0}
    .modal-head h2{margin:0;font-size:18px}
    .modal-close{background:rgba(255,255,255,.2);border:none;color:white;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:15px;display:flex;align-items:center;justify-content:center}
    .modal-close:hover{background:rgba(255,255,255,.35)}
    .modal-body{padding:26px}
    .form-group{margin-bottom:18px}
    .form-group label{display:block;font-size:13px;font-weight:700;color:#555;margin-bottom:6px;text-transform:uppercase}
    .form-control{width:100%;padding:10px 14px;border:2px solid #dee2e6;border-radius:7px;font-size:14px;font-family:inherit;transition:.2s}
    .form-control:focus{outline:none;border-color:#28a745}
    .file-drop{border:2px dashed #dee2e6;border-radius:8px;padding:28px;text-align:center;cursor:pointer;transition:.2s;color:#888}
    .file-drop:hover,.file-drop.drag{border-color:#28a745;background:#f0fff4;color:#28a745}
    .file-drop i{font-size:30px;display:block;margin-bottom:8px}
    .chosen-file{font-size:13px;color:#28a745;font-weight:600;margin-top:8px}
    .btn-submit{width:100%;background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:13px;border:none;border-radius:8px;font-size:15px;font-weight:700;cursor:pointer;transition:.2s;display:flex;align-items:center;justify-content:center;gap:9px}
    .btn-submit:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(40,167,69,.35)}
    .btn-submit:disabled{opacity:.6;cursor:not-allowed;transform:none}
    .progress-bar{height:6px;background:#dee2e6;border-radius:3px;margin-top:12px;overflow:hidden;display:none}
    .progress-fill{height:100%;background:linear-gradient(135deg,#28a745,#20c997);width:0;transition:width .3s}
    .toast{display:none;position:fixed;bottom:28px;right:28px;padding:14px 22px;border-radius:10px;font-size:14px;font-weight:600;z-index:99999;align-items:center;gap:10px;box-shadow:0 6px 20px rgba(0,0,0,.2)}
    .toast.show{display:flex;animation:tIn .3s ease}
    .toast-ok{background:#28a745;color:white}.toast-err{background:#dc3545;color:white}
    @keyframes tIn{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}
    .footer{background:#2c3e50;color:white;text-align:center;padding:20px;margin-top:30px}
  </style>
</head>
<body>
<div class="top-header">Course Materials — Upload and manage learning resources for your students</div>
<div class="container">
  <div class="page-header">
    <div>
      <h1><i class="fa fa-book-open"></i> Course Materials</h1>
      <div class="breadcrumb"><a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Course Materials</div>
    </div>
    <button class="btn-upload" onclick="openUpload()"><i class="fa fa-upload"></i> Upload Material</button>
  </div>
  <div class="filter-bar">
    <select class="filter-select" id="filterSubject" onchange="applyFilter()">
      <option value="">All Subjects</option>
      <?php foreach($subjects as $s) echo '<option value="'.htmlspecialchars($s).'">'.htmlspecialchars($s).'</option>'; ?>
    </select>
    <select class="filter-select" id="filterType" onchange="applyFilter()">
      <option value="">All Types</option>
      <option>PDF</option><option>DOC</option><option>PPT</option>
      <option>MP4</option><option>ZIP</option><option>XLS</option>
    </select>
    <input type="text" class="search-input" id="searchInput" placeholder="Search materials..." oninput="applyFilter()">
  </div>
  <div class="materials-grid" id="materialsGrid">
    <?php if(empty($materials)): ?>
    <div class="empty-state"><i class="fa fa-folder-open"></i><p>No materials uploaded yet.</p><small>Click "Upload Material" to add your first resource.</small></div>
    <?php else: foreach($materials as $m):
      $iconClass = 'icon-' . $m['file_type'];
      $faIcon = 'fa-file';
      if($m['file_type']==='PDF') $faIcon='fa-file-pdf';
      elseif($m['file_type']==='DOC') $faIcon='fa-file-word';
      elseif($m['file_type']==='PPT') $faIcon='fa-file-powerpoint';
      elseif($m['file_type']==='ZIP') $faIcon='fa-file-archive';
      elseif($m['file_type']==='MP4') $faIcon='fa-file-video';
      elseif($m['file_type']==='XLS') $faIcon='fa-file-excel';
      $date = date('M j, Y', strtotime($m['created_at']));
    ?>
    <div class="material-card"
         data-subject="<?=htmlspecialchars($m['subject'])?>"
         data-type="<?=htmlspecialchars($m['file_type'])?>"
         data-title="<?=htmlspecialchars(strtolower($m['title']))?>">
      <div class="material-header">
        <div class="file-icon <?=$iconClass?>"><i class="fa <?=$faIcon?>"></i></div>
        <div>
          <div class="material-title"><?=htmlspecialchars($m['title'])?></div>
          <div class="material-subject"><?=htmlspecialchars($m['subject'])?></div>
        </div>
      </div>
      <div class="material-body">
        <div class="material-meta">
          <span class="meta-tag"><i class="fa fa-file"></i> <?=htmlspecialchars($m['file_size'])?></span>
          <span class="meta-tag"><i class="fa fa-calendar"></i> <?=$date?></span>
          <span class="meta-tag dl-count"><i class="fa fa-download"></i> <?=$m['downloads']?> downloads</span>
        </div>
        <div class="material-actions">
          <button class="btn-sm btn-download" onclick="downloadMaterial(<?=$m['id']?>, this)"><i class="fa fa-download"></i> Download</button>
          <button class="btn-sm btn-delete" onclick="deleteMaterial(<?=$m['id']?>, this)"><i class="fa fa-trash"></i> Delete</button>
        </div>
      </div>
    </div>
    <?php endforeach; endif; ?>
  </div>
</div>
<footer class="footer"><p>© 2025 SCTI - Teacher Portal</p></footer>

<!-- UPLOAD MODAL -->
<div class="modal-overlay" id="uploadModal">
  <div class="modal-box">
    <div class="modal-head">
      <h2><i class="fa fa-upload"></i> Upload Material</h2>
      <button class="modal-close" onclick="closeUpload()"><i class="fa fa-times"></i></button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label>Title</label>
        <input type="text" id="upTitle" class="form-control" placeholder="e.g. Chapter 1 - Intro to Programming">
      </div>
      <div class="form-group">
        <label>Subject</label>
        <select id="upSubject" class="form-control">
          <?php foreach($subjects as $s) echo '<option value="'.htmlspecialchars($s).'">'.htmlspecialchars($s).'</option>'; ?>
        </select>
      </div>
      <div class="form-group">
        <label>File</label>
        <div class="file-drop" id="fileDrop" onclick="document.getElementById('upFile').click()">
          <i class="fa fa-cloud-upload-alt"></i>
          <div>Click to browse or drag &amp; drop</div>
          <div style="font-size:12px;margin-top:6px;color:#aaa">PDF, DOC, PPT, XLS, ZIP, MP4 — max 200MB</div>
          <div class="chosen-file" id="chosenFile"></div>
        </div>
        <input type="file" id="upFile" style="display:none" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.mp4,.avi,.mkv,.txt" onchange="fileChosen(this)">
      </div>
      <div class="progress-bar" id="progressBar"><div class="progress-fill" id="progressFill"></div></div>
      <button class="btn-submit" id="upBtn" onclick="uploadMaterial()"><i class="fa fa-upload"></i> Upload</button>
    </div>
  </div>
</div>

<div class="toast toast-ok" id="toastOk"><i class="fa fa-check-circle"></i><span id="toastOkMsg">Done!</span></div>
<div class="toast toast-err" id="toastErr"><i class="fa fa-times-circle"></i><span id="toastErrMsg">Error</span></div>

<script>
function openUpload()  { document.getElementById('uploadModal').classList.add('open'); }
function closeUpload() { document.getElementById('uploadModal').classList.remove('open'); }
document.getElementById('uploadModal').addEventListener('click', function(e){ if(e.target===this) closeUpload(); });

var drop = document.getElementById('fileDrop');
drop.addEventListener('dragover',  function(e){ e.preventDefault(); drop.classList.add('drag'); });
drop.addEventListener('dragleave', function(){ drop.classList.remove('drag'); });
drop.addEventListener('drop', function(e){
  e.preventDefault(); drop.classList.remove('drag');
  var f = e.dataTransfer.files[0];
  if (f) { document.getElementById('upFile').files = e.dataTransfer.files; showChosen(f); }
});

function fileChosen(inp) { if(inp.files[0]) showChosen(inp.files[0]); }
function showChosen(f) {
  var sz = f.size > 1048576 ? (f.size/1048576).toFixed(1)+' MB' : (f.size/1024).toFixed(0)+' KB';
  document.getElementById('chosenFile').textContent = f.name + ' (' + sz + ')';
}

function uploadMaterial() {
  var title   = document.getElementById('upTitle').value.trim();
  var subject = document.getElementById('upSubject').value;
  var file    = document.getElementById('upFile').files[0];
  if (!title) { document.getElementById('upTitle').focus(); showToast('err','Please enter a title.'); return; }
  if (!file)  { showToast('err','Please select a file.'); return; }

  var fd = new FormData();
  fd.append('title', title);
  fd.append('subject', subject);
  fd.append('file', file);

  var btn = document.getElementById('upBtn');
  btn.disabled = true; btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Uploading...';
  var pb = document.getElementById('progressBar');
  var pf = document.getElementById('progressFill');
  pb.style.display = 'block'; pf.style.width = '0%';

  var xhr = new XMLHttpRequest();
  xhr.upload.onprogress = function(e){ if(e.lengthComputable) pf.style.width = Math.round(e.loaded/e.total*100)+'%'; };
  xhr.onload = function(){
    btn.disabled = false; btn.innerHTML = '<i class="fa fa-upload"></i> Upload';
    pb.style.display = 'none';
    try {
      var res = JSON.parse(xhr.responseText);
      if (res.success) { closeUpload(); showToast('ok', res.message); setTimeout(function(){ location.reload(); }, 1200); }
      else { showToast('err', res.message); }
    } catch(e){ showToast('err','Unexpected response.'); }
  };
  xhr.onerror = function(){ btn.disabled=false; btn.innerHTML='<i class="fa fa-upload"></i> Upload'; showToast('err','Network error.'); };
  xhr.open('POST','material-upload.php');
  xhr.send(fd);
}

function downloadMaterial(id, btn) {
  window.open('material-download.php?id='+id, '_blank');
  setTimeout(function(){
    var dlTag = btn.closest('.material-card').querySelector('.dl-count');
    if (dlTag) {
      var cur = parseInt(dlTag.textContent) || 0;
      dlTag.innerHTML = '<i class="fa fa-download" style="color:#28a745"></i> ' + (cur+1) + ' downloads';
    }
  }, 800);
}

function deleteMaterial(id, btn) {
  if (!confirm('Delete this material? This cannot be undone.')) return;
  var card = btn.closest('.material-card');
  var fd = new FormData(); fd.append('id', id);
  fetch('material-delete.php', {method:'POST', body:fd})
    .then(function(r){ return r.json(); })
    .then(function(res){
      if (res.success) {
        card.style.transition = 'all .3s';
        card.style.opacity = '0'; card.style.transform = 'scale(.9)';
        setTimeout(function(){ card.remove(); checkEmpty(); }, 300);
        showToast('ok', res.message);
      } else { showToast('err', res.message); }
    })
    .catch(function(){ showToast('err','Network error.'); });
}

function checkEmpty() {
  if (!document.querySelectorAll('.material-card').length) {
    document.getElementById('materialsGrid').innerHTML =
      '<div class="empty-state"><i class="fa fa-folder-open"></i><p>No materials uploaded yet.</p></div>';
  }
}

function applyFilter() {
  var subj   = document.getElementById('filterSubject').value.toLowerCase();
  var type   = document.getElementById('filterType').value.toLowerCase();
  var search = document.getElementById('searchInput').value.toLowerCase();
  document.querySelectorAll('.material-card').forEach(function(card){
    var ms = card.dataset.subject.toLowerCase();
    var mt = card.dataset.type.toLowerCase();
    var tt = card.dataset.title;
    var show = (!subj || ms===subj) && (!type || mt===type) && (!search || tt.includes(search) || ms.includes(search));
    card.style.display = show ? '' : 'none';
  });
}

function showToast(type, msg) {
  var id = type==='ok' ? 'toastOk' : 'toastErr';
  var mid = type==='ok' ? 'toastOkMsg' : 'toastErrMsg';
  document.getElementById(mid).textContent = msg;
  var t = document.getElementById(id);
  t.classList.add('show');
  setTimeout(function(){ t.classList.remove('show'); }, 3000);
}
</script>
</body>
</html>
