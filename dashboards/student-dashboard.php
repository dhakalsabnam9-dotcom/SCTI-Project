<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header('Location: ../index.php'); exit();
}
$username = $_SESSION['username'] ?? 'Student';
$fullName = $_SESSION['full_name'] ?? 'Student';
$studentId = $_SESSION['user_id'] ?? 0;

require_once '../includes/config.php';
try {
    $db = getDBConnection();

    // Student data
    $stu = $db->prepare("SELECT * FROM students WHERE id=? LIMIT 1");
    $stu->execute([$studentId]);
    $stuData = $stu->fetch() ?: [];

    // Enrolled courses (from programs table based on student course)
    $program = $stuData['course'] ?? '';
    $enrolledCourses = 0;
    if ($program) {
        $enrolledCourses = $db->query("SELECT COUNT(*) FROM programs WHERE status='active'")->fetchColumn();
    }

    // Assignments count
    $totalAssignments = $db->query("SELECT COUNT(*) FROM assignments")->fetchColumn();
    $now = date('Y-m-d H:i:s');
    $pendingAssign = $db->query("SELECT COUNT(*) FROM assignments WHERE due_date > '$now'")->fetchColumn();

    // Attendance
    $attStmt = $db->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status='present' THEN 1 ELSE 0 END) as present FROM attendance WHERE student_id=?");
    $attStmt->execute([$studentId]);
    $attRow = $attStmt->fetch();
    $attPct = ($attRow['total'] > 0) ? round(($attRow['present'] / $attRow['total']) * 100) : 0;

    // Grades / GPA
    $gradeStmt = $db->prepare("SELECT AVG(internal_marks + external_marks) as avg_marks FROM grades WHERE student_id=?");
    $gradeStmt->execute([$studentId]);
    $gradeRow = $gradeStmt->fetch();
    $avgMarks = floatval($gradeRow['avg_marks'] ?? 0);
    $gpa = $avgMarks > 0 ? round(($avgMarks / 100) * 4, 1) : 0;

    // Recent notices
    $notices = $db->query("SELECT title, created_at FROM notices ORDER BY created_at DESC LIMIT 4")->fetchAll();

    // Courses list
    $courses = $db->query("SELECT * FROM programs WHERE status='active' ORDER BY id ASC LIMIT 4")->fetchAll();

} catch(Exception $e) {
    $enrolledCourses = 0; $pendingAssign = 0; $attPct = 0; $gpa = 0;
    $notices = []; $courses = [];
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
  <title>Student Dashboard | SCTI</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    .top-header{background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:8px 20px;font-size:13px}
    footer.footer{background:#00264d;color:white;text-align:center;padding:16px;font-size:13px}
    body{font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f5f7fa}
    .dashboard-container{max-width:1400px;margin:0 auto;padding:20px}
    .dashboard-header{background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:30px;border-radius:10px;margin-bottom:30px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 15px rgba(0,64,128,.2)}
    .dashboard-header h1{margin:0;font-size:28px}
    .user-info{display:flex;align-items:center;gap:20px}
    .logout-btn{background:rgba(255,255,255,.2);color:white;padding:10px 20px;border-radius:5px;text-decoration:none;transition:.3s}
    .logout-btn:hover{background:rgba(255,255,255,.3)}
    /* -- STAT CARDS -- */
    .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;margin-bottom:30px}
    .stat-card{border-radius:16px;padding:24px 22px;display:flex;align-items:center;gap:18px;cursor:pointer;position:relative;overflow:hidden;transition:all .35s cubic-bezier(.25,.8,.25,1);box-shadow:0 4px 18px rgba(0,0,0,.12);border:none}
    .stat-card::after{content:'';position:absolute;top:-40%;right:-30%;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,.12);transition:transform .4s}
    .stat-card:hover::after{transform:scale(1.6)}
    .stat-card::before{content:'';position:absolute;bottom:-30%;left:-20%;width:90px;height:90px;border-radius:50%;background:rgba(255,255,255,.08)}
    .stat-card:hover{transform:translateY(-7px) scale(1.02);box-shadow:0 18px 40px rgba(0,0,0,.2)}
    .stat-card:active{transform:translateY(-2px) scale(.98)}
    .stat-card.blue{background:linear-gradient(135deg,#004080 0%,#0077cc 100%)}
    .stat-card.green{background:linear-gradient(135deg,#1a7a4a 0%,#20c997 100%)}
    .stat-card.orange{background:linear-gradient(135deg,#e05c00 0%,#ffc107 100%)}
    .stat-card.purple{background:linear-gradient(135deg,#5a1fa0 0%,#e83e8c 100%)}
    .stat-icon{width:58px;height:58px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:24px;color:white;background:rgba(255,255,255,.22);flex-shrink:0;transition:transform .3s}
    .stat-card:hover .stat-icon{transform:scale(1.12) rotate(-5deg)}
    .stat-info{flex:1;position:relative;z-index:1}
    .stat-info h3{margin:0;font-size:34px;font-weight:800;color:#fff;line-height:1}
    .stat-info p{margin:5px 0 0;color:rgba(255,255,255,.85);font-size:13px;font-weight:500}
    .card-arrow{position:absolute;right:14px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,.4);font-size:14px;transition:all .3s;opacity:0}
    .stat-card:hover .card-arrow{opacity:1;color:rgba(255,255,255,.9);right:10px}
    /* -- QUICK LINKS -- */
    .quick-links{display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:14px}
    .quick-link{border-radius:14px;color:white;padding:22px 14px;text-decoration:none;text-align:center;transition:all .35s cubic-bezier(.25,.8,.25,1);display:flex;flex-direction:column;align-items:center;gap:10px;position:relative;overflow:hidden;box-shadow:0 4px 14px rgba(0,0,0,.15)}
    .quick-link::before{content:'';position:absolute;top:50%;left:50%;width:0;height:0;border-radius:50%;background:rgba(255,255,255,.22);transform:translate(-50%,-50%);transition:width .55s,height .55s}
    .quick-link:hover::before{width:280px;height:280px}
    .quick-link:hover{transform:translateY(-7px) scale(1.04);box-shadow:0 16px 32px rgba(0,0,0,.25)}
    .quick-link:active{transform:translateY(-2px) scale(.96)}
    .quick-link i{font-size:26px;position:relative;z-index:1;transition:transform .3s}
    .quick-link:hover i{transform:scale(1.25) rotate(-8deg)}
    .quick-link span{position:relative;z-index:1;font-weight:700;font-size:13px}
    .ql-blue{background:linear-gradient(135deg,#004080,#0077cc)}
    .ql-green{background:linear-gradient(135deg,#1a7a4a,#20c997)}
    .ql-orange{background:linear-gradient(135deg,#e05c00,#ffc107)}
    .ql-teal{background:linear-gradient(135deg,#0d7377,#14a085)}
    .ql-purple{background:linear-gradient(135deg,#5a1fa0,#9b59b6)}
    .ql-pink{background:linear-gradient(135deg,#c0392b,#e83e8c)}
    .content-grid{display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:30px}
    .card{background:white;padding:30px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.1)}
    .card h2{margin-top:0;color:#004080;margin-bottom:20px}
    .course-list{list-style:none;padding:0;margin:0}
    .course-item{padding:15px 18px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center;cursor:pointer;transition:all .22s cubic-bezier(.25,.8,.25,1);border-radius:8px;border-left:3px solid transparent;text-decoration:none;color:inherit}
    .course-item:last-child{border-bottom:none}
    .course-item:hover{background:#f0f4ff;border-left-color:#004080;transform:translateX(4px);box-shadow:0 2px 10px rgba(0,64,128,.1)}
    .course-item:active{transform:translateX(2px) scale(0.99)}
    .course-item .ci-arrow{color:#ccc;font-size:12px;transition:all .22s;opacity:0}
    .course-item:hover .ci-arrow{opacity:1;color:#004080;transform:translateX(3px)}
    .course-name{font-weight:600;color:#333}
    .notice-list{list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:8px}
    .notice-item{padding:13px 16px;border-left:4px solid #004080;background:#f8f9fa;border-radius:6px;cursor:pointer;transition:all .22s cubic-bezier(.25,.8,.25,1);display:flex;justify-content:space-between;align-items:center;text-decoration:none;color:inherit}
    .notice-item:hover{background:#e8f0fe;border-left-color:#0059b3;transform:translateX(4px);box-shadow:0 3px 12px rgba(0,64,128,.12)}
    .notice-item:active{transform:translateX(2px) scale(0.99)}
    .notice-item .ni-arrow{color:#ccc;font-size:12px;transition:all .22s;opacity:0;flex-shrink:0}
    .notice-item:hover .ni-arrow{opacity:1;color:#004080;transform:translateX(3px)}
    .notice-title{font-weight:600;color:#004080;margin-bottom:4px;font-size:14px}
    .notice-date{font-size:12px;color:#999}

  </style>
</head>
<body>
<div class="top-header"><marquee>Welcome to SCTI Student Portal - Your gateway to academic excellence</marquee></div>
<div class="dashboard-container">
  <div class="dashboard-header">
    <div>
      <h1><i class="fa fa-graduation-cap"></i> Student Dashboard</h1>
      <p style="margin:5px 0 0;opacity:.9">Welcome back, <?=htmlspecialchars($fullName)?>!</p>
    </div>
    <div class="user-info">
      <a href="../pages/student-profile.php" style="color:white;text-decoration:none;display:flex;align-items:center;gap:6px;background:rgba(255,255,255,0.15);padding:8px 16px;border-radius:5px;transition:.2s" onmouseover="this.style.background='rgba(255,255,255,0.28)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'"><i class="fa fa-user-graduate"></i> Student</a>
      <a href="../includes/logout.php" class="logout-btn"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </div>
  </div>

  <div class="stats-grid">
    <div class="stat-card blue" onclick="window.location.href='../pages/student-courses.php'">
      <div class="stat-icon"><i class="fa fa-book"></i></div>
      <div class="stat-info"><h3><?=$enrolledCourses?></h3><p>Enrolled Courses</p></div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>
    <div class="stat-card green" onclick="window.location.href='../pages/student-attendance.php'">
      <div class="stat-icon"><i class="fa fa-check-circle"></i></div>
      <div class="stat-info"><h3><?=$attPct?>%</h3><p>Attendance</p></div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>
    <div class="stat-card orange" onclick="window.location.href='../pages/student-grades.php'">
      <div class="stat-icon"><i class="fa fa-star"></i></div>
      <div class="stat-info"><h3><?=number_format($gpa,1)?></h3><p>GPA</p></div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>
    <div class="stat-card purple" onclick="window.location.href='../pages/student-assignments.php'">
      <div class="stat-icon"><i class="fa fa-tasks"></i></div>
      <div class="stat-info"><h3><?=$pendingAssign?></h3><p>Pending Assignments</p></div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>
  </div>

  <div class="content-grid">
    <div class="card">
      <h2><i class="fa fa-book-open"></i> My Courses</h2>
      <ul class="course-list">
        <?php if (empty($courses)): ?>
        <li class="course-item"><span style="color:#999">No courses found.</span></li>
        <?php else: ?>
        <?php foreach ($courses as $c): ?>
        <li class="course-item" onclick="window.location.href='../pages/student-courses.php'">
          <div>
            <div class="course-name"><?=htmlspecialchars($c['name'])?></div>
            <small style="color:#666"><?=htmlspecialchars($c['duration'] ?? '')?></small>
          </div>
          <div style="display:flex;align-items:center;gap:10px">
            <span style="background:#cce5ff;color:#004080;padding:4px 10px;border-radius:12px;font-size:12px"><?=htmlspecialchars($c['status'] ?? 'Active')?></span>
            <i class="fa fa-chevron-right ci-arrow"></i>
          </div>
        </li>
        <?php endforeach; ?>
        <?php endif; ?>
      </ul>
      <div style="margin-top:12px;text-align:right">
        <a href="../pages/student-courses.php" style="color:#004080;font-size:13px;text-decoration:none;font-weight:600"><i class="fa fa-arrow-right"></i> View All Courses</a>
      </div>
    </div>

    <div class="card">
      <h2><i class="fa fa-bullhorn"></i> Recent Notices</h2>
      <ul class="notice-list">
        <?php if (empty($notices)): ?>
        <li class="notice-item"><div class="notice-title" style="color:#999">No notices yet.</div></li>
        <?php else: ?>
        <?php foreach ($notices as $n): ?>
        <li class="notice-item" onclick="window.location.href='../index.php?page=notices'">
          <div>
            <div class="notice-title"><?=htmlspecialchars($n['title'])?></div>
            <div class="notice-date"><i class="fa fa-clock" style="margin-right:4px"></i><?=timeAgo($n['created_at'])?></div>
          </div>
          <i class="fa fa-chevron-right ni-arrow"></i>
        </li>
        <?php endforeach; ?>
        <?php endif; ?>
      </ul>
      <div style="margin-top:12px;text-align:right">
        <a href="../index.php?page=notices" style="color:#004080;font-size:13px;text-decoration:none;font-weight:600"><i class="fa fa-arrow-right"></i> View All Notices</a>
      </div>
    </div>
  </div>

  <div class="card">
    <h2><i class="fa fa-bolt"></i> Quick Links</h2>
    <div class="quick-links">
      <a href="../pages/student-attendance.php" class="quick-link ql-blue"><i class="fa fa-calendar-check"></i><span>Attendance</span></a>
      <a href="../pages/student-grades.php" class="quick-link ql-green"><i class="fa fa-chart-line"></i><span>Grades</span></a>
      <a href="../pages/student-assignments.php" class="quick-link ql-orange"><i class="fa fa-file-alt"></i><span>Assignments</span></a>
      <a href="../pages/student-timetable.php" class="quick-link ql-teal"><i class="fa fa-clock"></i><span>Timetable</span></a>
      <a href="../pages/student-library.php" class="quick-link ql-purple"><i class="fa fa-book"></i><span>Library</span></a>
      <a href="../pages/student-profile.php" class="quick-link ql-pink"><i class="fa fa-user"></i><span>Profile</span></a>
    </div>
  </div>
</div>
<footer class="footer" style="margin-top:40px"><p>� 2025 Sindhuli Community Technical Institute (SCTI) - Student Portal</p></footer>

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
