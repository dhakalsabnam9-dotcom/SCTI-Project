<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../index.php'); exit();
}
$teacherName = $_SESSION['full_name'] ?? 'Teacher';
require_once '../includes/config.php';
try {
    $db = getDBConnection();
    $students = $db->query("SELECT id, full_name, student_id, course, semester FROM students WHERE status='active' ORDER BY full_name ASC")->fetchAll();
} catch(Exception $e) { $students = []; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Attendance Charts | SCTI Teacher</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
.btn-hdr.mark{background:rgba(255,255,255,.9);color:#28a745}
.btn-hdr.mark:hover{background:#fff}
/* Tabs */
.tabs{display:flex;gap:0;background:white;border-radius:14px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);margin:24px 20px 0;max-width:1100px;margin-left:auto;margin-right:auto}
.tab-btn{flex:1;padding:16px;border:none;background:transparent;font-size:14px;font-weight:700;cursor:pointer;color:#666;transition:.2s;display:flex;align-items:center;justify-content:center;gap:8px;border-bottom:3px solid transparent}
.tab-btn.active{color:#28a745;border-bottom-color:#28a745;background:#f8fffe}
.tab-btn:hover:not(.active){background:#f8f9fa;color:#28a745}
.wrap{max-width:1100px;margin:20px auto 60px;padding:0 20px}
/* Section */
.section{background:white;border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,.08);overflow:hidden;margin-bottom:22px}
.section-head{background:linear-gradient(135deg,#28a745,#20c997);padding:16px 24px;color:white;display:flex;align-items:center;gap:10px}
.section-head h3{margin:0;font-size:16px;font-weight:700}
.section-body{padding:22px 24px}
/* Controls */
.ctrl-row{display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;margin-bottom:20px}
.ctrl-group{display:flex;flex-direction:column;gap:5px;flex:1;min-width:140px}
.ctrl-group label{font-size:11px;font-weight:700;color:#666;text-transform:uppercase;letter-spacing:.5px}
.ctrl-group select,.ctrl-group input{padding:10px 13px;border:2px solid #e0e6ef;border-radius:9px;font-size:13px;font-family:inherit;transition:.2s}
.ctrl-group select:focus,.ctrl-group input:focus{outline:none;border-color:#28a745;box-shadow:0 0 0 3px rgba(40,167,69,.1)}
.btn-load{padding:10px 22px;background:linear-gradient(135deg,#28a745,#20c997);color:white;border:none;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:7px;transition:.2s;white-space:nowrap;align-self:flex-end}
.btn-load:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(40,167,69,.35)}
.custom-range{display:none;gap:10px;flex-wrap:wrap}
.custom-range.show{display:flex}
/* Stats pills */
.stats-row{display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap}
.stat-pill{flex:1;min-width:90px;border-radius:12px;padding:14px 16px;text-align:center}
.stat-pill .sv{font-size:24px;font-weight:800;line-height:1}
.stat-pill .sl{font-size:11px;margin-top:3px;font-weight:600;opacity:.8}
.sp-rate{background:linear-gradient(135deg,#28a745,#20c997);color:white}
.sp-present{background:#d1fae5;color:#065f46}
.sp-absent{background:#fee2e2;color:#991b1b}
.sp-late{background:#fff7ed;color:#9a3412}
/* Chart containers */
.chart-wrap{position:relative;height:320px;margin-bottom:20px}
.chart-wrap.tall{height:380px}
/* Donut + line side by side */
.chart-duo{display:grid;grid-template-columns:1fr 2fr;gap:20px;align-items:start}
.chart-duo .chart-wrap{height:280px}
/* Student table */
.stu-table{width:100%;border-collapse:collapse;font-size:13px}
.stu-table th{background:#f8fffe;padding:10px 14px;text-align:left;font-size:11px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e8f5e9}
.stu-table td{padding:10px 14px;border-bottom:1px solid #f0f0f0;color:#333}
.stu-table tr:last-child td{border-bottom:none}
.stu-table tr:hover td{background:#f8fffe}
.rate-bar-wrap{display:flex;align-items:center;gap:8px}
.rate-bar{height:8px;border-radius:4px;background:#e0e6ef;flex:1;overflow:hidden}
.rate-bar-fill{height:100%;border-radius:4px;transition:.4s}
.rate-val{font-size:12px;font-weight:700;min-width:38px;text-align:right}
/* Loading */
.loading{text-align:center;padding:60px 20px;color:#aaa}
.loading i{font-size:40px;display:block;margin-bottom:12px;color:#a7f3d0}
/* Tab panels */
.tab-panel{display:none}
.tab-panel.active{display:block}
footer{background:#00264d;color:white;text-align:center;padding:12px;font-size:13px;margin-top:40px}
@media(max-width:700px){.chart-duo{grid-template-columns:1fr}.tabs{flex-direction:column}.chart-wrap{height:240px}}
</style>
</head>
<body>
<div class="top-bar"><marquee>SCTI Teacher Portal — Attendance Charts & Analytics</marquee></div>
<div class="pg-header">
  <div>
    <h1><i class="fa fa-chart-bar"></i> Attendance Charts</h1>
    <div class="bc"><a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Attendance Charts</div>
  </div>
  <div class="hdr-btns">
    <a href="teacher-attendance.php" class="btn-hdr mark"><i class="fa fa-calendar-check"></i> Mark Attendance</a>
    <a href="../dashboards/teacher-dashboard.php" class="btn-hdr ghost"><i class="fa fa-arrow-left"></i> Back</a>
  </div>
</div>

<!-- Tabs -->
<div class="tabs">
  <button class="tab-btn active" onclick="switchTab('individual')"><i class="fa fa-user-graduate"></i> Individual Student</button>
  <button class="tab-btn" onclick="switchTab('all')"><i class="fa fa-users"></i> All Students</button>
</div>

<div class="wrap">

  <!-- ═══════════════════════════════════════════════════════════════
       TAB 1 — INDIVIDUAL STUDENT
  ═══════════════════════════════════════════════════════════════ -->
  <div class="tab-panel active" id="panel-individual">

    <div class="section">
      <div class="section-head"><i class="fa fa-user-graduate"></i><h3>Individual Student Attendance</h3></div>
      <div class="section-body">
        <div class="ctrl-row">
          <div class="ctrl-group" style="max-width:280px">
            <label><i class="fa fa-user"></i> Select Student</label>
            <select id="indStudent">
              <option value="">— Choose Student —</option>
              <?php foreach ($students as $s): ?>
              <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['full_name']) ?> (<?= htmlspecialchars($s['student_id']) ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="ctrl-group" style="max-width:200px">
            <label><i class="fa fa-calendar-alt"></i> Range</label>
            <select id="indRange" onchange="toggleCustomInd()">
              <option value="1week">Last 1 Week</option>
              <option value="1month" selected>Last 1 Month</option>
              <option value="3months">Last 3 Months</option>
              <option value="6months">Last 6 Months</option>
              <option value="1year">Last 1 Year</option>
              <option value="custom">Custom Range</option>
            </select>
          </div>
          <div class="custom-range" id="indCustom">
            <div class="ctrl-group"><label>From</label><input type="date" id="indFrom"></div>
            <div class="ctrl-group"><label>To</label><input type="date" id="indTo" value="<?= date('Y-m-d') ?>"></div>
          </div>
          <button class="btn-load" onclick="loadIndividual()"><i class="fa fa-chart-line"></i> Load Chart</button>
        </div>

        <div id="indContent">
          <div class="loading"><i class="fa fa-chart-pie"></i><p>Select a student and click Load Chart</p></div>
        </div>
      </div>
    </div>

  </div><!-- /panel-individual -->

  <!-- ═══════════════════════════════════════════════════════════════
       TAB 2 — ALL STUDENTS
  ═══════════════════════════════════════════════════════════════ -->
  <div class="tab-panel" id="panel-all">

    <div class="section">
      <div class="section-head"><i class="fa fa-users"></i><h3>All Students Attendance Overview</h3></div>
      <div class="section-body">
        <div class="ctrl-row">
          <div class="ctrl-group" style="max-width:200px">
            <label><i class="fa fa-calendar-alt"></i> Range</label>
            <select id="allRange" onchange="toggleCustomAll()">
              <option value="1week">Last 1 Week</option>
              <option value="1month" selected>Last 1 Month</option>
              <option value="3months">Last 3 Months</option>
              <option value="6months">Last 6 Months</option>
              <option value="1year">Last 1 Year</option>
              <option value="custom">Custom Range</option>
            </select>
          </div>
          <div class="custom-range" id="allCustom">
            <div class="ctrl-group"><label>From</label><input type="date" id="allFrom"></div>
            <div class="ctrl-group"><label>To</label><input type="date" id="allTo" value="<?= date('Y-m-d') ?>"></div>
          </div>
          <button class="btn-load" onclick="loadAll()"><i class="fa fa-chart-bar"></i> Load Charts</button>
        </div>

        <div id="allContent">
          <div class="loading"><i class="fa fa-chart-bar"></i><p>Click Load Charts to view all students</p></div>
        </div>
      </div>
    </div>

  </div><!-- /panel-all -->

</div><!-- /wrap -->

<footer>© 2025 SCTI — Teacher Portal</footer>

<script>
// ── Tab switching ────────────────────────────────────────────────────────────
function switchTab(name) {
  document.querySelectorAll('.tab-btn').forEach(function(b,i){
    b.classList.toggle('active', (i===0&&name==='individual')||(i===1&&name==='all'));
  });
  document.querySelectorAll('.tab-panel').forEach(function(p){
    p.classList.toggle('active', p.id==='panel-'+name);
  });
}

// ── Custom range toggles ─────────────────────────────────────────────────────
function toggleCustomInd() {
  document.getElementById('indCustom').classList.toggle('show', document.getElementById('indRange').value==='custom');
}
function toggleCustomAll() {
  document.getElementById('allCustom').classList.toggle('show', document.getElementById('allRange').value==='custom');
}

// ── Chart instances (destroy before recreate) ────────────────────────────────
var charts = {};
function destroyChart(id) { if (charts[id]) { charts[id].destroy(); delete charts[id]; } }

// ════════════════════════════════════════════════════════════════════════════
//  INDIVIDUAL STUDENT
// ════════════════════════════════════════════════════════════════════════════
function loadIndividual() {
  var sid   = document.getElementById('indStudent').value;
  var range = document.getElementById('indRange').value;
  var from  = document.getElementById('indFrom') ? document.getElementById('indFrom').value : '';
  var to    = document.getElementById('indTo')   ? document.getElementById('indTo').value   : '';
  if (!sid) { alert('Please select a student.'); return; }

  document.getElementById('indContent').innerHTML = '<div class="loading"><i class="fa fa-spinner fa-spin" style="color:#28a745"></i><p>Loading...</p></div>';

  var url = 'attendance-data.php?action=student_chart&student_id='+sid+'&range='+range;
  if (range==='custom') url += '&from='+from+'&to='+to;

  fetch(url).then(function(r){ return r.json(); }).then(function(d) {
    if (!d.success) { document.getElementById('indContent').innerHTML = '<div class="loading"><i class="fa fa-exclamation-triangle" style="color:#dc3545"></i><p>'+d.message+'</p></div>'; return; }
    renderIndividual(d);
  }).catch(function(e){ document.getElementById('indContent').innerHTML = '<div class="loading"><i class="fa fa-exclamation-triangle" style="color:#dc3545"></i><p>Network error: '+e.message+'</p></div>'; });
}

function renderIndividual(d) {
  var rateColor = d.rate >= 75 ? '#28a745' : d.rate >= 50 ? '#fd7e14' : '#dc3545';
  var html = '<div class="stats-row">'
    + '<div class="stat-pill sp-rate"><div class="sv">'+d.rate+'%</div><div class="sl">Attendance Rate</div></div>'
    + '<div class="stat-pill sp-present"><div class="sv">'+d.counts.present+'</div><div class="sl">Present</div></div>'
    + '<div class="stat-pill sp-absent"><div class="sv">'+d.counts.absent+'</div><div class="sl">Absent</div></div>'
    + '<div class="stat-pill sp-late"><div class="sv">'+d.counts.late+'</div><div class="sl">Late</div></div>'
    + '</div>';

  if (d.total === 0) {
    html += '<div class="loading"><i class="fa fa-calendar-xmark" style="color:#a7f3d0"></i><p>No attendance records found for this period.</p></div>';
    document.getElementById('indContent').innerHTML = html;
    return;
  }

  html += '<div class="chart-duo">'
    + '<div><div class="chart-wrap"><canvas id="indDonut"></canvas></div></div>'
    + '<div><div class="chart-wrap"><canvas id="indLine"></canvas></div></div>'
    + '</div>';

  document.getElementById('indContent').innerHTML = html;

  // Donut
  destroyChart('indDonut');
  charts['indDonut'] = new Chart(document.getElementById('indDonut'), {
    type: 'doughnut',
    data: {
      labels: ['Present','Absent','Late'],
      datasets: [{ data: [d.counts.present, d.counts.absent, d.counts.late],
        backgroundColor: ['#28a745','#dc3545','#fd7e14'],
        borderWidth: 3, borderColor: '#fff', hoverOffset: 8 }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: {
        legend: { position: 'bottom', labels: { padding: 16, font: { size: 13, weight: '700' } } },
        title: { display: true, text: 'Attendance Breakdown', font: { size: 14, weight: '700' }, color: '#333' }
      },
      cutout: '65%'
    }
  });

  // Line chart
  destroyChart('indLine');
  charts['indLine'] = new Chart(document.getElementById('indLine'), {
    type: 'bar',
    data: {
      labels: d.labels,
      datasets: [
        { label: 'Present', data: d.present, backgroundColor: 'rgba(40,167,69,.75)', borderRadius: 5 },
        { label: 'Absent',  data: d.absent,  backgroundColor: 'rgba(220,53,69,.75)', borderRadius: 5 },
        { label: 'Late',    data: d.late,    backgroundColor: 'rgba(253,126,20,.75)', borderRadius: 5 }
      ]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: {
        legend: { position: 'top', labels: { font: { size: 12, weight: '700' } } },
        title: { display: true, text: 'Attendance Over Time', font: { size: 14, weight: '700' }, color: '#333' }
      },
      scales: {
        x: { stacked: true, grid: { display: false }, ticks: { font: { size: 11 } } },
        y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } } }
      }
    }
  });
}

// ════════════════════════════════════════════════════════════════════════════
//  ALL STUDENTS
// ════════════════════════════════════════════════════════════════════════════
function loadAll() {
  var range = document.getElementById('allRange').value;
  var from  = document.getElementById('allFrom') ? document.getElementById('allFrom').value : '';
  var to    = document.getElementById('allTo')   ? document.getElementById('allTo').value   : '';

  document.getElementById('allContent').innerHTML = '<div class="loading"><i class="fa fa-spinner fa-spin" style="color:#28a745"></i><p>Loading...</p></div>';

  var url = 'attendance-data.php?action=all_chart&range='+range;
  if (range==='custom') url += '&from='+from+'&to='+to;

  fetch(url).then(function(r){ return r.json(); }).then(function(d) {
    if (!d.success) { document.getElementById('allContent').innerHTML = '<div class="loading"><i class="fa fa-exclamation-triangle" style="color:#dc3545"></i><p>'+d.message+'</p></div>'; return; }
    renderAll(d);
  }).catch(function(e){ document.getElementById('allContent').innerHTML = '<div class="loading"><i class="fa fa-exclamation-triangle" style="color:#dc3545"></i><p>Network error: '+e.message+'</p></div>'; });
}

function renderAll(d) {
  var html = '';

  // Trend line chart
  html += '<div class="section" style="margin-bottom:20px">'
    + '<div class="section-head" style="background:linear-gradient(135deg,#004080,#0059b3)"><i class="fa fa-chart-line"></i><h3 style="margin:0 0 0 8px">Daily Attendance Trend (All Students)</h3></div>'
    + '<div class="section-body"><div class="chart-wrap tall"><canvas id="allTrend"></canvas></div></div>'
    + '</div>';

  // Per-student rate bar chart
  html += '<div class="section" style="margin-bottom:20px">'
    + '<div class="section-head" style="background:linear-gradient(135deg,#6f42c1,#e83e8c)"><i class="fa fa-chart-bar"></i><h3 style="margin:0 0 0 8px">Attendance Rate per Student</h3></div>'
    + '<div class="section-body"><div class="chart-wrap tall"><canvas id="allBar"></canvas></div></div>'
    + '</div>';

  // Table
  html += '<div class="section">'
    + '<div class="section-head" style="background:linear-gradient(135deg,#fd7e14,#ffc107)"><i class="fa fa-table"></i><h3 style="margin:0 0 0 8px">Student Summary Table</h3></div>'
    + '<div class="section-body" style="padding:0"><div style="overflow-x:auto"><table class="stu-table">'
    + '<thead><tr><th>#</th><th>Student</th><th>Present</th><th>Absent</th><th>Late</th><th>Total</th><th>Rate</th></tr></thead><tbody>';

  d.labels.forEach(function(name, i) {
    var total = d.presents[i] + d.absents[i] + d.lates[i];
    var rate  = total > 0 ? Math.round((d.presents[i]+d.lates[i])/total*100) : 0;
    var rateColor = rate>=75?'#28a745':rate>=50?'#fd7e14':'#dc3545';
    html += '<tr>'
      + '<td style="color:#aaa;font-size:12px">'+(i+1)+'</td>'
      + '<td style="font-weight:700">'+esc(name)+'</td>'
      + '<td style="color:#28a745;font-weight:700">'+d.presents[i]+'</td>'
      + '<td style="color:#dc3545;font-weight:700">'+d.absents[i]+'</td>'
      + '<td style="color:#fd7e14;font-weight:700">'+d.lates[i]+'</td>'
      + '<td>'+total+'</td>'
      + '<td><div class="rate-bar-wrap"><div class="rate-bar"><div class="rate-bar-fill" style="width:'+rate+'%;background:'+rateColor+'"></div></div><span class="rate-val" style="color:'+rateColor+'">'+rate+'%</span></div></td>'
      + '</tr>';
  });
  html += '</tbody></table></div></div></div>';

  document.getElementById('allContent').innerHTML = html;

  // Trend chart
  destroyChart('allTrend');
  charts['allTrend'] = new Chart(document.getElementById('allTrend'), {
    type: 'line',
    data: {
      labels: d.trend.labels,
      datasets: [
        { label:'Present', data:d.trend.present, borderColor:'#28a745', backgroundColor:'rgba(40,167,69,.12)', fill:true, tension:.35, pointRadius:3, borderWidth:2 },
        { label:'Absent',  data:d.trend.absent,  borderColor:'#dc3545', backgroundColor:'rgba(220,53,69,.08)',  fill:true, tension:.35, pointRadius:3, borderWidth:2 },
        { label:'Late',    data:d.trend.late,    borderColor:'#fd7e14', backgroundColor:'rgba(253,126,20,.08)', fill:true, tension:.35, pointRadius:3, borderWidth:2 }
      ]
    },
    options: {
      responsive:true, maintainAspectRatio:false,
      plugins:{ legend:{ position:'top', labels:{ font:{size:12,weight:'700'} } } },
      scales:{
        x:{ grid:{display:false}, ticks:{font:{size:11}} },
        y:{ beginAtZero:true, ticks:{stepSize:1,font:{size:11}} }
      }
    }
  });

  // Per-student bar chart
  destroyChart('allBar');
  var bgColors = d.rates.map(function(r){ return r>=75?'rgba(40,167,69,.8)':r>=50?'rgba(253,126,20,.8)':'rgba(220,53,69,.8)'; });
  charts['allBar'] = new Chart(document.getElementById('allBar'), {
    type: 'bar',
    data: {
      labels: d.labels,
      datasets: [{ label:'Attendance Rate (%)', data:d.rates, backgroundColor:bgColors, borderRadius:6, borderSkipped:false }]
    },
    options: {
      responsive:true, maintainAspectRatio:false,
      plugins:{
        legend:{ display:false },
        tooltip:{ callbacks:{ label:function(ctx){ return ctx.parsed.y+'%'; } } }
      },
      scales:{
        x:{ grid:{display:false}, ticks:{font:{size:11}} },
        y:{ beginAtZero:true, max:100, ticks:{ callback:function(v){ return v+'%'; }, font:{size:11} },
            grid:{ color:'rgba(0,0,0,.05)' } }
      }
    }
  });
}

function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
</script>
</body>
</html>
