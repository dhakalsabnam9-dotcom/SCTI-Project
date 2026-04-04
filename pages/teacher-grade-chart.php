<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../index.php'); exit();
}
require_once '../includes/config.php';
try {
    $db = getDBConnection();
    $students = $db->query("SELECT id, full_name, student_id, course, semester FROM students WHERE status='active' ORDER BY full_name ASC")->fetchAll();
    $subjects = [];
    $progs = $db->query("SELECT content FROM programs WHERE status='active' AND content IS NOT NULL AND content != ''")->fetchAll();
    foreach ($progs as $p) {
        $sep = strpos($p['content'], '|') !== false ? '|' : ',';
        foreach (array_filter(array_map('trim', explode($sep, $p['content']))) as $s) {
            if (!in_array($s, $subjects)) $subjects[] = $s;
        }
    }
    sort($subjects);
    if (empty($subjects)) $subjects = ['Programming Fundamentals','Database Management','Web Development','Data Structures'];
    $semesters = [];
    $semRows = $db->query("SELECT DISTINCT semester FROM students WHERE status='active' AND semester IS NOT NULL AND semester != '' ORDER BY semester ASC")->fetchAll();
    foreach ($semRows as $sr) {
        $sem = trim($sr['semester']);
        if (is_numeric($sem)) $sem = 'Semester '.$sem;
        if ($sem && !in_array($sem, $semesters)) $semesters[] = $sem;
    }
    if (empty($semesters)) $semesters = ['Semester 1','Semester 2','Semester 3','Semester 4','Semester 5','Semester 6'];
} catch(Exception $e) { $students = []; $subjects = []; $semesters = []; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Grade Charts | SCTI Teacher</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',sans-serif;background:#f0f4f8;min-height:100vh}
.top-bar{background:#00264d;color:white;padding:7px 20px;font-size:13px}
.pg-header{background:linear-gradient(135deg,#6f42c1,#e83e8c);color:#fff;padding:20px 28px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 18px rgba(111,66,193,.25)}
.pg-header h1{font-size:22px;font-weight:700;display:flex;align-items:center;gap:10px;margin:0 0 3px}
.pg-header .bc{font-size:12px;color:rgba(255,255,255,.75)}
.pg-header .bc a{color:#fff;text-decoration:none}
.hdr-btns{display:flex;gap:8px}
.btn-hdr{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;border:none;transition:.2s;text-decoration:none}
.btn-hdr.ghost{background:rgba(255,255,255,.18);color:#fff}
.btn-hdr.ghost:hover{background:rgba(255,255,255,.32)}
.btn-hdr.enter{background:rgba(255,255,255,.9);color:#6f42c1}
.btn-hdr.enter:hover{background:#fff}
.tabs{display:flex;background:white;border-radius:14px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08);margin:24px auto 0;max-width:1100px}
.tab-btn{flex:1;padding:16px;border:none;background:transparent;font-size:14px;font-weight:700;cursor:pointer;color:#666;transition:.2s;display:flex;align-items:center;justify-content:center;gap:8px;border-bottom:3px solid transparent}
.tab-btn.active{color:#6f42c1;border-bottom-color:#6f42c1;background:#fdf8ff}
.tab-btn:hover:not(.active){background:#f8f9fa;color:#6f42c1}
.wrap{max-width:1100px;margin:20px auto 60px;padding:0 20px}
.section{background:white;border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,.08);overflow:hidden;margin-bottom:22px}
.section-head{background:linear-gradient(135deg,#6f42c1,#e83e8c);padding:16px 24px;color:white;display:flex;align-items:center;gap:10px}
.section-head h3{margin:0;font-size:16px;font-weight:700}
.section-body{padding:22px 24px}
.ctrl-row{display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;margin-bottom:20px}
.ctrl-group{display:flex;flex-direction:column;gap:5px;flex:1;min-width:140px}
.ctrl-group label{font-size:11px;font-weight:700;color:#666;text-transform:uppercase;letter-spacing:.5px}
.ctrl-group select,.ctrl-group input{padding:10px 13px;border:2px solid #e0e6ef;border-radius:9px;font-size:13px;font-family:inherit;transition:.2s}
.ctrl-group select:focus,.ctrl-group input:focus{outline:none;border-color:#6f42c1;box-shadow:0 0 0 3px rgba(111,66,193,.1)}
.btn-load{padding:10px 22px;background:linear-gradient(135deg,#6f42c1,#e83e8c);color:white;border:none;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:7px;transition:.2s;white-space:nowrap;align-self:flex-end}
.btn-load:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(111,66,193,.35)}
.stats-row{display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap}
.stat-pill{flex:1;min-width:90px;border-radius:12px;padding:14px 16px;text-align:center}
.stat-pill .sv{font-size:24px;font-weight:800;line-height:1}
.stat-pill .sl{font-size:11px;margin-top:3px;font-weight:600;opacity:.85}
.sp-avg{background:linear-gradient(135deg,#6f42c1,#e83e8c);color:white}
.sp-high{background:#d4edda;color:#155724}
.sp-low{background:#f8d7da;color:#721c24}
.sp-pass{background:#d1ecf1;color:#0c5460}
.chart-wrap{position:relative;height:320px;margin-bottom:20px}
.chart-wrap.tall{height:380px}
.chart-duo{display:grid;grid-template-columns:1fr 2fr;gap:20px;align-items:start}
.chart-duo .chart-wrap{height:280px}
.grade-table{width:100%;border-collapse:collapse;font-size:13px}
.grade-table th{background:#fdf8ff;padding:10px 14px;text-align:left;font-size:11px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e9d8fd}
.grade-table td{padding:10px 14px;border-bottom:1px solid #f0f0f0;color:#333}
.grade-table tr:last-child td{border-bottom:none}
.grade-table tr:hover td{background:#fdf8ff}
.grade-badge{display:inline-block;padding:3px 10px;border-radius:5px;font-weight:700;font-size:12px}
.g-ap{background:#d4edda;color:#155724}.g-a{background:#d4edda;color:#155724}
.g-am{background:#d4edda;color:#155724}.g-bp{background:#d1ecf1;color:#0c5460}
.g-b{background:#d1ecf1;color:#0c5460}.g-bm{background:#d1ecf1;color:#0c5460}
.g-cp{background:#fff3cd;color:#856404}.g-c{background:#fff3cd;color:#856404}
.g-f{background:#f8d7da;color:#721c24}.g-na{background:#f0f0f0;color:#999}
.bar-wrap{display:flex;align-items:center;gap:8px}
.bar-bg{height:8px;border-radius:4px;background:#e0e6ef;flex:1;overflow:hidden}
.bar-fill{height:100%;border-radius:4px;background:linear-gradient(90deg,#6f42c1,#e83e8c);transition:.4s}
.loading{text-align:center;padding:60px 20px;color:#aaa}
.loading i{font-size:40px;display:block;margin-bottom:12px;color:#e9d8fd}
.tab-panel{display:none}
.tab-panel.active{display:block}
footer{background:#00264d;color:white;text-align:center;padding:12px;font-size:13px;margin-top:40px}
@media(max-width:700px){.chart-duo{grid-template-columns:1fr}.tabs{flex-direction:column}.chart-wrap{height:240px}}
</style>
</head>
<body>
<div class="top-bar"><marquee>SCTI Teacher Portal — Grade Charts & Analytics</marquee></div>
<div class="pg-header">
  <div>
    <h1><i class="fa fa-chart-line"></i> Grade Charts</h1>
    <div class="bc"><a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Grade Charts</div>
  </div>
  <div class="hdr-btns">
    <a href="teacher-grades.php" class="btn-hdr enter"><i class="fa fa-star"></i> Enter Grades</a>
    <a href="../dashboards/teacher-dashboard.php" class="btn-hdr ghost"><i class="fa fa-arrow-left"></i> Back</a>
  </div>
</div>

<div class="tabs">
  <button class="tab-btn active" onclick="switchTab('individual')"><i class="fa fa-user-graduate"></i> Individual Student</button>
  <button class="tab-btn" onclick="switchTab('all')"><i class="fa fa-users"></i> All Students</button>
</div>

<div class="wrap">

  <!-- TAB 1: INDIVIDUAL -->
  <div class="tab-panel active" id="panel-individual">
    <div class="section">
      <div class="section-head"><i class="fa fa-user-graduate"></i><h3>Individual Student Grades</h3></div>
      <div class="section-body">
        <div class="ctrl-row">
          <div class="ctrl-group" style="max-width:260px">
            <label><i class="fa fa-user"></i> Student</label>
            <select id="indStudent">
              <option value="">-- Select Student --</option>
              <?php foreach($students as $s): ?>
              <option value="<?=$s['id']?>"><?=htmlspecialchars($s['full_name'])?> (<?=htmlspecialchars($s['student_id'])?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="ctrl-group" style="max-width:180px">
            <label><i class="fa fa-layer-group"></i> Semester</label>
            <select id="indSemester">
              <option value="">All Semesters</option>
              <?php foreach($semesters as $sem): ?>
              <option><?=htmlspecialchars($sem)?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="ctrl-group" style="max-width:180px">
            <label><i class="fa fa-book"></i> Subject</label>
            <select id="indSubject">
              <option value="">All Subjects</option>
              <?php foreach($subjects as $s): ?>
              <option><?=htmlspecialchars($s)?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="ctrl-group" style="max-width:180px">
            <label><i class="fa fa-clipboard-list"></i> Exam Type</label>
            <select id="indExam">
              <option value="">All Exams</option>
              <option>Mid-Term</option>
              <option>Final</option>
              <option>Internal Assessment</option>
            </select>
          </div>
          <button class="btn-load" onclick="loadIndividual()"><i class="fa fa-chart-line"></i> Load Chart</button>
        </div>
        <div id="indContent">
          <div class="loading"><i class="fa fa-chart-pie"></i><p>Select a student and click Load Chart</p></div>
        </div>
      </div>
    </div>
  </div>

  <!-- TAB 2: ALL STUDENTS -->
  <div class="tab-panel" id="panel-all">
    <div class="section">
      <div class="section-head"><i class="fa fa-users"></i><h3>All Students Grade Overview</h3></div>
      <div class="section-body">
        <div class="ctrl-row">
          <div class="ctrl-group" style="max-width:180px">
            <label><i class="fa fa-layer-group"></i> Semester</label>
            <select id="allSemester">
              <option value="">All Semesters</option>
              <?php foreach($semesters as $sem): ?>
              <option><?=htmlspecialchars($sem)?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="ctrl-group" style="max-width:200px">
            <label><i class="fa fa-book"></i> Subject</label>
            <select id="allSubject">
              <option value="">All Subjects</option>
              <?php foreach($subjects as $s): ?>
              <option><?=htmlspecialchars($s)?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="ctrl-group" style="max-width:180px">
            <label><i class="fa fa-clipboard-list"></i> Exam Type</label>
            <select id="allExam">
              <option value="">All Exams</option>
              <option>Mid-Term</option>
              <option>Final</option>
              <option>Internal Assessment</option>
            </select>
          </div>
          <button class="btn-load" onclick="loadAll()"><i class="fa fa-chart-bar"></i> Load Charts</button>
        </div>
        <div id="allContent">
          <div class="loading"><i class="fa fa-chart-bar"></i><p>Click Load Charts to view all students</p></div>
        </div>
      </div>
    </div>
  </div>

</div>
<footer>&copy; 2025 SCTI &mdash; Teacher Portal</footer>

<script>
var charts = {};
function destroyChart(id){ if(charts[id]){ charts[id].destroy(); delete charts[id]; } }

function switchTab(name){
  document.querySelectorAll('.tab-btn').forEach(function(b,i){
    b.classList.toggle('active',(i===0&&name==='individual')||(i===1&&name==='all'));
  });
  document.querySelectorAll('.tab-panel').forEach(function(p){
    p.classList.toggle('active', p.id==='panel-'+name);
  });
}

function getGrade(t){
  if(t===''||isNaN(t)) return {label:'—',cls:'g-na'};
  t=parseInt(t);
  if(t>=90) return {label:'A+',cls:'g-ap'};
  if(t>=80) return {label:'A', cls:'g-a'};
  if(t>=75) return {label:'A-',cls:'g-am'};
  if(t>=70) return {label:'B+',cls:'g-bp'};
  if(t>=65) return {label:'B', cls:'g-b'};
  if(t>=60) return {label:'B-',cls:'g-bm'};
  if(t>=55) return {label:'C+',cls:'g-cp'};
  if(t>=50) return {label:'C', cls:'g-c'};
  return {label:'F',cls:'g-f'};
}

// ── INDIVIDUAL ───────────────────────────────────────────────────────────────
function loadIndividual(){
  var sid  = document.getElementById('indStudent').value;
  var sem  = document.getElementById('indSemester').value;
  var subj = document.getElementById('indSubject').value;
  var exam = document.getElementById('indExam').value;
  if(!sid){ alert('Please select a student.'); return; }

  document.getElementById('indContent').innerHTML = '<div class="loading"><i class="fa fa-spinner fa-spin" style="color:#6f42c1"></i><p>Loading...</p></div>';

  var url = 'grade-chart-data.php?action=individual&student_id='+sid;
  if(sem)  url += '&semester='+encodeURIComponent(sem);
  if(subj) url += '&subject='+encodeURIComponent(subj);
  if(exam) url += '&exam_type='+encodeURIComponent(exam);

  fetch(url).then(function(r){return r.json();}).then(function(d){
    if(!d.success){ document.getElementById('indContent').innerHTML='<div class="loading"><i class="fa fa-exclamation-triangle" style="color:#dc3545"></i><p>'+(d.message||'Error')+'</p></div>'; return; }
    renderIndividual(d);
  }).catch(function(e){ document.getElementById('indContent').innerHTML='<div class="loading"><i class="fa fa-exclamation-triangle" style="color:#dc3545"></i><p>Network error: '+e.message+'</p></div>'; });
}

function renderIndividual(d){
  if(!d.rows||!d.rows.length){
    document.getElementById('indContent').innerHTML='<div class="loading"><i class="fa fa-star" style="color:#e9d8fd"></i><p>No grade records found for this filter.</p></div>';
    return;
  }
  var avg = d.avg_total, high = d.max_total, low = d.min_total;
  var passCount = d.rows.filter(function(r){return parseFloat(r.total)>=50;}).length;
  var html = '<div class="stats-row">'
    +'<div class="stat-pill sp-avg"><div class="sv">'+avg+'</div><div class="sl">Avg Total</div></div>'
    +'<div class="stat-pill sp-high"><div class="sv">'+high+'</div><div class="sl">Highest</div></div>'
    +'<div class="stat-pill sp-low"><div class="sv">'+low+'</div><div class="sl">Lowest</div></div>'
    +'<div class="stat-pill sp-pass"><div class="sv">'+passCount+'/'+d.rows.length+'</div><div class="sl">Passed</div></div>'
    +'</div>';

  // Bar chart: total per subject/exam
  var labels = d.rows.map(function(r){ return r.subject+(r.exam_type?' ('+r.exam_type+')':''); });
  var internals = d.rows.map(function(r){ return parseFloat(r.internal_marks)||0; });
  var externals = d.rows.map(function(r){ return parseFloat(r.external_marks)||0; });

  html += '<div class="chart-wrap tall"><canvas id="indBar"></canvas></div>';

  // Table
  html += '<div style="overflow-x:auto"><table class="grade-table"><thead><tr>'
    +'<th>#</th><th>Subject</th><th>Exam Type</th><th>Semester</th><th>Internal</th><th>External</th><th>Total</th><th>Grade</th>'
    +'</tr></thead><tbody>';
  d.rows.forEach(function(r,i){
    var total = (parseFloat(r.internal_marks)||0)+(parseFloat(r.external_marks)||0);
    var g = getGrade(total);
    html += '<tr>'
      +'<td style="color:#aaa;font-size:12px">'+(i+1)+'</td>'
      +'<td style="font-weight:600">'+esc(r.subject)+'</td>'
      +'<td>'+esc(r.exam_type||'—')+'</td>'
      +'<td>'+esc(r.semester||'—')+'</td>'
      +'<td style="color:#6f42c1;font-weight:700">'+r.internal_marks+'</td>'
      +'<td style="color:#e83e8c;font-weight:700">'+r.external_marks+'</td>'
      +'<td style="font-weight:800;font-size:15px">'+total+'</td>'
      +'<td><span class="grade-badge '+g.cls+'">'+g.label+'</span></td>'
      +'</tr>';
  });
  html += '</tbody></table></div>';
  document.getElementById('indContent').innerHTML = html;

  destroyChart('indBar');
  charts['indBar'] = new Chart(document.getElementById('indBar'),{
    type:'bar',
    data:{
      labels:labels,
      datasets:[
        {label:'Internal',data:internals,backgroundColor:'rgba(111,66,193,.75)',borderRadius:5},
        {label:'External',data:externals,backgroundColor:'rgba(232,62,140,.75)',borderRadius:5}
      ]
    },
    options:{
      responsive:true,maintainAspectRatio:false,
      plugins:{legend:{position:'top',labels:{font:{size:12,weight:'700'}}},
        title:{display:true,text:'Marks by Subject / Exam',font:{size:14,weight:'700'},color:'#333'}},
      scales:{
        x:{stacked:false,grid:{display:false},ticks:{font:{size:11}}},
        y:{beginAtZero:true,max:100,ticks:{font:{size:11}},grid:{color:'rgba(0,0,0,.05)'}}
      }
    }
  });
}

// ── ALL STUDENTS ─────────────────────────────────────────────────────────────
function loadAll(){
  var sem  = document.getElementById('allSemester').value;
  var subj = document.getElementById('allSubject').value;
  var exam = document.getElementById('allExam').value;

  document.getElementById('allContent').innerHTML='<div class="loading"><i class="fa fa-spinner fa-spin" style="color:#6f42c1"></i><p>Loading...</p></div>';

  var url = 'grade-chart-data.php?action=all';
  if(sem)  url += '&semester='+encodeURIComponent(sem);
  if(subj) url += '&subject='+encodeURIComponent(subj);
  if(exam) url += '&exam_type='+encodeURIComponent(exam);

  fetch(url).then(function(r){return r.json();}).then(function(d){
    if(!d.success){ document.getElementById('allContent').innerHTML='<div class="loading"><i class="fa fa-exclamation-triangle" style="color:#dc3545"></i><p>'+(d.message||'Error')+'</p></div>'; return; }
    renderAll(d);
  }).catch(function(e){ document.getElementById('allContent').innerHTML='<div class="loading"><i class="fa fa-exclamation-triangle" style="color:#dc3545"></i><p>Network error: '+e.message+'</p></div>'; });
}

function renderAll(d){
  if(!d.students||!d.students.length){
    document.getElementById('allContent').innerHTML='<div class="loading"><i class="fa fa-star" style="color:#e9d8fd"></i><p>No grade records found for this filter.</p></div>';
    return;
  }

  var labels   = d.students.map(function(s){return s.name;});
  var avgs     = d.students.map(function(s){return parseFloat(s.avg_total)||0;});
  var bgColors = avgs.map(function(a){return a>=75?'rgba(40,167,69,.8)':a>=50?'rgba(111,66,193,.8)':'rgba(220,53,69,.8)';});

  var html = '';

  // Bar chart: avg total per student
  html += '<div class="section" style="margin-bottom:20px">'
    +'<div class="section-head"><i class="fa fa-chart-bar"></i><h3>Average Total per Student</h3></div>'
    +'<div class="section-body"><div class="chart-wrap tall"><canvas id="allBar"></canvas></div></div>'
    +'</div>';

  // Grade distribution donut
  html += '<div class="section" style="margin-bottom:20px">'
    +'<div class="section-head" style="background:linear-gradient(135deg,#fd7e14,#ffc107)"><i class="fa fa-chart-pie"></i><h3>Grade Distribution</h3></div>'
    +'<div class="section-body"><div class="chart-duo">'
    +'<div class="chart-wrap"><canvas id="allDonut"></canvas></div>'
    +'<div class="chart-wrap"><canvas id="allPassFail"></canvas></div>'
    +'</div></div></div>';

  // Summary table
  html += '<div class="section"><div class="section-head" style="background:linear-gradient(135deg,#17a2b8,#138496)"><i class="fa fa-table"></i><h3>Student Summary</h3></div>'
    +'<div class="section-body" style="padding:0"><div style="overflow-x:auto"><table class="grade-table">'
    +'<thead><tr><th>#</th><th>Student</th><th>Avg Internal</th><th>Avg External</th><th>Avg Total</th><th>Grade</th><th>Performance</th></tr></thead><tbody>';

  d.students.forEach(function(s,i){
    var g = getGrade(Math.round(s.avg_total));
    var pct = Math.min(100, Math.round(s.avg_total));
    html += '<tr>'
      +'<td style="color:#aaa;font-size:12px">'+(i+1)+'</td>'
      +'<td style="font-weight:700">'+esc(s.name)+'</td>'
      +'<td style="color:#6f42c1;font-weight:700">'+parseFloat(s.avg_internal).toFixed(1)+'</td>'
      +'<td style="color:#e83e8c;font-weight:700">'+parseFloat(s.avg_external).toFixed(1)+'</td>'
      +'<td style="font-weight:800;font-size:15px">'+parseFloat(s.avg_total).toFixed(1)+'</td>'
      +'<td><span class="grade-badge '+g.cls+'">'+g.label+'</span></td>'
      +'<td><div class="bar-wrap"><div class="bar-bg"><div class="bar-fill" style="width:'+pct+'%"></div></div><span style="font-size:12px;font-weight:700;min-width:38px;text-align:right;color:#6f42c1">'+pct+'%</span></div></td>'
      +'</tr>';
  });
  html += '</tbody></table></div></div></div>';

  document.getElementById('allContent').innerHTML = html;

  // Avg bar chart
  destroyChart('allBar');
  charts['allBar'] = new Chart(document.getElementById('allBar'),{
    type:'bar',
    data:{labels:labels,datasets:[{label:'Avg Total',data:avgs,backgroundColor:bgColors,borderRadius:6,borderSkipped:false}]},
    options:{
      responsive:true,maintainAspectRatio:false,
      plugins:{legend:{display:false},tooltip:{callbacks:{label:function(c){return c.parsed.y.toFixed(1)+'/100';}}}},
      scales:{
        x:{grid:{display:false},ticks:{font:{size:11}}},
        y:{beginAtZero:true,max:100,ticks:{callback:function(v){return v;},font:{size:11}},grid:{color:'rgba(0,0,0,.05)'}}
      }
    }
  });

  // Grade distribution donut
  var gradeCounts = {A:0,B:0,C:0,F:0};
  d.students.forEach(function(s){
    var g = getGrade(Math.round(s.avg_total));
    if(g.label.startsWith('A')) gradeCounts.A++;
    else if(g.label.startsWith('B')) gradeCounts.B++;
    else if(g.label.startsWith('C')) gradeCounts.C++;
    else gradeCounts.F++;
  });
  destroyChart('allDonut');
  charts['allDonut'] = new Chart(document.getElementById('allDonut'),{
    type:'doughnut',
    data:{
      labels:['A (90+)','B (65-89)','C (50-64)','F (<50)'],
      datasets:[{data:[gradeCounts.A,gradeCounts.B,gradeCounts.C,gradeCounts.F],
        backgroundColor:['#28a745','#17a2b8','#ffc107','#dc3545'],
        borderWidth:3,borderColor:'#fff',hoverOffset:8}]
    },
    options:{responsive:true,maintainAspectRatio:false,cutout:'65%',
      plugins:{legend:{position:'bottom',labels:{padding:14,font:{size:12,weight:'700'}}},
        title:{display:true,text:'Grade Distribution',font:{size:13,weight:'700'},color:'#333'}}}
  });

  // Pass/Fail bar
  var passCount = d.students.filter(function(s){return parseFloat(s.avg_total)>=50;}).length;
  var failCount = d.students.length - passCount;
  destroyChart('allPassFail');
  charts['allPassFail'] = new Chart(document.getElementById('allPassFail'),{
    type:'bar',
    data:{
      labels:['Pass (>=50)','Fail (<50)'],
      datasets:[{data:[passCount,failCount],backgroundColor:['rgba(40,167,69,.8)','rgba(220,53,69,.8)'],borderRadius:8,borderSkipped:false}]
    },
    options:{responsive:true,maintainAspectRatio:false,
      plugins:{legend:{display:false},title:{display:true,text:'Pass / Fail Count',font:{size:13,weight:'700'},color:'#333'}},
      scales:{x:{grid:{display:false}},y:{beginAtZero:true,ticks:{stepSize:1}}}}
  });
}

function esc(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
</script>
</body>
</html>
