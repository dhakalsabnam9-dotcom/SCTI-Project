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
  <title>Grades & GPA | SCTI Student Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
    .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
    
    .page-header {
      background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
      color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;
      box-shadow: 0 4px 15px rgba(255,193,7,0.2);
    }
    .page-header h1 { margin: 0 0 10px 0; font-size: 28px; }
    .breadcrumb { opacity: 1; font-size: 14px; background: transparent; padding: 0; }
    .breadcrumb a { color: white; text-decoration: none; }
    
    .gpa-card {
      background: white; padding: 40px; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px;
      text-align: center;
    }
    .gpa-display {
      font-size: 72px; font-weight: bold;
      background: linear-gradient(135deg, #ffc107, #fd7e14);
      -webkit-background-clip: text; -webkit-text-fill-color: transparent;
      margin: 20px 0;
    }
    
    .semester-tabs {
      display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;
    }
    .tab {
      padding: 12px 24px; border: 2px solid #004080; border-radius: 6px;
      background: white; color: #004080; cursor: pointer;
      transition: all 0.3s; font-weight: 600;
    }
    .tab:hover { background: #f0f0f0; }
    .tab.active { background: #004080; color: white; }
    
    .grades-table {
      width: 100%; border-collapse: collapse; background: white;
      border-radius: 10px; overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .grades-table th {
      background: linear-gradient(135deg, #004080, #0059b3);
      color: white; padding: 15px; text-align: left; font-weight: 600;
    }
    .grades-table td {
      padding: 15px; border-bottom: 1px solid #dee2e6;
    }
    .grades-table tr:hover {
      background: #f8f9fa;
    }
    
    .grade-badge {
      padding: 8px 16px; border-radius: 6px; font-weight: bold;
      display: inline-block; font-size: 16px;
    }
    .grade-a { background: #d4edda; color: #155724; }
    .grade-b { background: #d1ecf1; color: #0c5460; }
    .grade-c { background: #fff3cd; color: #856404; }
    .grade-d { background: #f8d7da; color: #721c24; }
    
    .chart-container {
      background: white; padding: 30px; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-top: 30px;
    }
    .chart-container h2 { color: #004080; margin-bottom: 20px; }
    
    .bar-chart {
      display: flex; align-items: flex-end; gap: 20px;
      height: 250px; padding: 20px 0;
    }
    .bar {
      flex: 1; background: linear-gradient(to top, #004080, #0059b3);
      border-radius: 8px 8px 0 0; position: relative;
      transition: all 0.3s;
    }
    .bar:hover {
      background: linear-gradient(to top, #0059b3, #004080);
      transform: scaleY(1.05);
    }
    .bar-label {
      position: absolute; bottom: -30px; left: 50%;
      transform: translateX(-50%); font-size: 12px;
      color: #666; white-space: nowrap;
    }
    .bar-value {
      position: absolute; top: -25px; left: 50%;
      transform: translateX(-50%); font-weight: bold;
      color: #004080;
    }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Academic Performance - Track your grades and maintain excellence</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <h1><i class="fa fa-chart-line"></i> Grades & GPA</h1>
    <div class="breadcrumb">
      <a href="../dashboards/student-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Grades
    </div>
  </div>

  <div class="gpa-card">
    <h2 style="color: #666; margin: 0;">Current GPA</h2>
    <div class="gpa-display">3.6</div>
    <p style="color: #666; font-size: 16px;">Out of 4.0 - Excellent Performance!</p>
  </div>

  <div class="semester-tabs">
    <button class="tab active">Semester 1</button>
    <button class="tab">Semester 2</button>
    <button class="tab">Semester 3</button>
    <button class="tab">All Semesters</button>
  </div>

  <table class="grades-table">
    <thead>
      <tr>
        <th>Course Code</th>
        <th>Course Name</th>
        <th>Credits</th>
        <th>Internal (40%)</th>
        <th>External (60%)</th>
        <th>Total</th>
        <th>Grade</th>
        <th>Grade Point</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>CS101</td>
        <td>Programming Fundamentals</td>
        <td>4</td>
        <td>35/40</td>
        <td>52/60</td>
        <td>87/100</td>
        <td><span class="grade-badge grade-a">A</span></td>
        <td>4.0</td>
      </tr>
      <tr>
        <td>CS201</td>
        <td>Database Management</td>
        <td>3</td>
        <td>32/40</td>
        <td>48/60</td>
        <td>80/100</td>
        <td><span class="grade-badge grade-a">A-</span></td>
        <td>3.7</td>
      </tr>
      <tr>
        <td>CS301</td>
        <td>Web Development</td>
        <td>4</td>
        <td>38/40</td>
        <td>55/60</td>
        <td>93/100</td>
        <td><span class="grade-badge grade-a">A+</span></td>
        <td>4.0</td>
      </tr>
      <tr>
        <td>CS102</td>
        <td>Data Structures</td>
        <td>3</td>
        <td>28/40</td>
        <td>42/60</td>
        <td>70/100</td>
        <td><span class="grade-badge grade-b">B+</span></td>
        <td>3.3</td>
      </tr>
      <tr>
        <td>MATH201</td>
        <td>Discrete Mathematics</td>
        <td>3</td>
        <td>30/40</td>
        <td>45/60</td>
        <td>75/100</td>
        <td><span class="grade-badge grade-b">B+</span></td>
        <td>3.3</td>
      </tr>
    </tbody>
  </table>

  <div class="chart-container">
    <h2><i class="fa fa-chart-bar"></i> Grade Distribution</h2>
    <div class="bar-chart">
      <div class="bar" style="height: 90%;">
        <span class="bar-value">87</span>
        <span class="bar-label">Programming</span>
      </div>
      <div class="bar" style="height: 80%;">
        <span class="bar-value">80</span>
        <span class="bar-label">Database</span>
      </div>
      <div class="bar" style="height: 95%;">
        <span class="bar-value">93</span>
        <span class="bar-label">Web Dev</span>
      </div>
      <div class="bar" style="height: 70%;">
        <span class="bar-value">70</span>
        <span class="bar-label">Data Struct</span>
      </div>
      <div class="bar" style="height: 75%;">
        <span class="bar-value">75</span>
        <span class="bar-label">Math</span>
      </div>
    </div>
  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Student Portal</p>
</footer>

</body>
</html>
