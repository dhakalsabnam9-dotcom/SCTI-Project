<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../index.php'); exit();
}
$fullName  = $_SESSION['full_name'] ?? 'Teacher';
$teacherId = $_SESSION['user_id']   ?? 0;
require_once '../includes/config.php';
try {
    $db = getDBConnection();
    $totalStudents = $db->query("SELECT COUNT(*) FROM students WHERE status='active'")->fetchColumn();
    $stmt = $db->prepare("SELECT COUNT(*) FROM assignments WHERE created_by=?");
    $stmt->execute([$teacherId]);
    $totalAssignments = $stmt->fetchColumn();
    $tStmt = $db->prepare("SELECT subjects FROM teachers WHERE id=? LIMIT 1");
    $tStmt->execute([$teacherId]);
    $tRow = $tStmt->fetch();
    $subjects = array_filter(array_map('trim', explode(',', $tRow['subjects'] ?? '')));
    $subjectCount = count($subjects) ?: 5;
} catch(Exception $e) { $totalStudents = 0; $totalAssignments = 0; $subjectCount = 5; }

$days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
$todayName = $days[date('w')];
$todayIdx  = date('w'); // 0=Sun

$schedule = [
    ['time'=>'9:00 – 10:30 AM',  'subject'=>'Programming Fundamentals', 'room'=>'Room 101', 'students'=>35, 'days'=>[0,2,4]],
    ['time'=>'11:00 AM – 12:30 PM','subject'=>'Database Management',    'room'=>'Room 203', 'students'=>28, 'days'=>[1,3]],
    ['time'=>'1:30 – 3:00 PM',   'subject'=>'Web Development',          'room'=>'Lab 3',    'students'=>32, 'days'=>[0,2,4]],
    ['time'=>'3:30 – 5:00 PM',   'subject'=>'Data Structures',          'room'=>'Room 102', 'students'=>25, 'days'=>[1,3]],
];

// Today's classes
$todayClasses = array_filter($schedule, function($s) use ($todayIdx) {
    return in_array($todayIdx, $s['days']);
});

// Current hour for "Now" badge
$currentHour = (int)date('G');
$timeRanges  = [[9,10],[11,12],[13,15],[15,17]];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Schedule | SCTI Teacher Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Segoe UI',sans-serif;background:#f0f7f2;min-height:100vh}
    .top-header{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:8px 20px;font-size:13px}
    .footer{background:#2c3e50;color:white;text-align:center;padding:18px;margin-top:30px;font-size:13px}
    .container{max-width:1300px;margin:0 auto;padding:22px}

    /* HEADER */
    .page-header{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:28px 32px;border-radius:14px;margin-bottom:28px;box-shadow:0 6px 20px rgba(40,167,69,.25);display:flex;justify-content:space-between;align-items:center}
    .page-header h1{margin:0 0 6px;font-size:26px;display:flex;align-items:center;gap:10px}
    .breadcrumb{font-size:13px;opacity:.85}
    .breadcrumb a{color:white;text-decoration:none}
    .hdr-actions{display:flex;gap:10px}
    .btn-hdr{padding:9px 18px;border-radius:9px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:7px;border:none;background:rgba(255,255,255,.2);color:white;text-decoration:none;transition:.2s}
    .btn-hdr:hover{background:rgba(255,255,255,.35);transform:translateY(-1px)}

    /* STAT CARDS */
    .stats-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:26px}
    .stat-box{background:white;border-radius:14px;padding:22px 20px;box-shadow:0 2px 12px rgba(0,0,0,.08);text-align:center;border-top:4px solid #28a745;cursor:pointer;transition:.3s;position:relative;overflow:hidden;text-decoration:none;display:block}
    .stat-box::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,#28a745,#20c997);opacity:0;transition:.3s}
    .stat-box:hover{transform:translateY(-6px);box-shadow:0 10px 28px rgba(40,167,69,.25)}
    .stat-box:hover::before{opacity:.06}
    .stat-box .num{font-size:36px;font-weight:800;color:#28a745;line-height:1;position:relative}
    .stat-box .lbl{color:#666;font-size:13px;margin-top:6px;position:relative}
    .stat-box .ico{font-size:28px;color:#e8f5e9;position:absolute;right:16px;top:16px}

    /* TODAY SECTION */
    .section-title{font-size:18px;font-weight:700;color:#28a745;margin-bottom:16px;display:flex;align-items:center;gap:9px}
    .card{background:white;border-radius:14px;padding:26px;box-shadow:0 2px 12px rgba(0,0,0,.08);margin-bottom:24px}

    .today-grid{display:grid;gap:12px}
    .today-item{display:flex;align-items:center;gap:16px;padding:16px 20px;background:#f8fffe;border-radius:10px;border-left:5px solid #28a745;cursor:pointer;transition:.3s;text-decoration:none;color:inherit}
    .today-item:hover{background:#e8f5e9;transform:translateX(6px);box-shadow:0 4px 14px rgba(40,167,69,.15)}
    .today-item.active-now{border-left-color:#ff6b35;background:#fff8f5}
    .today-item.active-now:hover{background:#ffe8de}
    .today-time{font-weight:700;color:#28a745;min-width:150px;font-size:14px}
    .today-item.active-now .today-time{color:#ff6b35}
    .today-subject{font-weight:700;color:#222;font-size:15px;margin-bottom:4px}
    .today-meta{color:#666;font-size:13px;display:flex;gap:14px;flex-wrap:wrap}
    .today-meta i{color:#28a745}
    .badge-now{background:linear-gradient(135deg,#ff6b35,#f7931e);color:white;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700;margin-left:8px;animation:pulse 1.5s infinite}
    @keyframes pulse{0%,100%{opacity:1}50%{opacity:.7}}
    .badge-upcoming{background:#e8f5e9;color:#28a745;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700;margin-left:8px}
    .no-class{text-align:center;padding:40px;color:#aaa}
    .no-class i{font-size:48px;display:block;margin-bottom:12px;color:#ddd}

    /* WEEKLY TABLE */
    .timetable{width:100%;border-collapse:collapse;font-size:13px}
    .timetable th{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:13px 10px;text-align:center;font-size:13px;font-weight:600}
    .timetable th.today-hdr{background:linear-gradient(135deg,#1a7a32,#17a86e);position:relative}
    .timetable th.today-hdr::after{content:'Today';position:absolute;bottom:-1px;left:50%;transform:translateX(-50%);background:#ff6b35;color:white;font-size:9px;padding:1px 6px;border-radius:4px 4px 0 0}
    .timetable td{padding:8px;border:1px solid #e9ecef;text-align:center;vertical-align:middle}
    .timetable tr:hover td{background:#f0fff4}
    .time-col{font-weight:700;color:#28a745;background:#f8fffe!important;white-space:nowrap;font-size:12px;padding:10px 12px!important}
    .today-col{background:#f0fff4!important}
    .class-slot{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:10px 8px;border-radius:8px;cursor:pointer;transition:.2s;line-height:1.4}
    .class-slot:hover{transform:scale(1.04);box-shadow:0 4px 12px rgba(40,167,69,.35)}
    .class-slot .subj{font-weight:700;font-size:12px}
    .class-slot .rm{opacity:.9;font-size:10px;margin-top:3px}
    .free-slot{color:#ccc;font-style:italic;font-size:11px}

    /* QUICK ACTIONS */
    .qa-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px}
    .qa-btn{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:18px 14px;border-radius:12px;text-decoration:none;text-align:center;transition:.3s;display:flex;flex-direction:column;align-items:center;gap:9px;font-size:13px;font-weight:600;position:relative;overflow:hidden}
    .qa-btn::before{content:'';position:absolute;top:50%;left:50%;width:0;height:0;border-radius:50%;background:rgba(255,255,255,.25);transform:translate(-50%,-50%);transition:.5s}
    .qa-btn:hover::before{width:250px;height:250px}
    .qa-btn:hover{transform:translateY(-5px);box-shadow:0 8px 22px rgba(40,167,69,.4)}
    .qa-btn i{font-size:22px;position:relative;z-index:1}
    .qa-btn span{position:relative;z-index:1}

    @media(max-width:768px){.stats-row{grid-template-columns:repeat(2,1fr)}.page-header{flex-direction:column;gap:14px}.today-time{min-width:110px}}
  </style>
</head>
<body>
<div class="top-header"><marquee>My Teaching Schedule — Weekly timetable and class overview</marquee></div>
<div class="container">

  <div class="page-header">
    <div>
      <h1><i class="fa fa-calendar-alt"></i> My Schedule</h1>
      <div class="breadcrumb"><a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / My Schedule</div>
    </div>
    <div class="hdr-actions">
      <a href="../dashboards/teacher-dashboard.php" class="btn-hdr"><i class="fa fa-arrow-left"></i> Back</a>
      <a href="teacher-assignments.php" class="btn-hdr"><i class="fa fa-file-alt"></i> Assignments</a>
    </div>
  </div>

  <!-- STAT CARDS -->
  <div class="stats-row">
    <a href="teacher-students.php" class="stat-box">
      <i class="fa fa-calendar-day ico"></i>
      <div class="num"><?=count($todayClasses)?></div>
      <div class="lbl">Classes Today</div>
    </a>
    <a href="teacher-students.php" class="stat-box">
      <i class="fa fa-book ico"></i>
      <div class="num"><?=$subjectCount?></div>
      <div class="lbl">Subjects Teaching</div>
    </a>
    <a href="teacher-assignments.php" class="stat-box">
      <i class="fa fa-clock ico"></i>
      <div class="num">18</div>
      <div class="lbl">Hours/Week</div>
    </a>
    <a href="teacher-students.php" class="stat-box">
      <i class="fa fa-users ico"></i>
      <div class="num"><?=$totalStudents?></div>
      <div class="lbl">Total Students</div>
    </a>
  </div>

  <!-- TODAY'S CLASSES -->
  <div class="card">
    <div class="section-title"><i class="fa fa-sun"></i> Today's Classes (<?=$todayName?>)</div>
    <div class="today-grid">
      <?php if (empty($todayClasses)): ?>
      <div class="no-class"><i class="fa fa-coffee"></i><p>No classes scheduled today. Enjoy your day off!</p></div>
      <?php else: ?>
      <?php foreach (array_values($todayClasses) as $i => $cls):
          $isNow = ($currentHour >= $timeRanges[$i][0] && $currentHour < $timeRanges[$i][1]);
      ?>
      <a href="teacher-students.php" class="today-item <?=$isNow?'active-now':''?>">
        <div class="today-time"><?=$cls['time']?></div>
        <div style="flex:1">
          <div class="today-subject">
            <?=htmlspecialchars($cls['subject'])?>
            <?php if ($isNow): ?><span class="badge-now"><i class="fa fa-circle"></i> Now</span>
            <?php else: ?><span class="badge-upcoming">Upcoming</span><?php endif; ?>
          </div>
          <div class="today-meta">
            <span><i class="fa fa-door-open"></i> <?=$cls['room']?></span>
            <span><i class="fa fa-users"></i> <?=$cls['students']?> Students</span>
          </div>
        </div>
        <i class="fa fa-chevron-right" style="color:#ccc"></i>
      </a>
      <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- WEEKLY TIMETABLE -->
  <div class="card">
    <div class="section-title"><i class="fa fa-table"></i> Weekly Timetable</div>
    <div style="overflow-x:auto">
    <table class="timetable">
      <thead>
        <tr>
          <th>Time</th>
          <?php foreach ($days as $di => $d): ?>
          <th class="<?=$di===$todayIdx?'today-hdr':''?>"><?=$d?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($schedule as $row): ?>
        <tr>
          <td class="time-col"><?=$row['time']?></td>
          <?php foreach ($days as $di => $d): ?>
          <td class="<?=$di===$todayIdx?'today-col':''?>">
            <?php if (in_array($di, $row['days'])): ?>
            <div class="class-slot" onclick="window.location.href='teacher-students.php'">
              <div class="subj"><?=htmlspecialchars($row['subject'])?></div>
              <div class="rm"><i class="fa fa-door-open"></i> <?=$row['room']?></div>
            </div>
            <?php else: ?>
            <span class="free-slot">—</span>
            <?php endif; ?>
          </td>
          <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  </div>

  <!-- QUICK ACTIONS -->
  <div class="card">
    <div class="section-title"><i class="fa fa-bolt"></i> Quick Actions</div>
    <div class="qa-grid">
      <a href="teacher-attendance.php" class="qa-btn"><i class="fa fa-calendar-check"></i><span>Mark Attendance</span></a>
      <a href="teacher-grades.php"     class="qa-btn"><i class="fa fa-chart-line"></i><span>Enter Grades</span></a>
      <a href="teacher-assignments.php" class="qa-btn"><i class="fa fa-file-alt"></i><span>Assignments</span></a>
      <a href="teacher-students.php"   class="qa-btn"><i class="fa fa-users"></i><span>View Students</span></a>
      <a href="teacher-materials.php"  class="qa-btn"><i class="fa fa-book"></i><span>Materials</span></a>
      <a href="teacher-profile.php"    class="qa-btn"><i class="fa fa-user"></i><span>My Profile</span></a>
    </div>
  </div>

</div>
<footer class="footer"><p>© 2025 SCTI — Teacher Portal</p></footer>
</body>
</html>
