<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../index.php'); exit();
}
$fullName  = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Teacher';
$teacherId = $_SESSION['user_id'] ?? 0;
require_once '../includes/config.php';
try {
    $db = getDBConnection();
    $totalStudents = $db->query("SELECT COUNT(*) FROM students WHERE status='active'")->fetchColumn();
} catch(Exception $e) { $totalStudents = 0; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Schedule | SCTI Teacher Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; }
    .top-header { background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 10px 20px; font-size: 14px; }
    .footer { background: #2c3e50; color: white; text-align: center; padding: 20px; margin-top: 30px; }
    .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
    .page-header {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;
      box-shadow: 0 4px 15px rgba(40,167,69,0.2);
    }
    .page-header h1 { margin: 0 0 8px 0; font-size: 28px; }
    .breadcrumb { background: transparent !important; padding: 0; font-size: 14px; }
    .breadcrumb a { color: white; text-decoration: none; }
    .card { background: white; border-radius: 10px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 25px; }
    .card h2 { color: #28a745; margin-bottom: 20px; font-size: 20px; }
    .timetable { width: 100%; border-collapse: collapse; }
    .timetable th {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white; padding: 14px 10px; text-align: center; font-size: 14px;
    }
    .timetable td { padding: 10px; border: 1px solid #e9ecef; text-align: center; font-size: 13px; vertical-align: middle; }
    .timetable tr:hover td { background: #f0fff4; }
    .time-col { font-weight: 600; color: #28a745; background: #f8f9fa !important; white-space: nowrap; }
    .class-slot {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white; padding: 10px 8px; border-radius: 6px;
      font-size: 12px; line-height: 1.4;
    }
    .class-slot .subject { font-weight: 700; font-size: 13px; }
    .class-slot .room { opacity: 0.9; font-size: 11px; margin-top: 3px; }
    .free-slot { color: #aaa; font-style: italic; font-size: 12px; }
    .today-col { background: #f0fff4 !important; }
    .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 25px; }
    .stat-box {
      background: white; border-radius: 10px; padding: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;
      border-top: 4px solid #28a745;
    }
    .stat-box .num { font-size: 32px; font-weight: 700; color: #28a745; }
    .stat-box .lbl { color: #666; font-size: 13px; margin-top: 5px; }
    .today-schedule { display: grid; gap: 12px; }
    .today-item {
      display: flex; align-items: center; gap: 15px;
      padding: 15px; background: #f8f9fa; border-radius: 8px;
      border-left: 4px solid #28a745;
    }
    .today-time { font-weight: 700; color: #28a745; min-width: 130px; font-size: 14px; }
    .today-subject { font-weight: 600; color: #333; }
    .today-room { color: #666; font-size: 13px; }
    .badge-now { background: #28a745; color: white; padding: 3px 8px; border-radius: 10px; font-size: 11px; margin-left: 8px; }
  </style>
</head>
<body>
<div class="top-header"><marquee>My Teaching Schedule - Weekly timetable and class overview</marquee></div>
<div class="container">
  <div class="page-header">
    <h1><i class="fa fa-calendar-alt"></i> My Schedule</h1>
    <div class="breadcrumb">
      <a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / My Schedule
    </div>
  </div>

  <div class="stats-row">
    <div class="stat-box"><div class="num">4</div><div class="lbl">Classes Today</div></div>
    <div class="stat-box"><div class="num">5</div><div class="lbl">Subjects Teaching</div></div>
    <div class="stat-box"><div class="num">18</div><div class="lbl">Hours/Week</div></div>
    <div class="stat-box"><div class="num"><?php echo $totalStudents; ?></div><div class="lbl">Total Students</div></div>
  </div>

  <div class="card">
    <h2><i class="fa fa-sun"></i> Today's Classes (Saturday)</h2>
    <div class="today-schedule">
      <div class="today-item">
        <div class="today-time">9:00 - 10:30 AM</div>
        <div><div class="today-subject">Programming Fundamentals <span class="badge-now">Now</span></div><div class="today-room"><i class="fa fa-door-open"></i> Room 101 &nbsp;|&nbsp; <i class="fa fa-users"></i> 35 Students</div></div>
      </div>
      <div class="today-item">
        <div class="today-time">11:00 AM - 12:30 PM</div>
        <div><div class="today-subject">Database Management</div><div class="today-room"><i class="fa fa-door-open"></i> Room 203 &nbsp;|&nbsp; <i class="fa fa-users"></i> 28 Students</div></div>
      </div>
      <div class="today-item">
        <div class="today-time">1:30 - 3:00 PM</div>
        <div><div class="today-subject">Web Development</div><div class="today-room"><i class="fa fa-door-open"></i> Lab 3 &nbsp;|&nbsp; <i class="fa fa-users"></i> 32 Students</div></div>
      </div>
      <div class="today-item">
        <div class="today-time">3:30 - 5:00 PM</div>
        <div><div class="today-subject">Data Structures</div><div class="today-room"><i class="fa fa-door-open"></i> Room 102 &nbsp;|&nbsp; <i class="fa fa-users"></i> 25 Students</div></div>
      </div>
    </div>
  </div>

  <div class="card">
    <h2><i class="fa fa-table"></i> Weekly Timetable</h2>
    <div style="overflow-x:auto;">
    <table class="timetable">
      <thead>
        <tr>
          <th>Time</th><th>Sunday</th><th>Monday</th><th>Tuesday</th><th>Wednesday</th><th class="today-col">Thursday</th><th>Friday</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="time-col">9:00–10:30</td>
          <td><div class="class-slot"><div class="subject">Prog. Fundamentals</div><div class="room">Room 101</div></div></td>
          <td><span class="free-slot">Free</span></td>
          <td><div class="class-slot"><div class="subject">Prog. Fundamentals</div><div class="room">Room 101</div></div></td>
          <td><span class="free-slot">Free</span></td>
          <td class="today-col"><div class="class-slot"><div class="subject">Prog. Fundamentals</div><div class="room">Room 101</div></div></td>
          <td><span class="free-slot">Free</span></td>
        </tr>
        <tr>
          <td class="time-col">11:00–12:30</td>
          <td><span class="free-slot">Free</span></td>
          <td><div class="class-slot"><div class="subject">Database Mgmt</div><div class="room">Room 203</div></div></td>
          <td><span class="free-slot">Free</span></td>
          <td><div class="class-slot"><div class="subject">Database Mgmt</div><div class="room">Room 203</div></div></td>
          <td class="today-col"><span class="free-slot">Free</span></td>
          <td><span class="free-slot">Free</span></td>
        </tr>
        <tr>
          <td class="time-col">1:30–3:00</td>
          <td><div class="class-slot"><div class="subject">Web Development</div><div class="room">Lab 3</div></div></td>
          <td><span class="free-slot">Free</span></td>
          <td><div class="class-slot"><div class="subject">Web Development</div><div class="room">Lab 3</div></div></td>
          <td><span class="free-slot">Free</span></td>
          <td class="today-col"><div class="class-slot"><div class="subject">Web Development</div><div class="room">Lab 3</div></div></td>
          <td><span class="free-slot">Free</span></td>
        </tr>
        <tr>
          <td class="time-col">3:30–5:00</td>
          <td><span class="free-slot">Free</span></td>
          <td><div class="class-slot"><div class="subject">Data Structures</div><div class="room">Room 102</div></div></td>
          <td><span class="free-slot">Free</span></td>
          <td><div class="class-slot"><div class="subject">Data Structures</div><div class="room">Room 102</div></div></td>
          <td class="today-col"><span class="free-slot">Free</span></td>
          <td><span class="free-slot">Free</span></td>
        </tr>
      </tbody>
    </table>
    </div>
  </div>
</div>
<footer class="footer" style="background:#2c3e50;color:white;text-align:center;padding:20px;margin-top:30px;"><p>© 2025 SCTI - Teacher Portal</p></footer>
</body>
</html>
