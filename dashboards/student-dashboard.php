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
    $stu = $db->prepare("SELECT * FROM students WHERE id=? LIMIT 1");
    $stu->execute([$studentId]);
    $stuData = $stu->fetch() ?: [];

    $program = $stuData['course'] ?? '';
    $enrolledCourses = 0;
    $programRow = null;
    if ($program) {
        $pStmt = $db->prepare("SELECT * FROM programs WHERE title=? AND status='active' LIMIT 1");
        $pStmt->execute([$program]);
        $programRow = $pStmt->fetch();
        if (!$programRow) {
            $pStmt2 = $db->prepare("SELECT * FROM programs WHERE title LIKE ? AND status='active' LIMIT 1");
            $pStmt2->execute(['%'.$program.'%']);
            $programRow = $pStmt2->fetch();
        }
        if ($programRow && !empty($programRow['content'])) {
            $enrolledCourses = count(array_filter(array_map('trim', explode('|', $programRow['content']))));
        } else {
            $enrolledCourses = 1;
        }
    }

    $now = date('Y-m-d H:i:s');
    $pendingAssign = $db->query("SELECT COUNT(*) FROM assignments WHERE due_date > '$now'")->fetchColumn();

    $attStmt = $db->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status='present' OR status='late' THEN 1 ELSE 0 END) as present FROM attendance WHERE student_id=?");
    $attStmt->execute([$studentId]);
    $attRow = $attStmt->fetch();
    $attPct = ($attRow['total'] > 0) ? round(($attRow['present'] / $attRow['total']) * 100) : 0;

    $gradeStmt = $db->prepare("SELECT AVG(internal_marks + external_marks) as avg_marks FROM grades WHERE student_id=?");
    $gradeStmt->execute([$studentId]);
    $gradeRow = $gradeStmt->fetch();
    $avgMarks = floatval($gradeRow['avg_marks'] ?? 0);
    $gpa = $avgMarks > 0 ? round(($avgMarks / 100) * 4, 1) : 0;

    // Recent activity
    $activities = [];
    $recentGrades = $db->prepare("SELECT subject, internal_marks+external_marks as marks, created_at FROM grades WHERE student_id=? ORDER BY created_at DESC LIMIT 2");
    $recentGrades->execute([$studentId]);
    foreach ($recentGrades->fetchAll() as $r) {
        $activities[] = ['icon'=>'fa-star','color'=>'#fd7e14','label'=>'Grade received','name'=>$r['subject'].' — '.$r['marks'].' marks','time'=>$r['created_at'],'link'=>'../pages/student-grades.php'];
    }
    $recentAtt = $db->prepare("SELECT attendance_date, status FROM attendance WHERE student_id=? ORDER BY attendance_date DESC LIMIT 2");
    $recentAtt->execute([$studentId]);
    foreach ($recentAtt->fetchAll() as $r) {
        $activities[] = ['icon'=>'fa-calendar-check','color'=>'#004080','label'=>'Attendance marked','name'=>date('M d, Y', strtotime($r['attendance_date'])).' — '.ucfirst($r['status']),'time'=>$r['attendance_date'],'link'=>'../pages/student-attendance.php'];
    }
    $recentSub = $db->prepare("SELECT a.title, s.submitted_at FROM assignment_submissions s JOIN assignments a ON a.id=s.assignment_id WHERE s.student_id=? ORDER BY s.submitted_at DESC LIMIT 2");
    $recentSub->execute([$studentId]);
    foreach ($recentSub->fetchAll() as $r) {
        $activities[] = ['icon'=>'fa-file-upload','color'=>'#28a745','label'=>'Assignment submitted','name'=>$r['title'],'time'=>$r['submitted_at'],'link'=>'../pages/student-assignments.php'];
    }
    usort($activities, function($a,$b){ return strtotime($b['time']) - strtotime($a['time']); });
    $activities = array_slice($activities, 0, 6);

    // Subjects list
    $subjects = [];
    if ($programRow && !empty($programRow['content'])) {
        $subjects = array_values(array_filter(array_map('trim', explode('|', $programRow['content']))));
    }

} catch(Exception $e) {
    $enrolledCourses = 0; $pendingAssign = 0; $attPct = 0; $gpa = 0;
    $activities = []; $subjects = []; $stuData = []; $programRow = null;
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

    /* STAT CARDS — admin style */
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
    .stat-icon.cyan   { background: linear-gradient(135deg, #17a2b8, #138496); }
    .stat-info h3 { margin: 0; font-size: 32px; color: #004080; }
    .stat-info p  { margin: 5px 0 0 0; color: #666; font-size: 14px; }

    /* QUICK ACTIONS — admin style */
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
    .action-btn .badge-pill {
      position: absolute; top: 10px; right: 10px; background: #ff4757; color: white;
      border-radius: 10px; padding: 2px 7px; font-size: 11px; font-weight: 700; z-index: 2;
    }
    .action-btn .badge-warn {
      position: absolute; top: 10px; right: 10px; background: #ffc107; color: #333;
      border-radius: 10px; padding: 2px 7px; font-size: 11px; font-weight: 700; z-index: 2;
    }

    /* CONTENT GRID */
    .content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 30px; }
    .card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .card h2 { margin-top: 0; color: #004080; margin-bottom: 20px; }

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

    /* SUBJECTS CARD */
    .subject-list { list-style: none; padding: 0; margin: 0; }
    .subject-item {
      padding: 13px 16px; border-bottom: 1px solid #eee; display: flex; align-items: center;
      gap: 12px; cursor: pointer; transition: all 0.22s; border-radius: 8px; border-left: 3px solid transparent;
    }
    .subject-item:last-child { border-bottom: none; }
    .subject-item:hover { background: #f0f4ff; border-left-color: #004080; transform: translateX(4px); }
    .subject-num { width: 30px; height: 30px; border-radius: 8px; background: linear-gradient(135deg,#004080,#0059b3); display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: 700; flex-shrink: 0; }

    /* NOTICE */
    .notice-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px; }
    .notice-item { padding: 13px 16px; border-left: 4px solid #004080; background: #f8f9fa; border-radius: 6px; cursor: pointer; transition: all 0.22s; display: flex; justify-content: space-between; align-items: center; text-decoration: none; color: inherit; }
    .notice-item:hover { background: #e8f0fe; border-left-color: #0059b3; transform: translateX(4px); }
    .notice-title { font-weight: 600; color: #004080; margin-bottom: 4px; font-size: 14px; }
    .notice-date { font-size: 12px; color: #999; }
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

  <!-- Stat Cards -->
  <div class="stats-grid">
    <div class="stat-card" onclick="window.location.href='../pages/student-courses.php'">
      <div class="stat-icon blue"><i class="fa fa-book"></i></div>
      <div class="stat-info"><h3><?=$enrolledCourses?></h3><p>Enrolled Subjects</p></div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>
    <div class="stat-card" onclick="window.location.href='../pages/student-attendance.php'">
      <div class="stat-icon green"><i class="fa fa-calendar-check"></i></div>
      <div class="stat-info"><h3><?=$attPct?>%</h3><p>Attendance Rate</p></div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>
    <div class="stat-card" onclick="window.location.href='../pages/student-grades.php'">
      <div class="stat-icon orange"><i class="fa fa-star"></i></div>
      <div class="stat-info"><h3><?=number_format($gpa,1)?></h3><p>Current GPA</p></div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>
    <div class="stat-card" onclick="window.location.href='../pages/student-assignments.php'">
      <div class="stat-icon purple"><i class="fa fa-tasks"></i></div>
      <div class="stat-info"><h3><?=$pendingAssign?></h3><p>Pending Assignments</p></div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="quick-actions">
    <h2><i class="fa fa-bolt"></i> Quick Actions</h2>
    <div class="action-grid">
      <a href="../pages/student-attendance.php" class="action-btn">
        <?php if ($attPct < 75 && $attPct > 0): ?><span class="badge-warn">!</span><?php endif; ?>
        <i class="fa fa-calendar-check"></i>
        <div><span>Attendance</span><span class="btn-sub"><?=$attPct?>% this term</span></div>
      </a>
      <a href="../pages/student-grades.php" class="action-btn" style="background:linear-gradient(135deg,#fd7e14,#ffc107)">
        <i class="fa fa-chart-line"></i>
        <div><span>My Grades</span><span class="btn-sub">GPA <?=number_format($gpa,1)?></span></div>
      </a>
      <a href="../pages/student-assignments.php" class="action-btn" style="background:linear-gradient(135deg,#6f42c1,#e83e8c)">
        <?php if ($pendingAssign > 0): ?><span class="badge-pill"><?=$pendingAssign?></span><?php endif; ?>
        <i class="fa fa-file-alt"></i>
        <div><span>Assignments</span><span class="btn-sub"><?=$pendingAssign?> pending</span></div>
      </a>
      <a href="../pages/student-timetable.php" class="action-btn" style="background:linear-gradient(135deg,#17a2b8,#138496)">
        <i class="fa fa-clock"></i>
        <div><span>Timetable</span><span class="btn-sub">View schedule</span></div>
      </a>
      <a href="../pages/student-library.php" class="action-btn" style="background:linear-gradient(135deg,#28a745,#20c997)">
        <i class="fa fa-book-open"></i>
        <div><span>Library</span><span class="btn-sub">Course materials</span></div>
      </a>
      <a href="../pages/student-profile.php" class="action-btn" style="background:linear-gradient(135deg,#dc3545,#c82333)">
        <i class="fa fa-user-circle"></i>
        <div><span>My Profile</span><span class="btn-sub">View &amp; edit</span></div>
      </a>
    </div>
  </div>

  <!-- My Courses + Notices -->
  <div class="content-grid">
    <div class="card">
      <h2><i class="fa fa-book-open"></i> My Courses</h2>
      <?php if (!empty($stuData['course'])): ?>
      <div style="background:linear-gradient(135deg,#004080,#0059b3);border-radius:10px;padding:14px 18px;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px">
        <div>
          <div style="color:#fff;font-weight:700;font-size:15px"><?=htmlspecialchars($stuData['course'])?></div>
          <div style="color:rgba(255,255,255,.75);font-size:12px;margin-top:2px"><i class="fa fa-layer-group"></i> <?=htmlspecialchars($stuData['semester'] ?? '')?></div>
        </div>
        <span style="background:rgba(255,255,255,.2);color:#fff;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700"><i class="fa fa-book"></i> <?=$enrolledCourses?> Subjects</span>
      </div>
      <?php endif; ?>
      <ul class="subject-list">
        <?php if (empty($subjects)): ?>
        <li class="subject-item"><span style="color:#999">No subjects found. Contact admin.</span></li>
        <?php else: ?>
        <?php foreach (array_slice($subjects, 0, 6) as $idx => $subj): ?>
        <li class="subject-item" onclick="window.location.href='../pages/student-courses.php'">
          <div class="subject-num"><?=$idx+1?></div>
          <span style="font-weight:600;color:#333"><?=htmlspecialchars($subj)?></span>
          <i class="fa fa-chevron-right" style="margin-left:auto;color:#ccc;font-size:12px"></i>
        </li>
        <?php endforeach; ?>
        <?php endif; ?>
      </ul>
      <div style="margin-top:12px;text-align:right">
        <a href="../pages/student-courses.php" style="color:#004080;font-size:13px;text-decoration:none;font-weight:600"><i class="fa fa-arrow-right"></i> View All</a>
      </div>
    </div>
    <div class="card">
      <h2><i class="fa fa-bullhorn"></i> Recent Notices</h2>
      <?php require_once '../pages/notice-widget.php'; renderNoticeWidget('student'); ?>
      <div style="margin-top:12px;text-align:right">
        <a href="../pages/notice-board.php" style="color:#004080;font-size:13px;text-decoration:none;font-weight:600"><i class="fa fa-arrow-right"></i> View All</a>
      </div>
    </div>
  </div>

  <!-- Profile Edit Form -->
  <div style="display:grid;grid-template-columns:400px 1fr;gap:20px;margin-bottom:30px;align-items:start">

    <!-- PROFILE FORM -->
    <div class="card" style="padding:24px">
      <h2 style="margin-top:0;color:#004080;margin-bottom:18px;padding-bottom:10px;border-bottom:2px solid #e8f0fe;font-size:16px"><i class="fa fa-user-edit"></i> My Profile</h2>
      <div id="spAlert" style="display:none;padding:9px 12px;border-radius:8px;font-size:13px;margin-bottom:12px"></div>

      <div style="margin-bottom:12px">
        <label style="display:block;font-size:12px;font-weight:600;color:#555;margin-bottom:5px">Full Name</label>
        <input type="text" id="spName" value="<?=htmlspecialchars($stuData['full_name'] ?? $fullName)?>" style="width:100%;padding:9px 12px;border:2px solid #dee2e6;border-radius:8px;font-size:13px;font-family:inherit;transition:.2s" onfocus="this.style.borderColor='#004080'" onblur="this.style.borderColor='#dee2e6'">
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px">
        <div>
          <label style="display:block;font-size:12px;font-weight:600;color:#555;margin-bottom:5px">Student ID <span style="display:inline-flex;align-items:center;gap:3px;background:#e8f0fe;color:#004080;font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px;margin-left:4px"><i class="fa fa-lock"></i> Fixed</span></label>
          <input type="text" value="<?=htmlspecialchars($stuData['student_id'] ?? '-')?>" readonly style="width:100%;padding:9px 12px;border:2px solid #dee2e6;border-radius:8px;font-size:13px;background:#f8f9fa;color:#666">
        </div>
        <div>
          <label style="display:block;font-size:12px;font-weight:600;color:#555;margin-bottom:5px">Program</label>
          <input type="text" value="<?=htmlspecialchars($stuData['course'] ?? '-')?>" readonly style="width:100%;padding:9px 12px;border:2px solid #dee2e6;border-radius:8px;font-size:13px;background:#f8f9fa;color:#666">
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px">
        <div>
          <label style="display:block;font-size:12px;font-weight:600;color:#555;margin-bottom:5px">Username <span style="display:inline-flex;align-items:center;gap:3px;background:#e8f0fe;color:#004080;font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px;margin-left:4px"><i class="fa fa-lock"></i> Fixed</span></label>
          <input type="text" value="<?=htmlspecialchars($stuData['username'] ?? $username)?>" readonly style="width:100%;padding:9px 12px;border:2px solid #dee2e6;border-radius:8px;font-size:13px;background:#f8f9fa;color:#666">
        </div>
        <div>
          <label style="display:block;font-size:12px;font-weight:600;color:#555;margin-bottom:5px">Semester</label>
          <input type="text" value="<?=htmlspecialchars($stuData['semester'] ?? '-')?>" readonly style="width:100%;padding:9px 12px;border:2px solid #dee2e6;border-radius:8px;font-size:13px;background:#f8f9fa;color:#666">
        </div>
      </div>

      <hr style="border:none;border-top:1px dashed #e0e6ef;margin:12px 0">

      <div style="margin-bottom:12px">
        <label style="display:block;font-size:12px;font-weight:600;color:#555;margin-bottom:5px">Email</label>
        <input type="email" id="spEmail" value="<?=htmlspecialchars($stuData['email'] ?? '')?>" placeholder="your@email.com" style="width:100%;padding:9px 12px;border:2px solid #dee2e6;border-radius:8px;font-size:13px" onfocus="this.style.borderColor='#004080'" onblur="this.style.borderColor='#dee2e6'">
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px">
        <div>
          <label style="display:block;font-size:12px;font-weight:600;color:#555;margin-bottom:5px">Phone</label>
          <input type="text" id="spPhone" value="<?=htmlspecialchars($stuData['phone'] ?? '')?>" placeholder="98XXXXXXXX" style="width:100%;padding:9px 12px;border:2px solid #dee2e6;border-radius:8px;font-size:13px" onfocus="this.style.borderColor='#004080'" onblur="this.style.borderColor='#dee2e6'">
        </div>
        <div>
          <label style="display:block;font-size:12px;font-weight:600;color:#555;margin-bottom:5px">Qualification</label>
          <input type="text" id="spQual" value="<?=htmlspecialchars($stuData['qualification'] ?? '')?>" placeholder="e.g. +2 Science" style="width:100%;padding:9px 12px;border:2px solid #dee2e6;border-radius:8px;font-size:13px" onfocus="this.style.borderColor='#004080'" onblur="this.style.borderColor='#dee2e6'">
        </div>
      </div>

      <div style="margin-bottom:16px">
        <label style="display:block;font-size:12px;font-weight:600;color:#555;margin-bottom:5px">Address</label>
        <input type="text" id="spAddress" value="<?=htmlspecialchars($stuData['address'] ?? '')?>" placeholder="e.g. Sindhuli" style="width:100%;padding:9px 12px;border:2px solid #dee2e6;border-radius:8px;font-size:13px" onfocus="this.style.borderColor='#004080'" onblur="this.style.borderColor='#dee2e6'">
      </div>

      <button onclick="spSave()" id="spSaveBtn" style="width:100%;padding:12px;background:linear-gradient(135deg,#004080,#0059b3);color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:.25s;position:relative;z-index:1">
        <i class="fa fa-save"></i> Update Profile
      </button>
      <a href="../pages/change-password.php" style="display:block;text-align:center;margin-top:10px;padding:10px;background:#f0f0f0;color:#555;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;position:relative;z-index:1">
        <i class="fa fa-key"></i> Change Password
      </a>
    </div>

    <!-- GRADES / ATTENDANCE SUMMARY TABLE -->
    <div class="card" style="padding:24px;overflow:hidden">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
        <h2 style="margin:0;color:#004080;font-size:16px"><i class="fa fa-chart-bar"></i> My Academic Summary</h2>
        <div style="display:flex;gap:8px">
          <button onclick="spLoadTab('grades')" id="spTabGrades" style="padding:6px 14px;border:2px solid #004080;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;background:#004080;color:#fff;transition:.2s">Grades</button>
          <button onclick="spLoadTab('attendance')" id="spTabAtt" style="padding:6px 14px;border:2px solid #dee2e6;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;background:#fff;color:#555;transition:.2s">Attendance</button>
        </div>
      </div>
      <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse;background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.07)">
          <thead id="spTableHead">
            <tr>
              <th style="background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:11px 13px;text-align:left;font-size:12px">#</th>
              <th style="background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:11px 13px;text-align:left;font-size:12px">Subject</th>
              <th style="background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:11px 13px;text-align:left;font-size:12px">Internal</th>
              <th style="background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:11px 13px;text-align:left;font-size:12px">External</th>
              <th style="background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:11px 13px;text-align:left;font-size:12px">Total</th>
              <th style="background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:11px 13px;text-align:left;font-size:12px">Grade</th>
            </tr>
          </thead>
          <tbody id="spTableBody">
            <tr><td colspan="6" style="text-align:center;padding:30px;color:#999"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>
          </tbody>
        </table>
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
<footer class="footer" style="margin-top:40px"><p>&copy; 2025 Sindhuli Community Technical Institute (SCTI) - Student Portal</p></footer>

<script>
var spCurrentTab = 'grades';

function spSave() {
  var btn = document.getElementById('spSaveBtn');
  var alert = document.getElementById('spAlert');
  btn.disabled = true; btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
  var fd = new FormData();
  fd.append('full_name', document.getElementById('spName').value.trim());
  fd.append('email', document.getElementById('spEmail').value.trim());
  fd.append('phone', document.getElementById('spPhone').value.trim());
  fd.append('qualification', document.getElementById('spQual').value.trim());
  fd.append('address', document.getElementById('spAddress').value.trim());
  fetch('../pages/update-profile.php', {method:'POST', body:fd})
    .then(function(r){return r.json();})
    .then(function(d){
      btn.disabled=false; btn.innerHTML='<i class="fa fa-save"></i> Update Profile';
      if (d.success) {
        alert.style.cssText='display:block;padding:9px 12px;border-radius:8px;font-size:13px;margin-bottom:12px;background:#d4edda;color:#155724;border:1px solid #c3e6cb';
        alert.textContent = 'Profile updated successfully!';
      } else {
        alert.style.cssText='display:block;padding:9px 12px;border-radius:8px;font-size:13px;margin-bottom:12px;background:#f8d7da;color:#721c24;border:1px solid #f5c6cb';
        alert.textContent = d.message || 'Update failed';
      }
      setTimeout(function(){ alert.style.display='none'; }, 5000);
    }).catch(function(){ btn.disabled=false; btn.innerHTML='<i class="fa fa-save"></i> Update Profile'; });
}

function spLoadTab(tab) {
  spCurrentTab = tab;
  var gBtn = document.getElementById('spTabGrades');
  var aBtn = document.getElementById('spTabAtt');
  if (tab === 'grades') {
    gBtn.style.cssText='padding:6px 14px;border:2px solid #004080;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;background:#004080;color:#fff;transition:.2s';
    aBtn.style.cssText='padding:6px 14px;border:2px solid #dee2e6;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;background:#fff;color:#555;transition:.2s';
    document.getElementById('spTableHead').innerHTML = '<tr>'
      +'<th style="background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:11px 13px;text-align:left;font-size:12px">#</th>'
      +'<th style="background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:11px 13px;text-align:left;font-size:12px">Subject</th>'
      +'<th style="background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:11px 13px;text-align:left;font-size:12px">Internal</th>'
      +'<th style="background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:11px 13px;text-align:left;font-size:12px">External</th>'
      +'<th style="background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:11px 13px;text-align:left;font-size:12px">Total</th>'
      +'<th style="background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:11px 13px;text-align:left;font-size:12px">Grade</th>'
      +'</tr>';
    document.getElementById('spTableBody').innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:#999"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>';
    fetch('../pages/grade-get.php')
      .then(function(r){return r.json();})
      .then(function(d){
        var rows = (d.grades||[]);
        if (!rows.length) { document.getElementById('spTableBody').innerHTML='<tr><td colspan="6" style="text-align:center;padding:30px;color:#999">No grades recorded yet</td></tr>'; return; }
        document.getElementById('spTableBody').innerHTML = rows.map(function(g,i){
          var total = (parseFloat(g.internal_marks)||0)+(parseFloat(g.external_marks)||0);
          var grade = total>=90?'A+':total>=80?'A':total>=70?'B+':total>=60?'B':total>=50?'C':'F';
          var gc = total>=50?'background:#d4edda;color:#155724':'background:#f8d7da;color:#721c24';
          return '<tr onmouseover="this.style.background=\'#f0f4ff\'" onmouseout="this.style.background=\'\'">'
            +'<td style="padding:10px 13px;border-bottom:1px solid #f0f0f0;font-size:13px">'+(i+1)+'</td>'
            +'<td style="padding:10px 13px;border-bottom:1px solid #f0f0f0;font-size:13px">'+esc3(g.subject)+'</td>'
            +'<td style="padding:10px 13px;border-bottom:1px solid #f0f0f0;font-size:13px">'+g.internal_marks+'</td>'
            +'<td style="padding:10px 13px;border-bottom:1px solid #f0f0f0;font-size:13px">'+g.external_marks+'</td>'
            +'<td style="padding:10px 13px;border-bottom:1px solid #f0f0f0;font-size:13px;font-weight:700">'+total+'</td>'
            +'<td style="padding:10px 13px;border-bottom:1px solid #f0f0f0;font-size:13px"><span style="padding:3px 9px;border-radius:10px;font-size:11px;font-weight:700;'+gc+'">'+grade+'</span></td>'
            +'</tr>';
        }).join('');
      }).catch(function(){ document.getElementById('spTableBody').innerHTML='<tr><td colspan="6" style="text-align:center;padding:30px;color:#999">Failed to load</td></tr>'; });
  } else {
    gBtn.style.cssText='padding:6px 14px;border:2px solid #dee2e6;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;background:#fff;color:#555;transition:.2s';
    aBtn.style.cssText='padding:6px 14px;border:2px solid #004080;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;background:#004080;color:#fff;transition:.2s';
    document.getElementById('spTableHead').innerHTML = '<tr>'
      +'<th style="background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:11px 13px;text-align:left;font-size:12px">#</th>'
      +'<th style="background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:11px 13px;text-align:left;font-size:12px">Date</th>'
      +'<th style="background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:11px 13px;text-align:left;font-size:12px">Class</th>'
      +'<th style="background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:11px 13px;text-align:left;font-size:12px">Status</th>'
      +'<th style="background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:11px 13px;text-align:left;font-size:12px">Remarks</th>'
      +'</tr>';
    document.getElementById('spTableBody').innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:#999"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>';
    fetch('../pages/student-attendance-data.php?action=log')
      .then(function(r){return r.json();})
      .then(function(d){
        var rows = (d.records||[]);
        if (!rows.length) { document.getElementById('spTableBody').innerHTML='<tr><td colspan="5" style="text-align:center;padding:30px;color:#999">No attendance records yet</td></tr>'; return; }
        document.getElementById('spTableBody').innerHTML = rows.slice(0,20).map(function(a,i){
          var isP = a.status==='present' || a.status==='late';
          var sc = isP?'background:#d4edda;color:#155724':'background:#f8d7da;color:#721c24';
          return '<tr onmouseover="this.style.background=\'#f0f4ff\'" onmouseout="this.style.background=\'\'">'
            +'<td style="padding:10px 13px;border-bottom:1px solid #f0f0f0;font-size:13px">'+(i+1)+'</td>'
            +'<td style="padding:10px 13px;border-bottom:1px solid #f0f0f0;font-size:13px">'+esc3(a.attendance_date)+'</td>'
            +'<td style="padding:10px 13px;border-bottom:1px solid #f0f0f0;font-size:13px">'+esc3(a.class_name||'—')+'</td>'
            +'<td style="padding:10px 13px;border-bottom:1px solid #f0f0f0;font-size:13px"><span style="padding:3px 9px;border-radius:10px;font-size:11px;font-weight:700;'+sc+'">'+cap3(a.status)+'</span></td>'
            +'<td style="padding:10px 13px;border-bottom:1px solid #f0f0f0;font-size:13px">'+esc3(a.remarks||'—')+'</td>'
            +'</tr>';
        }).join('');
      }).catch(function(){ document.getElementById('spTableBody').innerHTML='<tr><td colspan="5" style="text-align:center;padding:30px;color:#999">Failed to load</td></tr>'; });
  }
}
function esc3(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
function cap3(s){return s?s.charAt(0).toUpperCase()+s.slice(1):'';}
spLoadTab('grades');
</script>

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
