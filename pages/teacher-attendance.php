<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../index.php'); exit();
}
require_once '../includes/config.php';
try {
    $db = getDBConnection();
    $students = $db->query("SELECT id, full_name, student_id, course, semester FROM students WHERE status='active' ORDER BY full_name ASC")->fetchAll();
} catch(Exception $e) { $students = []; }
$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Mark Attendance | SCTI Teacher Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Segoe UI',sans-serif;background:#f5f7fa}
    .top-header{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:10px 0;text-align:center;font-size:14px}
    .container{max-width:1400px;margin:0 auto;padding:20px}
    .page-header{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:30px;border-radius:10px;margin-bottom:30px;box-shadow:0 4px 15px rgba(40,167,69,.2)}
    .page-header h1{margin:0 0 8px;font-size:28px}
    .breadcrumb{font-size:14px}
    .breadcrumb a{color:white;text-decoration:none}
    .controls{background:white;padding:22px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.1);margin-bottom:24px;display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px}
    .ctrl-group label{display:block;margin-bottom:7px;color:#555;font-weight:600;font-size:13px}
    .ctrl-input{width:100%;padding:11px 14px;border:2px solid #dee2e6;border-radius:7px;font-size:14px;font-family:inherit}
    .ctrl-input:focus{outline:none;border-color:#28a745}
    .summary{display:grid;grid-template-columns:repeat(4,1fr);gap:15px;margin-bottom:24px}
    .sum-box{background:white;padding:20px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.1);text-align:center}
    .sum-box h3{font-size:32px;margin:8px 0;font-weight:800}
    .sum-box p{font-size:13px;color:#888}
    .sum-present h3{color:#28a745}
    .sum-absent h3{color:#dc3545}
    .sum-late h3{color:#ffc107}
    .sum-rate h3{color:#004080}
    .table-wrap{background:white;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.1);overflow:hidden}
    .att-table{width:100%;border-collapse:collapse}
    .att-table th{background:#f8f9fa;padding:14px 16px;text-align:left;color:#333;font-weight:700;font-size:13px;border-bottom:2px solid #dee2e6}
    .att-table td{padding:13px 16px;border-bottom:1px solid #f0f0f0;vertical-align:middle}
    .att-table tr:last-child td{border-bottom:none}
    .att-table tbody tr:hover{background:#f8fffe}
    .avatar{width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#28a745,#20c997);display:inline-flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:13px;flex-shrink:0}
    .stu-info{display:flex;align-items:center;gap:10px}
    .att-btns{display:flex;gap:7px}
    .att-btn{padding:8px 14px;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:700;transition:.2s;display:inline-flex;align-items:center;gap:5px}
    .btn-present{background:#d4edda;color:#155724}
    .btn-present.active,.btn-present:hover{background:#28a745;color:white}
    .btn-absent{background:#f8d7da;color:#721c24}
    .btn-absent.active,.btn-absent:hover{background:#dc3545;color:white}
    .btn-late{background:#fff3cd;color:#856404}
    .btn-late.active,.btn-late:hover{background:#ffc107;color:#333}
    .remark-input{padding:8px 10px;border:1px solid #dee2e6;border-radius:5px;width:100%;font-size:13px;font-family:inherit}
    .remark-input:focus{outline:none;border-color:#28a745}
    .save-wrap{text-align:center;padding:28px}
    .save-btn{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:14px 44px;border:none;border-radius:8px;cursor:pointer;font-size:16px;font-weight:700;transition:.2s;display:inline-flex;align-items:center;gap:10px}
    .save-btn:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(40,167,69,.35)}
    .save-btn:disabled{opacity:.6;cursor:not-allowed;transform:none}
    .toast{display:none;position:fixed;bottom:28px;right:28px;padding:14px 22px;border-radius:10px;font-size:14px;font-weight:600;z-index:9999;align-items:center;gap:10px;box-shadow:0 6px 20px rgba(0,0,0,.2)}
    .toast.show{display:flex;animation:tIn .3s ease}
    .toast-ok{background:#28a745;color:white}
    .toast-err{background:#dc3545;color:white}
    @keyframes tIn{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}
    .footer{background:#2c3e50;color:white;text-align:center;padding:20px;border-radius:10px;margin-top:30px}
    @media(max-width:600px){.summary{grid-template-columns:1fr 1fr}.att-btns{flex-direction:column}}
  </style>
</head>
<body>
<div class="top-header">Attendance Management — Mark and save student attendance</div>
<div class="container">
  <div class="page-header">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:10px">
      <div>
        <h1><i class="fa fa-calendar-check"></i> Mark Attendance</h1>
        <div class="breadcrumb"><a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Attendance</div>
      </div>
      <div style="display:flex;gap:10px">
        <button onclick="showTab('mark')" id="tabMark" style="background:rgba(255,255,255,.9);color:#28a745;border:none;padding:9px 18px;border-radius:8px;font-weight:700;cursor:pointer;font-size:13px"><i class="fa fa-edit"></i> Mark</button>
        <button onclick="showTab('history')" id="tabHistory" style="background:rgba(255,255,255,.2);color:white;border:2px solid rgba(255,255,255,.5);padding:9px 18px;border-radius:8px;font-weight:700;cursor:pointer;font-size:13px"><i class="fa fa-history"></i> History</button>
      </div>
    </div>
  </div>

  <div id="sectionMark">
  <div class="controls">
    <div class="ctrl-group">
      <label><i class="fa fa-book"></i> Class / Course</label>
      <input type="text" class="ctrl-input" id="className" placeholder="e.g. Database Management" list="courseList">
      <datalist id="courseList">
        <?php $courses = array_unique(array_column($students,'course')); foreach($courses as $c) if($c) echo '<option value="'.htmlspecialchars($c).'">'; ?>
      </datalist>
    </div>
    <div class="ctrl-group">
      <label><i class="fa fa-calendar"></i> Date</label>
      <input type="date" class="ctrl-input" id="attDate" value="<?=$today?>">
    </div>
    <div class="ctrl-group">
      <label><i class="fa fa-clock"></i> Period</label>
      <select class="ctrl-input" id="attPeriod">
        <option>9:00 AM - 10:30 AM</option>
        <option>11:00 AM - 12:30 PM</option>
        <option>1:30 PM - 3:00 PM</option>
        <option>3:30 PM - 5:00 PM</option>
      </select>
    </div>
  </div>

  <div class="summary">
    <div class="sum-box sum-present"><i class="fa fa-check-circle" style="font-size:22px;color:#28a745"></i><h3 id="cntPresent">0</h3><p>Present</p></div>
    <div class="sum-box sum-absent"><i class="fa fa-times-circle" style="font-size:22px;color:#dc3545"></i><h3 id="cntAbsent">0</h3><p>Absent</p></div>
    <div class="sum-box sum-late"><i class="fa fa-clock" style="font-size:22px;color:#ffc107"></i><h3 id="cntLate">0</h3><p>Late</p></div>
    <div class="sum-box sum-rate"><i class="fa fa-percent" style="font-size:22px;color:#004080"></i><h3 id="cntRate">0%</h3><p>Attendance Rate</p></div>
  </div>

  <div class="table-wrap">
    <table class="att-table">
      <thead>
        <tr><th>Roll No.</th><th>Student Name</th><th>Student ID</th><th>Mark Attendance</th><th>Remarks</th></tr>
      </thead>
      <tbody>
        <?php if(empty($students)): ?>
        <tr><td colspan="5" style="text-align:center;padding:50px;color:#aaa">No students found in database.</td></tr>
        <?php else: foreach($students as $i=>$s):
          $parts = explode(' ', $s['full_name']);
          $initials = strtoupper(substr($parts[0],0,1).(count($parts)>1?substr($parts[count($parts)-1],0,1):''));
        ?>
        <tr>
          <td><?=$i+1?></td>
          <td><div class="stu-info"><div class="avatar"><?=htmlspecialchars($initials)?></div><span><?=htmlspecialchars($s['full_name'])?></span></div></td>
          <td><?=htmlspecialchars($s['student_id'])?></td>
          <td>
            <div class="att-btns" data-student-id="<?=$s['id']?>" data-student-db-id="<?=htmlspecialchars($s['student_id'])?>">
              <button class="att-btn btn-present active" onclick="setAtt(this,'present')"><i class="fa fa-check"></i> Present</button>
              <button class="att-btn btn-absent" onclick="setAtt(this,'absent')"><i class="fa fa-times"></i> Absent</button>
              <button class="att-btn btn-late" onclick="setAtt(this,'late')"><i class="fa fa-clock"></i> Late</button>
            </div>
          </td>
          <td><input type="text" class="remark-input" placeholder="Add remarks..."></td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
    <div class="save-wrap">
      <button class="save-btn" id="saveBtn" onclick="saveAttendance()"><i class="fa fa-save"></i> Save Attendance</button>
    </div>
  </div>
  </div><!-- end sectionMark -->

  <!-- HISTORY SECTION -->
  <div id="sectionHistory" style="display:none">
    <div style="background:white;padding:20px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.1);margin-bottom:20px;display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
      <div>
        <label style="display:block;font-size:12px;font-weight:600;color:#555;margin-bottom:5px">Filter by Date</label>
        <input type="date" id="histDate" class="ctrl-input" style="width:180px">
      </div>
      <div>
        <label style="display:block;font-size:12px;font-weight:600;color:#555;margin-bottom:5px">Filter by Class</label>
        <input type="text" id="histClass" class="ctrl-input" placeholder="Class name..." style="width:200px">
      </div>
      <button onclick="loadHistory()" style="padding:11px 20px;background:linear-gradient(135deg,#28a745,#20c997);color:white;border:none;border-radius:7px;font-weight:700;cursor:pointer;font-size:13px"><i class="fa fa-search"></i> Search</button>
    </div>
    <div class="table-wrap">
      <table class="att-table">
        <thead><tr><th>Date</th><th>Student</th><th>Class</th><th>Period</th><th>Status</th><th>Remarks</th></tr></thead>
        <tbody id="histBody"><tr><td colspan="6" style="text-align:center;padding:40px;color:#aaa">Click Search to load history</td></tr></tbody>
      </table>
    </div>
  </div>
</div>
<footer class="footer"><p>© 2025 SCTI - Teacher Portal</p></footer>

<div class="toast toast-ok" id="toastOk"><i class="fa fa-check-circle"></i><span id="toastOkMsg">Saved!</span></div>
<div class="toast toast-err" id="toastErr"><i class="fa fa-times-circle"></i><span id="toastErrMsg">Error</span></div>

<script>
function setAtt(btn, status) {
  var group = btn.parentElement;
  group.querySelectorAll('.att-btn').forEach(function(b){ b.classList.remove('active'); });
  btn.classList.add('active');
  updateSummary();
}

function updateSummary() {
  var p=0,a=0,l=0;
  document.querySelectorAll('.att-btns').forEach(function(g){
    var active = g.querySelector('.att-btn.active');
    if (!active) return;
    if (active.classList.contains('btn-present')) p++;
    else if (active.classList.contains('btn-absent')) a++;
    else if (active.classList.contains('btn-late')) l++;
  });
  var total = p+a+l;
  document.getElementById('cntPresent').textContent = p;
  document.getElementById('cntAbsent').textContent  = a;
  document.getElementById('cntLate').textContent    = l;
  document.getElementById('cntRate').textContent    = total>0 ? Math.round((p/total)*100)+'%' : '0%';
}

function saveAttendance() {
  var className = document.getElementById('className').value.trim();
  var date      = document.getElementById('attDate').value;
  var period    = document.getElementById('attPeriod').value;
  if (!className) { showToast('err','Please enter a class name.'); return; }
  if (!date)      { showToast('err','Please select a date.'); return; }

  var records = [];
  document.querySelectorAll('.att-btns').forEach(function(g){
    var active = g.querySelector('.att-btn.active');
    var row    = g.closest('tr');
    var remark = row.querySelector('.remark-input').value.trim();
    records.push({
      student_id:    parseInt(g.dataset.studentId),
      student_db_id: g.dataset.studentDbId,
      status:        active ? (active.classList.contains('btn-present')?'present':active.classList.contains('btn-absent')?'absent':'late') : 'present',
      remarks:       remark
    });
  });

  if (!records.length) { showToast('err','No students to save.'); return; }

  var btn = document.getElementById('saveBtn');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';

  fetch('attendance-save.php', {
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body: JSON.stringify({ class_name:className, date:date, period:period, records:records })
  })
  .then(function(r){ return r.json(); })
  .then(function(res){
    btn.disabled = false;
    btn.innerHTML = '<i class="fa fa-save"></i> Save Attendance';
    if (res.success) { showToast('ok', res.message || 'Attendance saved!'); }
    else { showToast('err', res.message || 'Save failed.'); }
  })
  .catch(function(){
    btn.disabled = false;
    btn.innerHTML = '<i class="fa fa-save"></i> Save Attendance';
    showToast('err','Network error.');
  });
}

function showToast(type, msg) {
  if (type==='ok') {
    document.getElementById('toastOkMsg').textContent = msg;
    var t = document.getElementById('toastOk');
    t.classList.add('show');
    setTimeout(function(){ t.classList.remove('show'); }, 3000);
  } else {
    document.getElementById('toastErrMsg').textContent = msg;
    var t = document.getElementById('toastErr');
    t.classList.add('show');
    setTimeout(function(){ t.classList.remove('show'); }, 3500);
  }
}

// Init summary on load
updateSummary();

function showTab(tab) {
  var isMark = tab === 'mark';
  document.getElementById('sectionMark').style.display    = isMark ? '' : 'none';
  document.getElementById('sectionHistory').style.display = isMark ? 'none' : '';
  document.getElementById('tabMark').style.background    = isMark ? 'rgba(255,255,255,.9)' : 'rgba(255,255,255,.2)';
  document.getElementById('tabMark').style.color         = isMark ? '#28a745' : 'white';
  document.getElementById('tabMark').style.border        = isMark ? 'none' : '2px solid rgba(255,255,255,.5)';
  document.getElementById('tabHistory').style.background = isMark ? 'rgba(255,255,255,.2)' : 'rgba(255,255,255,.9)';
  document.getElementById('tabHistory').style.color      = isMark ? 'white' : '#28a745';
  document.getElementById('tabHistory').style.border     = isMark ? '2px solid rgba(255,255,255,.5)' : 'none';
}

function loadHistory() {
  var date  = document.getElementById('histDate').value;
  var cls   = document.getElementById('histClass').value.trim();
  var tbody = document.getElementById('histBody');
  tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>';
  var url = 'attendance-history.php?date=' + encodeURIComponent(date) + '&class=' + encodeURIComponent(cls);
  fetch(url)
    .then(function(r){ return r.json(); })
    .then(function(d){
      if (!d.success || !d.records.length) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:40px;color:#aaa">No records found.</td></tr>'; return;
      }
      tbody.innerHTML = d.records.map(function(r){
        var st = r.status || 'present';
        var cls = st==='present'?'#d4edda;color:#155724':st==='absent'?'#f8d7da;color:#721c24':'#fff3cd;color:#856404';
        return '<tr>'
          + '<td>'+esc(r.attendance_date)+'</td>'
          + '<td>'+esc(r.student_name||'—')+'</td>'
          + '<td>'+esc(r.class_name||'—')+'</td>'
          + '<td style="font-size:12px;color:#666">'+esc(r.period||'—')+'</td>'
          + '<td><span style="background:'+cls+';padding:3px 10px;border-radius:10px;font-size:11px;font-weight:700">'+cap(st)+'</span></td>'
          + '<td style="font-size:12px;color:#666">'+esc(r.remarks||'—')+'</td>'
          + '</tr>';
      }).join('');
    })
    .catch(function(){ tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:red;padding:30px">Network error</td></tr>'; });
}
function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function cap(s){ return s ? s.charAt(0).toUpperCase()+s.slice(1) : ''; }
</script>
</body>
</html>
