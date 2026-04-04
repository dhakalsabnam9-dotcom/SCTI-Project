<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header('Location: ../index.php'); exit();
}
require_once '../includes/config.php';

$studentId = $_SESSION['user_id'] ?? 0;

try {
    $db = getDBConnection();
    $stu = $db->prepare("SELECT * FROM students WHERE id=? LIMIT 1");
    $stu->execute([$studentId]);
    $stuData = $stu->fetch() ?: [];
    $semester = $stuData['semester'] ?? '';
    if (is_numeric(trim($semester))) $semester = 'Semester ' . trim($semester);

    $stmt = $db->prepare("SELECT subject, exam_type, semester, internal_marks, external_marks,
                          (internal_marks + external_marks) AS total_marks
                          FROM grades WHERE student_id = ?
                          ORDER BY subject ASC, exam_type ASC");
    $stmt->execute([$studentId]);
    $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group by subject
    $bySubject = [];
    $examTypes = [];
    foreach ($grades as $g) {
        $bySubject[$g['subject']][] = $g;
        if ($g['exam_type'] && !in_array($g['exam_type'], $examTypes)) $examTypes[] = $g['exam_type'];
    }

    // Stats
    $totals = array_filter(array_map(function($g){ return floatval($g['total_marks']); }, $grades));
    $avgTotal  = count($totals) ? round(array_sum($totals)/count($totals), 1) : 0;
    $maxTotal  = count($totals) ? max($totals) : 0;
    $minTotal  = count($totals) ? min($totals) : 0;
    $passCount = count(array_filter($totals, function($t){ return $t >= 50; }));

    // GPA
    $gpaTotal = 0; $gpaCount = 0;
    foreach ($grades as $g) {
        if (!empty($g['total_marks'])) {
            $gpaTotal += getGradePoint(floatval($g['total_marks']));
            $gpaCount++;
        }
    }
    $gpa = $gpaCount > 0 ? round($gpaTotal / $gpaCount, 2) : 0;

} catch(Exception $e) {
    $grades = []; $bySubject = []; $examTypes = []; $gpa = 0; $stuData = [];
    $avgTotal = 0; $maxTotal = 0; $minTotal = 0; $passCount = 0; $semester = '';
}

function getGradeLetter($m) {
    if ($m >= 90) return ['A+','g-ap'];
    if ($m >= 80) return ['A', 'g-a'];
    if ($m >= 75) return ['A-','g-am'];
    if ($m >= 70) return ['B+','g-bp'];
    if ($m >= 65) return ['B', 'g-b'];
    if ($m >= 60) return ['B-','g-bm'];
    if ($m >= 55) return ['C+','g-cp'];
    if ($m >= 50) return ['C', 'g-c'];
    return ['F','g-f'];
}
function getGradePoint($m) {
    if ($m >= 90) return 4.0;
    if ($m >= 80) return 3.7;
    if ($m >= 75) return 3.5;
    if ($m >= 70) return 3.3;
    if ($m >= 65) return 3.0;
    if ($m >= 60) return 2.7;
    if ($m >= 55) return 2.3;
    if ($m >= 50) return 2.0;
    return 0.0;
}
function gpaLabel($gpa) {
    if ($gpa >= 3.7) return ['Excellent', '#28a745'];
    if ($gpa >= 3.0) return ['Good', '#17a2b8'];
    if ($gpa >= 2.0) return ['Average', '#ffc107'];
    return ['Needs Improvement', '#dc3545'];
}
[$gpaText, $gpaColor] = gpaLabel($gpa);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Grades | SCTI Student Portal</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',sans-serif;background:#f5f7fa;min-height:100vh}
.top-bar{background:linear-gradient(135deg,#ffc107,#fd7e14);color:white;padding:8px 20px;font-size:13px}
.pg-header{background:linear-gradient(135deg,#ffc107,#fd7e14);color:#fff;padding:22px 28px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 18px rgba(255,193,7,.3)}
.pg-header h1{font-size:22px;font-weight:700;display:flex;align-items:center;gap:10px;margin:0 0 3px}
.pg-header .bc{font-size:12px;color:rgba(255,255,255,.8)}
.pg-header .bc a{color:#fff;text-decoration:none}
.btn-back{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;background:rgba(255,255,255,.2);color:#fff;text-decoration:none;display:flex;align-items:center;gap:6px;transition:.2s}
.btn-back:hover{background:rgba(255,255,255,.35)}
.wrap{max-width:1100px;margin:24px auto 60px;padding:0 20px}
/* Summary cards */
.summary-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:22px}
.sum-card{background:white;border-radius:14px;padding:18px 16px;text-align:center;box-shadow:0 2px 12px rgba(0,0,0,.07)}
.sum-card .sv{font-size:28px;font-weight:800;line-height:1}
.sum-card .sl{font-size:11px;color:#888;margin-top:4px;font-weight:600;text-transform:uppercase;letter-spacing:.5px}
.sc-gpa{border-top:4px solid #ffc107}.sc-gpa .sv{color:#fd7e14}
.sc-avg{border-top:4px solid #17a2b8}.sc-avg .sv{color:#17a2b8}
.sc-high{border-top:4px solid #28a745}.sc-high .sv{color:#28a745}
.sc-low{border-top:4px solid #dc3545}.sc-low .sv{color:#dc3545}
.sc-pass{border-top:4px solid #6f42c1}.sc-pass .sv{color:#6f42c1}
/* Filter bar */
.filter-bar{background:white;border-radius:14px;padding:16px 20px;box-shadow:0 2px 12px rgba(0,0,0,.07);margin-bottom:20px;display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end}
.fg{display:flex;flex-direction:column;gap:5px;flex:1;min-width:140px}
.fg label{font-size:11px;font-weight:700;color:#666;text-transform:uppercase;letter-spacing:.5px}
.fg select{padding:9px 12px;border:2px solid #e0e6ef;border-radius:9px;font-size:13px;font-family:inherit;transition:.2s}
.fg select:focus{outline:none;border-color:#ffc107;box-shadow:0 0 0 3px rgba(255,193,7,.15)}
/* Chart section */
.chart-section{background:white;border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,.07);overflow:hidden;margin-bottom:22px}
.section-head{background:linear-gradient(135deg,#ffc107,#fd7e14);padding:14px 20px;color:white;display:flex;align-items:center;gap:8px}
.section-head h3{margin:0;font-size:15px;font-weight:700}
.section-body{padding:20px}
.chart-wrap{position:relative;height:280px}
/* Subject cards */
.subject-card{background:white;border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,.07);overflow:hidden;margin-bottom:16px}
.subj-head{padding:14px 20px;display:flex;justify-content:space-between;align-items:center;cursor:pointer;transition:.15s}
.subj-head:hover{background:#fffbf0}
.subj-title{font-size:15px;font-weight:700;color:#1a202c;display:flex;align-items:center;gap:8px}
.subj-title i{color:#ffc107}
.subj-meta{display:flex;gap:10px;align-items:center}
.exam-pills{display:flex;gap:6px;flex-wrap:wrap}
.exam-pill{padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;background:#fff3cd;color:#856404}
.subj-body{border-top:1px solid #f0f0f0;overflow:visible;max-height:2000px;transition:max-height .3s ease}
.exam-row{display:grid;grid-template-columns:1fr 1fr 1fr 80px 80px 80px 80px;gap:0;border-bottom:1px solid #f8f8f8}
.exam-row:last-child{border-bottom:none}
.exam-row.header{background:#fafafa;font-size:11px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.5px}
.exam-row div{padding:12px 16px;font-size:13px;display:flex;align-items:center}
.exam-row.header div{padding:10px 16px}
/* Grade badges */
.grade-badge{display:inline-block;padding:4px 12px;border-radius:6px;font-weight:700;font-size:13px}
.g-ap,.g-a,.g-am{background:#d4edda;color:#155724}
.g-bp,.g-b,.g-bm{background:#d1ecf1;color:#0c5460}
.g-cp,.g-c{background:#fff3cd;color:#856404}
.g-f{background:#f8d7da;color:#721c24}
/* Progress bar */
.prog-wrap{display:flex;align-items:center;gap:8px;width:100%}
.prog-bg{height:6px;border-radius:3px;background:#e0e6ef;flex:1;overflow:hidden}
.prog-fill{height:100%;border-radius:3px;background:linear-gradient(90deg,#ffc107,#fd7e14);transition:.4s}
/* Empty */
.empty-state{text-align:center;padding:60px 20px;color:#aaa}
.empty-state i{font-size:56px;display:block;margin-bottom:14px;color:#ffe69c}
/* GPA ring */
.gpa-ring-wrap{display:flex;align-items:center;gap:24px;flex-wrap:wrap}
.gpa-ring{width:120px;height:120px;border-radius:50%;display:flex;flex-direction:column;align-items:center;justify-content:center;border:8px solid;flex-shrink:0}
.gpa-ring .gv{font-size:28px;font-weight:800}
.gpa-ring .gl{font-size:11px;font-weight:600;opacity:.8}
.gpa-info{flex:1}
.gpa-info h2{font-size:20px;font-weight:800;margin-bottom:4px}
.gpa-info p{font-size:13px;color:#666;margin-bottom:8px}
.gpa-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:700;color:white}
footer{background:#2c3e50;color:white;text-align:center;padding:14px;font-size:13px;margin-top:40px}
@media(max-width:700px){.exam-row{grid-template-columns:1fr 1fr;}.exam-row div:nth-child(n+3){display:none}.gpa-ring-wrap{justify-content:center}.charts-row{grid-template-columns:1fr!important}}
</style>
</head>
<body>
<div class="top-bar"><marquee>SCTI Student Portal &mdash; Academic Performance &amp; Grades</marquee></div>
<div class="pg-header">
  <div>
    <h1><i class="fa fa-chart-line"></i> My Grades</h1>
    <div class="bc"><a href="../dashboards/student-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Grades</div>
  </div>
  <a href="../dashboards/student-dashboard.php" class="btn-back"><i class="fa fa-arrow-left"></i> Back</a>
</div>

<div class="wrap">

  <!-- Summary cards -->
  <div class="summary-grid">
    <div class="sum-card sc-gpa">
      <div class="sv"><?=number_format($gpa,1)?></div>
      <div class="sl">GPA / 4.0</div>
    </div>
    <div class="sum-card sc-avg">
      <div class="sv"><?=$avgTotal?></div>
      <div class="sl">Avg Score</div>
    </div>
    <div class="sum-card sc-high">
      <div class="sv"><?=$maxTotal?></div>
      <div class="sl">Highest</div>
    </div>
    <div class="sum-card sc-low">
      <div class="sv"><?=$minTotal?></div>
      <div class="sl">Lowest</div>
    </div>
    <div class="sum-card sc-pass">
      <div class="sv"><?=$passCount?>/<?=count($grades)?></div>
      <div class="sl">Passed</div>
    </div>
  </div>

  <?php if (empty($grades)): ?>
  <div class="empty-state">
    <i class="fa fa-chart-bar"></i>
    <p>No grades recorded yet. Check back after exams.</p>
  </div>
  <?php else: ?>

  <!-- GPA card -->
  <div class="chart-section" style="margin-bottom:22px">
    <div class="section-head"><i class="fa fa-star"></i><h3>GPA Overview &mdash; <?=htmlspecialchars($stuData['full_name'] ?? '')?><?=$semester ? ' &nbsp;|&nbsp; '.$semester : ''?></h3></div>
    <div class="section-body">
      <div class="gpa-ring-wrap">
        <div class="gpa-ring" style="border-color:<?=$gpaColor?>;color:<?=$gpaColor?>">
          <span class="gv"><?=number_format($gpa,1)?></span>
          <span class="gl">/ 4.0</span>
        </div>
        <div class="gpa-info">
          <h2 style="color:<?=$gpaColor?>"><?=$gpaText?></h2>
          <p><?=count($grades)?> exam record(s) across <?=count($bySubject)?> subject(s)</p>
          <span class="gpa-badge" style="background:<?=$gpaColor?>"><?=$gpaText?> &mdash; GPA <?=number_format($gpa,2)?></span>
        </div>
        <div style="flex:2;min-width:200px"><div class="chart-wrap" style="height:200px"><canvas id="gpaDonut"></canvas></div></div>
      </div>
    </div>
  </div>

  <!-- Filter bar -->
  <div class="filter-bar">
    <div class="fg">
      <label><i class="fa fa-book"></i> Filter by Subject</label>
      <select id="filterSubject" onchange="applyFilter()">
        <option value="">All Subjects</option>
        <?php foreach(array_keys($bySubject) as $subj): ?>
        <option value="<?=htmlspecialchars($subj)?>"><?=htmlspecialchars($subj)?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="fg">
      <label><i class="fa fa-clipboard-list"></i> Filter by Exam Type</label>
      <select id="filterExam" onchange="applyFilter()">
        <option value="">All Exam Types</option>
        <?php foreach($examTypes as $et): ?>
        <option value="<?=htmlspecialchars($et)?>"><?=htmlspecialchars($et)?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <!-- Charts row -->
  <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-bottom:22px" class="charts-row">
    <!-- Marks bar chart -->
    <div class="chart-section" style="margin-bottom:0">
      <div class="section-head"><i class="fa fa-chart-bar"></i><h3>Marks by Subject &amp; Exam Type</h3></div>
      <div class="section-body"><div class="chart-wrap" style="height:300px"><canvas id="marksBar"></canvas></div></div>
    </div>
    <!-- Avg per exam type -->
    <div class="chart-section" style="margin-bottom:0">
      <div class="section-head"><i class="fa fa-chart-pie"></i><h3>Avg Score per Exam Type</h3></div>
      <div class="section-body">
        <div class="chart-wrap" style="height:200px"><canvas id="examTypeChart"></canvas></div>
        <div id="examTypeStats" style="margin-top:14px"></div>
      </div>
    </div>
  </div>

  <!-- Subject cards -->
  <div id="subjectCards">
  <?php foreach ($bySubject as $subj => $rows): ?>
  <?php
    $subjTotals = array_map(function($r){ return floatval($r['total_marks']); }, $rows);
    $subjAvg = count($subjTotals) ? round(array_sum($subjTotals)/count($subjTotals),1) : 0;
    [$bestLetter, $bestCls] = getGradeLetter($subjAvg);
  ?>
  <div class="subject-card" data-subject="<?=htmlspecialchars($subj)?>">
    <div class="subj-head" onclick="toggleCard(this)">
      <div class="subj-title"><i class="fa fa-book-open"></i><?=htmlspecialchars($subj)?></div>
      <div class="subj-meta">
        <div class="exam-pills">
          <?php foreach($rows as $r): ?>
          <span class="exam-pill" data-exam="<?=htmlspecialchars($r['exam_type'])?>"><?=htmlspecialchars($r['exam_type'])?></span>
          <?php endforeach; ?>
        </div>
        <span class="grade-badge <?=$bestCls?>"><?=$bestLetter?></span>
        <i class="fa fa-chevron-down" style="color:#aaa;font-size:12px;transition:.2s"></i>
      </div>
    </div>
    <div class="subj-body">
      <div class="exam-row header">
        <div>Exam Type</div><div>Semester</div><div>Internal /40</div><div>External /60</div><div>Total /100</div><div>Grade</div><div>Performance</div>
      </div>
      <?php foreach($rows as $r):
        $total = floatval($r['total_marks']);
        [$letter, $cls] = getGradeLetter($total);
        $pct = min(100, $total);
      ?>
      <div class="exam-row" data-exam="<?=htmlspecialchars($r['exam_type'])?>">
        <div style="font-weight:700"><?=htmlspecialchars($r['exam_type'] ?: '—')?></div>
        <div style="color:#888"><?=htmlspecialchars($r['semester'] ?: ($semester ?: '—'))?></div>
        <div style="color:#6f42c1;font-weight:700"><?=htmlspecialchars($r['internal_marks'])?></div>
        <div style="color:#e83e8c;font-weight:700"><?=htmlspecialchars($r['external_marks'])?></div>
        <div style="font-weight:800;font-size:15px"><?=$total > 0 ? $total.'/100' : '—'?></div>
        <div><?=$total > 0 ? '<span class="grade-badge '.$cls.'">'.$letter.'</span>' : '—'?></div>
        <div><div class="prog-wrap"><div class="prog-bg"><div class="prog-fill" style="width:<?=$pct?>%"></div></div><span style="font-size:11px;font-weight:700;color:#fd7e14;min-width:32px;text-align:right"><?=$pct?>%</span></div></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>
  </div>

  <?php endif; ?>
</div>
<footer>&copy; 2025 SCTI &mdash; Student Portal</footer>

<script>
var gradesData = <?=json_encode(array_values($grades))?>;
var charts = {};
function destroyChart(id){ if(charts[id]){ charts[id].destroy(); delete charts[id]; } }

// ── GPA Donut ────────────────────────────────────────────────────────────────
(function(){
  var canvas = document.getElementById('gpaDonut');
  if(!canvas||!gradesData.length) return;
  var gc={A:0,B:0,C:0,F:0};
  gradesData.forEach(function(g){
    var t=parseFloat(g.total_marks)||0;
    if(t>=70) gc.A++; else if(t>=60) gc.B++; else if(t>=50) gc.C++; else gc.F++;
  });
  charts['gpaDonut']=new Chart(canvas,{type:'doughnut',
    data:{labels:['A (70+)','B (60-69)','C (50-59)','F (<50)'],
      datasets:[{data:[gc.A,gc.B,gc.C,gc.F],backgroundColor:['#28a745','#17a2b8','#ffc107','#dc3545'],borderWidth:3,borderColor:'#fff',hoverOffset:6}]},
    options:{responsive:true,maintainAspectRatio:false,cutout:'65%',
      plugins:{legend:{position:'bottom',labels:{padding:10,font:{size:11,weight:'700'}}},
        title:{display:true,text:'Grade Distribution',font:{size:12,weight:'700'},color:'#555'}}}});
})();

// ── Avg per Exam Type chart ──────────────────────────────────────────────────
function buildExamTypeChart(data){
  destroyChart('examTypeChart');
  var canvas=document.getElementById('examTypeChart');
  if(!canvas) return;
  // Group by exam_type
  var map={};
  data.forEach(function(g){
    var et=g.exam_type||'Unknown';
    if(!map[et]) map[et]={sum:0,count:0};
    map[et].sum+=parseFloat(g.total_marks)||0;
    map[et].count++;
  });
  var labels=Object.keys(map);
  var avgs=labels.map(function(et){ return map[et].count?+(map[et].sum/map[et].count).toFixed(1):0; });
  var colors=['rgba(255,193,7,.85)','rgba(253,126,20,.85)','rgba(111,66,193,.85)','rgba(23,162,184,.85)'];

  charts['examTypeChart']=new Chart(canvas,{type:'bar',
    data:{labels:labels,datasets:[{label:'Avg Total',data:avgs,
      backgroundColor:labels.map(function(_,i){return colors[i%colors.length];}),
      borderRadius:8,borderSkipped:false}]},
    options:{responsive:true,maintainAspectRatio:false,
      plugins:{legend:{display:false},tooltip:{callbacks:{label:function(c){return c.parsed.y+'/100';}}}},
      scales:{x:{grid:{display:false},ticks:{font:{size:11}}},
        y:{beginAtZero:true,max:100,ticks:{font:{size:11}},grid:{color:'rgba(0,0,0,.05)'}}}
    }});

  // Stats below chart
  var html='';
  labels.forEach(function(et,i){
    var avg=avgs[i];
    var pct=Math.min(100,avg);
    var color=avg>=70?'#28a745':avg>=50?'#ffc107':'#dc3545';
    html+='<div style="margin-bottom:10px">'
      +'<div style="display:flex;justify-content:space-between;font-size:12px;font-weight:700;margin-bottom:3px">'
      +'<span>'+et+'</span><span style="color:'+color+'">'+avg+'/100</span></div>'
      +'<div style="height:7px;background:#e0e6ef;border-radius:4px;overflow:hidden">'
      +'<div style="height:100%;width:'+pct+'%;background:'+color+';border-radius:4px;transition:.4s"></div></div></div>';
  });
  var el=document.getElementById('examTypeStats');
  if(el) el.innerHTML=html;
}
buildExamTypeChart(gradesData);

// ── Marks Bar Chart ──────────────────────────────────────────────────────────
function buildBarChart(data){
  destroyChart('marksBar');
  var canvas=document.getElementById('marksBar');
  if(!canvas) return;
  var labels=data.map(function(g){return g.subject+(g.exam_type?' ('+g.exam_type+')':'');});
  var internals=data.map(function(g){return parseFloat(g.internal_marks)||0;});
  var externals=data.map(function(g){return parseFloat(g.external_marks)||0;});
  charts['marksBar']=new Chart(canvas,{type:'bar',
    data:{labels:labels,datasets:[
      {label:'Internal /40',data:internals,backgroundColor:'rgba(111,66,193,.75)',borderRadius:5},
      {label:'External /60',data:externals,backgroundColor:'rgba(253,126,20,.75)',borderRadius:5}
    ]},
    options:{responsive:true,maintainAspectRatio:false,
      plugins:{legend:{position:'top',labels:{font:{size:12,weight:'700'}}}},
      scales:{x:{grid:{display:false},ticks:{font:{size:11},maxRotation:40}},
        y:{beginAtZero:true,max:100,ticks:{font:{size:11}},grid:{color:'rgba(0,0,0,.05)'}}}
    }});
}
buildBarChart(gradesData);

// ── Filter ───────────────────────────────────────────────────────────────────
function applyFilter(){
  var subj=document.getElementById('filterSubject').value;
  var exam=document.getElementById('filterExam').value;

  document.querySelectorAll('.subject-card').forEach(function(card){
    var matchSubj=!subj||card.dataset.subject===subj;
    var rows=card.querySelectorAll('.exam-row:not(.header)');
    var anyMatch=false;
    rows.forEach(function(row){
      var matchExam=!exam||row.dataset.exam===exam;
      row.style.display=(matchSubj&&matchExam)?'':'none';
      if(matchSubj&&matchExam) anyMatch=true;
    });
    card.style.display=(matchSubj&&anyMatch)?'':'none';
  });

  var filtered=gradesData.filter(function(g){
    return(!subj||g.subject===subj)&&(!exam||g.exam_type===exam);
  });
  buildBarChart(filtered);
  buildExamTypeChart(filtered);
}

// ── Toggle subject card ──────────────────────────────────────────────────────
function toggleCard(head){
  var body=head.nextElementSibling;
  var icon=head.querySelector('.fa-chevron-down');
  var isOpen=body.style.maxHeight!=='0px'&&body.style.maxHeight!=='';
  if(isOpen){
    body.style.maxHeight='0px';
    body.style.overflow='hidden';
    if(icon) icon.style.transform='rotate(0deg)';
  } else {
    body.style.maxHeight='2000px';
    body.style.overflow='visible';
    if(icon) icon.style.transform='rotate(180deg)';
  }
}
</script>
</body>
</html>
