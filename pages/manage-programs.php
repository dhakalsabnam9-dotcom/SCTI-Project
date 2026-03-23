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
  <title>Course Manager | SCTI Admin</title>
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
    .btn-hdr.white{background:#fff;color:#004080;}
    .btn-hdr.white:hover{background:#e8f0fe;}
    .btn-hdr.ghost{background:rgba(255,255,255,.18);color:#fff;}
    .btn-hdr.ghost:hover{background:rgba(255,255,255,.32);}
    /* STATS */
    .stats{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;padding:18px 28px;background:#fff;border-bottom:1px solid #e9ecef;}
    .stat{display:flex;align-items:center;gap:12px;background:#f8f9fa;border-radius:10px;padding:12px 16px;cursor:pointer;transition:.2s;}
    .stat:hover{background:#e8f0fe;}
    .stat.active-filter{background:#e8f0fe;outline:2px solid #004080;}
    .stat-ico{width:42px;height:42px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:17px;color:#fff;flex-shrink:0;}
    .ico-blue{background:linear-gradient(135deg,#004080,#0059b3);}
    .ico-green{background:linear-gradient(135deg,#28a745,#20c997);}
    .ico-orange{background:linear-gradient(135deg,#fd7e14,#ffc107);}
    .ico-grey{background:linear-gradient(135deg,#6c757d,#495057);}
    .ico-gold{background:linear-gradient(135deg,#f59e0b,#d97706);}
    .stat-val{font-size:24px;font-weight:700;color:#222;line-height:1;}
    .stat-lbl{font-size:11px;color:#999;margin-top:2px;}
    /* LAYOUT */
    .layout{display:grid;grid-template-columns:380px 1fr;min-height:calc(100vh - 185px);}
    .panel{background:#fff;border-right:1px solid #e9ecef;padding:0;overflow-y:auto;display:flex;flex-direction:column;}
    .panel-tabs{display:flex;border-bottom:2px solid #f0f0f0;}
    .ptab{flex:1;padding:13px 8px;text-align:center;font-size:12px;font-weight:600;cursor:pointer;color:#999;border-bottom:3px solid transparent;margin-bottom:-2px;transition:.2s;display:flex;align-items:center;justify-content:center;gap:5px;}
    .ptab.active{color:#004080;border-bottom-color:#004080;}
    .ptab:hover:not(.active){color:#555;}
    .panel-body{padding:20px;flex:1;overflow-y:auto;}
    .tab-pane{display:none;}
    .tab-pane.active{display:block;}
    .content{padding:22px;overflow-y:auto;}
    /* FORM */
    .fg{margin-bottom:13px;}
    .fg label{display:block;font-size:12px;font-weight:600;color:#555;margin-bottom:5px;}
    .fc{width:100%;padding:9px 12px;border:2px solid #dee2e6;border-radius:8px;font-size:13px;font-family:inherit;transition:.2s;}
    .fc:focus{outline:none;border-color:#004080;box-shadow:0 0 0 3px rgba(0,64,128,.1);}
    textarea.fc{resize:vertical;min-height:65px;}
    .frow{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
    .frow3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;}
    .btn-submit{width:100%;padding:12px;background:linear-gradient(135deg,#004080,#0059b3);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:.25s;margin-top:4px;}
    .btn-submit:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,64,128,.35);}
    .btn-reset{width:100%;padding:10px;background:#f0f0f0;color:#555;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;margin-top:8px;}
    .btn-reset:hover{background:#e0e0e0;}
    .alert{padding:10px 13px;border-radius:8px;font-size:13px;margin-bottom:12px;display:none;}
    .alert-ok{background:#d4edda;color:#155724;border:1px solid #c3e6cb;}
    .alert-err{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;}
    .toggle-row{display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:#f8f9fa;border-radius:8px;margin-bottom:13px;}
    .toggle-row label{font-size:13px;font-weight:600;color:#333;}
    .toggle{position:relative;width:44px;height:24px;}
    .toggle input{opacity:0;width:0;height:0;}
    .slider{position:absolute;inset:0;background:#ccc;border-radius:24px;cursor:pointer;transition:.3s;}
    .slider:before{content:'';position:absolute;width:18px;height:18px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.3s;}
    input:checked+.slider{background:#004080;}
    input:checked+.slider:before{transform:translateX(20px);}
    /* TOOLBAR */
    .toolbar{display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap;align-items:center;}
    .toolbar select,.toolbar input{padding:9px 13px;border:2px solid #dee2e6;border-radius:8px;font-size:13px;font-family:inherit;}
    .toolbar input{flex:1;min-width:160px;}
    .toolbar select:focus,.toolbar input:focus{outline:none;border-color:#004080;}
    .btn-refresh{padding:9px 16px;background:linear-gradient(135deg,#004080,#0059b3);color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;transition:.2s;}
    .btn-refresh:hover{transform:translateY(-1px);}
    /* COURSE GRID */
    .pgrid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:18px;}
    .pcard{background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);transition:.3s;position:relative;}
    .pcard:hover{transform:translateY(-6px);box-shadow:0 14px 32px rgba(0,64,128,.18);}
    .pcard-banner{height:6px;}
    .banner-blue{background:linear-gradient(90deg,#004080,#0059b3);}
    .banner-green{background:linear-gradient(90deg,#28a745,#20c997);}
    .banner-orange{background:linear-gradient(90deg,#fd7e14,#ffc107);}
    .banner-purple{background:linear-gradient(90deg,#6f42c1,#e83e8c);}
    .banner-red{background:linear-gradient(90deg,#dc3545,#c82333);}
    .banner-teal{background:linear-gradient(90deg,#17a2b8,#138496);}
    .pcard-img{height:120px;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;}
    .pcard-icon-big{font-size:48px;color:white;position:relative;z-index:1;filter:drop-shadow(0 4px 8px rgba(0,0,0,.3));}
    .featured-badge{position:absolute;top:10px;right:10px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;display:flex;align-items:center;gap:4px;z-index:2;}
    .status-dot{position:absolute;top:10px;left:10px;width:10px;height:10px;border-radius:50%;border:2px solid rgba(255,255,255,.8);z-index:2;}
    .dot-active{background:#28a745;}
    .dot-upcoming{background:#ffc107;}
    .dot-inactive{background:#6c757d;}
    .pcard-body{padding:14px;}
    .pcard-title{font-weight:700;color:#1a202c;font-size:14px;margin-bottom:2px;line-height:1.3;}
    .pcard-code{color:#aaa;font-size:10px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;margin-bottom:8px;}
    .pcard-desc{color:#888;font-size:11px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;margin-bottom:10px;line-height:1.5;}
    .pcard-pills{display:flex;flex-wrap:wrap;gap:5px;margin-bottom:10px;}
    .pill{padding:3px 9px;border-radius:20px;font-size:10px;font-weight:600;}
    .pill-blue{background:#e8f0fe;color:#004080;}
    .pill-green{background:#d4edda;color:#155724;}
    .pill-orange{background:#fff3cd;color:#856404;}
    .pill-grey{background:#e2e3e5;color:#383d41;}
    .pill-gold{background:#fef3c7;color:#92400e;}
    .pcard-stats{display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-bottom:10px;}
    .pstat{background:#f8f9fa;border-radius:7px;padding:7px 10px;text-align:center;}
    .pstat-val{font-size:15px;font-weight:700;color:#004080;}
    .pstat-lbl{font-size:9px;color:#aaa;margin-top:1px;}
    .pcard-actions{display:flex;gap:6px;}
    .pbtn{flex:1;padding:7px;border:none;border-radius:8px;cursor:pointer;font-size:11px;font-weight:600;transition:.2s;display:flex;align-items:center;justify-content:center;gap:4px;}
    .pbtn-view{background:#e8f0fe;color:#004080;}
    .pbtn-view:hover{background:#004080;color:#fff;}
    .pbtn-edit{background:#fff3cd;color:#856404;}
    .pbtn-edit:hover{background:#ffc107;color:#fff;}
    .pbtn-del{background:#f8d7da;color:#721c24;flex:0;padding:7px 10px;}
    .pbtn-del:hover{background:#dc3545;color:#fff;}
    .empty{text-align:center;padding:60px 20px;color:#ccc;grid-column:1/-1;}
    .empty i{font-size:56px;display:block;margin-bottom:12px;}
    /* MODAL */
    .modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;}
    .modal.on{display:flex;}
    .mbox{background:#fff;border-radius:14px;width:90%;max-width:560px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 50px rgba(0,0,0,.2);}
    .mhead{display:flex;justify-content:space-between;align-items:center;padding:18px 22px;border-bottom:2px solid #f0f0f0;}
    .mhead h3{margin:0;color:#004080;font-size:16px;display:flex;align-items:center;gap:8px;}
    .mclose{background:none;border:none;font-size:22px;cursor:pointer;color:#bbb;line-height:1;}
    .mclose:hover{color:#333;}
    .mbody{padding:22px;}
    .btn-save{width:100%;padding:11px;background:linear-gradient(135deg,#004080,#0059b3);color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:.2s;}
    .btn-save:hover{transform:translateY(-1px);box-shadow:0 5px 14px rgba(0,64,128,.3);}
    .btn-cancel{width:100%;padding:11px;background:#f0f0f0;color:#555;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;margin-top:8px;}
    .btn-cancel:hover{background:#e0e0e0;}
    /* VIEW MODAL */
    .view-hero{padding:24px 22px 16px;display:flex;align-items:center;gap:16px;}
    .view-icon{width:68px;height:68px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:30px;color:white;flex-shrink:0;}
    .view-title{font-size:20px;font-weight:800;color:#1a202c;margin-bottom:4px;}
    .view-sub{font-size:12px;color:#aaa;}
    .view-divider{height:1px;background:#f0f0f0;margin:0 22px;}
    .view-grid{display:grid;grid-template-columns:1fr 1fr;gap:0;padding:0 22px;}
    .view-item{padding:12px 0;border-bottom:1px solid #f5f5f5;display:flex;flex-direction:column;gap:3px;}
    .view-item-lbl{font-size:10px;color:#aaa;font-weight:600;text-transform:uppercase;letter-spacing:.5px;}
    .view-item-val{font-size:13px;color:#333;font-weight:600;}
    .view-topics{padding:14px 22px 22px;}
    .view-topics h4{font-size:12px;color:#aaa;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;}
    .topic-chip{display:inline-block;background:#e8f0fe;color:#004080;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;margin:3px;}
    .toast{position:fixed;bottom:22px;right:22px;color:white;padding:11px 18px;border-radius:9px;font-size:13px;font-weight:700;z-index:99999;display:none;}
    .toast.ok{background:#28a745;}
    .toast.err{background:#dc3545;}
    footer{background:#00264d;color:white;text-align:center;padding:12px;font-size:13px;}
    @media(max-width:900px){.layout{grid-template-columns:1fr;}.stats{grid-template-columns:repeat(3,1fr);}}
  </style>
</head>
<body>
<div class="top-bar"><marquee>Course Manager — Create and manage academic programs for SCTI</marquee></div>
<div class="pg-header">
  <div>
    <h1><i class="fa fa-graduation-cap"></i> Course Manager</h1>
    <div class="bc"><a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Courses</div>
  </div>
  <div class="hdr-btns">
    <button class="btn-hdr white" onclick="switchTab('add')"><i class="fa fa-plus"></i> Add Course</button>
    <a href="../dashboards/admin-dashboard.php" class="btn-hdr ghost"><i class="fa fa-arrow-left"></i> Back</a>
  </div>
</div>

<!-- STATS -->
<div class="stats">
  <div class="stat" onclick="filterByStatus('all')" id="stat-all">
    <div class="stat-ico ico-blue"><i class="fa fa-graduation-cap"></i></div>
    <div><div class="stat-val" id="sTotal">—</div><div class="stat-lbl">Total Courses</div></div>
  </div>
  <div class="stat" onclick="filterByStatus('active')" id="stat-active">
    <div class="stat-ico ico-green"><i class="fa fa-check-circle"></i></div>
    <div><div class="stat-val" id="sActive">—</div><div class="stat-lbl">Active</div></div>
  </div>
  <div class="stat" onclick="filterByStatus('upcoming')" id="stat-upcoming">
    <div class="stat-ico ico-orange"><i class="fa fa-clock"></i></div>
    <div><div class="stat-val" id="sUpcoming">—</div><div class="stat-lbl">Upcoming</div></div>
  </div>
  <div class="stat" onclick="filterByStatus('inactive')" id="stat-inactive">
    <div class="stat-ico ico-grey"><i class="fa fa-archive"></i></div>
    <div><div class="stat-val" id="sInactive">—</div><div class="stat-lbl">Inactive</div></div>
  </div>
  <div class="stat" onclick="filterFeatured()" id="stat-featured">
    <div class="stat-ico ico-gold"><i class="fa fa-star"></i></div>
    <div><div class="stat-val" id="sFeatured">—</div><div class="stat-lbl">Featured</div></div>
  </div>
</div>

<div class="layout">
  <!-- LEFT PANEL -->
  <div class="panel">
    <div class="panel-tabs">
      <div class="ptab active" id="tab-add" onclick="switchTab('add')"><i class="fa fa-plus-circle"></i> Add Course</div>
      <div class="ptab" id="tab-detail" onclick="switchTab('detail')"><i class="fa fa-eye"></i> Details</div>
    </div>
    <div class="panel-body">

      <!-- ADD TAB -->
      <div class="tab-pane active" id="pane-add">
        <div id="formAlert" class="alert"></div>
        <input type="hidden" id="fId" value="0">
        <div class="fg"><label>Course Title *</label><input type="text" id="fTitle" class="fc" placeholder="e.g. Bachelor in IT"></div>
        <div class="frow">
          <div class="fg"><label>Code *</label><input type="text" id="fCode" class="fc" placeholder="e.g. BTIT"></div>
          <div class="fg"><label>Color Theme</label>
            <select id="fColor" class="fc">
              <option value="blue">Blue</option><option value="green">Green</option>
              <option value="orange">Orange</option><option value="purple">Purple</option>
              <option value="red">Red</option><option value="teal">Teal</option>
            </select>
          </div>
        </div>
        <div class="fg"><label>Icon</label>
          <select id="fIcon" class="fc">
            <option value="fa-graduation-cap">🎓 Graduation Cap</option>
            <option value="fa-laptop-code">💻 Laptop Code</option>
            <option value="fa-hard-hat">⛑ Hard Hat</option>
            <option value="fa-building">🏢 Building</option>
            <option value="fa-bolt">⚡ Bolt</option>
            <option value="fa-tools">🔧 Tools</option>
            <option value="fa-flask">🧪 Flask</option>
            <option value="fa-book">📚 Book</option>
            <option value="fa-stethoscope">🩺 Stethoscope</option>
            <option value="fa-calculator">🔢 Calculator</option>
            <option value="fa-paint-brush">🎨 Art</option>
            <option value="fa-leaf">🌿 Agriculture</option>
          </select>
        </div>
        <div class="frow">
          <div class="fg"><label>Duration</label><input type="text" id="fDuration" class="fc" placeholder="e.g. 3 years"></div>
          <div class="fg"><label>Semesters</label><input type="number" id="fSemesters" class="fc" placeholder="6" min="1" max="12"></div>
        </div>
        <div class="frow">
          <div class="fg"><label>Affiliation</label><input type="text" id="fAffil" class="fc" placeholder="e.g. CTEVT"></div>
          <div class="fg"><label>Seats</label><input type="number" id="fSeats" class="fc" placeholder="e.g. 40"></div>
        </div>
        <div class="frow">
          <div class="fg"><label>Fee</label><input type="text" id="fFee" class="fc" placeholder="e.g. Rs. 25,000/yr"></div>
          <div class="fg"><label>Assessment</label><input type="text" id="fAssess" class="fc" placeholder="50% Int + 50% Ext"></div>
        </div>
        <div class="fg"><label>Eligibility</label><input type="text" id="fEligibility" class="fc" placeholder="e.g. SEE passed with min 2.0 GPA"></div>
        <div class="fg"><label>Description</label><textarea id="fDesc" class="fc" placeholder="Short description..."></textarea></div>
        <div class="fg"><label>Course Topics <small style="color:#aaa">(pipe-separated)</small></label><textarea id="fContent" class="fc" placeholder="Topic 1|Topic 2|Topic 3"></textarea></div>
        <div class="frow">
          <div class="fg"><label>Status</label>
            <select id="fStat" class="fc">
              <option value="active">Active</option>
              <option value="upcoming">Upcoming</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          <div class="fg" style="display:flex;align-items:flex-end;padding-bottom:2px;">
            <div class="toggle-row" style="width:100%;margin-bottom:0;">
              <label><i class="fa fa-star" style="color:#f59e0b"></i> Featured</label>
              <label class="toggle"><input type="checkbox" id="fFeatured"><span class="slider"></span></label>
            </div>
          </div>
        </div>
        <button class="btn-submit" onclick="saveProgram()"><i class="fa fa-save"></i> <span id="btnTxt">Save Course</span></button>
        <button class="btn-reset" onclick="resetForm()"><i class="fa fa-times"></i> Cancel / Reset</button>
      </div>

      <!-- DETAIL TAB -->
      <div class="tab-pane" id="pane-detail">
        <div id="detailPane" style="text-align:center;padding:50px 20px;color:#ccc;">
          <i class="fa fa-graduation-cap" style="font-size:52px;display:block;margin-bottom:12px;"></i>
          <p>Click a course card to view details</p>
        </div>
      </div>

    </div>
  </div>

  <!-- RIGHT CONTENT -->
  <div class="content">
    <div class="toolbar">
      <select id="filterStatus" onchange="loadPrograms()">
        <option value="all">All Status</option>
        <option value="active">Active</option>
        <option value="upcoming">Upcoming</option>
        <option value="inactive">Inactive</option>
      </select>
      <input type="text" id="searchInput" placeholder="Search courses..." oninput="filterLocal()">
      <button class="btn-refresh" onclick="loadPrograms()"><i class="fa fa-sync"></i> Refresh</button>
    </div>
    <div class="pgrid" id="pgrid">
      <div class="empty"><i class="fa fa-spinner fa-spin"></i><p>Loading...</p></div>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal" id="editModal">
  <div class="mbox">
    <div class="mhead"><h3><i class="fa fa-edit"></i> Edit Course</h3><button class="mclose" onclick="closeMod('editModal')">×</button></div>
    <div class="mbody">
      <div id="editAlert" class="alert"></div>
      <input type="hidden" id="eId">
      <div class="fg"><label>Course Title *</label><input type="text" id="eTitle" class="fc"></div>
      <div class="frow">
        <div class="fg"><label>Code *</label><input type="text" id="eCode" class="fc"></div>
        <div class="fg"><label>Color</label>
          <select id="eColor" class="fc">
            <option value="blue">Blue</option><option value="green">Green</option>
            <option value="orange">Orange</option><option value="purple">Purple</option>
            <option value="red">Red</option><option value="teal">Teal</option>
          </select>
        </div>
      </div>
      <div class="fg"><label>Icon</label>
        <select id="eIcon" class="fc">
          <option value="fa-graduation-cap">🎓 Graduation Cap</option>
          <option value="fa-laptop-code">💻 Laptop Code</option>
          <option value="fa-hard-hat">⛑ Hard Hat</option>
          <option value="fa-building">🏢 Building</option>
          <option value="fa-bolt">⚡ Bolt</option>
          <option value="fa-tools">🔧 Tools</option>
          <option value="fa-flask">🧪 Flask</option>
          <option value="fa-book">📚 Book</option>
          <option value="fa-stethoscope">🩺 Stethoscope</option>
          <option value="fa-calculator">🔢 Calculator</option>
          <option value="fa-paint-brush">🎨 Art</option>
          <option value="fa-leaf">🌿 Agriculture</option>
        </select>
      </div>
      <div class="frow">
        <div class="fg"><label>Duration</label><input type="text" id="eDuration" class="fc"></div>
        <div class="fg"><label>Semesters</label><input type="number" id="eSemesters" class="fc" min="1" max="12"></div>
      </div>
      <div class="frow">
        <div class="fg"><label>Affiliation</label><input type="text" id="eAffil" class="fc"></div>
        <div class="fg"><label>Seats</label><input type="number" id="eSeats" class="fc"></div>
      </div>
      <div class="frow">
        <div class="fg"><label>Fee</label><input type="text" id="eFee" class="fc"></div>
        <div class="fg"><label>Assessment</label><input type="text" id="eAssess" class="fc"></div>
      </div>
      <div class="fg"><label>Eligibility</label><input type="text" id="eEligibility" class="fc"></div>
      <div class="fg"><label>Description</label><textarea id="eDesc" class="fc"></textarea></div>
      <div class="fg"><label>Course Topics <small style="color:#aaa">(pipe-separated)</small></label><textarea id="eContent" class="fc"></textarea></div>
      <div class="frow">
        <div class="fg"><label>Status</label>
          <select id="eStat" class="fc">
            <option value="active">Active</option>
            <option value="upcoming">Upcoming</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div class="fg" style="display:flex;align-items:flex-end;padding-bottom:2px;">
          <div class="toggle-row" style="width:100%;margin-bottom:0;">
            <label><i class="fa fa-star" style="color:#f59e0b"></i> Featured</label>
            <label class="toggle"><input type="checkbox" id="eFeatured"><span class="slider"></span></label>
          </div>
        </div>
      </div>
      <button class="btn-save" onclick="updateProgram()"><i class="fa fa-save"></i> Save Changes</button>
      <button class="btn-cancel" onclick="closeMod('editModal')">Cancel</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>
<footer>© 2025 SCTI — Admin Panel</footer>

<script>
var allPrograms = [];
var currentFilter = 'all';
var showFeaturedOnly = false;
var gradMap = {
  blue:'linear-gradient(135deg,#004080,#0059b3)',
  green:'linear-gradient(135deg,#28a745,#20c997)',
  orange:'linear-gradient(135deg,#fd7e14,#ffc107)',
  purple:'linear-gradient(135deg,#6f42c1,#e83e8c)',
  red:'linear-gradient(135deg,#dc3545,#c82333)',
  teal:'linear-gradient(135deg,#17a2b8,#138496)'
};

function openMod(id)  { document.getElementById(id).classList.add('on'); }
function closeMod(id) { document.getElementById(id).classList.remove('on'); }
function switchTab(tab) {
  document.querySelectorAll('.ptab').forEach(function(t){ t.classList.remove('active'); });
  document.querySelectorAll('.tab-pane').forEach(function(p){ p.classList.remove('active'); });
  document.getElementById('tab-'+tab).classList.add('active');
  document.getElementById('pane-'+tab).classList.add('active');
}

function filterByStatus(status) {
  showFeaturedOnly = false;
  currentFilter = status;
  document.getElementById('filterStatus').value = status === 'all' ? 'all' : status;
  document.querySelectorAll('.stat').forEach(function(s){ s.classList.remove('active-filter'); });
  document.getElementById('stat-' + status).classList.add('active-filter');
  loadPrograms();
}
function filterFeatured() {
  showFeaturedOnly = !showFeaturedOnly;
  document.querySelectorAll('.stat').forEach(function(s){ s.classList.remove('active-filter'); });
  if (showFeaturedOnly) document.getElementById('stat-featured').classList.add('active-filter');
  filterLocal();
}

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
      updateStats();
      filterLocal();
    })
    .catch(function(e){ grid.innerHTML='<div class="empty"><i class="fa fa-exclamation-triangle"></i><p>'+e.message+'</p></div>'; });
}

function updateStats() {
  document.getElementById('sTotal').textContent    = allPrograms.length;
  document.getElementById('sActive').textContent   = allPrograms.filter(function(p){return p.status==='active';}).length;
  document.getElementById('sUpcoming').textContent = allPrograms.filter(function(p){return p.status==='upcoming';}).length;
  document.getElementById('sInactive').textContent = allPrograms.filter(function(p){return p.status==='inactive';}).length;
  document.getElementById('sFeatured').textContent = allPrograms.filter(function(p){return parseInt(p.featured);}).length;
}

function filterLocal() {
  var q = document.getElementById('searchInput').value.toLowerCase();
  var list = allPrograms.filter(function(p){
    if (showFeaturedOnly && !parseInt(p.featured)) return false;
    return !q || (p.title||'').toLowerCase().indexOf(q)>-1 || (p.code||'').toLowerCase().indexOf(q)>-1 || (p.description||'').toLowerCase().indexOf(q)>-1;
  });
  renderGrid(list);
}

function renderGrid(list) {
  var grid = document.getElementById('pgrid');
  if (!list.length) { grid.innerHTML='<div class="empty"><i class="fa fa-graduation-cap"></i><p>No courses found</p></div>'; return; }
  grid.innerHTML = list.map(buildCard).join('');
}

function buildCard(p) {
  var color  = p.color||'blue';
  var icon   = p.icon||'fa-graduation-cap';
  var grad   = gradMap[color]||gradMap.blue;
  var dotCls = p.status==='active'?'dot-active':p.status==='upcoming'?'dot-upcoming':'dot-inactive';
  var statusLabel = p.status==='active'?'Active':p.status==='upcoming'?'Upcoming':'Inactive';
  var statusPill  = p.status==='active'?'pill-green':p.status==='upcoming'?'pill-orange':'pill-grey';
  var featured = parseInt(p.featured);
  return '<div class="pcard">'
    +'<div class="pcard-banner banner-'+color+'"></div>'
    +'<div class="pcard-img" style="background:'+grad+'">'
    +'<div class="status-dot '+dotCls+'" title="'+statusLabel+'"></div>'
    +(featured?'<div class="featured-badge"><i class="fa fa-star"></i> Featured</div>':'')
    +'<i class="fa '+icon+' pcard-icon-big"></i>'
    +'</div>'
    +'<div class="pcard-body">'
    +'<div class="pcard-title">'+esc(p.title)+'</div>'
    +'<div class="pcard-code">'+esc(p.code)+(p.affiliation?' · '+esc(p.affiliation):'')+'</div>'
    +'<div class="pcard-desc">'+esc(p.description||'No description provided.')+'</div>'
    +'<div class="pcard-pills">'
    +'<span class="pill '+statusPill+'">'+statusLabel+'</span>'
    +(p.duration?'<span class="pill pill-blue"><i class="fa fa-clock"></i> '+esc(p.duration)+'</span>':'')
    +(p.semesters?'<span class="pill pill-blue">'+esc(p.semesters)+' Sem</span>':'')
    +(p.fee?'<span class="pill pill-gold">'+esc(p.fee)+'</span>':'')
    +'</div>'
    +'<div class="pcard-stats">'
    +'<div class="pstat"><div class="pstat-val">'+(p.seats||'—')+'</div><div class="pstat-lbl">Seats</div></div>'
    +'<div class="pstat"><div class="pstat-val">'+(p.semesters||'—')+'</div><div class="pstat-lbl">Semesters</div></div>'
    +'</div>'
    +'<div class="pcard-actions">'
    +'<button class="pbtn pbtn-view" onclick="viewProgram('+p.id+')"><i class="fa fa-eye"></i> View</button>'
    +'<button class="pbtn pbtn-edit" onclick="editProgram('+p.id+')"><i class="fa fa-edit"></i> Edit</button>'
    +'<button class="pbtn pbtn-del"  onclick="deleteProgram('+p.id+')" title="Delete"><i class="fa fa-trash"></i></button>'
    +'</div>'
    +'</div></div>';
}

function viewProgram(id) {
  var p = allPrograms.find(function(x){ return String(x.id)===String(id); });
  if (!p) return;
  switchTab('detail');
  var color = p.color||'blue';
  var grad  = gradMap[color]||gradMap.blue;
  var statusPill = p.status==='active'?'pill-green':p.status==='upcoming'?'pill-orange':'pill-grey';
  var topics = (p.content||'').split('|').filter(function(t){return t.trim();});
  document.getElementById('detailPane').innerHTML =
    '<div style="background:'+grad+';padding:20px;border-radius:10px;display:flex;align-items:center;gap:14px;margin-bottom:16px">'
    +'<div style="width:60px;height:60px;border-radius:14px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:26px;color:#fff"><i class="fa '+(p.icon||'fa-graduation-cap')+'"></i></div>'
    +'<div><div style="font-size:16px;font-weight:800;color:#fff">'+esc(p.title)+'</div>'
    +'<div style="font-size:11px;color:rgba(255,255,255,.8)">'+esc(p.code)+(p.affiliation?' · '+esc(p.affiliation):'')+'</div>'
    +(parseInt(p.featured)?'<span style="background:rgba(255,255,255,.25);color:#fff;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;margin-top:4px;display:inline-block"><i class="fa fa-star"></i> Featured</span>':'')
    +'</div></div>'
    +'<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:14px">'
    +detailItem('fa-clock','Duration',p.duration||'—')
    +detailItem('fa-layer-group','Semesters',(p.semesters||'—')+' semesters')
    +detailItem('fa-university','Affiliation',p.affiliation||'—')
    +detailItem('fa-users','Seats',p.seats||'—')
    +detailItem('fa-money-bill','Fee',p.fee||'—')
    +detailItem('fa-chart-bar','Assessment',p.assessment||'—')
    +'</div>'
    +(p.eligibility?'<div style="background:#f8f9fa;border-radius:8px;padding:10px 12px;margin-bottom:12px;font-size:12px;color:#555"><strong style="color:#004080"><i class="fa fa-check-circle"></i> Eligibility:</strong> '+esc(p.eligibility)+'</div>':'')
    +(p.description?'<div style="background:#f8f9fa;border-radius:8px;padding:10px 12px;margin-bottom:12px;font-size:12px;color:#555;line-height:1.6">'+esc(p.description)+'</div>':'')
    +(topics.length?'<div><div style="font-size:11px;color:#aaa;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">Course Topics</div>'
    +topics.map(function(t){return '<span class="topic-chip">'+esc(t.trim())+'</span>';}).join('')+'</div>':'')
    +'<div style="display:flex;gap:8px;margin-top:16px">'
    +'<button onclick="editProgram('+p.id+')" style="flex:1;padding:9px;background:linear-gradient(135deg,#004080,#0059b3);color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:12px;font-weight:600"><i class="fa fa-edit"></i> Edit</button>'
    +'<button onclick="deleteProgram('+p.id+')" style="padding:9px 14px;background:#f8d7da;color:#dc3545;border:none;border-radius:8px;cursor:pointer;font-size:12px;font-weight:600"><i class="fa fa-trash"></i></button>'
    +'</div>';
}

function detailItem(icon, label, val) {
  return '<div style="background:#f8f9fa;border-radius:8px;padding:10px 12px">'
    +'<div style="font-size:10px;color:#aaa;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px"><i class="fa '+icon+'" style="color:#004080"></i> '+label+'</div>'
    +'<div style="font-size:13px;font-weight:700;color:#333">'+esc(String(val))+'</div>'
    +'</div>';
}

function editProgram(id) {
  var p = allPrograms.find(function(x){ return String(x.id)===String(id); });
  if (!p) { toast('Not found','err'); return; }
  document.getElementById('eId').value=p.id;
  document.getElementById('eTitle').value=p.title||'';
  document.getElementById('eCode').value=p.code||'';
  document.getElementById('eColor').value=p.color||'blue';
  document.getElementById('eIcon').value=p.icon||'fa-graduation-cap';
  document.getElementById('eDuration').value=p.duration||'';
  document.getElementById('eSemesters').value=p.semesters||'';
  document.getElementById('eAffil').value=p.affiliation||'';
  document.getElementById('eSeats').value=p.seats||'';
  document.getElementById('eFee').value=p.fee||'';
  document.getElementById('eAssess').value=p.assessment||'';
  document.getElementById('eEligibility').value=p.eligibility||'';
  document.getElementById('eDesc').value=p.description||'';
  document.getElementById('eContent').value=p.content||'';
  document.getElementById('eStat').value=p.status||'active';
  document.getElementById('eFeatured').checked=!!parseInt(p.featured);
  document.getElementById('editAlert').style.display='none';
  openMod('editModal');
}

function updateProgram() {
  var payload = buildPayload('e');
  if (!payload) return;
  fetch('program-save.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)})
    .then(function(r){return r.text();})
    .then(function(txt){
      var d; try{d=JSON.parse(txt);}catch(e){showEditAlert('Server error','err');return;}
      if(d.success){toast('Updated successfully','ok');closeMod('editModal');loadPrograms();}
      else showEditAlert(d.message||'Failed','err');
    }).catch(function(){showEditAlert('Network error','err');});
}

function resetForm() {
  document.getElementById('fId').value='0';
  ['fTitle','fCode','fDuration','fAffil','fAssess','fDesc','fContent','fFee','fEligibility'].forEach(function(id){document.getElementById(id).value='';});
  document.getElementById('fSemesters').value='';
  document.getElementById('fSeats').value='';
  document.getElementById('fColor').value='blue';
  document.getElementById('fIcon').value='fa-graduation-cap';
  document.getElementById('fStat').value='active';
  document.getElementById('fFeatured').checked=false;
  document.getElementById('btnTxt').textContent='Save Course';
  document.getElementById('formAlert').style.display='none';
}

function saveProgram() {
  var payload = buildPayload('f');
  if (!payload) return;
  fetch('program-save.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)})
    .then(function(r){return r.text();})
    .then(function(txt){
      var d; try{d=JSON.parse(txt);}catch(e){showAlert('Server error','err');return;}
      if(d.success){showAlert(d.message,'ok');resetForm();loadPrograms();}
      else showAlert(d.message||'Failed','err');
    }).catch(function(e){showAlert('Network error: '+e.message,'err');});
}

function buildPayload(pfx) {
  var title = document.getElementById(pfx+'Title').value.trim();
  var code  = document.getElementById(pfx+'Code').value.trim();
  if (!title||!code) { pfx==='f'?showAlert('Title and code required','err'):showEditAlert('Title and code required','err'); return null; }
  return {
    id: pfx==='e' ? parseInt(document.getElementById('eId').value) : parseInt(document.getElementById('fId').value),
    title:title, code:code,
    color:document.getElementById(pfx+'Color').value,
    icon:document.getElementById(pfx+'Icon').value,
    duration:document.getElementById(pfx+'Duration').value.trim(),
    semesters:document.getElementById(pfx+'Semesters').value||null,
    affiliation:document.getElementById(pfx+'Affil').value.trim(),
    seats:document.getElementById(pfx+'Seats').value||null,
    fee:document.getElementById(pfx+'Fee').value.trim(),
    assessment:document.getElementById(pfx+'Assess').value.trim(),
    eligibility:document.getElementById(pfx+'Eligibility').value.trim(),
    description:document.getElementById(pfx+'Desc').value.trim(),
    content:document.getElementById(pfx+'Content').value.trim(),
    status:document.getElementById(pfx+'Stat').value,
    featured:document.getElementById(pfx+'Featured').checked?1:0
  };
}

function deleteProgram(id) {
  if (!confirm('Delete this course? This cannot be undone.')) return;
  fetch('program-delete.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id:parseInt(id)})})
    .then(function(r){return r.text();})
    .then(function(txt){
      var d; try{d=JSON.parse(txt);}catch(e){toast('Server error','err');return;}
      if(d.success){toast('Course deleted','ok');closeMod('editModal');loadPrograms();}
      else toast(d.message||'Failed','err');
    }).catch(function(){toast('Network error','err');});
}

function showAlert(msg,type){var el=document.getElementById('formAlert');el.className='alert alert-'+type;el.textContent=msg;el.style.display='block';setTimeout(function(){el.style.display='none';},4000);}
function showEditAlert(msg,type){var el=document.getElementById('editAlert');el.className='alert alert-'+type;el.textContent=msg;el.style.display='block';}
function toast(msg,type){var t=document.getElementById('toast');t.textContent=msg;t.className='toast '+(type||'ok');t.style.display='block';setTimeout(function(){t.style.display='none';},3000);}
function esc(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}

loadPrograms();
</script>
</body>
</html>
