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
  <title>Program Manager | SCTI Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box;}
    body{font-family:'Segoe UI',sans-serif;background:#f0f4f8;min-height:100vh;}
    .top-bar{background:#00264d;color:white;padding:7px 20px;font-size:13px;}
    .pg-header{background:linear-gradient(135deg,#004080,#0059b3);color:#fff;padding:20px 28px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 18px rgba(0,64,128,.25);}
    .pg-header h1{font-size:22px;font-weight:700;display:flex;align-items:center;gap:10px;margin:0 0 3px;}
    .pg-header .bc{font-size:12px;color:rgba(255,255,255,.75);}
    .pg-header .bc a{color:#fff;text-decoration:none;}
    .hdr-btns{display:flex;gap:8px;}
    .btn-hdr{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;border:none;transition:.2s;text-decoration:none;}
    .btn-hdr.ghost{background:rgba(255,255,255,.18);color:#fff;}
    .btn-hdr.ghost:hover{background:rgba(255,255,255,.32);}
    .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;padding:18px 28px;background:#fff;border-bottom:1px solid #e9ecef;}
    .stat{display:flex;align-items:center;gap:12px;background:#f8f9fa;border-radius:10px;padding:12px 16px;}
    .stat-ico{width:42px;height:42px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:17px;color:#fff;flex-shrink:0;}
    .ico-blue{background:linear-gradient(135deg,#004080,#0059b3);}
    .ico-green{background:linear-gradient(135deg,#28a745,#20c997);}
    .ico-orange{background:linear-gradient(135deg,#fd7e14,#ffc107);}
    .ico-grey{background:linear-gradient(135deg,#6c757d,#495057);}
    .stat-val{font-size:24px;font-weight:700;color:#222;line-height:1;}
    .stat-lbl{font-size:11px;color:#999;margin-top:2px;}
    .layout{display:grid;grid-template-columns:360px 1fr;min-height:calc(100vh - 170px);}
    .panel{background:#fff;border-right:1px solid #e9ecef;padding:22px;overflow-y:auto;}
    .panel h3{font-size:15px;color:#004080;margin-bottom:16px;padding-bottom:10px;border-bottom:2px solid #e8f0fe;display:flex;align-items:center;gap:8px;}
    .content{padding:22px;overflow-y:auto;}
    .fg{margin-bottom:13px;}
    .fg label{display:block;font-size:12px;font-weight:600;color:#555;margin-bottom:5px;}
    .fc{width:100%;padding:9px 12px;border:2px solid #dee2e6;border-radius:8px;font-size:13px;font-family:inherit;transition:.2s;}
    .fc:focus{outline:none;border-color:#004080;box-shadow:0 0 0 3px rgba(0,64,128,.1);}
    textarea.fc{resize:vertical;min-height:70px;}
    .frow{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
    .btn-submit{width:100%;padding:12px;background:linear-gradient(135deg,#004080,#0059b3);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:.25s;margin-top:4px;}
    .btn-submit:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,64,128,.35);}
    .btn-reset{width:100%;padding:10px;background:#f0f0f0;color:#555;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;margin-top:8px;}
    .btn-reset:hover{background:#e0e0e0;}
    .alert{padding:10px 13px;border-radius:8px;font-size:13px;margin-bottom:12px;display:none;}
    .alert-ok{background:#d4edda;color:#155724;border:1px solid #c3e6cb;}
    .alert-err{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;}
    .toolbar{display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap;align-items:center;}
    .toolbar select,.toolbar input{padding:9px 13px;border:2px solid #dee2e6;border-radius:8px;font-size:13px;font-family:inherit;}
    .toolbar input{flex:1;min-width:160px;}
    .toolbar select:focus,.toolbar input:focus{outline:none;border-color:#004080;}
    .btn-refresh{padding:9px 16px;background:linear-gradient(135deg,#004080,#0059b3);color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;transition:.2s;}
    .btn-refresh:hover{transform:translateY(-1px);}
    /* PROGRAM GRID */
    .pgrid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;}
    .pcard{background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.07);transition:.25s;}
    .pcard:hover{transform:translateY(-5px);box-shadow:0 10px 28px rgba(0,64,128,.18);}
    .pcard-banner{height:5px;}
    .banner-blue{background:linear-gradient(90deg,#004080,#0059b3);}
    .banner-green{background:linear-gradient(90deg,#28a745,#20c997);}
    .banner-orange{background:linear-gradient(90deg,#fd7e14,#ffc107);}
    .banner-purple{background:linear-gradient(90deg,#6f42c1,#e83e8c);}
    .banner-red{background:linear-gradient(90deg,#dc3545,#c82333);}
    .banner-teal{background:linear-gradient(90deg,#17a2b8,#138496);}
    .pcard-body{padding:14px;}
    .pcard-icon{width:40px;height:40px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:17px;color:#fff;margin-bottom:10px;}
    .icon-blue{background:linear-gradient(135deg,#004080,#0059b3);}
    .icon-green{background:linear-gradient(135deg,#28a745,#20c997);}
    .icon-orange{background:linear-gradient(135deg,#fd7e14,#ffc107);}
    .icon-purple{background:linear-gradient(135deg,#6f42c1,#e83e8c);}
    .icon-red{background:linear-gradient(135deg,#dc3545,#c82333);}
    .icon-teal{background:linear-gradient(135deg,#17a2b8,#138496);}
    .pcard-title{font-weight:700;color:#222;font-size:13px;margin-bottom:2px;line-height:1.3;}
    .pcard-code{color:#aaa;font-size:10px;margin-bottom:6px;}
    .pcard-desc{color:#888;font-size:11px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;margin-bottom:8px;}
    .pcard-meta{font-size:10px;color:#ccc;margin-bottom:9px;display:flex;flex-direction:column;gap:3px;}
    .pcard-meta span{display:flex;align-items:center;gap:4px;}
    .pcard-meta i{color:#004080;}
    .pbdg{padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;display:inline-block;margin-bottom:8px;}
    .pbdg-active{background:#d4edda;color:#155724;}
    .pbdg-inactive{background:#e2e3e5;color:#383d41;}
    .pbdg-upcoming{background:#fff3cd;color:#856404;}
    .pcard-actions{display:flex;gap:7px;}
    .pbtn{flex:1;padding:6px;border:none;border-radius:7px;cursor:pointer;font-size:11px;font-weight:600;transition:.2s;display:flex;align-items:center;justify-content:center;gap:4px;}
    .pbtn-edit{background:#fff3cd;color:#856404;}
    .pbtn-edit:hover{background:#ffc107;color:#fff;}
    .pbtn-del{background:#f8d7da;color:#721c24;}
    .pbtn-del:hover{background:#dc3545;color:#fff;}
    .empty{text-align:center;padding:60px 20px;color:#ccc;grid-column:1/-1;}
    .empty i{font-size:56px;display:block;margin-bottom:12px;}
    .toast{position:fixed;bottom:22px;right:22px;color:white;padding:11px 18px;border-radius:9px;font-size:13px;font-weight:700;z-index:99999;display:none;}
    .toast.ok{background:#28a745;}
    .toast.err{background:#dc3545;}
    footer{background:#00264d;color:white;text-align:center;padding:12px;font-size:13px;}
    @media(max-width:860px){.layout{grid-template-columns:1fr;}.stats{grid-template-columns:repeat(2,1fr);}}
  </style>
</head>
<body>
<div class="top-bar"><marquee>Program Manager — Create and manage academic programs for the SCTI website</marquee></div>
<div class="pg-header">
  <div>
    <h1><i class="fa fa-graduation-cap"></i> Program Manager</h1>
    <div class="bc"><a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Programs</div>
  </div>
  <div class="hdr-btns">
    <a href="../dashboards/admin-dashboard.php" class="btn-hdr ghost"><i class="fa fa-arrow-left"></i> Back</a>
  </div>
</div>
<div class="stats">
  <div class="stat"><div class="stat-ico ico-blue"><i class="fa fa-graduation-cap"></i></div><div><div class="stat-val" id="sTotal">—</div><div class="stat-lbl">Total Programs</div></div></div>
  <div class="stat"><div class="stat-ico ico-green"><i class="fa fa-check-circle"></i></div><div><div class="stat-val" id="sActive">—</div><div class="stat-lbl">Active</div></div></div>
  <div class="stat"><div class="stat-ico ico-orange"><i class="fa fa-clock"></i></div><div><div class="stat-val" id="sUpcoming">—</div><div class="stat-lbl">Upcoming</div></div></div>
  <div class="stat"><div class="stat-ico ico-grey"><i class="fa fa-archive"></i></div><div><div class="stat-val" id="sInactive">—</div><div class="stat-lbl">Inactive</div></div></div>
</div>
<div class="layout">
  <div class="panel">
    <h3 id="formTitle"><i class="fa fa-plus-circle"></i> Add Program</h3>
    <div id="formAlert" class="alert"></div>
    <input type="hidden" id="fId" value="0">
    <div class="fg"><label>Title *</label><input type="text" id="fTitle" class="fc" placeholder="Program title..."></div>
    <div class="frow">
      <div class="fg"><label>Code *</label><input type="text" id="fCode" class="fc" placeholder="e.g. BTIT"></div>
      <div class="fg">
        <label>Color</label>
        <select id="fColor" class="fc">
          <option value="blue">Blue</option>
          <option value="green">Green</option>
          <option value="orange">Orange</option>
          <option value="purple">Purple</option>
          <option value="red">Red</option>
          <option value="teal">Teal</option>
        </select>
      </div>
    </div>
    <div class="fg">
      <label>Icon (Font Awesome class)</label>
      <select id="fIcon" class="fc">
        <option value="fa-graduation-cap">Graduation Cap</option>
        <option value="fa-laptop-code">Laptop Code</option>
        <option value="fa-hard-hat">Hard Hat</option>
        <option value="fa-building">Building</option>
        <option value="fa-paw">Paw</option>
        <option value="fa-bolt">Bolt</option>
        <option value="fa-tools">Tools</option>
        <option value="fa-flask">Flask</option>
        <option value="fa-book">Book</option>
        <option value="fa-stethoscope">Stethoscope</option>
      </select>
    </div>
    <div class="frow">
      <div class="fg"><label>Duration</label><input type="text" id="fDuration" class="fc" placeholder="e.g. 3 years"></div>
      <div class="fg"><label>Affiliation</label><input type="text" id="fAffil" class="fc" placeholder="e.g. CTEVT"></div>
    </div>
    <div class="fg"><label>Assessment</label><input type="text" id="fAssess" class="fc" placeholder="e.g. 50% Internal + 50% External"></div>
    <div class="fg"><label>Description</label><textarea id="fDesc" class="fc" placeholder="Short description..."></textarea></div>
    <div class="fg"><label>Course Content (pipe-separated)</label><textarea id="fContent" class="fc" placeholder="Topic 1|Topic 2|Topic 3"></textarea></div>
    <div class="fg">
      <label>Status</label>
      <select id="fStat" class="fc">
        <option value="active">Active</option>
        <option value="upcoming">Upcoming</option>
        <option value="inactive">Inactive</option>
      </select>
    </div>
    <button class="btn-submit" onclick="saveProgram()"><i class="fa fa-save"></i> <span id="btnTxt">Save Program</span></button>
    <button class="btn-reset" onclick="resetForm()"><i class="fa fa-times"></i> Cancel / Reset</button>
  </div>
  <div class="content">
    <div class="toolbar">
      <select id="filterStatus" onchange="loadPrograms()">
        <option value="all">All Status</option>
        <option value="active">Active</option>
        <option value="upcoming">Upcoming</option>
        <option value="inactive">Inactive</option>
      </select>
      <input type="text" id="searchInput" placeholder="Search programs..." oninput="filterLocal()">
      <button class="btn-refresh" onclick="loadPrograms()"><i class="fa fa-sync"></i> Refresh</button>
    </div>
    <div class="pgrid" id="pgrid">
      <div class="empty"><i class="fa fa-spinner fa-spin"></i><p>Loading...</p></div>
    </div>
  </div>
</div>
<div class="toast" id="toast"></div>
<footer>© 2025 SCTI — Admin Panel</footer>
<script>
var allPrograms = [];
function loadPrograms() {
  var status = document.getElementById('filterStatus').value;
  var grid = document.getElementById('pgrid');
  grid.innerHTML = '<div class="empty"><i class="fa fa-spinner fa-spin"></i><p>Loading...</p></div>';
  fetch('program-list.php?status=' + encodeURIComponent(status))
    .then(function(r){ return r.text(); })
    .then(function(txt){
      var d; try { d=JSON.parse(txt); } catch(e){ grid.innerHTML='<div class="empty"><i class="fa fa-exclamation-triangle"></i><p>Parse error</p></div>'; return; }
      if (!d.success) { grid.innerHTML='<div class="empty"><i class="fa fa-exclamation-triangle"></i><p>'+(d.message||'Error')+'</p></div>'; return; }
      allPrograms = d.programs||[];
      updateStats(); renderGrid(allPrograms);
    })
    .catch(function(e){ grid.innerHTML='<div class="empty"><i class="fa fa-exclamation-triangle"></i><p>'+e.message+'</p></div>'; });
}
function updateStats() {
  document.getElementById('sTotal').textContent    = allPrograms.length;
  document.getElementById('sActive').textContent   = allPrograms.filter(function(p){return p.status==='active';}).length;
  document.getElementById('sUpcoming').textContent = allPrograms.filter(function(p){return p.status==='upcoming';}).length;
  document.getElementById('sInactive').textContent = allPrograms.filter(function(p){return p.status==='inactive';}).length;
}
function filterLocal() {
  var q = document.getElementById('searchInput').value.toLowerCase();
  var list = allPrograms.filter(function(p){ return !q || (p.title||'').toLowerCase().indexOf(q)>-1 || (p.code||'').toLowerCase().indexOf(q)>-1; });
  renderGrid(list);
}
function renderGrid(list) {
  var grid = document.getElementById('pgrid');
  if (!list.length) { grid.innerHTML='<div class="empty"><i class="fa fa-graduation-cap"></i><p>No programs found</p></div>'; return; }
  grid.innerHTML = list.map(buildCard).join('');
}
function buildCard(p) {
  var color = p.color||'blue';
  var icon  = p.icon||'fa-graduation-cap';
  var bdgCls = p.status==='active' ? 'pbdg-active' : p.status==='upcoming' ? 'pbdg-upcoming' : 'pbdg-inactive';
  var bdgLbl = cap(p.status);
  return '<div class="pcard">'
    +'<div class="pcard-banner banner-'+color+'"></div>'
    +'<div class="pcard-body">'
    +'<div class="pcard-icon icon-'+color+'"><i class="fa '+icon+'"></i></div>'
    +'<div class="pcard-title">'+esc(p.title)+'</div>'
    +'<div class="pcard-code">'+esc(p.code)+' | '+esc(p.affiliation||'')+'</div>'
    +'<div class="pcard-desc">'+esc(p.description||'')+'</div>'
    +'<div class="pcard-meta">'
    +'<span><i class="fa fa-clock"></i> '+esc(p.duration||'')+'</span>'
    +'<span><i class="fa fa-chart-bar"></i> '+esc(p.assessment||'')+'</span>'
    +'</div>'
    +'<span class="pbdg '+bdgCls+'">'+bdgLbl+'</span>'
    +'<div class="pcard-actions">'
    +'<button class="pbtn pbtn-edit" onclick="editProgram('+p.id+')"><i class="fa fa-edit"></i> Edit</button>'
    +'<button class="pbtn pbtn-del"  onclick="deleteProgram('+p.id+')"><i class="fa fa-trash"></i> Del</button>'
    +'</div>'
    +'</div></div>';
}
function resetForm() {
  document.getElementById('fId').value='0';
  document.getElementById('fTitle').value='';
  document.getElementById('fCode').value='';
  document.getElementById('fColor').value='blue';
  document.getElementById('fIcon').value='fa-graduation-cap';
  document.getElementById('fDuration').value='';
  document.getElementById('fAffil').value='';
  document.getElementById('fAssess').value='';
  document.getElementById('fDesc').value='';
  document.getElementById('fContent').value='';
  document.getElementById('fStat').value='active';
  document.getElementById('formTitle').innerHTML='<i class="fa fa-plus-circle"></i> Add Program';
  document.getElementById('btnTxt').textContent='Save Program';
  document.getElementById('formAlert').style.display='none';
}
function editProgram(id) {
  var p = allPrograms.find(function(x){ return String(x.id)===String(id); });
  if (!p) { toast('Not found','err'); return; }
  document.getElementById('fId').value=p.id;
  document.getElementById('fTitle').value=p.title||'';
  document.getElementById('fCode').value=p.code||'';
  document.getElementById('fColor').value=p.color||'blue';
  document.getElementById('fIcon').value=p.icon||'fa-graduation-cap';
  document.getElementById('fDuration').value=p.duration||'';
  document.getElementById('fAffil').value=p.affiliation||'';
  document.getElementById('fAssess').value=p.assessment||'';
  document.getElementById('fDesc').value=p.description||'';
  document.getElementById('fContent').value=p.content||'';
  document.getElementById('fStat').value=p.status||'active';
  document.getElementById('formTitle').innerHTML='<i class="fa fa-edit"></i> Edit Program';
  document.getElementById('btnTxt').textContent='Update Program';
  document.querySelector('.panel').scrollTop=0;
}
function saveProgram() {
  var payload = {
    id: parseInt(document.getElementById('fId').value),
    title: document.getElementById('fTitle').value.trim(),
    code: document.getElementById('fCode').value.trim(),
    color: document.getElementById('fColor').value,
    icon: document.getElementById('fIcon').value,
    duration: document.getElementById('fDuration').value.trim(),
    affiliation: document.getElementById('fAffil').value.trim(),
    assessment: document.getElementById('fAssess').value.trim(),
    description: document.getElementById('fDesc').value.trim(),
    content: document.getElementById('fContent').value.trim(),
    status: document.getElementById('fStat').value
  };
  if (!payload.title||!payload.code) { showAlert('Title and code required','err'); return; }
  fetch('program-save.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)})
    .then(function(r){return r.text();})
    .then(function(txt){
      var d; try{d=JSON.parse(txt);}catch(e){showAlert('Server error','err');return;}
      if(d.success){showAlert(d.message,'ok');resetForm();loadPrograms();}
      else showAlert(d.message||'Failed','err');
    })
    .catch(function(e){showAlert('Network error: '+e.message,'err');});
}
function deleteProgram(id) {
  if (!confirm('Delete this program?')) return;
  fetch('program-delete.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id:parseInt(id)})})
    .then(function(r){return r.text();})
    .then(function(txt){
      var d; try{d=JSON.parse(txt);}catch(e){toast('Server error','err');return;}
      if(d.success){toast('Program deleted','ok');loadPrograms();}
      else toast(d.message||'Failed','err');
    })
    .catch(function(e){toast('Network error: '+e.message,'err');});
}
function showAlert(msg,type){var el=document.getElementById('formAlert');el.className='alert alert-'+type;el.textContent=msg;el.style.display='block';setTimeout(function(){el.style.display='none';},4000);}
function toast(msg,type){var t=document.getElementById('toast');t.textContent=msg;t.className='toast '+(type||'ok');t.style.display='block';setTimeout(function(){t.style.display='none';},3000);}
function esc(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
function cap(s){return s?s.charAt(0).toUpperCase()+s.slice(1):'';}
loadPrograms();
</script>
</body>
</html>
