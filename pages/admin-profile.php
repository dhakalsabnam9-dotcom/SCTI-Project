<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php'); exit();
}
require_once '../includes/config.php';

$adminId  = $_SESSION['user_id'] ?? 0;
$username = $_SESSION['username'] ?? 'admin';
$fullName = $_SESSION['full_name'] ?? 'Administrator';

// Try to fetch from admins table if it exists, else use session
$a = [];
try {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM admins WHERE id=? LIMIT 1");
    $stmt->execute([$adminId]);
    $a = $stmt->fetch() ?: [];
} catch(Exception $e) { $a = []; }

$fullName = $a['full_name'] ?? $fullName;
$email    = $a['email']    ?? '—';
$phone    = $a['phone']    ?? '—';
$joinedDate = !empty($a['created_at']) ? date('M d, Y', strtotime($a['created_at'])) : '—';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Profile | SCTI</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Segoe UI',sans-serif;background:#f5f7fa}
    .top-header{background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:10px 20px;font-size:14px}
    .footer{background:#2c3e50;color:white;text-align:center;padding:20px;margin-top:30px}
    .container{max-width:900px;margin:0 auto;padding:20px}
    .page-header{background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:30px;border-radius:10px;margin-bottom:30px;box-shadow:0 4px 15px rgba(0,64,128,.2)}
    .page-header h1{margin:0 0 8px;font-size:28px}
    .breadcrumb{background:transparent!important;padding:0;font-size:14px}
    .breadcrumb a{color:white;text-decoration:none}
    .profile-grid{display:grid;grid-template-columns:260px 1fr;gap:25px}
    .profile-card{background:white;border-radius:10px;padding:30px;box-shadow:0 2px 10px rgba(0,0,0,.1);text-align:center}
    .avatar{width:110px;height:110px;border-radius:50%;background:linear-gradient(135deg,#004080,#0059b3);display:flex;align-items:center;justify-content:center;font-size:44px;color:white;margin:0 auto 18px}
    .profile-name{font-size:20px;font-weight:700;color:#333;margin-bottom:5px}
    .profile-role{display:inline-block;background:#cce5ff;color:#004080;padding:5px 15px;border-radius:20px;font-size:13px;margin-bottom:20px}
    .info-card{background:white;border-radius:10px;padding:25px;box-shadow:0 2px 10px rgba(0,0,0,.1);margin-bottom:20px}
    .info-card h3{color:#004080;margin-bottom:20px;font-size:17px;border-bottom:2px solid #f0f0f0;padding-bottom:10px}
    .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:15px}
    .info-item label{display:block;font-size:11px;color:#999;margin-bottom:4px;text-transform:uppercase}
    .info-item span{font-size:15px;color:#333;font-weight:500}
    .info-card h3{color:#004080;margin-bottom:20px;font-size:17px;border-bottom:2px solid #f0f0f0;padding-bottom:10px;display:flex;justify-content:space-between;align-items:center}
    /* Change Password */
    .fc{width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;font-size:14px;margin-top:4px}
    .fc:focus{outline:none;border-color:#004080}
    .btn-save{background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:10px 22px;border:none;border-radius:6px;cursor:pointer;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:7px;margin-top:14px;transition:.2s}
    .btn-save:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,64,128,.3)}
    #cpMsg{display:none;padding:10px;border-radius:6px;margin-bottom:12px;font-size:14px}
  </style>
</head>
<body>
<div class="top-header">Admin Profile</div>
<div class="container">
  <div class="page-header">
    <h1><i class="fa fa-user-shield"></i> Admin Profile</h1>
    <div class="breadcrumb"><a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Profile</div>
  </div>

  <div class="profile-grid">
    <div class="profile-card">
      <div class="avatar"><i class="fa fa-user-shield"></i></div>
      <div class="profile-name"><?=htmlspecialchars($fullName)?></div>
      <div class="profile-role">Administrator</div>
      <div style="font-size:13px;color:#666;margin-top:10px"><i class="fa fa-user"></i> <?=htmlspecialchars($username)?></div>
    </div>

    <div>
      <div class="info-card">
        <h3><span><i class="fa fa-id-card"></i> Account Information</span></h3>
        <div class="info-grid">
          <div class="info-item"><label>Full Name</label><span><?=htmlspecialchars($fullName)?></span></div>
          <div class="info-item"><label>Username</label><span><?=htmlspecialchars($username)?></span></div>
          <div class="info-item"><label>Email</label><span><?=htmlspecialchars($email)?></span></div>
          <div class="info-item"><label>Phone</label><span><?=htmlspecialchars($phone)?></span></div>
          <div class="info-item"><label>Role</label><span>Administrator</span></div>
          <div class="info-item"><label>Joined</label><span><?=$joinedDate?></span></div>
        </div>
      </div>

      <div class="info-card">
        <h3><span><i class="fa fa-lock"></i> Change Password</span></h3>
        <div id="cpMsg"></div>
        <div class="info-grid">
          <div class="info-item" style="grid-column:1/-1">
            <label>Current Password</label>
            <input type="password" id="cpCurrent" class="fc" placeholder="Enter current password">
          </div>
          <div class="info-item">
            <label>New Password</label>
            <input type="password" id="cpNew" class="fc" placeholder="Min. 6 characters">
          </div>
          <div class="info-item">
            <label>Confirm New Password</label>
            <input type="password" id="cpConfirm" class="fc" placeholder="Repeat new password">
          </div>
        </div>
        <button class="btn-save" onclick="changePassword()"><i class="fa fa-key"></i> Update Password</button>
      </div>
    </div>
  </div>
</div>
<footer class="footer"><p>© 2025 SCTI - Admin Panel</p></footer>

<script>
function changePassword() {
  var current = document.getElementById('cpCurrent').value.trim();
  var newPw   = document.getElementById('cpNew').value.trim();
  var confirm = document.getElementById('cpConfirm').value.trim();
  if (!current || !newPw || !confirm) { showMsg('All fields are required.','error'); return; }
  if (newPw.length < 6) { showMsg('New password must be at least 6 characters.','error'); return; }
  if (newPw !== confirm) { showMsg('Passwords do not match.','error'); return; }
  var fd = new FormData();
  fd.append('current_password', current);
  fd.append('new_password', newPw);
  fd.append('confirm_password', confirm);
  fetch('change-password.php', {method:'POST', body:fd})
    .then(function(r){ return r.json(); })
    .then(function(d){
      if (d.success) {
        showMsg(d.message, 'success');
        document.getElementById('cpCurrent').value = '';
        document.getElementById('cpNew').value = '';
        document.getElementById('cpConfirm').value = '';
      } else { showMsg(d.message, 'error'); }
    })
    .catch(function(){ showMsg('Network error.','error'); });
}
function showMsg(text, type) {
  var el = document.getElementById('cpMsg');
  el.style.display = 'block';
  el.style.background = type==='success' ? '#d4edda' : '#f8d7da';
  el.style.color      = type==='success' ? '#155724' : '#721c24';
  el.style.border     = '1px solid ' + (type==='success' ? '#c3e6cb' : '#f5c6cb');
  el.textContent = text;
}
</script>
</body>
</html>
