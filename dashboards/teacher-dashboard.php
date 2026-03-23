<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../index.php'); exit();
}
$username = $_SESSION['username'] ?? 'Teacher';
$fullName = $_SESSION['full_name'] ?? 'Teacher';
$teacherId = $_SESSION['user_id'] ?? 0;

require_once '../includes/config.php';
try {
    $db = getDBConnection();
    $totalStudents = $db->query("SELECT COUNT(*) FROM students WHERE status='active'")->fetchColumn();

    $stmt = $db->prepare("SELECT COUNT(*) FROM assignments WHERE created_by=?");
    $stmt->execute([$teacherId]);
    $assignCount = $stmt->fetchColumn();

    $stmt2 = $db->prepare("SELECT COUNT(*) FROM materials WHERE uploaded_by=?");
    $stmt2->execute([$teacherId]);
    $materialsCount = $stmt2->fetchColumn();

    $stmt3 = $db->prepare("SELECT COUNT(*) FROM assignment_submissions s JOIN assignments a ON a.id=s.assignment_id WHERE a.created_by=? AND s.grade IS NULL");
    $stmt3->execute([$teacherId]);
    $ungradedCount = $stmt3->fetchColumn();

    // Recent activity
    $activities = [];
    $recentAtt = $db->prepare("SELECT a.date, COUNT(*) as cnt FROM attendance a WHERE a.teacher_id=? GROUP BY a.date ORDER BY a.date DESC LIMIT 2");
    $recentAtt->execute([$teacherId]);
    foreach ($recentAtt->fetchAll() as $r) {
        $activities[] = ['icon'=>'fa-calendar-check','color'=>'#004080','label'=>'Attendance marked','name'=>date('M d, Y', strtotime($r['date'])).' — '.$r['cnt'].' students','time'=>$r['date'].' 00:00:00','link'=>'../pages/teacher-attendance.php'];
    }
    $recentAssign = $db->prepare("SELECT title, created_at FROM assignments WHERE created_by=? ORDER BY created_at DESC LIMIT 2");
    $recentAssign->execute([$teacherId]);
    foreach ($recentAssign->fetchAll() as $r) {
        $activities[] = ['icon'=>'fa-file-alt','color'=>'#6f42c1','label'=>'Assignment created','name'=>$r['title'],'time'=>$r['created_at'],'link'=>'../pages/teacher-assignments.php'];
    }
    $recentMat = $db->prepare("SELECT title, created_at FROM materials WHERE uploaded_by=? ORDER BY created_at DESC LIMIT 2");
    $recentMat->execute([$teacherId]);
    foreach ($recentMat->fetchAll() as $r) {
        $activities[] = ['icon'=>'fa-book','color'=>'#28a745','label'=>'Material uploaded','name'=>$r['title'],'time'=>$r['created_at'],'link'=>'../pages/teacher-materials.php'];
    }
    usort($activities, function($a,$b){ return strtotime($b['time']) - strtotime($a['time']); });
    $activities = array_slice($activities, 0, 6);

    // My assignments list
    $myAssignments = $db->prepare("SELECT * FROM assignments WHERE created_by=? ORDER BY due_date ASC LIMIT 5");
    $myAssignments->execute([$teacherId]);
    $myAssignments = $myAssignments->fetchAll();

} catch(Exception $e) {
    $totalStudents = 0; $assignCount = 0; $materialsCount = 0; $ungradedCount = 0;
    $activities = []; $myAssignments = [];
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
      background: linear-gradient(135deg, #004080 0%, #0059b3 100%);
      color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;
      display: flex; justify-content: space-between; align-items: center;
      box-shadow: 0 4px 15px rgba(0,64,128,0.2);
    }
    .dashboard-header h1 { margin: 0; font-size: 28px; }
    .user-info { display: flex; align-items: center; gap: 20px; }
    .logout-btn { background: rgba(255,255,255,0.2); color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; transition: all 0.3s; }
    .logout-btn:hover { background: rgba(255,255,255,0.3); }

    /* STAT CARDS */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .stat-card {
      background: white; padding: 25px; border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      display: flex; align-items: center; gap: 20px;
      transition: all 0.3s cubic-bezier(.25,.8,.25,1);
      cursor: pointer; position: relative; overflow: hidden; border: 2px solid transparent;
    }
    .stat-card::before {
      content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%;
      background: linear-gradient(90deg, transparent, rgba(0,64,128,0.1), transparent);
      transition: left 0.5s;
    }
    .stat-card:hover::before { left: 100%; }
    .stat-card:hover { transform: translateY(-6px); box-shadow: 0 12px 30px rgba(0,64,128,0.3); border-color: #004080; }
    .stat-card:active { transform: translateY(-2px) scale(0.98); }
    .stat-card .card-arrow { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #ccc; font-size: 13px; transition: all .3s; opacity: 0; }
    .stat-card:hover .card-arrow { opacity: 1; color: #004080; right: 10px; }
    .stat-icon { width: 60px; height: 60px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: white; }
    .stat-icon.blue   { background: linear-gradient(135deg, #004080, #0059b3); }
    .stat-icon.green  { background: linear-gradient(135deg, #28a745, #20c997); }
    .stat-icon.orange { background: linear-gradient(135deg, #fd7e14, #ffc107); }
    .stat-icon.purple { background: linear-gradient(135deg, #6f42c1, #e83e8c); }
    .stat-info h3 { margin: 0; font-size: 32px; color: #004080; }
    .stat-info p  { margin: 5px 0 0 0; color: #666; font-size: 14px; }

    /* QUICK ACTIONS */
    .quick-actions { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px; }
    .quick-actions h2 { margin-top: 0; color: #004080; margin-bottom: 20px; }
    .action-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
    .action-btn {
      background: linear-gradient(135deg, #004080, #0059b3);
      color: white; padding: 20px; border-radius: 8px; text-decoration: none;
      display: flex; align-items: center; gap: 15px; transition: all 0.3s;
      border: none; cursor: pointer; font-size: 16px; position: relative; overflow: hidden;
    }
    .action-btn::before {
      content: ''; position: absolute; top: 50%; left: 50%; width: 0; height: 0;
      border-radius: 50%; background: rgba(255,215,0,0.3);
      transform: translate(-50%, -50%); transition: width 0.6s, height 0.6s;
    }
    .action-btn:hover::before { width: 300px; height: 300px; }
    .action-btn:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,64,128,0.3); background: linear-gradient(135deg, #0059b3, #004080); }
    .action-btn:active { transform: translateY(-1px) scale(0.95); }
    .action-btn i { font-size: 24px; position: relative; z-index: 1; transition: transform 0.3s; }
    .action-btn:hover i { transform: scale(1.2) rotate(5deg); }
    .action-btn span { position: relative; z-index: 1; }
    .action-btn .btn-sub { display: block; font-size: 11px; opacity: 0.8; margin-top: 2px; position: relative; z-index: 1; }
    .action-btn .badge-pill { position: absolute; top: 10px; right: 10px; background: #ff4757; color: white; border-radius: 10px; padding: 2px 7px; font-size: 11px; font-weight: 700; z-index: 2; }

    /* CONTENT GRID */
    .content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 30px; }
    .card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .card h2 { margin-top: 0; color: #004080; margin-bottom: 20px; }

    /* ASSIGNMENT LIST */
    .assign-list { list-style: none; padding: 0; margin: 0; }
    .assign-item {
      padding: 14px 16px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between;
      align-items: center; cursor: pointer; transition: all 0.22s; border-radius: 8px; border-left: 3px solid transparent;
    }
    .assign-item:last-child { border-bottom: none; }
    .assign-item:hover { background: #f0f4ff; border-left-color: #004080; transform: translateX(4px); }
    .assign-badge { padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 700; }
    .badge-active { background: #d4edda; color: #155724; }
    .badge-overdue { background: #f8d7da; color: #721c24; }

    /* RECENT ACTIVITY */
    .recent-activity { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px; }
    .recent-activity h2 { margin-top: 0; color: #004080; margin-bottom: 20px; }
    .activity-list { list-style: none; padding: 0; margin: 0; }
    .activity-item {
      padding: 15px; border-bottom: 1px solid #eee; display: flex; align-items: center;
      gap: 15px; transition: all 0.3s; border-radius: 8px; margin-bottom: 5px; cursor: pointer;
    }
    .activity-item:hover { background: #f8f9fa; transform: translateX(5px); border-bottom-color: #004080; }
    .activity-item:last-child { border-bottom: none; }
    .activity-icon { width: 40px; height: 40px; border-radius: 50%; background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #004080; transition: all 0.3s; }
    .activity-item:hover .activity-icon { background: linear-gradient(135deg, #004080, #0059b3); color: white; transform: scale(1.1); }
    .activity-content { flex: 1; }
    .activity-content p { margin: 0; color: #333; }
    .activity-time { color: #999; font-size: 12px; }
  </style>
</head>
<body>
<div class="top-header"><marquee>Welcome to SCTI Teacher Portal - Empowering educators, inspiring students</marquee></div>
<div class="dashboard-container">

  <div class="dashboard-header">
    <div>
      <h1><i class="fa fa-chalkboard-teacher"></i> Teacher Dashboard</h1>
      <p style="margin:5px 0 0;opacity:.9">Welcome back, <?=htmlspecialchars($fullName)?>!</p>
    </div>
    <div class="user-info">
      <a href="../pages/teacher-profile.php" style="color:white;text-decoration:none;display:flex;align-items:center;gap:6px;background:rgba(255,255,255,0.15);padding:8px 16px;border-radius:5px;transition:.2s" onmouseover="this.style.background='rgba(255,255,255,0.28)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'"><i class="fa fa-chalkboard-teacher"></i> Teacher</a>
      <a href="../includes/logout.php" class="logout-btn"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </div>
  </div>

  <!-- Stat Cards -->
  <div class="stats-grid">
    <div class="stat-card" onclick="window.location.href='../pages/teacher-students.php'">
      <div class="stat-icon blue"><i class="fa fa-users"></i></div>
      <div class="stat-info"><h3><?=$totalStudents?></h3><p>Total Students</p></div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>
    <div class="stat-card" onclick="window.location.href='../pages/teacher-materials.php'">
      <div class="stat-icon green"><i class="fa fa-book"></i></div>
      <div class="stat-info"><h3><?=$materialsCount?></h3><p>Course Materials</p></div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>
    <div class="stat-card" onclick="window.location.href='../pages/teacher-assignments.php'">
      <div class="stat-icon orange"><i class="fa fa-file-alt"></i></div>
      <div class="stat-info"><h3><?=$assignCount?></h3><p>My Assignments</p></div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>
    <div class="stat-card" onclick="window.location.href='../pages/teacher-grades.php'">
      <div class="stat-icon purple"><i class="fa fa-clock"></i></div>
      <div class="stat-info"><h3><?=$ungradedCount?></h3><p>Ungraded Submissions</p></div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="quick-actions">
    <h2><i class="fa fa-bolt"></i> Quick Actions</h2>
    <div class="action-grid">
      <a href="../pages/teacher-attendance.php" class="action-btn">
        <i class="fa fa-calendar-check"></i>
        <div><span>Mark Attendance</span><span class="btn-sub">Record today</span></div>
      </a>
      <a href="../pages/teacher-attendance-chart.php" class="action-btn" style="background:linear-gradient(135deg,#17a2b8,#138496)">
        <i class="fa fa-chart-bar"></i>
        <div><span>Attendance Charts</span><span class="btn-sub">View analytics</span></div>
      </a>
      <a href="../pages/teacher-grades.php" class="action-btn" style="background:linear-gradient(135deg,#fd7e14,#ffc107)">
        <i class="fa fa-chart-line"></i>
        <div><span>Enter Grades</span><span class="btn-sub">Update marks</span></div>
      </a>
      <a href="../pages/teacher-assignments.php" class="action-btn" style="background:linear-gradient(135deg,#6f42c1,#e83e8c)">
        <?php if ($ungradedCount > 0): ?><span class="badge-pill"><?=$ungradedCount?></span><?php endif; ?>
        <i class="fa fa-file-alt"></i>
        <div><span>Assignments</span><span class="btn-sub"><?=$ungradedCount?> to grade</span></div>
      </a>
      <a href="../pages/teacher-materials.php" class="action-btn" style="background:linear-gradient(135deg,#28a745,#20c997)">
        <i class="fa fa-book-open"></i>
        <div><span>Course Materials</span><span class="btn-sub">Upload &amp; manage</span></div>
      </a>
      <a href="../pages/teacher-students.php" class="action-btn" style="background:linear-gradient(135deg,#dc3545,#c82333)">
        <i class="fa fa-users"></i>
        <div><span>View Students</span><span class="btn-sub"><?=$totalStudents?> active</span></div>
      </a>
      <a href="../pages/teacher-profile.php" class="action-btn" style="background:linear-gradient(135deg,#495057,#343a40)">
        <i class="fa fa-user"></i>
        <div><span>My Profile</span><span class="btn-sub">View &amp; edit</span></div>
      </a>
    </div>
  </div>

  <!-- Assignments + Notices -->
  <div class="content-grid">
    <div class="card">
      <h2><i class="fa fa-file-alt"></i> My Assignments</h2>
      <ul class="assign-list">
        <?php if (empty($myAssignments)): ?>
        <li class="assign-item"><span style="color:#999">No assignments created yet.</span></li>
        <?php else: ?>
        <?php foreach ($myAssignments as $a): ?>
        <li class="assign-item" onclick="window.location.href='../pages/teacher-assignments.php'">
          <div>
            <div style="font-weight:600;color:#333"><?=htmlspecialchars($a['title'])?></div>
            <small style="color:#666"><i class="fa fa-calendar" style="color:#004080"></i> Due: <?=date('M d, Y', strtotime($a['due_date']))?> &nbsp;|&nbsp; <?=htmlspecialchars($a['subject'] ?? 'General')?></small>
          </div>
          <span class="assign-badge <?=strtotime($a['due_date']) > time() ? 'badge-active' : 'badge-overdue'?>"><?=strtotime($a['due_date']) > time() ? 'Active' : 'Overdue'?></span>
        </li>
        <?php endforeach; ?>
        <?php endif; ?>
      </ul>
      <div style="margin-top:12px;text-align:right">
        <a href="../pages/teacher-assignments.php" style="color:#004080;font-size:13px;text-decoration:none;font-weight:600"><i class="fa fa-arrow-right"></i> View All</a>
      </div>
    </div>
    <div class="card">
      <h2><i class="fa fa-bullhorn"></i> Recent Notices</h2>
      <?php require_once '../pages/notice-widget.php'; renderNoticeWidget('teacher'); ?>
      <div style="margin-top:12px;text-align:right">
        <a href="../pages/notice-board.php" style="color:#004080;font-size:13px;text-decoration:none;font-weight:600"><i class="fa fa-arrow-right"></i> View All</a>
      </div>
    </div>
  </div>

  <!-- Recent Activity -->
  <div class="recent-activity">
    <h2><i class="fa fa-history"></i> Recent Activity</h2>
    <ul class="activity-list">
      <?php if (empty($activities)): ?>
      <li class="activity-item">
        <div class="activity-icon"><i class="fa fa-info-circle"></i></div>
        <div class="activity-content"><p>No recent activity found.</p></div>
      </li>
      <?php else: ?>
      <?php foreach ($activities as $act): ?>
      <li class="activity-item" onclick="window.location.href='<?=htmlspecialchars($act['link'])?>'">
        <div class="activity-icon"><i class="fa <?=htmlspecialchars($act['icon'])?>" style="color:<?=htmlspecialchars($act['color'])?>"></i></div>
        <div class="activity-content">
          <p><strong><?=htmlspecialchars($act['label'])?>:</strong> <?=htmlspecialchars($act['name'])?></p>
          <span class="activity-time"><?=timeAgo($act['time'])?></span>
        </div>
        <i class="fa fa-chevron-right" style="color:#ccc;font-size:12px"></i>
      </li>
      <?php endforeach; ?>
      <?php endif; ?>
    </ul>
  </div>

</div>
<footer class="footer" style="margin-top:40px"><p>&copy; 2025 Sindhuli Community Technical Institute (SCTI) - Teacher Portal</p></footer>

<?php if (!empty($_SESSION['first_login'])): ?>
<div id="firstLoginOverlay" style="position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:99999;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(4px)">
  <div style="background:white;border-radius:18px;width:100%;max-width:420px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,.3);overflow:hidden;animation:popIn .4s cubic-bezier(.175,.885,.32,1.275)">
    <style>@keyframes popIn{from{opacity:0;transform:scale(.8)}to{opacity:1;transform:scale(1)}}</style>
    <div style="background:linear-gradient(135deg,#004080,#0059b3);padding:28px 28px 22px;text-align:center;color:white">
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
          <input type="password" id="flNewPass" placeholder="Enter new password" style="width:100%;padding:11px 44px 11px 14px;border:2px solid #e0e0e0;border-radius:10px;font-size:14px;outline:none" onfocus="this.style.borderColor='#004080'" onblur="this.style.borderColor='#e0e0e0'">
          <i class="fa fa-eye" onclick="toggleFL('flNewPass',this)" style="position:absolute;right:13px;top:50%;transform:translateY(-50%);cursor:pointer;color:#aaa;font-size:15px"></i>
        </div>
        <div style="height:4px;background:#eee;border-radius:4px;margin-top:6px;overflow:hidden"><div id="flStrBar" style="height:100%;width:0;border-radius:4px;transition:.3s"></div></div>
        <div id="flStrLbl" style="font-size:11px;color:#999;margin-top:3px"></div>
      </div>
      <div style="margin-bottom:18px">
        <label style="display:block;font-size:13px;font-weight:600;color:#444;margin-bottom:6px"><i class="fa fa-lock"></i> Confirm Password</label>
        <div style="position:relative">
          <input type="password" id="flConfPass" placeholder="Re-enter new password" style="width:100%;padding:11px 44px 11px 14px;border:2px solid #e0e0e0;border-radius:10px;font-size:14px;outline:none" onfocus="this.style.borderColor='#004080'" onblur="this.style.borderColor='#e0e0e0'">
          <i class="fa fa-eye" onclick="toggleFL('flConfPass',this)" style="position:absolute;right:13px;top:50%;transform:translateY(-50%);cursor:pointer;color:#aaa;font-size:15px"></i>
        </div>
      </div>
      <button onclick="submitFirstLogin()" id="flBtn" style="width:100%;padding:13px;background:linear-gradient(135deg,#004080,#0059b3);color:white;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px">
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
