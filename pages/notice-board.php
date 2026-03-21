<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: ../pages/login-simple.php'); exit();
}
require_once '../includes/config.php';
$userType = $_SESSION['user_type'] ?? 'student';
$fullName = $_SESSION['full_name'] ?? '';
$dashUrl  = '../dashboards/'.$userType.'-dashboard.php';
$accentGrad = ($userType === 'teacher') ? 'linear-gradient(135deg,#28a745,#20c997)' : 'linear-gradient(135deg,#004080,#0059b3)';
$accentColor = ($userType === 'teacher') ? '#28a745' : '#004080';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Notice Board | SCTI</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Segoe UI',sans-serif;background:#f5f7fa}
    .top-header{background:<?=$accentGrad?>;color:white;padding:8px 20px;font-size:13px}
    .page-header{background:<?=$accentGrad?>;color:white;padding:24px 28px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 18px rgba(0,0,0,.15)}
    .page-header h1{font-size:22px;font-weight:700;display:flex;align-items:center;gap:10px;margin:0 0 4px}
    .bc{font-size:12px;opacity:.8}.bc a{color:white;text-decoration:none}
    .back-btn{background:rgba(255,255,255,.2);color:white;padding:9px 18px;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;transition:.2s}
    .back-btn:hover{background:rgba(255,255,255,.35)}
    .container{max-width:900px;margin:0 auto;padding:24px 20px}
    /* FILTER TABS */
    .filter-bar{background:white;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.07);padding:14px 18px;margin-bottom:22px;display:flex;gap:8px;flex-wrap:wrap;align-items:center}
    .filter-label{font-size:11px;font-weight:700;color:#aaa;text-transform:uppercase;letter-spacing:1px;margin-right:4px}
    .ftab{padding:7px 16px;border:2px solid #e0e6ef;background:#f8fafc;color:#555;border-radius:20px;cursor:pointer;font-size:12px;font-weight:700;transition:.2s;display:inline-flex;align-items:center;gap:5px}
    .ftab:hover{border-color:<?=$accentColor?>;color:<?=$accentColor?>}
    .ftab.active{background:<?=$accentGrad?>;color:white;border-color:transparent}
    /* NOTICE CARDS */
    .notice-list{display:flex;flex-direction:column;gap:14px}
    .ncard{background:white;border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,.07);border-left:5px solid <?=$accentColor?>;overflow:hidden;transition:.25s}
    .ncard:hover{transform:translateY(-3px);box-shadow:0 10px 28px rgba(0,0,0,.12)}
    .ncard.emergency{border-left-color:#dc3545;background:#fff8f8}
    .ncard.urgent-pri{border-left-color:#dc3545}
    .ncard-body{padding:18px 20px}
    .ncard-meta{display:flex;gap:7px;flex-wrap:wrap;margin-bottom:10px;align-items:center}
    .nbadge{padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:4px}
    .nb-all{background:#ede9fe;color:#5b21b6}
    .nb-student{background:#cce5ff;color:#004085}
    .nb-teacher{background:#d4edda;color:#155724}
    .nb-emergency{background:#f8d7da;color:#721c24}
    .nb-urgent{background:#f8d7da;color:#721c24}
    .nb-high{background:#fff3cd;color:#856404}
    .nb-normal{background:#e2e3e5;color:#383d41}
    .nb-cat{background:#f0f4f8;color:#4a5568}
    .ncard-date{font-size:11px;color:#aaa;margin-left:auto;display:flex;align-items:center;gap:4px}
    .ncard-title{font-size:16px;font-weight:700;color:#1a202c;margin-bottom:8px}
    .ncard-desc{font-size:13px;color:#555;line-height:1.7}
    .empty-state{text-align:center;padding:60px 20px;color:#aaa;background:white;border-radius:12px}
    .empty-state i{font-size:48px;display:block;margin-bottom:12px;color:#ddd}
    .loading{text-align:center;padding:60px;color:#aaa}
    footer{background:#2c3e50;color:white;text-align:center;padding:16px;margin-top:40px;font-size:13px}
  </style>
</head>
<body>
<div class="top-header"><marquee>Notice Board — Stay updated with the latest announcements from SCTI</marquee></div>
<div class="page-header">
  <div>
    <h1><i class="fa fa-bullhorn"></i> Notice Board</h1>
    <div class="bc"><a href="<?=$dashUrl?>"><i class="fa fa-home"></i> Dashboard</a> / Notices</div>
  </div>
  <a href="<?=$dashUrl?>" class="back-btn"><i class="fa fa-arrow-left"></i> Back</a>
</div>

<div class="container">
  <div class="filter-bar">
    <span class="filter-label">Filter:</span>
    <button class="ftab active" onclick="filterNotices(this,'')"><i class="fa fa-border-all"></i> All</button>
    <button class="ftab" onclick="filterNotices(this,'emergency')"><i class="fa fa-triangle-exclamation"></i> Emergency</button>
    <button class="ftab" onclick="filterNotices(this,'all')"><i class="fa fa-globe"></i> General</button>
    <?php if ($userType === 'student'): ?>
    <button class="ftab" onclick="filterNotices(this,'student')"><i class="fa fa-user-graduate"></i> For Students</button>
    <?php elseif ($userType === 'teacher'): ?>
    <button class="ftab" onclick="filterNotices(this,'teacher')"><i class="fa fa-chalkboard-teacher"></i> For Teachers</button>
    <?php endif; ?>
    <button class="ftab" onclick="filterNotices(this,'urgent_pri')"><i class="fa fa-circle-exclamation"></i> Urgent</button>
  </div>

  <div id="noticeContainer">
    <div class="loading"><i class="fa fa-spinner fa-spin" style="font-size:28px;color:#aaa"></i><p style="margin-top:10px">Loading notices...</p></div>
  </div>
</div>

<footer>© 2025 Sindhuli Community Technical Institute (SCTI)</footer>

<script>
var allNotices = [];
var currentFilter = '';
var userType = '<?=$userType?>';

var audIcons = {all:'fa-globe',student:'fa-user-graduate',teacher:'fa-chalkboard-teacher',emergency:'fa-triangle-exclamation'};
var audLabels = {all:'General',student:'Students',teacher:'Teachers',emergency:'Emergency'};

function filterNotices(btn, f) {
  currentFilter = f;
  document.querySelectorAll('.ftab').forEach(function(b){ b.classList.remove('active'); });
  btn.classList.add('active');
  renderNotices();
}

function renderNotices() {
  var list = allNotices.filter(function(n) {
    if (!currentFilter) return true;
    if (currentFilter === 'urgent_pri') return n.priority === 'urgent';
    if (currentFilter === 'emergency') return n.audience === 'emergency' || n.priority === 'urgent';
    return n.audience === currentFilter;
  });

  var container = document.getElementById('noticeContainer');
  if (!list.length) {
    container.innerHTML = '<div class="empty-state"><i class="fa fa-bullhorn"></i><p>No notices found in this category.</p></div>';
    return;
  }

  container.innerHTML = '<div class="notice-list">' + list.map(buildCard).join('') + '</div>';
}

function buildCard(n) {
  var aud = n.audience || 'all';
  var pri = n.priority || 'normal';
  var isEmergency = aud === 'emergency' || pri === 'urgent';
  var cls = isEmergency ? 'ncard emergency' : (pri === 'urgent' ? 'ncard urgent-pri' : 'ncard');
  var dt = new Date(n.notice_date || n.created_at).toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric'});

  return '<div class="' + cls + '">'
    + '<div class="ncard-body">'
    +   '<div class="ncard-meta">'
    +     '<span class="nbadge nb-' + aud + '"><i class="fa ' + (audIcons[aud]||'fa-globe') + '"></i> ' + (audLabels[aud]||aud) + '</span>'
    +     (pri !== 'normal' ? '<span class="nbadge nb-' + pri + '">' + pri.charAt(0).toUpperCase()+pri.slice(1) + '</span>' : '')
    +     '<span class="nbadge nb-cat"><i class="fa fa-tag"></i> ' + (n.category||'general').charAt(0).toUpperCase()+(n.category||'general').slice(1) + '</span>'
    +     '<span class="ncard-date"><i class="fa fa-calendar"></i> ' + dt + '</span>'
    +   '</div>'
    +   '<div class="ncard-title">' + esc(n.title) + '</div>'
    +   '<div class="ncard-desc">' + esc(n.description || '') + '</div>'
    + '</div>'
    + '</div>';
}

function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

// Load notices for this user type (show audience=userType + audience=all + emergency)
fetch('notice-list.php?status=active&audience=<?=$userType?>')
  .then(function(r){ return r.json(); })
  .then(function(d){
    allNotices = (d.notices || []);
    renderNotices();
  })
  .catch(function(){
    document.getElementById('noticeContainer').innerHTML = '<div class="empty-state"><i class="fa fa-exclamation-triangle"></i><p>Failed to load notices.</p></div>';
  });
</script>
</body>
</html>
