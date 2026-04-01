<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../index.php'); exit();
}
$teacherName = $_SESSION['full_name'] ?? 'Teacher';
$teacherId   = $_SESSION['user_id']   ?? 0;
require_once '../includes/config.php';
try {
    $db = getDBConnection();
    $progRows = $db->query("SELECT DISTINCT title FROM programs WHERE status='active' ORDER BY title ASC")->fetchAll();
    $programs = array_column($progRows, 'title');
    if (empty($programs)) $programs = ['Animal Husbandry','B.Tech Ed in IT','B.Tech Ed in Civil','Diploma in Civil','Diploma Electrical'];
} catch(Exception $e) {
    $programs = ['Animal Husbandry','B.Tech Ed in IT','B.Tech Ed in Civil','Diploma in Civil','Diploma Electrical'];
}
$programsJson = json_encode($programs);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Mark Attendance | SCTI Teacher</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',sans-serif;background:#f0f4f8;min-height:100vh}
.top-bar{background:#00264d;color:white;padding:7px 20px;font-size:13px}
.pg-header{background:linear-gradient(135deg,#28a745,#20c997);color:#fff;padding:20px 28px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 18px rgba(40,167,69,.25)}
.pg-header h1{font-size:22px;font-weight:700;display:flex;align-items:center;gap:10px;margin:0 0 3px}
.pg-header .bc{font-size:12px;color:rgba(255,255,255,.75)}
.pg-header .bc a{color:#fff;text-decoration:none}
.hdr-btns{display:flex;gap:8px}
.btn-hdr{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;border:none;transition:.2s;text-decoration:none}
.btn-hdr.ghost{background:rgba(255,255,255,.18);color:#fff}
.btn-hdr.ghost:hover{background:rgba(255,255,255,.32)}
.btn-hdr.chart{background:rgba(255,255,255,.9);color:#28a745}
.btn-hdr.chart:hover{background:#fff}
.wrap{max-width:1100px;margin:28px auto;padding:0 20px 60px}
/* Controls */
.ctrl-bar{background:white;border-radius:14px;padding:20px 24px;box-shadow:0 2px 12px rgba(0,0,0,.08);margin-bottom:22px;display:flex;gap:14px;flex-wrap:wrap;align-items:flex-end}
.ctrl-group{display:flex;flex-direction:column;gap:5px;flex:1;min-width:160px}
.ctrl-group label{font-size:11px;font-weight:700;color:#666;text-transform:uppercase;letter-spacing:.5px}
.ctrl-group input,.ctrl-group select{padding:10px 13px;border:2px solid #e0e6ef;border-radius:9px;font-size:13px;font-family:inherit;transition:.2s}
.ctrl-group input:focus,.ctrl-group select:focus{outline:none;border-color:#28a745;box-shadow:0 0 0 3px rgba(40,167,69,.1)}
.btn-load{padding:10px 22px;background:linear-gradient(135deg,#28a745,#20c997);color:white;border:none;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:7px;transition:.2s;white-space:nowrap;align-self:flex-end}
.btn-load:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(40,167,69,.35)}
/* Stats bar */
.stats-row{display:flex;gap:12px;margin-bottom:18px;flex-wrap:wrap}
.stat-pill{flex:1;min-width:100px;background:white;border-radius:12px;padding:14px 18px;box-shadow:0 2px 10px rgba(0,0,0,.07);text-align:center}
.stat-pill .sv{font-size:26px;font-weight:800;line-height:1}
.stat-pill .sl{font-size:11px;color:#888;margin-top:3px;font-weight:600}
.sp-total{border-top:4px solid #6c757d}.sp-total .sv{color:#495057}
.sp-present{border-top:4px solid #28a745}.sp-present .sv{color:#28a745}
.sp-absent{border-top:4px solid #dc3545}.sp-absent .sv{color:#dc3545}
.sp-late{border-top:4px solid #fd7e14}.sp-late .sv{color:#fd7e14}
/* Table */
.att-table-wrap{background:white;border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,.08);overflow:hidden}
.att-table-head{background:linear-gradient(135deg,#28a745,#20c997);padding:16px 24px;display:flex;justify-content:space-between;align-items:center;color:white}
.att-table-head h3{margin:0;font-size:16px;font-weight:700;display:flex;align-items:center;gap:8px}
.bulk-btns{display:flex;gap:8px}
.bulk-btn{padding:7px 14px;border:2px solid rgba(255,255,255,.5);background:transparent;color:white;border-radius:8px;cursor:pointer;font-size:12px;font-weight:700;transition:.2s}
.bulk-btn:hover{background:rgba(255,255,255,.2)}
.bulk-btn.all-present{border-color:#a7f3d0;color:#a7f3d0}
.bulk-btn.all-absent{border-color:#fca5a5;color:#fca5a5}
table{width:100%;border-collapse:collapse}
thead th{background:#f8fffe;padding:12px 16px;text-align:left;font-size:12px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e8f5e9}
tbody tr{border-bottom:1px solid #f0f0f0;transition:.15s}
tbody tr:hover{background:#f8fffe}
tbody tr:last-child{border-bottom:none}
td{padding:12px 16px;font-size:13px;color:#333;vertical-align:middle}
.student-name{font-weight:700;color:#1a202c}
.student-meta{font-size:11px;color:#888;margin-top:2px}
/* Status radio buttons */
.status-group{display:flex;gap:6px;flex-wrap:wrap}
.status-radio{display:none}
.status-label{padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;cursor:pointer;border:2px solid #e0e6ef;color:#666;transition:.2s;white-space:nowrap}
.status-radio:checked + .status-label{border-color:transparent;color:white}
.status-radio.r-present:checked + .status-label{background:#28a745}
.status-radio.r-absent:checked  + .status-label{background:#dc3545}
.status-radio.r-late:checked   + .status-label{background:#fd7e14}
.status-label:hover{transform:translateY(-1px)}
/* Late reason input */
.late-reason-wrap{display:none;margin-top:6px}
.late-reason-wrap input{width:100%;padding:7px 11px;border:2px solid #fed7aa;border-radius:8px;font-size:12px;font-family:inherit;outline:none;transition:.2s}
.late-reason-wrap input:focus{border-color:#fd7e14;box-shadow:0 0 0 3px rgba(253,126,20,.1)}
/* Remarks */
.remarks-input{width:100%;padding:7px 11px;border:2px solid #e0e6ef;border-radius:8px;font-size:12px;font-family:inherit;outline:none;transition:.2s}
.remarks-input:focus{border-color:#28a745;box-shadow:0 0 0 3px rgba(40,167,69,.1)}
/* Save bar */
.save-bar{position:sticky;bottom:0;background:white;border-top:2px solid #e8f5e9;padding:16px 24px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 -4px 18px rgba(0,0,0,.08)}
.save-info{font-size:13px;color:#666}
.btn-save{padding:12px 32px;background:linear-gradient(135deg,#28a745,#20c997);color:white;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:8px;transition:.2s}
.btn-save:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(40,167,69,.4)}
.btn-save:disabled{opacity:.6;cursor:not-allowed;transform:none}
/* Alert */
.alert{padding:12px 18px;border-radius:10px;font-size:13px;margin-bottom:16px;display:none;align-items:center;gap:10px}
.alert.ok{background:#d1fae5;color:#065f46;border:1px solid #a7f3d0}
.alert.err{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5}
/* Empty state */
.empty-state{text-align:center;padding:60px 20px;color:#aaa}
.empty-state i{font-size:56px;display:block;margin-bottom:14px;color:#d1fae5}
/* Search */
.search-bar{padding:12px 24px;border-bottom:1px solid #f0f0f0;background:#fafffe}
.search-bar input{width:100%;padding:9px 14px;border:2px solid #e0e6ef;border-radius:9px;font-size:13px;font-family:inherit;outline:none;transition:.2s}
.search-bar input:focus{border-color:#28a745}
/* Already marked badge */
.marked-badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:10px;font-size:11px;font-weight:700;background:#d1fae5;color:#065f46}
footer{background:#00264d;color:white;text-align:center;padding:12px;font-size:13px;margin-top:40px}
@media(max-width:700px){.ctrl-bar{flex-direction:column}.status-group{gap:4px}.status-label{padding:5px 10px;font-size:11px}td{padding:10px 10px}thead th{padding:10px 10px}}
</style>
</head>
<body>
<div class="top-bar"><marquee>SCTI Teacher Portal "” Mark Attendance</marquee></div>
<div class="pg-header">
  <div>
    <h1><i class="fa fa-calendar-check"></i> Mark Attendance</h1>
    <div class="bc"><a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Attendance</div>
  </div>
  <div class="hdr-btns">
    <a href="teacher-attendance-chart.php" class="btn-hdr chart"><i class="fa fa-chart-bar"></i> View Charts</a>
    <a href="../dashboards/teacher-dashboard.php" class="btn-hdr ghost"><i class="fa fa-arrow-left"></i> Back</a>
  </div>
</div>

<div class="wrap">
  <div id="alertBox" class="alert"></div>

  <!-- Controls -->
  <div class="ctrl-bar">
    <div class="ctrl-group">
      <label><i class="fa fa-calendar"></i> Date</label>
      <input type="date" id="attDate" value="<?= date('Y-m-d') ?>">
    </div>
    <div class="ctrl-group">
      <label><i class="fa fa-book"></i> Subject</label>
      <select id="attClass" style="padding:10px 13px;border:2px solid #e0e6ef;border-radius:9px;font-size:13px;font-family:inherit;transition:.2s;background:white">
        <option value="">"” Select Program "”</option>
        <?php foreach($programs as $p): ?>
        <option><?=htmlspecialchars($p)?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="ctrl-group">
      <label><i class="fa fa-layer-group"></i> Semester</label>
      <select id="attPeriod" style="padding:10px 13px;border:2px solid #e0e6ef;border-radius:9px;font-size:13px;font-family:inherit;transition:.2s;background:white">
        <option value="">"” Select Semester "”</option>
        <option>Semester 1</option>
        <option>Semester 2</option>
        <option>Semester 3</option>
        <option>Semester 4</option>
        <option>Semester 5</option>
        <option>Semester 6</option>
      </select>
    </div>
    <button class="btn-load" onclick="loadStudents()"><i class="fa fa-users"></i> Load Students</button>
  </div>

  <!-- Stats -->
  <div class="stats-row">
    <div class="stat-pill sp-total"><div class="sv" id="stTotal">0</div><div class="sl">Total</div></div>
    <div class="stat-pill sp-present"><div class="sv" id="stPresent">0</div><div class="sl">Present</div></div>
    <div class="stat-pill sp-absent"><div class="sv" id="stAbsent">0</div><div class="sl">Absent</div></div>
    <div class="stat-pill sp-late"><div class="sv" id="stLate">0</div><div class="sl">Late</div></div>
  </div>

  <!-- Table -->
  <div class="att-table-wrap">
    <div class="att-table-head">
      <h3><i class="fa fa-list-check"></i> Student Attendance List</h3>
      <div class="bulk-btns">
        <button class="bulk-btn all-present" onclick="markAll('present')"><i class="fa fa-check"></i> All Present</button>
        <button class="bulk-btn all-absent"  onclick="markAll('absent')"><i class="fa fa-times"></i> All Absent</button>
      </div>
    </div>
    <div class="search-bar">
      <input type="text" id="searchStudent" placeholder="Search student by name or ID..." oninput="filterStudents(this.value)">
    </div>
    <div id="tableWrap">
      <div class="empty-state">
        <i class="fa fa-calendar-check"></i>
        <p>Select a date and class, then click <strong>Load Students</strong></p>
      </div>
    </div>
    <div class="save-bar">
      <div class="save-info" id="saveInfo">No attendance loaded</div>
      <button class="btn-save" id="btnSave" onclick="saveAttendance()" disabled>
        <i class="fa fa-save"></i> Save Attendance
      </button>
    </div>
  </div>
</div>

<footer>© 2025 SCTI "” Teacher Portal</footer>

<script>
var allStudents = [];
var existingMap = {};

// Load subjects (programs) and semesters from DB
window.addEventListener('DOMContentLoaded', function(){
  var semesters = ['Semester 1','Semester 2','Semester 3','Semester 4','Semester 5','Semester 6'];
  var semSel = document.getElementById('attPeriod');
  semesters.forEach(function(s){
    var opt = document.createElement('option'); opt.value = s; opt.textContent = s; semSel.appendChild(opt);
  });
});

function loadStudents() {
  var date  = document.getElementById('attDate').value;
  var subj  = document.getElementById('attClass').value.trim();
  var sem   = document.getElementById('attPeriod').value.trim();
  if (!date || !subj || !sem) { showAlert('Please select date, subject and semester.','err'); return; }

  document.getElementById('tableWrap').innerHTML = '<div class="empty-state"><i class="fa fa-spinner fa-spin" style="color:#28a745"></i><p>Loading students...</p></div>';
  document.getElementById('btnSave').disabled = true;

  var cls = subj + ' ' + sem;
  Promise.all([
    fetch('attendance-data.php?action=students&class='+encodeURIComponent(cls)).then(r=>r.json()),
    fetch('attendance-data.php?action=existing&date='+encodeURIComponent(date)+'&class='+encodeURIComponent(cls)).then(r=>r.json())
  ]).then(function(results) {
    var sRes = results[0], eRes = results[1];
    if (!sRes.success) { showAlert(sRes.message||'Failed to load students','err'); return; }
    allStudents = sRes.students || [];
    existingMap = (eRes.success && eRes.records) ? eRes.records : {};
    renderTable(allStudents);
    updateStats();
    document.getElementById('btnSave').disabled = allStudents.length === 0;
    document.getElementById('saveInfo').textContent = allStudents.length + ' students "” ' + subj + ' / ' + sem;
  }).catch(function(e){ showAlert('Network error: '+e.message,'err'); });
}

function renderTable(list) {
  if (!list.length) {
    document.getElementById('tableWrap').innerHTML = '<div class="empty-state"><i class="fa fa-user-slash"></i><p>No active students found.</p></div>';
    return;
  }
  var html = '<table><thead><tr>'
    + '<th>#</th><th>Student</th><th>Status</th><th>Late Reason</th><th>Remarks</th>'
    + '</tr></thead><tbody>';

  list.forEach(function(s, i) {
    var ex      = existingMap[s.id] || {};
    var status  = ex.status || 'present';
    var lr      = ex.late_reason || '';
    var remarks = ex.remarks || '';
    var lrStyle = status === 'late' ? 'display:block' : 'display:none';

    html += '<tr id="row_'+s.id+'">'
      + '<td style="color:#aaa;font-size:12px">'+(i+1)+'</td>'
      + '<td><div class="student-name">'+esc(s.full_name)+'</div>'
      +   '<div class="student-meta">'+esc(s.student_id)+' &nbsp;|&nbsp; '+esc(s.course||'')+(s.semester?' &nbsp;'+esc(s.semester):'')+'</div>'
      + '</td>'
      + '<td>'
      +   '<div class="status-group">'
      +     radioBtn(s.id,'present',status)+' '
      +     radioBtn(s.id,'absent', status)+' '
      +     radioBtn(s.id,'late',   status)
      +   '</div>'
      + '</td>'
      + '<td><div class="late-reason-wrap" id="lr_'+s.id+'" style="'+lrStyle+'">'
      +   '<input type="text" id="lrInput_'+s.id+'" placeholder="Reason for being late..." value="'+esc(lr)+'" oninput="updateStats()">'
      + '</div></td>'
      + '<td><input type="text" class="remarks-input" id="rem_'+s.id+'" placeholder="Optional remark..." value="'+esc(remarks)+'"></td>'
      + '</tr>';
  });
  html += '</tbody></table>';
  document.getElementById('tableWrap').innerHTML = html;
  updateStats();
}

function radioBtn(sid, val, checked) {
  var id  = 'r_'+sid+'_'+val;
  var cls = 'r-'+val;
  var lbl = val.charAt(0).toUpperCase()+val.slice(1);
  var chk = checked===val ? 'checked' : '';
  return '<input type="radio" class="status-radio '+cls+'" name="status_'+sid+'" id="'+id+'" value="'+val+'" '+chk
    + ' onchange="onStatusChange('+sid+',\''+val+'\')">'
    + '<label class="status-label" for="'+id+'">'+lbl+'</label>';
}

function onStatusChange(sid, val) {
  var lrWrap = document.getElementById('lr_'+sid);
  if (lrWrap) lrWrap.style.display = val==='late' ? 'block' : 'none';
  updateStats();
}

function markAll(status) {
  allStudents.forEach(function(s) {
    var radio = document.getElementById('r_'+s.id+'_'+status);
    if (radio) { radio.checked = true; onStatusChange(s.id, status); }
  });
}

function filterStudents(q) {
  q = q.toLowerCase();
  var rows = document.querySelectorAll('tbody tr');
  rows.forEach(function(row) {
    var text = row.textContent.toLowerCase();
    row.style.display = text.includes(q) ? '' : 'none';
  });
}

function updateStats() {
  var p=0,a=0,l=0;
  allStudents.forEach(function(s) {
    var checked = document.querySelector('input[name="status_'+s.id+'"]:checked');
    var val = checked ? checked.value : 'present';
    if (val==='present') p++;
    else if (val==='absent') a++;
    else if (val==='late') l++;
  });
  document.getElementById('stTotal').textContent   = allStudents.length;
  document.getElementById('stPresent').textContent = p;
  document.getElementById('stAbsent').textContent  = a;
  document.getElementById('stLate').textContent    = l;
}

function saveAttendance() {
  var date  = document.getElementById('attDate').value;
  var subj  = document.getElementById('attClass').value.trim();
  var sem   = document.getElementById('attPeriod').value.trim();
  var cls   = subj + ' ' + sem;
  if (!date || !subj || !sem) { showAlert('Date, subject and semester are required.','err'); return; }
  if (!allStudents.length) { showAlert('No students loaded.','err'); return; }

  var records = allStudents.map(function(s) {
    var checked = document.querySelector('input[name="status_'+s.id+'"]:checked');
    var status  = checked ? checked.value : 'present';
    var lrInput = document.getElementById('lrInput_'+s.id);
    var remInput= document.getElementById('rem_'+s.id);
    return {
      student_id:  s.id,
      status:      status,
      late_reason: lrInput ? lrInput.value.trim() : '',
      remarks:     remInput ? remInput.value.trim() : ''
    };
  });

  var btn = document.getElementById('btnSave');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';

  fetch('attendance-save.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({class_name:cls, date:date, period:'', records:records})
  })
  .then(function(r){ return r.json(); })
  .then(function(d) {
    btn.disabled = false;
    btn.innerHTML = '<i class="fa fa-save"></i> Save Attendance';
    if (d.success) {
      showAlert('<i class="fa fa-check-circle"></i> '+d.message, 'ok');
      fetch('attendance-data.php?action=existing&date='+encodeURIComponent(date)+'&class='+encodeURIComponent(cls))
        .then(function(r){ return r.json(); }).then(function(e){ if(e.success) existingMap=e.records; });
    } else {
      showAlert(d.message || 'Failed to save attendance.', 'err');
    }
  }).catch(function(e){
    btn.disabled = false;
    btn.innerHTML = '<i class="fa fa-save"></i> Save Attendance';
    showAlert('Network error: '+e.message, 'err');
  });
}
function showAlert(msg, type) {
  var el = document.getElementById('alertBox');
  el.innerHTML = msg;
  el.className = 'alert '+type;
  el.style.display = 'flex';
  setTimeout(function(){ el.style.display='none'; }, 5000);
}

function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
</script>
</body>
</html>
