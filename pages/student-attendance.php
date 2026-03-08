<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header('Location: ../index.php');
    exit();
}
$fullName = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Attendance | SCTI Student Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
    .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
    
    .page-header {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;
      box-shadow: 0 4px 15px rgba(40,167,69,0.2);
    }
    .page-header h1 { margin: 0 0 10px 0; font-size: 28px; }
    .breadcrumb { opacity: 0.9; font-size: 14px; }
    .breadcrumb a { color: white; text-decoration: none; }
    
    .stats-row {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px; margin-bottom: 30px;
    }
    .stat-box {
      background: white; padding: 20px; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;
    }
    .stat-box h3 { font-size: 36px; color: #28a745; margin: 10px 0; }
    .stat-box p { color: #666; font-size: 14px; }
    
    .attendance-card {
      background: white; padding: 30px; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px;
    }
    .attendance-card h2 { color: #28a745; margin-bottom: 20px; }
    
    .attendance-table {
      width: 100%; border-collapse: collapse;
    }
    .attendance-table th {
      background: #f8f9fa; padding: 15px; text-align: left;
      color: #333; font-weight: 600; border-bottom: 2px solid #dee2e6;
    }
    .attendance-table td {
      padding: 15px; border-bottom: 1px solid #dee2e6;
    }
    .attendance-table tr:hover {
      background: #f8f9fa;
    }
    
    .status-badge {
      padding: 6px 12px; border-radius: 20px; font-size: 12px;
      font-weight: 600; display: inline-block;
    }
    .status-present { background: #d4edda; color: #155724; }
    .status-absent { background: #f8d7da; color: #721c24; }
    .status-late { background: #fff3cd; color: #856404; }
    
    .progress-circle {
      width: 120px; height: 120px; border-radius: 50%;
      background: conic-gradient(#28a745 0% 85%, #e0e0e0 85% 100%);
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 20px;
      position: relative;
    }
    .progress-circle::before {
      content: '85%'; position: absolute;
      font-size: 24px; font-weight: bold; color: #28a745;
    }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Attendance Tracking - Monitor your class attendance and maintain good academic standing</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <h1><i class="fa fa-calendar-check"></i> My Attendance</h1>
    <div class="breadcrumb">
      <a href="../dashboards/student-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Attendance
    </div>
  </div>

  <div class="stats-row">
    <div class="stat-box">
      <i class="fa fa-check-circle" style="font-size: 32px; color: #28a745;"></i>
      <h3>85%</h3>
      <p>Overall Attendance</p>
    </div>
    <div class="stat-box">
      <i class="fa fa-calendar-day" style="font-size: 32px; color: #004080;"></i>
      <h3>68</h3>
      <p>Classes Attended</p>
    </div>
    <div class="stat-box">
      <i class="fa fa-times-circle" style="font-size: 32px; color: #dc3545;"></i>
      <h3>12</h3>
      <p>Classes Missed</p>
    </div>
    <div class="stat-box">
      <i class="fa fa-clock" style="font-size: 32px; color: #ffc107;"></i>
      <h3>3</h3>
      <p>Late Arrivals</p>
    </div>
  </div>

  <div class="attendance-card">
    <h2><i class="fa fa-list"></i> Attendance Records</h2>
    <table class="attendance-table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Course</th>
          <th>Time</th>
          <th>Status</th>
          <th>Remarks</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Mar 7, 2026</td>
          <td>Programming Fundamentals</td>
          <td>9:00 AM - 10:30 AM</td>
          <td><span class="status-badge status-present">Present</span></td>
          <td>On time</td>
        </tr>
        <tr>
          <td>Mar 7, 2026</td>
          <td>Database Management</td>
          <td>11:00 AM - 12:30 PM</td>
          <td><span class="status-badge status-present">Present</span></td>
          <td>On time</td>
        </tr>
        <tr>
          <td>Mar 6, 2026</td>
          <td>Web Development</td>
          <td>2:00 PM - 3:30 PM</td>
          <td><span class="status-badge status-late">Late</span></td>
          <td>Arrived 10 mins late</td>
        </tr>
        <tr>
          <td>Mar 6, 2026</td>
          <td>Data Structures</td>
          <td>3:30 PM - 5:00 PM</td>
          <td><span class="status-badge status-present">Present</span></td>
          <td>On time</td>
        </tr>
        <tr>
          <td>Mar 5, 2026</td>
          <td>Programming Fundamentals</td>
          <td>9:00 AM - 10:30 AM</td>
          <td><span class="status-badge status-absent">Absent</span></td>
          <td>Medical leave</td>
        </tr>
        <tr>
          <td>Mar 5, 2026</td>
          <td>Web Development</td>
          <td>2:00 PM - 3:30 PM</td>
          <td><span class="status-badge status-present">Present</span></td>
          <td>On time</td>
        </tr>
        <tr>
          <td>Mar 4, 2026</td>
          <td>Database Management</td>
          <td>11:00 AM - 12:30 PM</td>
          <td><span class="status-badge status-present">Present</span></td>
          <td>On time</td>
        </tr>
        <tr>
          <td>Mar 4, 2026</td>
          <td>Data Structures</td>
          <td>3:30 PM - 5:00 PM</td>
          <td><span class="status-badge status-present">Present</span></td>
          <td>On time</td>
        </tr>
      </tbody>
    </table>
  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Student Portal</p>
</footer>

</body>
</html>
