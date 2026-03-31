<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../index.php'); exit();
}
require_once '../includes/config.php';

$teacherId = $_SESSION['user_id'] ?? 0;
$t = [];
try {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM teachers WHERE id = ? LIMIT 1");
    $stmt->execute([$teacherId]);
    $t = $stmt->fetch() ?: [];
    $totalStudents = $db->query("SELECT COUNT(*) FROM students WHERE status='active'")->fetchColumn();
    $stmt2 = $db->prepare("SELECT COUNT(*) FROM assignments WHERE created_by=?");
    $stmt2->execute([$teacherId]);
    $totalAssignments = $stmt2->fetchColumn();
} catch(Exception $e) { $totalStudents = 0; $totalAssignments = 0; }

$fullName      = $t['full_name']        ?? ($_SESSION['full_name'] ?? 'Teacher');
$username      = $t['username']         ?? ($_SESSION['username']  ?? 'teacher');
$email         = $t['email']            ?? 'â€”';
$phone         = $t['phone']            ?? 'â€”';
$address       = $t['address']          ?? 'â€”';
$department    = $t['department']       ?? 'â€”';
$designation   = $t['designation']      ?? 'â€”';
$teacherDbId   = $t['teacher_id']       ?? 'â€”';
$qualification = $t['qualification']    ?? 'â€”';
$expYears      = $t['experience']        ?? 'â€”';
$joinedDate    = !empty($t['created_at']) ? date('M Y', strtotime($t['created_at'])) : 'â€”';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile | SCTI Teacher Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Segoe UI',sans-serif;background:#f5f7fa}
    .top-header{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:10px 20px;font-size:14px}
    .footer{background:#2c3e50;color:white;text-align:center;padding:20px;margin-top:30px}
    .container{max-width:1200px;margin:0 auto;padding:20px}
    .page-header{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:30px;border-radius:10px;margin-bottom:30px;box-shadow:0 4px 15px rgba(40,167,69,.2)}
    .page-header h1{margin:0 0 8px;font-size:28px}
    .breadcrumb{background:transparent!important;padding:0;font-size:14px}
    .breadcrumb a{color:white;text-decoration:none}
    .profile-grid{display:grid;grid-template-columns:300px 1fr;gap:25px}
    .profile-card{background:white;border-radius:10px;padding:30px;box-shadow:0 2px 10px rgba(0,0,0,.1);text-align:center}
    .avatar{width:120px;height:120px;border-radius:50%;background:linear-gradient(135deg,#28a745,#20c997);display:flex;align-items:center;justify-content:center;font-size:48px;color:white;margin:0 auto 20px}
    .profile-name{font-size:22px;font-weight:700;color:#333;margin-bottom:5px}
    .profile-role{display:inline-block;background:#d4edda;color:#155724;padding:5px 15px;border-radius:20px;font-size:13px;margin-bottom:20px}
    .profile-stats{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:20px}
    .pstat{background:#f8f9fa;border-radius:8px;padding:12px}
    .pstat .num{font-size:22px;font-weight:700;color:#28a745}
    .pstat .lbl{font-size:11px;color:#666}
    .info-card{background:white;border-radius:10px;padding:25px;box-shadow:0 2px 10px rgba(0,0,0,.1);margin-bottom:20px}
    .info-card h3{color:#28a745;margin-bottom:20px;font-size:18px;border-bottom:2px solid #f0f0f0;padding-bottom:10px;display:flex;justify-content:space-between;align-items:center}
    .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:15px}
    .info-item label{display:block;font-size:12px;color:#999;margin-bottom:4px;text-transform:uppercase}
    .info-item span{font-size:15px;color:#333;font-weight:500}
    .edit-btn{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:10px 20px;border:none;border-radius:6px;cursor:pointer;font-size:14px;transition:.3s;display:inline-flex;align-items:center;gap:8px}
    .edit-btn:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(40,167,69,.3)}
    .edit-btn-sm{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:6px 14px;border:none;border-radius:5px;cursor:pointer;font-size:12px;display:inline-flex;align-items:center;gap:5px}
    /* MODAL */
    .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;align-items:center;justify-content:center}
    .modal-overlay.open{display:flex}
    .modal-box{background:white;border-radius:14px;width:100%;max-width:560px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.3);animation:mIn .25s ease}
    @keyframes mIn{from{transform:translateY(-24px);opacity:0}to{transform:translateY(0);opacity:1}}
    .modal-head{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:20px 26px;display:flex;justify-content:space-between;align-items:center;border-radius:14px 14px 0 0}
    .modal-head h2{margin:0;font-size:18px}
    .modal-close{background:rgba(255,255,255,.2);border:none;color:white;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:15px;display:flex;align-items:center;justify-content:center}
    .modal-close:hover{background:rgba(255,255,255,.35)}
    .modal-body{padding:26px}
    .form-group{margin-bottom:16px}
    .form-group label{display:block;font-size:12px;font-weight:700;color:#555;margin-bottom:5px;text-transform:uppercase}
    .form-control{width:100%;padding:10px 14px;border:2px solid #dee2e6;border-radius:7px;font-size:14px;font-family:inherit;transition:.2s}
    .form-control:focus{outline:none;border-color:#28a745}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .btn-save{width:100%;background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:13px;border:none;border-radius:8px;font-size:15px;font-weight:700;cursor:pointer;transition:.2s;display:flex;align-items:center;justify-content:center;gap:9px;margin-top:6px}
    .btn-save:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(40,167,69,.35)}
    .btn-save:disabled{opacity:.6;cursor:not-allowed;transform:none}
    .toast{display:none;position:fixed;bottom:28px;right:28px;padding:14px 22px;border-radius:10px;font-size:14px;font-weight:600;z-index:99999;align-items:center;gap:10px;box-shadow:0 6px 20px rgba(0,0,0,.2)}
    .toast.show{display:flex;animation:tIn .3s ease}
    .toast-ok{background:#28a745;color:white}.toast-err{background:#dc3545;color:white}
    @keyframes tIn{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}
  </style>
</head>
<body>
<div class="top-header">My Profile - View and manage your personal and professional information</div>
<div class="container">
  <div class="page-header">
    <h1><i class="fa fa-user-circle"></i> My Profile</h1>
    <div class="breadcrumb"><a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / My Profile</div>
  </div>

  <div class="profile-grid">
    <div>
      <div class="profile-card">
        <div class="avatar"><i class="fa fa-user-tie"></i></div>
        <div class="profile-name"><?=htmlspecialchars($fullName)?></div>
        <div class="profile-role"><?=htmlspecialchars($designation !== 'â€”' ? $designation : 'Teacher')?></div>
        <div class="profile-stats">
          <div class="pstat"><div class="num"><?=$totalAssignments?></div><div class="lbl">Assignments</div></div>
          <div class="pstat"><div class="num"><?=$totalStudents?></div><div class="lbl">Students</div></div>
          <div class="pstat"><div class="num"><?=htmlspecialchars($expYears !== 'â€”' ? $expYears : 'â€”')?></div><div class="lbl">Yrs Exp.</div></div>
          <div class="pstat"><div class="num"><?=htmlspecialchars($department !== 'â€”' ? $department : 'â€”')?></div><div class="lbl">Dept.</div></div>
        </div>
      </div>
    </div>

    <div>
      <div class="info-card">
        <h3>
          <span><i class="fa fa-id-card"></i> Personal Information</span>
          
        </h3>
        <div class="info-grid">
          <div class="info-item"><label>Full Name</label><span><?=htmlspecialchars($fullName)?></span></div>
          <div class="info-item"><label>Username</label><span><?=htmlspecialchars($username)?></span></div>
          <div class="info-item"><label>Email</label><span><?=htmlspecialchars($email)?></span></div>
          <div class="info-item"><label>Phone</label><span><?=htmlspecialchars($phone)?></span></div>
          <div class="info-item"><label>Designation</label><span><?=htmlspecialchars($designation)?></span></div>
          <div class="info-item"><label>Status</label><span><?=ucfirst($t['status'] ?? 'active')?></span></div>
          <div class="info-item"><label>Address</label><span><?=htmlspecialchars($address)?></span></div>
          <div class="info-item"><label>Joined</label><span><?=$joinedDate?></span></div>
        </div>
      </div>

      <div class="info-card">
        <h3><span><i class="fa fa-graduation-cap"></i> Professional Information</span></h3>
        <div class="info-grid">
          <div class="info-item"><label>Employee ID</label><span><?=htmlspecialchars($teacherDbId)?></span></div>
          <div class="info-item"><label>Department</label><span><?=htmlspecialchars($department)?></span></div>
          <div class="info-item"><label>Qualification</label><span><?=htmlspecialchars($qualification)?></span></div>
          <div class="info-item"><label>Experience</label><span><?=($expYears !== 'â€”' ? htmlspecialchars($expYears).' Years' : 'â€”')?></span></div>
        </div>
      </div>

      <div class="info-card">
        <h3><span><i class="fa fa-lock"></i> Change Password</span></h3>
        <div id="cpMsg" style="display:none;padding:10px;border-radius:6px;margin-bottom:15px;font-size:14px;"></div>
        <div class="info-grid">
          <div class="info-item" style="grid-column:1/-1;">
            <label>Current Password</label>
            <input type="password" id="cpCurrent" placeholder="Enter current password" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;font-size:14px;margin-top:4px;">
          </div>
          <div class="info-item">
            <label>New Password</label>
            <input type="password" id="cpNew" placeholder="Min. 6 characters" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;font-size:14px;margin-top:4px;">
          </div>
          <div class="info-item">
            <label>Confirm New Password</label>
            <input type="password" id="cpConfirm" placeholder="Repeat new password" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;font-size:14px;margin-top:4px;">
          </div>
        </div>
        <div style="margin-top:15px;">
          <button class="edit-btn" onclick="changePassword()"><i class="fa fa-key"></i> Update Password</button>
        </div>
      </div>
    </div>
  </div>
</div>
<footer class="footer"><p>Â© 2025 SCTI - Teacher Portal</p></footer>

<div class="toast toast-ok" id="toastOk"><i class="fa fa-check-circle"></i><span id="toastOkMsg">Saved!</span></div>
<div class="toast toast-err" id="toastErr"><i class="fa fa-times-circle"></i><span id="toastErrMsg">Error</span></div>

<script>
function changePassword() { {
  var current = document.getElementById('cpCurrent').value.trim();
  var newPw   = document.getElementById('cpNew').value.trim();
  var confirm = document.getElementById('cpConfirm').value.trim();
  if (!current || !newPw || !confirm) { showCpMsg('All fields are required.','error'); return; }
  if (newPw.length < 6) { showCpMsg('New password must be at least 6 characters.','error'); return; }
  if (newPw !== confirm) { showCpMsg('New passwords do not match.','error'); return; }
  var fd = new FormData();
  fd.append('current_password', current);
  fd.append('new_password', newPw);
  fd.append('confirm_password', confirm);
  fetch('change-password.php', {method:'POST', body:fd})
    .then(function(r){ return r.json(); })
    .then(function(data){
      if (data.success) {
        showCpMsg(data.message,'success');
        document.getElementById('cpCurrent').value='';
        document.getElementById('cpNew').value='';
        document.getElementById('cpConfirm').value='';
      } else { showCpMsg(data.message,'error'); }
    })
    .catch(function(){ showCpMsg('Network error. Please try again.','error'); });
}

function showCpMsg(text, type) {
  var el = document.getElementById('cpMsg');
  el.style.display = 'block';
  el.style.background = type==='success' ? '#d4edda' : '#f8d7da';
  el.style.color      = type==='success' ? '#155724' : '#721c24';
  el.style.border     = '1px solid ' + (type==='success' ? '#c3e6cb' : '#f5c6cb');
  el.textContent = text;
}

function showToast(type, msg) {
  var id = type==='ok' ? 'toastOk' : 'toastErr';
  var mid = type==='ok' ? 'toastOkMsg' : 'toastErrMsg';
  document.getElementById(mid).textContent = msg;
  var t = document.getElementById(id);
  t.classList.add('show');
  setTimeout(function(){ t.classList.remove('show'); }, 3000);
}
</script>
</body>
</html>
