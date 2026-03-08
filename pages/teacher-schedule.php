<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../index.php');
    exit();
}
$fullName = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Teacher';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Schedule | SCTI Teacher Portal</title>
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
    
    .schedule-container {
      background: white; border-radius: 10px; padding: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .schedule-table {
      width: 100%; border-collapse: collapse;
    }
    .schedule-table th {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white; padding: 15px; text-align: center;
      font-weight: 600; border: 1px solid #20c997;
    }
    .schedule-table td {
      padding: 15px; border: 1px solid #dee2e6;
      text-align: center; vertical-align: top;
    }
    .time-slot {
      background: #f8f9fa; font-weight: 600;
      color: #28a745;
    }
    
    .class-slot {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white; padding: 12px; border-radius: 6px;
      margin: 5px 0; cursor: pointer;
      transition: all 0.3s;
    }
    .class-slot:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(40,167,69,0.3);
    }
    .class-name {
      font-weight: 600; margin-bottom: 5px;
    }
    .class-room {
      font-size: 12px; opacity: 0.9;
    }
    
    .empty-slot {
      color: #999; font-style: italic;
    }
    
    .break-slot {
      background: #fff3cd; color: #856404;
      padding: 12px; border-radius: 6px;
      font-weight: 600;
    }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>My Schedule - View your weekly teaching schedule</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <h1><i class="fa fa-calendar"></i> My Teaching Schedule</h1>
    <div class="breadcrumb">
      <a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / My Schedule
    </div>
  </div>

  <div class="schedule-container">
    <table class="schedule-table">
      <thead>
        <tr>
          <th style="width: 120px;">Time</th>
          <th>Monday</th>
          <th>Tuesday</th>
          <th>Wednesday</th>
          <th>Thursday</th>
          <th>Friday</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="time-slot">9:00 AM<br>10:30 AM</td>
          <td>
            <div class="class-slot">
              <div class="class-name">Programming Fundamentals</div>
              <div class="class-room">Room 101 / Lab 1</div>
            </div>
          </td>
          <td><div class="empty-slot">Free Period</div></td>
          <td>
            <div class="class-slot">
              <div class="class-name">Programming Fundamentals</div>
              <div class="class-room">Room 101 / Lab 1</div>
            </div>
          </td>
          <td><div class="empty-slot">Free Period</div></td>
          <td>
            <div class="class-slot">
              <div class="class-name">Programming Fundamentals</div>
              <div class="class-room">Room 101 / Lab 1</div>
            </div>
          </td>
        </tr>
        
        <tr>
          <td class="time-slot">10:30 AM<br>11:00 AM</td>
          <td colspan="5"><div class="break-slot"><i class="fa fa-coffee"></i> Tea Break</div></td>
        </tr>
        
        <tr>
          <td class="time-slot">11:00 AM<br>12:30 PM</td>
          <td><div class="empty-slot">Free Period</div></td>
          <td>
            <div class="class-slot">
              <div class="class-name">Database Management</div>
              <div class="class-room">Room 203 / Lab 2</div>
            </div>
          </td>
          <td><div class="empty-slot">Free Period</div></td>
          <td>
            <div class="class-slot">
              <div class="class-name">Database Management</div>
              <div class="class-room">Room 203 / Lab 2</div>
            </div>
          </td>
          <td><div class="empty-slot">Free Period</div></td>
        </tr>
        
        <tr>
          <td class="time-slot">12:30 PM<br>1:30 PM</td>
          <td colspan="5"><div class="break-slot"><i class="fa fa-utensils"></i> Lunch Break</div></td>
        </tr>
        
        <tr>
          <td class="time-slot">1:30 PM<br>3:00 PM</td>
          <td>
            <div class="class-slot">
              <div class="class-name">Web Development</div>
              <div class="class-room">Lab 3</div>
            </div>
          </td>
          <td><div class="empty-slot">Free Period</div></td>
          <td>
            <div class="class-slot">
              <div class="class-name">Web Development</div>
              <div class="class-room">Lab 3</div>
            </div>
          </td>
          <td><div class="empty-slot">Free Period</div></td>
          <td><div class="empty-slot">Free Period</div></td>
        </tr>
        
        <tr>
          <td class="time-slot">3:00 PM<br>3:30 PM</td>
          <td colspan="5"><div class="break-slot"><i class="fa fa-coffee"></i> Tea Break</div></td>
        </tr>
        
        <tr>
          <td class="time-slot">3:30 PM<br>5:00 PM</td>
          <td><div class="empty-slot">Free Period</div></td>
          <td>
            <div class="class-slot">
              <div class="class-name">Data Structures</div>
              <div class="class-room">Room 102 / Lab 1</div>
            </div>
          </td>
          <td><div class="empty-slot">Free Period</div></td>
          <td>
            <div class="class-slot">
              <div class="class-name">Data Structures</div>
              <div class="class-room">Room 102 / Lab 1</div>
            </div>
          </td>
          <td><div class="empty-slot">Free Period</div></td>
        </tr>
      </tbody>
    </table>
  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Teacher Portal</p>
</footer>

</body>
</html>
