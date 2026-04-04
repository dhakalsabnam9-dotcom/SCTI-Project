<?php
ob_start();
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../index.php'); exit();
}
require_once '../includes/config.php';
try {
    $db = getDBConnection();
    $students = $db->query("SELECT id, full_name, student_id, course, semester FROM students WHERE status='active' ORDER BY full_name ASC")->fetchAll();
    
    // Load subjects from programs table (extract from content field)
    $subjects = [];
    $progs = $db->query("SELECT content FROM programs WHERE status='active' AND content IS NOT NULL AND content != ''")->fetchAll();
    foreach ($progs as $p) {
        // Support both pipe (|) and comma (,) separators
        $sep = strpos($p['content'], '|') !== false ? '|' : ',';
        $subjectList = array_filter(array_map('trim', explode($sep, $p['content'])));
        foreach ($subjectList as $subj) {
            if ($subj && !in_array($subj, $subjects)) {
                $subjects[] = $subj;
            }
        }
    }
    sort($subjects);
    
    // Get distinct semesters from students
    $semesters = [];
    $semRows = $db->query("SELECT DISTINCT semester FROM students WHERE status='active' AND semester IS NOT NULL AND semester != '' ORDER BY semester ASC")->fetchAll();
    foreach ($semRows as $sr) {
        $sem = trim($sr['semester']);
        if (is_numeric($sem)) $sem = 'Semester ' . $sem;
        if ($sem && !in_array($sem, $semesters)) $semesters[] = $sem;
    }
    if (empty($semesters)) $semesters = ['Semester 1','Semester 2','Semester 3','Semester 4','Semester 5','Semester 6'];

    // Fallback subjects
    if (empty($subjects)) {
        $subjects = ['Programming Fundamentals','Database Management','Web Development','Data Structures'];
    }
} catch(Exception $e) {
    $students  = [];
    $subjects  = ['Programming Fundamentals','Database Management','Web Development','Data Structures'];
    $semesters = ['Semester 1','Semester 2','Semester 3','Semester 4','Semester 5','Semester 6'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Enter Grades | SCTI Teacher Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Segoe UI',sans-serif;background:#f5f7fa}
    .top-header{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:10px 0;text-align:center;font-size:14px}
    .container{max-width:1400px;margin:0 auto;padding:20px}
    .page-header{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:30px;border-radius:10px;margin-bottom:24px;box-shadow:0 4px 15px rgba(40,167,69,.2)}
    .page-header h1{margin:0 0 8px;font-size:28px}
    .breadcrumb{font-size:14px}.breadcrumb a{color:white;text-decoration:none}
    .controls{background:white;padding:18px 22px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.1);margin-bottom:22px;display:flex;gap:15px;align-items:flex-end;flex-wrap:wrap}
    .ctrl-group{display:flex;flex-direction:column;gap:6px}
    .ctrl-group label{font-size:13px;font-weight:600;color:#555}
    .ctrl-input{padding:10px 14px;border:2px solid #dee2e6;border-radius:7px;font-size:14px;font-family:inherit}
    .ctrl-input:focus{outline:none;border-color:#28a745}
    .table-wrap{background:white;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.1);overflow:hidden}
    .grades-table{width:100%;border-collapse:collapse}
    .grades-table th{background:#f8f9fa;padding:13px 14px;text-align:left;color:#333;font-weight:700;font-size:13px;border-bottom:2px solid #dee2e6}
    .grades-table td{padding:11px 14px;border-bottom:1px solid #f0f0f0;vertical-align:middle}
    .grades-table tbody tr:hover{background:#f8fffe}
    .grade-input{width:72px;padding:7px 9px;border:2px solid #dee2e6;border-radius:5px;text-align:center;font-size:14px;font-family:inherit}
    .grade-input:focus{outline:none;border-color:#28a745}
    .grade-badge{display:inline-block;padding:4px 10px;border-radius:5px;font-weight:700;font-size:13px}
    .g-ap{background:#d4edda;color:#155724}.g-a{background:#d4edda;color:#155724}
    .g-am{background:#d4edda;color:#155724}.g-bp{background:#d1ecf1;color:#0c5460}
    .g-b{background:#d1ecf1;color:#0c5460}.g-bm{background:#d1ecf1;color:#0c5460}
    .g-cp{background:#fff3cd;color:#856404}.g-c{background:#fff3cd;color:#856404}
    .g-f{background:#f8d7da;color:#721c24}.g-na{background:#f0f0f0;color:#999}
    .save-wrap{text-align:center;padding:24px}
    .save-btn{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:13px 40px;border:none;border-radius:8px;cursor:pointer;font-size:15px;font-weight:700;transition:.2s;display:inline-flex;align-items:center;gap:9px}
    .save-btn:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(40,167,69,.35)}
    .save-btn:disabled{opacity:.6;cursor:not-allowed;transform:none}
    .toast{display:none;position:fixed;bottom:28px;right:28px;padding:14px 22px;border-radius:10px;font-size:14px;font-weight:600;z-index:9999;align-items:center;gap:10px;box-shadow:0 6px 20px rgba(0,0,0,.2)}
    .toast.show{display:flex;animation:tIn .3s ease}
    .toast-ok{background:#28a745;color:white}.toast-err{background:#dc3545;color:white}
    @keyframes tIn{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}
    .footer{background:#2c3e50;color:white;text-align:center;padding:20px;border-radius:10px;margin-top:30px}
    .stu-info{display:flex;align-items:center;gap:9px}
    .avatar{width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#28a745,#20c997);display:inline-flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:12px;flex-shrink:0}
    .total-cell{font-weight:700;font-size:15px}
  </style>
</head>
<body>
<div class="top-header">Grade Management — Enter and save student marks</div>
<div class="container">
  <div class="page-header">
    <h1><i class="fa fa-star"></i> Enter Grades</h1>
    <div class="breadcrumb"><a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Grades</div>
  </div>

  <div class="controls">
    <div class="ctrl-group">
      <label><i class="fa fa-layer-group"></i> Semester</label>
      <select class="ctrl-input" id="semesterSel">
        <option value="">-- All Semesters --</option>
        <?php foreach($semesters as $sem) echo '<option value="'.htmlspecialchars($sem).'">'.htmlspecialchars($sem).'</option>'; ?>
      </select>
    </div>
    <div class="ctrl-group">
      <label><i class="fa fa-book"></i> Subject</label>
      <select class="ctrl-input" id="subjectSel">
        <?php foreach($subjects as $s) echo '<option value="'.htmlspecialchars($s).'">'.htmlspecialchars($s).'</option>'; ?>
      </select>
    </div>
    <div class="ctrl-group">
      <label><i class="fa fa-calendar"></i> Exam Type</label>
      <select class="ctrl-input" id="examType">
        <option>Mid-Term</option>
        <option>Final</option>
        <option>Internal Assessment</option>
      </select>
    </div>
  </div>

  <div class="table-wrap">
    <table class="grades-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Student Name</th>
          <th>Roll No.</th>
          <th>Student ID</th>
          <th>Internal (40)</th>
          <th>External (60)</th>
          <th>Total</th>
          <th>Grade</th>
        </tr>
      </thead>
      <tbody id="gradesBody">
        <?php if(empty($students)): ?>
        <tr><td colspan="8" style="text-align:center;padding:50px;color:#aaa">No students found.</td></tr>
        <?php else: foreach($students as $i=>$s):
          $parts = explode(' ', $s['full_name']);
          $initials = strtoupper(substr($parts[0],0,1).(count($parts)>1?substr($parts[count($parts)-1],0,1):''));
        ?>
        <tr data-student-id="<?=$s['id']?>" data-student-db-id="<?=htmlspecialchars($s['student_id'])?>" data-semester="<?=htmlspecialchars(is_numeric(trim($s['semester']??'')) ? 'Semester '.trim($s['semester']) : trim($s['semester']??''))?>">
          <td><?=$i+1?></td>
          <td><div class="stu-info"><div class="avatar"><?=htmlspecialchars($initials)?></div><span><?=htmlspecialchars($s['full_name'])?></span></div></td>
          <td style="font-weight:700;color:#004080"><?=$i+1?></td>
          <td><?=htmlspecialchars($s['student_id'])?></td>
          <td><input type="number" class="grade-input internal-input" min="0" max="40" placeholder="0-40" oninput="calcTotal(this)"></td>
          <td><input type="number" class="grade-input external-input" min="0" max="60" placeholder="0-60" oninput="calcTotal(this)"></td>
          <td class="total-cell">—</td>
          <td class="grade-cell">—</td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
    <div class="save-wrap">
      <button class="save-btn" id="saveBtn" onclick="saveGrades()"><i class="fa fa-save"></i> Save Grades</button>
    </div>
  </div>
</div>
<footer class="footer"><p>© 2025 SCTI - Teacher Portal</p></footer>

<div class="toast toast-ok" id="toastOk"><i class="fa fa-check-circle"></i><span id="toastOkMsg">Saved!</span></div>
<div class="toast toast-err" id="toastErr"><i class="fa fa-times-circle"></i><span id="toastErrMsg">Error</span></div>

<script>
var subjects   = <?=json_encode($subjects)?>;
var semesters  = <?=json_encode($semesters)?>;

function getGrade(total) {
  if (total === '' || isNaN(total)) return {label:'—', cls:'g-na'};
  total = parseInt(total);
  if (total >= 90) return {label:'A+', cls:'g-ap'};
  if (total >= 80) return {label:'A',  cls:'g-a'};
  if (total >= 75) return {label:'A-', cls:'g-am'};
  if (total >= 70) return {label:'B+', cls:'g-bp'};
  if (total >= 65) return {label:'B',  cls:'g-b'};
  if (total >= 60) return {label:'B-', cls:'g-bm'};
  if (total >= 55) return {label:'C+', cls:'g-cp'};
  if (total >= 50) return {label:'C',  cls:'g-c'};
  return {label:'F', cls:'g-f'};
}

function calcTotal(inp) {
  var row = inp.closest('tr');
  var i = parseFloat(row.querySelector('.internal-input').value) || 0;
  var e = parseFloat(row.querySelector('.external-input').value) || 0;
  var total = i + e;
  var g = getGrade(total);
  row.querySelector('.total-cell').textContent = (i || e) ? total : '—';
  var gc = row.querySelector('.grade-cell');
  gc.innerHTML = (i || e) ? '<span class="grade-badge '+g.cls+'">'+g.label+'</span>' : '—';
}

function loadSubjectGrades() {
  var subj = document.getElementById('subjectSel').value;
  var exam = document.getElementById('examType').value;
  var sem  = document.getElementById('semesterSel').value;

  // Filter rows by semester first
  document.querySelectorAll('#gradesBody tr[data-student-id]').forEach(function(row) {
    var rowSem = row.dataset.semester || '';
    row.style.display = (!sem || rowSem === sem) ? '' : 'none';
  });

  // Clear inputs
  document.querySelectorAll('#gradesBody tr[data-student-id]').forEach(function(row) {
    row.querySelector('.internal-input').value = '';
    row.querySelector('.internal-input').placeholder = '...';
    row.querySelector('.external-input').value = '';
    row.querySelector('.external-input').placeholder = '...';
    row.querySelector('.total-cell').textContent = '—';
    row.querySelector('.grade-cell').innerHTML = '—';
  });

  var url = 'grade-get.php?subject=' + encodeURIComponent(subj) + '&exam_type=' + encodeURIComponent(exam);
  if (sem) url += '&semester=' + encodeURIComponent(sem);

  fetch(url)
    .then(function(r){ return r.json(); })
    .then(function(res){
      document.querySelectorAll('#gradesBody tr[data-student-id]').forEach(function(row) {
        var sid = row.dataset.studentId;
        var g = res.grades && res.grades[sid] ? res.grades[sid] : null;
        var iInput = row.querySelector('.internal-input');
        var eInput = row.querySelector('.external-input');
        iInput.value = g ? g.internal : '';
        iInput.placeholder = '0-40';
        eInput.value = g ? g.external : '';
        eInput.placeholder = '0-60';
        calcTotal(iInput);
      });
    })
    .catch(function(){
      document.querySelectorAll('#gradesBody tr[data-student-id]').forEach(function(row) {
        row.querySelector('.internal-input').placeholder = '0-40';
        row.querySelector('.external-input').placeholder = '0-60';
      });
    });
}

document.getElementById('subjectSel').addEventListener('change', loadSubjectGrades);
document.getElementById('examType').addEventListener('change', loadSubjectGrades);
document.getElementById('semesterSel').addEventListener('change', loadSubjectGrades);

function saveGrades() {
  var subject  = document.getElementById('subjectSel').value;
  var examType = document.getElementById('examType').value;
  var semester = document.getElementById('semesterSel').value;
  var records  = [];
  document.querySelectorAll('#gradesBody tr[data-student-id]').forEach(function(row) {
    if (row.style.display === 'none') return; // skip filtered-out rows
    var internal = row.querySelector('.internal-input').value;
    var external = row.querySelector('.external-input').value;
    if (internal === '' && external === '') return;
    records.push({
      student_id:    parseInt(row.dataset.studentId),
      student_db_id: row.dataset.studentDbId,
      internal:      parseFloat(internal) || 0,
      external:      parseFloat(external) || 0
    });
  });
  if (!records.length) { showToast('err','No grades to save.'); return; }
  var btn = document.getElementById('saveBtn');
  btn.disabled = true; btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
  fetch('grade-save.php', {
    method:'POST', headers:{'Content-Type':'application/json'},
    body: JSON.stringify({subject:subject, exam_type:examType, semester:semester, records:records})
  })
  .then(function(r){ return r.json(); })
  .then(function(res){
    btn.disabled = false; btn.innerHTML = '<i class="fa fa-save"></i> Save Grades';
    if (res.success) {
      showToast('ok', res.message || 'Grades saved!');
      resetGradeFields();
    } else { showToast('err', res.message || 'Save failed.'); }
  })
  .catch(function(){ btn.disabled=false; btn.innerHTML='<i class="fa fa-save"></i> Save Grades'; showToast('err','Network error.'); });
}

function resetGradeFields() {
  document.getElementById('subjectSel').selectedIndex = 0;
  document.getElementById('examType').selectedIndex = 0;
  document.getElementById('semesterSel').selectedIndex = 0;
  document.querySelectorAll('#gradesBody tr[data-student-id]').forEach(function(row) {
    row.querySelector('.internal-input').value = '';
    row.querySelector('.internal-input').placeholder = '0-40';
    row.querySelector('.external-input').value = '';
    row.querySelector('.external-input').placeholder = '0-60';
    row.querySelector('.total-cell').textContent = '—';
    row.querySelector('.grade-cell').innerHTML = '—';
  });
}

function showToast(type, msg) {
  var id = type==='ok' ? 'toastOk' : 'toastErr';
  var msgId = type==='ok' ? 'toastOkMsg' : 'toastErrMsg';
  document.getElementById(msgId).textContent = msg;
  var t = document.getElementById(id);
  t.classList.add('show');
  setTimeout(function(){ t.classList.remove('show'); }, 3000);
}

// Init — load grades for default subject+exam on page load
loadSubjectGrades();
</script>
</body>
</html>
