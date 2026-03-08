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
  <title>Timetable | SCTI Student Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
    .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
    
    .page-header {
      background: linear-gradient(135deg, #004080 0%, #0059b3 100%);
      color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;
      box-shadow: 0 4px 15px rgba(0,64,128,0.2);
    }
    .page-header h1 { margin: 0 0 10px 0; font-size: 28px; }
    .breadcrumb { opacity: 0.9; font-size: 14px; }
    .breadcrumb a { color: white; text-decoration: none; }
    
    .timetable-container {
      background: white; border-radius: 10px; padding: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow-x: auto;
    }
    
    .timetable {
      width: 100%; border-collapse: collapse; min-width: 800px;
    }
    .timetable th {
      background: linear-gradient(135deg, #004080, #0059b3);
      color: white; padding: 15px; text-align: center;
      font-weight: 600; border: 1px solid #0059b3;
    }
    .timetable td {
      padding: 15px; border: 1px solid #dee2e6;
      text-align: center; vertical-align: top;
    }
    .timetable .time-col {
      background: #f8f9fa; font-weight: 600;
      color: #004080;
    }
    
    .class-slot {
      background: linear-gradient(135deg, #e3f2fd, #bbdefb);
      padding: 12px; border-radius: 8px;
      border-left: 4px solid #004080;
      text-align: left; transition: all 0.3s;
      cursor: pointer;
    }
    .class-slot:hover {
      background: linear-gradient(135deg, #bbdefb, #90caf9);
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(0,64,128,0.2);
    }
    .class-name {
      font-weight: 600; color: #004080; margin-bottom: 5px;
      font-size: 14px;
    }
    .class-teacher {
      font-size: 12px; color: #666;
      display: flex; align-items: center; gap: 5px;
    }
    .class-room {
      font-size: 12px; color: #666; margin-top: 5px;
      display: flex; align-items: center; gap: 5px;
    }
    
    .empty-slot {
      color: #ccc; font-style: italic;
    }
    
    .legend {
      display: flex; gap: 20px; margin-top: 20px;
      flex-wrap: wrap; justify-content: center;
    }
    .legend-item {
      display: flex; align-items: center; gap: 8px;
    }
    .legend-color {
      width: 20px; height: 20px; border-radius: 4px;
    }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Class Timetable - Plan your week and never miss a class</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <h1><i class="fa fa-calendar-alt"></i> My Timetable</h1>
    <div class="breadcrumb">
      <a href="../dashboards/student-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Timetable
    </div>
  </div>

  <div class="timetable-container">
    <table class="timetable">
      <thead>
        <tr>
          <th>Time</th>
          <th>Sunday</th>
          <th>Monday</th>
          <th>Tuesday</th>
          <th>Wednesday</th>
          <th>Thursday</th>
          <th>Friday</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="time-col">9:00 AM<br>10:30 AM</td>
          <td><span class="empty-slot">No Class</span></td>
          <td>
            <div class="class-slot">
              <div class="class-name">Programming Fundamentals</div>
              <div class="class-teacher"><i class="fa fa-user"></i> Mr. Bibek Bhandari</div>
              <div class="class-room"><i class="fa fa-door-open"></i> Room 101</div>
            </div>
          </td>
          <td><span class="empty-slot">No Class</span></td>
          <td>
            <div class="class-slot">
              <div class="class-name">Programming Fundamentals</div>
              <div class="class-teacher"><i class="fa fa-user"></i> Mr. Bibek Bhandari</div>
              <div class="class-room"><i class="fa fa-door-open"></i> Room 101</div>
            </div>
          </td>
          <td><span class="empty-slot">No Class</span></td>
          <td>
            <div class="class-slot">
              <div class="class-name">Programming Fundamentals</div>
              <div class="class-teacher"><i class="fa fa-user"></i> Mr. Bibek Bhandari</div>
              <div class="class-room"><i class="fa fa-door-open"></i> Lab 1</div>
            </div>
          </td>
        </tr>
        <tr>
          <td class="time-col">11:00 AM<br>12:30 PM</td>
          <td><span class="empty-slot">No Class</span></td>
          <td><span class="empty-slot">No Class</span></td>
          <td>
            <div class="class-slot">
              <div class="class-name">Database Management</div>
              <div class="class-teacher"><i class="fa fa-user"></i> Mr. Santosh Sapkota</div>
              <div class="class-room"><i class="fa fa-door-open"></i> Room 203</div>
            </div>
          </td>
          <td><span class="empty-slot">No Class</span></td>
          <td>
            <div class="class-slot">
              <div class="class-name">Database Management</div>
              <div class="class-teacher"><i class="fa fa-user"></i> Mr. Santosh Sapkota</div>
              <div class="class-room"><i class="fa fa-door-open"></i> Lab 2</div>
            </div>
          </td>
          <td><span class="empty-slot">No Class</span></td>
        </tr>
        <tr>
          <td class="time-col">1:30 PM<br>3:00 PM</td>
          <td><span class="empty-slot">No Class</span></td>
          <td>
            <div class="class-slot">
              <div class="class-name">Web Development</div>
              <div class="class-teacher"><i class="fa fa-user"></i> Mr. Tej Bikram Thapa</div>
              <div class="class-room"><i class="fa fa-door-open"></i> Lab 3</div>
            </div>
          </td>
          <td><span class="empty-slot">No Class</span></td>
          <td>
            <div class="class-slot">
              <div class="class-name">Web Development</div>
              <div class="class-teacher"><i class="fa fa-user"></i> Mr. Tej Bikram Thapa</div>
              <div class="class-room"><i class="fa fa-door-open"></i> Lab 3</div>
            </div>
          </td>
          <td><span class="empty-slot">No Class</span></td>
          <td><span class="empty-slot">No Class</span></td>
        </tr>
        <tr>
          <td class="time-col">3:30 PM<br>5:00 PM</td>
          <td><span class="empty-slot">No Class</span></td>
          <td><span class="empty-slot">No Class</span></td>
          <td>
            <div class="class-slot">
              <div class="class-name">Data Structures</div>
              <div class="class-teacher"><i class="fa fa-user"></i> Mr. Bibek Bhandari</div>
              <div class="class-room"><i class="fa fa-door-open"></i> Room 102</div>
            </div>
          </td>
          <td><span class="empty-slot">No Class</span></td>
          <td>
            <div class="class-slot">
              <div class="class-name">Data Structures</div>
              <div class="class-teacher"><i class="fa fa-user"></i> Mr. Bibek Bhandari</div>
              <div class="class-room"><i class="fa fa-door-open"></i> Lab 1</div>
            </div>
          </td>
          <td><span class="empty-slot">No Class</span></td>
        </tr>
      </tbody>
    </table>
    
    <div class="legend">
      <div class="legend-item">
        <div class="legend-color" style="background: linear-gradient(135deg, #e3f2fd, #bbdefb);"></div>
        <span>Regular Class</span>
      </div>
      <div class="legend-item">
        <div class="legend-color" style="background: #f8f9fa;"></div>
        <span>No Class</span>
      </div>
    </div>
  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Student Portal</p>
</footer>

</body>
</html>
