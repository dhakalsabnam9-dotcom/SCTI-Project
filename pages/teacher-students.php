<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../index.php'); exit();
}
require_once '../includes/config.php';
try {
    $db = getDBConnection();
    $students = $db->query("SELECT * FROM students WHERE status='active' ORDER BY full_name ASC")->fetchAll();
} catch(Exception $e) { $students = []; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Students | SCTI Teacher Portal</title>
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
    .filter-bar{background:white;padding:20px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.1);margin-bottom:30px;display:flex;gap:15px;flex-wrap:wrap;align-items:center}
    .filter-select{padding:10px 15px;border:2px solid #dee2e6;border-radius:6px;font-size:14px}
    .search-input{flex:1;padding:10px 15px;border:2px solid #dee2e6;border-radius:6px;font-size:14px;min-width:250px}
    .table-wrap{background:white;border-radius:10px;padding:0;box-shadow:0 2px 10px rgba(0,0,0,.1);overflow:hidden}
    .students-table{width:100%;border-collapse:collapse}
    .students-table th{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:15px 16px;text-align:left;font-weight:600;font-size:13px}
    .students-table td{padding:14px 16px;border-bottom:1px solid #f0f0f0;font-size:14px;vertical-align:middle}
    .students-table tr:last-child td{border-bottom:none}
    .students-table tbody tr:hover{background:#f8fffe}
    .avatar{width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#28a745,#20c997);display:inline-flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:13px;flex-shrink:0}
    .student-name{display:flex;align-items:center;gap:10px}
    .status-badge{padding:5px 12px;border-radius:20px;font-size:11px;font-weight:700;display:inline-block}
    .status-active{background:#d4edda;color:#155724}
    .status-inactive{background:#f8d7da;color:#721c24}
    .status-suspended{background:#fff3cd;color:#856404}
    .action-btn{padding:7px 14px;border:none;border-radius:5px;cursor:pointer;font-size:12px;font-weight:600;transition:.2s;display:inline-flex;align-items:center;gap:5px}
    .btn-view{background:#004080;color:white}
    .btn-view:hover{background:#0059b3}
    .btn-msg{background:#28a745;color:white}
    .btn-msg:hover{background:#20c997}
    .empty-row td{text-align:center;padding:50px;color:#aaa;font-size:15px}
    /* MODAL */
    .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;align-items:center;justify-content:center}
    .modal-overlay.open{display:flex}
    .modal-box{background:white;border-radius:14px;width:100%;max-width:560px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.3);animation:mIn .25s ease}
    @keyframes mIn{from{transform:translateY(-28px);opacity:0}to{transform:translateY(0);opacity:1}}
    .modal-head{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:22px 28px;display:flex;justify-content:space-between;align-items:center;border-radius:14px 14px 0 0}
    .modal-head h2{margin:0;font-size:19px}
    .modal-close{background:rgba(255,255,255,.2);border:none;color:white;width:34px;height:34px;border-radius:50%;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center}
    .modal-close:hover{background:rgba(255,255,255,.35)}
    .modal-body{padding:28px}
    .profile-top{display:flex;align-items:center;gap:18px;margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid #eee}
    .profile-avatar{width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,#28a745,#20c997);display:flex;align-items:center;justify-content:center;color:white;font-size:24px;font-weight:700;flex-shrink:0}
    .profile-name{font-size:20px;font-weight:800;color:#1a202c;margin-bottom:4px}
    .profile-id{font-size:13px;color:#888}
    .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .info-item label{font-size:11px;font-weight:700;color:#aaa;text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:4px}
    .info-item span{font-size:14px;color:#333;font-weight:600}
    .footer{background:#2c3e50;color:white;text-align:center;padding:20px;border-radius:10px;margin-top:40px}
    /* MESSAGE MODAL */
    .msg-to{font-size:14px;color:#555;margin-bottom:18px;padding:10px 14px;background:#f0faf3;border-radius:8px;border-left:4px solid #28a745}
    .msg-to strong{color:#155724}
    .msg-label{font-size:12px;font-weight:700;color:#666;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;display:block}
    .msg-input{width:100%;padding:10px 14px;border:2px solid #dee2e6;border-radius:8px;font-size:14px;font-family:inherit;transition:.2s}
    .msg-input:focus{outline:none;border-color:#28a745;box-shadow:0 0 0 3px rgba(40,167,69,.1)}
    .msg-textarea{width:100%;padding:10px 14px;border:2px solid #dee2e6;border-radius:8px;font-size:14px;font-family:inherit;resize:vertical;min-height:120px;transition:.2s}
    .msg-textarea:focus{outline:none;border-color:#28a745;box-shadow:0 0 0 3px rgba(40,167,69,.1)}
    .msg-actions{display:flex;gap:10px;justify-content:flex-end;margin-top:20px}
    .btn-send{background:linear-gradient(135deg,#28a745,#20c997);color:white;border:none;padding:11px 28px;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:.2s}
    .btn-send:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(40,167,69,.35)}
    .btn-cancel{background:#f8f9fa;color:#555;border:2px solid #dee2e6;padding:11px 22px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;transition:.2s}
    .btn-cancel:hover{background:#e9ecef}
    .msg-success{display:none;text-align:center;padding:30px 20px}
    .msg-success i{font-size:52px;color:#28a745;margin-bottom:14px;display:block}
    .msg-success p{font-size:16px;color:#333;font-weight:600}
    .msg-success small{color:#888;font-size:13px}
  </style>
</head>
<body>
<div class="top-header">Student Management � View and manage your students</div>
<div class="container">
  <div class="page-header">
    <h1><i class="fa fa-users"></i> My Students</h1>
    <div class="breadcrumb"><a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Students</div>
  </div>
  <div class="filter-bar">
    <select class="filter-select" id="filterCourse" onchange="applyFilter()">
      <option value="">All Classes</option>
      <?php
      $courses = array_unique(array_column($students, 'course'));
      foreach($courses as $c) { if($c) echo '<option value="'.htmlspecialchars($c).'">'.htmlspecialchars($c).'</option>'; }
      ?>
    </select>
    <select class="filter-select" id="filterSem" onchange="applyFilter()">
      <option value="">All Semesters</option>
      <?php for($i=1;$i<=8;$i++) echo "<option value='$i'>Semester $i</option>"; ?>
    </select>
    <input type="text" class="search-input" id="searchInput" placeholder="Search by name, ID, or email..." oninput="applyFilter()">
  </div>
  <div class="table-wrap">
    <table class="students-table" id="studentsTable">
      <thead>
        <tr>
          <th>Student</th><th>Student ID</th><th>Email</th>
          <th>Program</th><th>Semester</th><th>Status</th><th>Actions</th>
        </tr>
      </thead>
      <tbody id="studentsBody">
        <?php if(empty($students)): ?>
        <tr class="empty-row"><td colspan="7"><i class="fa fa-users" style="font-size:40px;display:block;margin-bottom:12px;opacity:.3"></i>No students found in database.</td></tr>
        <?php else: foreach($students as $s):
          $initials = strtoupper(substr($s['full_name'],0,1));
          $parts = explode(' ',$s['full_name']);
          if(count($parts)>1) $initials = strtoupper(substr($parts[0],0,1).substr($parts[count($parts)-1],0,1));
          $badgeCls = $s['status']==='active' ? 'status-active' : ($s['status']==='suspended' ? 'status-suspended' : 'status-inactive');
        ?>
        <tr data-name="<?=htmlspecialchars(strtolower($s['full_name']))?>"
            data-id="<?=htmlspecialchars(strtolower($s['student_id']))?>"
            data-email="<?=htmlspecialchars(strtolower($s['email']))?>"
            data-course="<?=htmlspecialchars($s['course']??'')?>"
            data-sem="<?=htmlspecialchars($s['semester']??'')?>">
          <td><div class="student-name"><div class="avatar"><?=htmlspecialchars($initials)?></div><span><?=htmlspecialchars($s['full_name'])?></span></div></td>
          <td><?=htmlspecialchars($s['student_id'])?></td>
          <td><?=htmlspecialchars($s['email'])?></td>
          <td><?=htmlspecialchars($s['course']??'�')?></td>
          <td><?= $s['semester'] ? 'Sem '.$s['semester'] : '�' ?></td>
          <td><span class="status-badge <?=$badgeCls?>"><?=ucfirst($s['status'])?></span></td>
          <td>
            <button class="action-btn btn-view" onclick="viewStudent(<?=$s['id']?>)"><i class="fa fa-eye"></i> View</button>
            <button class="action-btn btn-msg" onclick="msgStudent('<?=htmlspecialchars($s['email'])?>','<?=htmlspecialchars($s['full_name'])?>')"><i class="fa fa-envelope"></i></button>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<footer class="footer"><p>� 2025 SCTI - Teacher Portal</p></footer>

<!-- VIEW MODAL -->
<div class="modal-overlay" id="viewModal">
  <div class="modal-box">
    <div class="modal-head">
      <h2><i class="fa fa-user"></i> Student Profile</h2>
      <button class="modal-close" onclick="document.getElementById('viewModal').classList.remove('open')"><i class="fa fa-times"></i></button>
    </div>
    <div class="modal-body" id="viewModalBody">Loading...</div>
  </div>
</div>

<!-- MESSAGE MODAL -->
<div class="modal-overlay" id="msgModal">
  <div class="modal-box">
    <div class="modal-head">
      <h2><i class="fa fa-envelope"></i> Send Message</h2>
      <button class="modal-close" onclick="closeMsgModal()"><i class="fa fa-times"></i></button>
    </div>
    <div class="modal-body">
      <!-- Form view -->
      <div id="msgForm">
        <div class="msg-to">To: <strong id="msgToName"></strong> &nbsp;�&nbsp; <span id="msgToEmail" style="color:#888;font-size:13px;"></span></div>
        <div style="margin-bottom:16px;">
          <label class="msg-label">Subject</label>
          <input type="text" id="msgSubject" class="msg-input" placeholder="Enter subject...">
        </div>
        <div>
          <label class="msg-label">Message</label>
          <textarea id="msgBody" class="msg-textarea" placeholder="Write your message here..."></textarea>
        </div>
        <div class="msg-actions">
          <button class="btn-cancel" onclick="closeMsgModal()">Cancel</button>
          <button class="btn-send" onclick="sendMessage()"><i class="fa fa-paper-plane"></i> Send Message</button>
        </div>
      </div>
      <!-- Success view -->
      <div class="msg-success" id="msgSuccess">
        <i class="fa fa-check-circle"></i>
        <p>Message Sent!</p>
        <small id="msgSuccessText"></small>
        <div style="margin-top:20px;">
          <button class="btn-send" onclick="closeMsgModal()"><i class="fa fa-check"></i> Done</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
var studentsData = <?php echo json_encode($students); ?>;

function viewStudent(id) {
  var s = studentsData.find(function(x){ return parseInt(x.id)===id; });
  if (!s) return;
  var parts = s.full_name.split(' ');
  var initials = parts.length > 1 ? (parts[0][0]+parts[parts.length-1][0]).toUpperCase() : s.full_name.substring(0,2).toUpperCase();
  document.getElementById('viewModalBody').innerHTML =
    '<div class="profile-top">'
    + '<div class="profile-avatar">' + initials + '</div>'
    + '<div><div class="profile-name">' + esc(s.full_name) + '</div><div class="profile-id">' + esc(s.student_id) + '</div></div>'
    + '</div>'
    + '<div class="info-grid">'
    + infoItem('Email', s.email)
    + infoItem('Phone', s.phone || '�')
    + infoItem('Program', s.course || '�')
    + infoItem('Semester', s.semester ? 'Semester ' + s.semester : '�')
    + infoItem('Status', s.status ? s.status.charAt(0).toUpperCase()+s.status.slice(1) : '�')
    + infoItem('Joined', s.created_at ? new Date(s.created_at).toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric'}) : '�')
    + infoItem('Address', s.address || '�')
    + infoItem('Username', s.username || '�')
    + '</div>';
  document.getElementById('viewModal').classList.add('open');
}

function infoItem(label, val) {
  return '<div class="info-item"><label>' + label + '</label><span>' + esc(String(val)) + '</span></div>';
}

function msgStudent(email, name) {
  document.getElementById('msgToName').textContent  = name;
  document.getElementById('msgToEmail').textContent = email;
  document.getElementById('msgSubject').value = '';
  document.getElementById('msgBody').value    = '';
  document.getElementById('msgForm').style.display    = 'block';
  document.getElementById('msgSuccess').style.display = 'none';
  document.getElementById('msgModal').classList.add('open');
}

function closeMsgModal() {
  document.getElementById('msgModal').classList.remove('open');
}

function sendMessage() {
  var subject = document.getElementById('msgSubject').value.trim();
  var body    = document.getElementById('msgBody').value.trim();
  var name    = document.getElementById('msgToName').textContent;
  var email   = document.getElementById('msgToEmail').textContent;

  if (!subject) { document.getElementById('msgSubject').focus(); return; }
  if (!body)    { document.getElementById('msgBody').focus();    return; }

  // Show success (in a real system you'd POST to a messages API here)
  document.getElementById('msgForm').style.display    = 'none';
  document.getElementById('msgSuccess').style.display = 'block';
  document.getElementById('msgSuccessText').textContent = 'Your message to ' + name + ' (' + email + ') has been sent.';
}

function applyFilter() {
  var course  = document.getElementById('filterCourse').value.toLowerCase();
  var sem     = document.getElementById('filterSem').value;
  var search  = document.getElementById('searchInput').value.toLowerCase();
  var rows    = document.querySelectorAll('#studentsBody tr[data-name]');
  rows.forEach(function(row) {
    var matchCourse = !course || row.dataset.course.toLowerCase() === course;
    var matchSem    = !sem    || String(row.dataset.sem) === sem;
    var matchSearch = !search || row.dataset.name.includes(search) || row.dataset.id.includes(search) || row.dataset.email.includes(search);
    row.style.display = (matchCourse && matchSem && matchSearch) ? '' : 'none';
  });
}

function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

document.getElementById('viewModal').addEventListener('click', function(e){ if(e.target===this) this.classList.remove('open'); });
document.getElementById('msgModal').addEventListener('click',  function(e){ if(e.target===this) closeMsgModal(); });
</script>
</body>
</html>
