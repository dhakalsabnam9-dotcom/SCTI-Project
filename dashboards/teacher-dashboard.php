<?php
session_start();

// Check if user is logged in and is teacher
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../index.php');
    exit();
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Teacher';
$fullName = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Teacher';
$teacherId = $_SESSION['user_id'] ?? 0;

require_once '../includes/config.php';
try {
    $db = getDBConnection();
    $totalStudents = $db->query("SELECT COUNT(*) FROM students WHERE status='active'")->fetchColumn();
    $stmt2 = $db->prepare("SELECT COUNT(*) FROM assignments WHERE created_by=?");
    $stmt2->execute([$teacherId]);
    $pendingAssign = $stmt2->fetchColumn();
    $totalMaterials = $db->prepare("SELECT COUNT(*) FROM materials WHERE teacher_id=?");
    $totalMaterials->execute([$teacherId]);
    $materialsCount = $totalMaterials->fetchColumn();
    // Recent notices
    $notices = $db->query("SELECT title, created_at FROM notices ORDER BY created_at DESC LIMIT 4")->fetchAll();
} catch(Exception $e) {
    $totalStudents = 0; $pendingAssign = 0; $materialsCount = 0; $notices = [];
}
function timeAgo($dt) {
    $diff = time() - strtotime($dt);
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff/60).' min ago';
    if ($diff < 86400) return floor($diff/3600).' hrs ago';
    return floor($diff/86400).' days ago';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Teacher Dashboard | SCTI</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
    
    .dashboard-container { max-width: 1400px; margin: 0 auto; padding: 20px; }
    
    .dashboard-header {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;
      display: flex; justify-content: space-between; align-items: center;
      box-shadow: 0 4px 15px rgba(40,167,69,0.2);
    }
    
    .dashboard-header h1 { margin: 0; font-size: 28px; }
    .user-info { display: flex; align-items: center; gap: 20px; }
    
    .logout-btn {
      background: rgba(255,255,255,0.2); color: white; padding: 10px 20px;
      border-radius: 5px; text-decoration: none; transition: all 0.3s;
    }
    .logout-btn:hover { background: rgba(255,255,255,0.3); }
    
    .stats-grid {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px; margin-bottom: 30px;
    }
    
    .stat-card {
      background: white; padding: 25px; border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      display: flex; align-items: center; gap: 20px;
      transition: all 0.3s cubic-bezier(.25,.8,.25,1);
      cursor: pointer;
      position: relative;
      overflow: hidden;
      border: 2px solid transparent;
    }
    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(40,167,69,0.1), transparent);
      transition: left 0.5s;
    }
    .stat-card:hover::before {
      left: 100%;
    }
    .stat-card:hover { 
      transform: translateY(-6px); 
      box-shadow: 0 12px 30px rgba(40,167,69,0.3);
      border-color: #28a745;
    }
    .stat-card:active {
      transform: translateY(-2px) scale(0.98);
      box-shadow: 0 5px 15px rgba(40,167,69,0.4);
    }
    .stat-card .card-arrow {
      position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
      color: #ccc; font-size: 13px; transition: all .3s; opacity: 0;
    }
    .stat-card:hover .card-arrow { opacity: 1; color: #28a745; right: 10px; }
    
    .stat-icon {
      width: 60px; height: 60px; border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 24px; color: white;
    }
    .stat-icon.green { background: linear-gradient(135deg, #28a745, #20c997); }
    .stat-icon.blue { background: linear-gradient(135deg, #004080, #0059b3); }
    .stat-icon.orange { background: linear-gradient(135deg, #fd7e14, #ffc107); }
    .stat-icon.purple { background: linear-gradient(135deg, #6f42c1, #e83e8c); }
    
    .stat-info h3 { margin: 0; font-size: 32px; color: #28a745; }
    .stat-info p { margin: 5px 0 0 0; color: #666; font-size: 14px; }
    
    .content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 30px; }
    
    .card {
      background: white; padding: 30px; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .card h2 { margin-top: 0; color: #28a745; margin-bottom: 20px; }
    
    .class-list { list-style: none; padding: 0; margin: 0; }
    .class-item {
      padding: 15px; border-bottom: 1px solid #eee;
      display: flex; justify-content: space-between; align-items: center;
    }
    .class-item:last-child { border-bottom: none; }
    .class-name { font-weight: 600; color: #333; }
    .class-badge {
      background: #28a745; color: white; padding: 5px 10px;
      border-radius: 4px; font-size: 12px;
    }
    
    .schedule-list { list-style: none; padding: 0; margin: 0; }
    .schedule-item {
      padding: 15px; border-left: 4px solid #28a745;
      background: #f8f9fa; margin-bottom: 10px; border-radius: 4px;
    }
    .schedule-time { font-weight: 600; color: #28a745; margin-bottom: 5px; }
    .schedule-class { font-size: 14px; color: #666; }
    
    .quick-links { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; }
    .quick-link {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white; padding: 20px; border-radius: 8px;
      text-decoration: none; text-align: center;
      transition: all 0.3s; display: flex;
      flex-direction: column; align-items: center; gap: 10px;
      position: relative;
      overflow: hidden;
    }
    .quick-link::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255,255,255,0.3);
      transform: translate(-50%, -50%);
      transition: width 0.6s, height 0.6s;
    }
    .quick-link:hover::before {
      width: 300px;
      height: 300px;
    }
    .quick-link:hover { 
      transform: translateY(-5px); 
      box-shadow: 0 8px 20px rgba(40,167,69,0.4);
      background: linear-gradient(135deg, #20c997, #28a745);
    }
    .quick-link:active {
      transform: translateY(-2px) scale(0.95);
      box-shadow: 0 4px 15px rgba(40,167,69,0.5);
    }
    .quick-link i { 
      font-size: 24px;
      position: relative;
      z-index: 1;
      transition: transform 0.3s;
    }
    .quick-link:hover i {
      transform: scale(1.2) rotate(5deg);
    }
    .quick-link span {
      position: relative;
      z-index: 1;
    }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Welcome to SCTI Teacher Portal - Empowering educators, inspiring students</marquee>
</div>

<div class="dashboard-container">
  
  <div class="dashboard-header">
    <div>
      <h1><i class="fa fa-chalkboard-teacher"></i> Teacher Dashboard</h1>
      <p style="margin: 5px 0 0 0; opacity: 0.9;">Welcome back, <?php echo htmlspecialchars($fullName); ?>!</p>
    </div>
    <div class="user-info">
      <a href="../pages/teacher-profile.php" style="color:white;text-decoration:none;display:flex;align-items:center;gap:6px;background:rgba(255,255,255,0.15);padding:8px 16px;border-radius:5px;transition:.2s" onmouseover="this.style.background='rgba(255,255,255,0.28)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'"><i class="fa fa-chalkboard-teacher"></i> Teacher</a>
      <a href="../includes/logout.php" class="logout-btn">
        <i class="fa fa-sign-out-alt"></i> Logout
      </a>
    </div>
  </div>

  <div class="stats-grid">
    <div class="stat-card" onclick="window.location.href='../pages/teacher-students.php'">
      <div class="stat-icon green"><i class="fa fa-users"></i></div>
      <div class="stat-info">
        <h3><?php echo $totalStudents; ?></h3>
        <p>Total Students</p>
      </div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>

    <div class="stat-card" onclick="window.location.href='../pages/teacher-materials.php'">
      <div class="stat-icon blue"><i class="fa fa-book"></i></div>
      <div class="stat-info">
        <h3><?php echo $materialsCount; ?></h3>
        <p>Course Materials</p>
      </div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>

    <div class="stat-card" onclick="window.location.href='../pages/teacher-assignments.php'">
      <div class="stat-icon orange"><i class="fa fa-file-alt"></i></div>
      <div class="stat-info">
        <h3><?php echo $pendingAssign; ?></h3>
        <p>Assignments</p>
      </div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>

    <div class="stat-card" onclick="window.location.href='../pages/teacher-schedule.php'">
      <div class="stat-icon purple"><i class="fa fa-calendar"></i></div>
      <div class="stat-info">
        <h3><?php echo $totalStudents; ?></h3>
        <p>Active Students</p>
      </div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>
  </div>

  <div class="content-grid">
    <div class="card">
      <h2><i class="fa fa-book-open"></i> My Assignments</h2>
      <?php
      try {
          $db2 = getDBConnection();
          $aStmt = $db2->prepare("SELECT * FROM assignments WHERE created_by=? ORDER BY due_date ASC LIMIT 5");
          $aStmt->execute([$teacherId]);
          $myAssignments = $aStmt->fetchAll();
      } catch(Exception $e) { $myAssignments = []; }
      ?>
      <ul class="class-list">
        <?php if (empty($myAssignments)): ?>
        <li class="class-item"><span style="color:#999">No assignments created yet.</span></li>
        <?php else: ?>
        <?php foreach ($myAssignments as $a): ?>
        <li class="class-item" onclick="window.location.href='../pages/teacher-assignments.php'" style="cursor:pointer;transition:.2s" onmouseover="this.style.background='#f0fff4'" onmouseout="this.style.background=''">
          <div>
            <div class="class-name"><?=htmlspecialchars($a['title'])?></div>
            <small style="color:#666">Due: <?=date('M d, Y', strtotime($a['due_date']))?> &nbsp;|&nbsp; <?=htmlspecialchars($a['subject'] ?? 'General')?></small>
          </div>
          <span class="class-badge"><?=strtotime($a['due_date']) > time() ? 'Active' : 'Overdue'?></span>
        </li>
        <?php endforeach; ?>
        <?php endif; ?>
      </ul>
      <div style="margin-top:12px;text-align:right">
        <a href="../pages/teacher-assignments.php" style="color:#28a745;font-size:13px;text-decoration:none;font-weight:600"><i class="fa fa-arrow-right"></i> View All Assignments</a>
      </div>
    </div>

    <div class="card">
      <h2><i class="fa fa-bullhorn"></i> Recent Notices</h2>
      <ul class="schedule-list">
        <?php if (empty($notices)): ?>
        <li class="schedule-item"><div class="schedule-class" style="color:#999">No notices yet.</div></li>
        <?php else: ?>
        <?php foreach ($notices as $n): ?>
        <li class="schedule-item" onclick="window.location.href='../index.php?page=notices'" style="cursor:pointer;transition:.2s" onmouseover="this.style.background='#e8f5e9'" onmouseout="this.style.background='#f8f9fa'">
          <div class="schedule-time"><?=timeAgo($n['created_at'])?></div>
          <div class="schedule-class"><?=htmlspecialchars($n['title'])?></div>
        </li>
        <?php endforeach; ?>
        <?php endif; ?>
      </ul>
      <div style="margin-top:12px;text-align:right">
        <a href="../index.php?page=notices" style="color:#28a745;font-size:13px;text-decoration:none;font-weight:600"><i class="fa fa-arrow-right"></i> View All Notices</a>
      </div>
    </div>
  </div>

  <div class="card">
    <h2><i class="fa fa-bolt"></i> Quick Actions</h2>
    <div class="quick-links">
      <a href="../pages/teacher-attendance.php" class="quick-link">
        <i class="fa fa-calendar-check"></i>
        <span>Mark Attendance</span>
      </a>
      <a href="../pages/teacher-grades.php" class="quick-link">
        <i class="fa fa-chart-line"></i>
        <span>Enter Grades</span>
      </a>
      <a href="../pages/teacher-assignments.php" class="quick-link">
        <i class="fa fa-file-alt"></i>
        <span>Assignments</span>
      </a>
      <a href="../pages/teacher-students.php" class="quick-link">
        <i class="fa fa-users"></i>
        <span>View Students</span>
      </a>
      <a href="../pages/teacher-materials.php" class="quick-link">
        <i class="fa fa-book"></i>
        <span>Course Materials</span>
      </a>
      <a href="../pages/teacher-profile.php" class="quick-link">
        <i class="fa fa-user"></i>
        <span>Profile</span>
      </a>
    </div>
  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 Sindhuli Community Technical Institute (SCTI) - Teacher Portal</p>
</footer>


<?php if (!empty($_SESSION['first_login'])): ?>
<div id="firstLoginOverlay" style="position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:99999;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(4px)">
  <div style="background:white;border-radius:18px;width:100%;max-width:420px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,.3);overflow:hidden;animation:popIn .4s cubic-bezier(.175,.885,.32,1.275)">
    <style>@keyframes popIn{from{opacity:0;transform:scale(.8)}to{opacity:1;transform:scale(1)}}</style>
    <div style="background:linear-gradient(135deg,#28a745,#20c997);padding:28px 28px 22px;text-align:center;color:white">
      <div style="width:64px;height:64px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:28px"><i class="fa fa-key"></i></div>
      <h2 style="margin:0 0 6px;font-size:20px">Welcome to SCTI Portal!</h2>
      <p style="margin:0;font-size:13px;opacity:.88">This is your first login. Please set a new password to continue.</p>
    </div>
    <div style="padding:24px 28px">
      <div style="background:#fff8e1;border-left:4px solid #ffc107;border-radius:8px;padding:10px 14px;font-size:13px;color:#7a5c00;margin-bottom:18px;display:flex;gap:8px;align-items:flex-start">
        <i class="fa fa-shield-alt" style="margin-top:2px"></i>
        <span>For your security, please change your temporary password before using the portal.</span>
      </div>
      <div id="flError" style="display:none;background:#fde8e8;border-left:4px solid #e74c3c;border-radius:8px;padding:10px 14px;font-size:13px;color:#c0392b;margin-bottom:14px"></div>
      <div style="margin-bottom:14px">
        <label style="display:block;font-size:13px;font-weight:600;color:#444;margin-bottom:6px"><i class="fa fa-lock"></i> New Password</label>
        <div style="position:relative">
          <input type="password" id="flNewPass" placeholder="Enter new password" style="width:100%;padding:11px 44px 11px 14px;border:2px solid #e0e0e0;border-radius:10px;font-size:14px;outline:none" onfocus="this.style.borderColor='#28a745'" onblur="this.style.borderColor='#e0e0e0'">
          <i class="fa fa-eye" onclick="toggleFL('flNewPass',this)" style="position:absolute;right:13px;top:50%;transform:translateY(-50%);cursor:pointer;color:#aaa;font-size:15px"></i>
        </div>
        <div style="height:4px;background:#eee;border-radius:4px;margin-top:6px;overflow:hidden"><div id="flStrBar" style="height:100%;width:0;border-radius:4px;transition:.3s"></div></div>
        <div id="flStrLbl" style="font-size:11px;color:#999;margin-top:3px"></div>
      </div>
      <div style="margin-bottom:18px">
        <label style="display:block;font-size:13px;font-weight:600;color:#444;margin-bottom:6px"><i class="fa fa-lock"></i> Confirm Password</label>
        <div style="position:relative">
          <input type="password" id="flConfPass" placeholder="Re-enter new password" style="width:100%;padding:11px 44px 11px 14px;border:2px solid #e0e0e0;border-radius:10px;font-size:14px;outline:none" onfocus="this.style.borderColor='#28a745'" onblur="this.style.borderColor='#e0e0e0'">
          <i class="fa fa-eye" onclick="toggleFL('flConfPass',this)" style="position:absolute;right:13px;top:50%;transform:translateY(-50%);cursor:pointer;color:#aaa;font-size:15px"></i>
        </div>
      </div>
      <button onclick="submitFirstLogin()" id="flBtn" style="width:100%;padding:13px;background:linear-gradient(135deg,#28a745,#20c997);color:white;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px">
        <i class="fa fa-key"></i> Set Password &amp; Continue
      </button>
    </div>
  </div>
</div>
<script>
function toggleFL(id,icon){var inp=document.getElementById(id);inp.type=inp.type==='password'?'text':'password';icon.classList.toggle('fa-eye');icon.classList.toggle('fa-eye-slash');}
document.getElementById('flNewPass').addEventListener('input',function(){var v=this.value,s=0;if(v.length>=6)s++;if(v.length>=10)s++;if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[^A-Za-z0-9]/.test(v))s++;var c=['#e74c3c','#e67e22','#f1c40f','#2ecc71','#27ae60'],l=['Very Weak','Weak','Fair','Strong','Very Strong'];document.getElementById('flStrBar').style.width=(s*20)+'%';document.getElementById('flStrBar').style.background=c[s-1]||'#eee';document.getElementById('flStrLbl').textContent=v.length?(l[s-1]||''):''});
function submitFirstLogin(){var np=document.getElementById('flNewPass').value,cp=document.getElementById('flConfPass').value,err=document.getElementById('flError'),btn=document.getElementById('flBtn');err.style.display='none';if(!np||!cp){err.textContent='Both fields are required';err.style.display='block';return;}if(np.length<6){err.textContent='Password must be at least 6 characters';err.style.display='block';return;}if(np!==cp){err.textContent='Passwords do not match';err.style.display='block';return;}btn.disabled=true;btn.innerHTML='<i class="fa fa-spinner fa-spin"></i> Saving...';var fd=new FormData();fd.append('new_password',np);fd.append('confirm_password',cp);fetch('../pages/first-login-save.php',{method:'POST',body:fd}).then(function(r){return r.json();}).then(function(d){if(d.success){document.getElementById('firstLoginOverlay').innerHTML='<div style="background:white;border-radius:18px;padding:40px;text-align:center;max-width:360px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,.3)"><div style="width:72px;height:72px;border-radius:50%;background:#d4edda;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:32px;color:#28a745"><i class="fa fa-check"></i></div><h3 style="color:#155724;margin:0 0 8px">Password Set!</h3><p style="color:#666;font-size:14px">Redirecting...</p></div>';setTimeout(function(){location.reload();},1500);}else{err.textContent=d.message;err.style.display='block';btn.disabled=false;btn.innerHTML='<i class="fa fa-key"></i> Set Password &amp; Continue';}}).catch(function(){err.textContent='Network error';err.style.display='block';btn.disabled=false;btn.innerHTML='<i class="fa fa-key"></i> Set Password &amp; Continue';});}
</script>
<?php endif; ?>
</body>
</html>
