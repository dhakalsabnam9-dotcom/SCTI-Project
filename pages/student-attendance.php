<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header('Location: ../index.php'); exit();
}
$fullName  = $_SESSION['full_name'] ?? 'Student';
$studentId = $_SESSION['user_id']   ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Attendance | SCTI Student</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',sans-serif;background:#f0f4f8;min-height:100vh}
.top-bar{background:#00264d;color:white;padding:7px 20px;font-size:13px}
.pg-header{background:linear-gradient(135deg,#004080,#0059b3);color:#fff;padding:20px 28px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 18px rgba(0,64,128,.25)}
.pg-header h1{font-size:22px;font-weight:700;display:flex;align-items:center;gap:10px;margin:0 0 3px}
.pg-header .bc{font-size:12px;color:rgba(255,255,255,.75)}
.pg-header .bc a{color:#fff;text-decoration:none}
.hdr-btns{display:flex;gap:8px}
.btn-hdr{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;border:none;transition:.2s;text-decoration:none}
.btn-hdr.ghost{background:rgba(255,255,255,.18);color:#fff}
.btn-hdr.ghost:hover{background:rgba(255,255,255,.32)}
.wrap{max-width:1100px;margin:24px auto 60px;padding:0 20px}
/* Section */
.section{background:white;border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,.08);overflow:hidden;margin-bottom:22px}
.section-head{background:linear-gradient(135deg,#004080,#0059b3);padding:16px 24px;color:white;display:flex;align-items:center;gap:10px}
.section-head h3{margin:0;font-size:16px;font-weight:700}
.section-body{padding:22px 24px}
/* Controls */
.ctrl-row{display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;margin-bottom:20px}
.ctrl-group{display:flex;flex-direction:column;gap:5px;flex:1;min-width:140px}
.ctrl-group label{font-size:11px;font-weight:700;color:#666;text-transform:uppercase;letter-spacing:.5px}
.ctrl-group select,.ctrl-group input{padding:10px 13px;border:2px solid #e0e6ef;border-radius:9px;font-size:13px;font-family:inherit;transition:.2s}
.ctrl-group select:focus,.ctrl-group input:focus{outline:none;border-color:#004080;box-shadow:0 0 0 3px rgba(0,64,128,.1)}
.btn-load{padding:10px 22px;background:linear-gradient(135deg,#004080,#0059b3);color:white;border:none;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:7px;transition:.2s;white-space:nowrap;align-self:flex-end}
.btn-load:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,64,128,.35)}
.custom-range{display:none;gap:10px;flex-wrap:wrap}
.custom-range.show{display:flex}
/* Stats pills */
.stats-row{display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap}
.stat-pill{flex:1;min-width:90px;border-radius:12px;padding:14px 16px;text-align:center}
.stat-pill .sv{font-size:26px;font-weight:800;line-height:1}
.stat-pill .sl{font-size:11px;margin-top:4px;font-weight:600;opacity:.85}
.sp-rate{background:linear-gradient(135deg,#004080,#0059b3);color:white}
.sp-present{background:#d1fae5;color:#065f46}
.sp-absent{background:#fee2e2;color:#991b1b}
.sp-late{background:#fff7ed;color:#9a3412}
/* Chart duo */
.chart-duo{display:grid;grid-template-columns:1fr 2fr;gap:20px;align-items:start}
.chart-wrap{position:relative;height:300px;margin-bottom:20px}
.chart-duo .chart-wrap{height:280px;margin-bottom:0}
/* Log table */
.log-table{width:100%;border-collapse:collapse;font-size:13px}
.log-table th{background:#f0f4ff;padding:10px 14px;text-align:left;font-size:11px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #dce8ff}
.log-table td{padding:10px 14px;border-bottom:1px solid #f0f0f0;color:#333}
.log-table tr:last-child td{border-bottom:none}
.log-table tr:hover td{background:#f5f8ff}
.badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.badge-present{background:#d1fae5;color:#065f46}
.badge-absent{background:#fee2e2;color:#991b1b}
.badge-late{background:#fff7ed;color:#9a3412}
/* Loading */
.loading{text-align:center;padding:60px 20px;color:#aaa}
.loading i{font-size:40px;display:block;margin-bottom:12px;color:#a0c4ff}
footer{background:#00264d;color:white;text-align:center;padding:12px;font-size:13px;margin-top:40px}
@media(max-width:700px){.chart-duo{grid-template-columns:1fr}.chart-wrap{height:240px}}
</style>
</head>
<body>
<div class="top-bar"><marquee>SCTI Student Portal — My Attendance Records</marquee></div>
<div class="pg-header">
  <div>
    <h1><i class="fa fa-calendar-check"></i> My Attendance</h1>
    <div class="bc"><a href="../dashboards/student-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / My Attendance</div>
  </div>
  <div class="hdr-btns">
    <a href="../dashboards/student-dashboard.php" class="btn-hdr ghost"><i class="fa fa-arrow-left"></i> Back</a>
  </div>
</div>

<div class="wrap">

  <!-- Charts Section -->
  <div class="section">
    <div class="section-head"><i class="fa fa-chart-pie"></i><h3>Attendance Overview</h3></div>
    <div class="section-body">
      <div class="ctrl-row">
        <div class="ctrl-group" style="max-width:220px">
          <label><i class="fa fa-calendar-alt"></i> Date Range</label>
          <select id="chartRange" onchange="toggleCustom()">
            <option value="1week">Last 1 Week</option>
            <option value="1month" selected>Last 1 Month</option>
            <option value="3months">Last 3 Months</option>
            <option value="6months">Last 6 Months</option>
            <option value="1year">Last 1 Year</option>
            <option value="custom">Custom Range</option>
          </select>
        </div>
        <div class="custom-range" id="customRange">
          <div class="ctrl-group"><label>From</label><input type="date" id="fromDate"></div>
          <div class="ctrl-group"><label>To</label><input type="date" id="toDate" value="<?= date('Y-m-d') ?>"></div>
        </div>
        <button class="btn-load" onclick="loadChart()"><i class="fa fa-chart-line"></i> Load</button>
      </div>
      <div id="chartContent">
        <div class="loading"><i class="fa fa-chart-pie"></i><p>Loading your attendance...</p></div>
      </div>
    </div>
  </div>

  <!-- Log Section -->
  <div class="section">
    <div class="section-head"><i class="fa fa-list-alt"></i><h3>Detailed Attendance Log</h3></div>
    <div class="section-body">
      <div id="logContent">
        <div class="loading"><i class="fa fa-spinner fa-spin" style="color:#a0c4ff"></i><p>Loading log...</p></div>
      </div>
    </div>
  </div>

</div>

<footer>© 2025 Sindhuli Community Technical Institute (SCTI) — Student Portal</footer>

<script>
var charts = {};
function destroyChart(id){ if(charts[id]){ charts[id].destroy(); delete charts[id]; } }

function toggleCustom(){
  document.getElementById('customRange').classList.toggle('show', document.getElementById('chartRange').value==='custom');
}

function buildUrl(base, action){
  var range = document.getElementById('chartRange').value;
  var from  = document.getElementById('fromDate').value;
  var to    = document.getElementById('toDate').value;
  var url   = base + '?action=' + action + '&range=' + range;
  if(range==='custom') url += '&from='+from+'&to='+to;
  return url;
}

function loadChart(){
  document.getElementById('chartContent').innerHTML = '<div class="loading"><i class="fa fa-spinner fa-spin" style="color:#004080"></i><p>Loading...</p></div>';
  document.getElementById('logContent').innerHTML   = '<div class="loading"><i class="fa fa-spinner fa-spin" style="color:#a0c4ff"></i><p>Loading log...</p></div>';

  var chartUrl = buildUrl('student-attendance-data.php','chart');
  var logUrl   = buildUrl('student-attendance-data.php','log');

  fetch(chartUrl).then(function(r){ return r.json(); }).then(function(d){
    if(!d.success){ document.getElementById('chartContent').innerHTML='<div class="loading"><i class="fa fa-exclamation-triangle" style="color:#dc3545"></i><p>'+d.message+'</p></div>'; return; }
    renderChart(d);
  }).catch(function(e){ document.getElementById('chartContent').innerHTML='<div class="loading"><i class="fa fa-exclamation-triangle" style="color:#dc3545"></i><p>Error: '+e.message+'</p></div>'; });

  fetch(logUrl).then(function(r){ return r.json(); }).then(function(d){
    if(!d.success){ document.getElementById('logContent').innerHTML='<div class="loading"><i class="fa fa-exclamation-triangle" style="color:#dc3545"></i><p>'+d.message+'</p></div>'; return; }
    renderLog(d.records);
  }).catch(function(e){ document.getElementById('logContent').innerHTML='<div class="loading"><i class="fa fa-exclamation-triangle" style="color:#dc3545"></i><p>Error: '+e.message+'</p></div>'; });
}

function renderChart(d){
  var html = '<div class="stats-row">'
    + '<div class="stat-pill sp-rate"><div class="sv">'+d.rate+'%</div><div class="sl">Attendance Rate</div></div>'
    + '<div class="stat-pill sp-present"><div class="sv">'+d.counts.present+'</div><div class="sl">Present</div></div>'
    + '<div class="stat-pill sp-absent"><div class="sv">'+d.counts.absent+'</div><div class="sl">Absent</div></div>'
    + '<div class="stat-pill sp-late"><div class="sv">'+d.counts.late+'</div><div class="sl">Late</div></div>'
    + '</div>';

  if(d.total === 0){
    html += '<div class="loading"><i class="fa fa-calendar-xmark" style="color:#a0c4ff"></i><p>No attendance records found for this period.</p></div>';
    document.getElementById('chartContent').innerHTML = html;
    return;
  }

  html += '<div class="chart-duo">'
    + '<div><div class="chart-wrap"><canvas id="donutChart"></canvas></div></div>'
    + '<div><div class="chart-wrap"><canvas id="barChart"></canvas></div></div>'
    + '</div>';

  document.getElementById('chartContent').innerHTML = html;

  // Donut
  destroyChart('donutChart');
  charts['donutChart'] = new Chart(document.getElementById('donutChart'),{
    type:'doughnut',
    data:{
      labels:['Present','Absent','Late'],
      datasets:[{ data:[d.counts.present,d.counts.absent,d.counts.late],
        backgroundColor:['#0059b3','#dc3545','#fd7e14'],
        borderWidth:3, borderColor:'#fff', hoverOffset:8 }]
    },
    options:{
      responsive:true, maintainAspectRatio:false,
      plugins:{
        legend:{ position:'bottom', labels:{ padding:16, font:{size:13,weight:'700'} } },
        title:{ display:true, text:'Attendance Breakdown', font:{size:14,weight:'700'}, color:'#333' }
      },
      cutout:'65%'
    }
  });

  // Stacked bar
  destroyChart('barChart');
  charts['barChart'] = new Chart(document.getElementById('barChart'),{
    type:'bar',
    data:{
      labels:d.labels,
      datasets:[
        { label:'Present', data:d.present, backgroundColor:'rgba(0,89,179,.75)', borderRadius:5 },
        { label:'Absent',  data:d.absent,  backgroundColor:'rgba(220,53,69,.75)', borderRadius:5 },
        { label:'Late',    data:d.late,    backgroundColor:'rgba(253,126,20,.75)', borderRadius:5 }
      ]
    },
    options:{
      responsive:true, maintainAspectRatio:false,
      plugins:{
        legend:{ position:'top', labels:{ font:{size:12,weight:'700'} } },
        title:{ display:true, text:'Attendance Over Time', font:{size:14,weight:'700'}, color:'#333' }
      },
      scales:{
        x:{ stacked:true, grid:{display:false}, ticks:{font:{size:11}} },
        y:{ stacked:true, beginAtZero:true, ticks:{stepSize:1,font:{size:11}} }
      }
    }
  });
}

function renderLog(records){
  if(!records || records.length===0){
    document.getElementById('logContent').innerHTML='<div class="loading"><i class="fa fa-calendar-xmark" style="color:#a0c4ff"></i><p>No records found for this period.</p></div>';
    return;
  }
  var html='<div style="overflow-x:auto"><table class="log-table"><thead><tr>'
    +'<th>Date</th><th>Class</th><th>Period</th><th>Status</th><th>Late Reason</th><th>Marked By</th>'
    +'</tr></thead><tbody>';
  records.forEach(function(r){
    var badgeCls = r.status==='present'?'badge-present':r.status==='absent'?'badge-absent':'badge-late';
    html+='<tr>'
      +'<td style="font-weight:600">'+esc(r.attendance_date)+'</td>'
      +'<td>'+esc(r.class_name||'—')+'</td>'
      +'<td>'+esc(r.period||'—')+'</td>'
      +'<td><span class="badge '+badgeCls+'">'+capitalize(r.status)+'</span></td>'
      +'<td style="color:#888;font-size:12px">'+esc(r.late_reason||'—')+'</td>'
      +'<td style="color:#555">'+esc(r.marked_by_name||'—')+'</td>'
      +'</tr>';
  });
  html+='</tbody></table></div>';
  document.getElementById('logContent').innerHTML=html;
}

function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function capitalize(s){ return s?s.charAt(0).toUpperCase()+s.slice(1):s; }

// Auto-load on page open
loadChart();
</script>
</body>
</html>
