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
  <title>Teaching Schedule | SCTI Teacher Portal</title>
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
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow-x: auto;
    }
    .schedule-table {
      width: 100%; border-collapse: collapse; min-width: 800px;
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
    .time-cell {
      background: #f8f9fa; font-weight: 600;
      color: #28a745; white-space: nowrap;
    }
    .class-slot {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white; padding: 12px; border-radius: 6px;
      font-size: 13px; line-height: 1.5;
      transition: all 0.3s; cursor: pointer;
    }
    .class-slot:hover {
      transform: scale(1.05);
      box-shadow: 0 4px 12px rgba(40,167,69,0.3);
    }
    .class-name {
      font-weight: 600; margin-bottom: 5px;
    }
    .class-room {
      font-size: 11px; opacity: 0.9;
    }
    .empty-slot {
      color: #999; font-style: italic;
    }
    .break-slot {
      background: #ffc107; color: #333;
      padding: 12px; border-radius: 6px;
      font-weight: 600;
    }
    .legend {
      display: flex; gap: 20px; margin-top: 20px;
      flex-wrap: wrap; padding: 15px;
      background: #f8f9fa; border-radius: 6px;
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
  <marquee>Teaching Schedule - View your weekly class schedule and timings</marquee>
</div>

<div class="container">
  <div class="page-header">
    <h1><i class="fa fa-calendar-alt"></i> My Teaching Schedule</h1>
    <div class="breadcrumb">
      <a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Schedule
    </div>
  </div>

  <div class="schedule-container">
    <table class="schedule-table">
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
          <td class="time-cell">9:00 AM<br>10:30 AM</td>
          <td>
            <div class="class-slot">
              <div class="class-name">Programming Fundamentals</div>
              <div class="class-room">Room: 101 | B.Tech IT Sem 1</div>
            </div>
          </td>
          <td>
            <div class="class-slot">
              <div class="class-name">Programming Fundamentals</div>
              <div class="class-room">Room: 101 | B.Tech IT Sem 1</div>
            </div>
          </td>
          <td>
            <div class="empty-slot">Free Period</div>
          </td>
          <td>
            <div class="class-slot">
              <div class="class-name">Data Structures</div>
              <div class="class-room">Room: 203 | B.Tech IT Sem 2</div>
            </div>
          </td>
          <td>
            <div class="class-slot">
              <div class="class-name">Programming Fundamentals</div>
              <div class="class-room">Room: 101 | B.Tech IT Sem 1</div>
            </div>
          </td>
          <td>
            <div class="empty-slot">Free Period</div>
          </td>
        </tr>
        <tr>
          <td class="time-cell">10:30 AM<br>11:00 AM</td>
          <td colspan="6">
            <div class="break-slot">Tea Break</div>
          </td>
        </tr>
        <tr>
          <td class="time-cell">11:00 AM<br>12:30 PM</td>
          <td>
            <div class="class-slot">
              <div class="class-name">Database Management</div>
              <div class="class-room">Room: 205 | B.Tech IT Sem 3</div>
            </div>
          </td>
          <td>
            <div class="empty-slot">Free Period</div>
          </td>
          <td>
            <div class="class-slot">
              <div class="class-name">Database Management</div>
              <div class="class-room">Room: 205 | B.Tech IT Sem 3</div>
            </div>
          </td>
          <td>
            <div class="class-slot">
              <div class="class-name">Database Management</div>
              <div class="class-room">Room: 205 | B.Tech IT Sem 3</div>
            </div>
          </td>
          <td>
            <div class="empty-slot">Free Period</div>
          </td>
          <td>
            <div class="class-slot">
              <div class="class-name">Data Structures</div>
              <div class="class-room">Room: 203 | B.Tech IT Sem 2</div>
            </div>
          </td>
        </tr>
        <tr>
          <td class="time-cell">12:30 PM<br>1:30 PM</td>
          <td colspan="6">
            <div class="break-slot">Lunch Break</div>
          </td>
        </tr>
        <tr>
          <td class="time-cell">1:30 PM<br>3:00 PM</td>
          <td>
            <div class="class-slot">
              <div class="class-name">Web Development</div>
              <div class="class-room">Room: 102 | Diploma Civil Sem 2</div>
            </div>
          </td>
          <td>
            <div class="class-slot">
              <div class="class-name">Web Development</div>
              <div class="class-room">Room: 102 | Diploma Civil Sem 2</div>
            </div>
          </td>
          <td>
            <div class="class-slot">
              <div class="class-name">Web Development</div>
              <div class="class-room">Room: 102 | Diploma Civil Sem 2</div>
            </div>
          </td>
          <td>
            <div class="empty-slot">Free Period</div>
          </td>
          <td>
            <div class="class-slot">
              <div class="class-name">Data Structures</div>
              <div class="class-room">Room: 203 | B.Tech IT Sem 2</div>
            </div>
          </td>
          <td>
            <div class="empty-slot">Free Period</div>
          </td>
        </tr>
        <tr>
          <td class="time-cell">3:00 PM<br>3:30 PM</td>
          <td colspan="6">
            <div class="break-slot">Tea Break</div>
          </td>
        </tr>
        <tr>
          <td class="time-cell">3:30 PM<br>5:00 PM</td>
          <td>
            <div class="class-slot">
              <div class="class-name">Data Structures</div>
              <div class="class-room">Room: 203 | B.Tech IT Sem 2</div>
            </div>
          </td>
          <td>
            <div class="empty-slot">Free Period</div>
          </td>
          <td>
            <div class="empty-slot">Free Period</div>
          </td>
          <td>
            <div class="class-slot">
              <div class="class-name">Programming Fundamentals</div>
              <div class="class-room">Room: 101 | B.Tech IT Sem 1</div>
            </div>
          </td>
          <td>
            <div class="empty-slot">Free Period</div>
          </td>
          <td>
            <div class="empty-slot">Free Period</div>
          </td>
        </tr>
      </tbody>
    </table>

    <div class="legend">
      <div class="legend-item">
        <div class="legend-color" style="background: linear-gradient(135deg, #28a745, #20c997);"></div>
        <span>Class Period</span>
      </div>
      <div class="legend-item">
        <div class="legend-color" style="background: #ffc107;"></div>
        <span>Break Time</span>
      </div>
      <div class="legend-item">
        <div class="legend-color" style="background: white; border: 1px solid #dee2e6;"></div>
        <span>Free Period</span>
      </div>
    </div>
  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Teacher Portal</p>
</footer>

</body>
</html>
