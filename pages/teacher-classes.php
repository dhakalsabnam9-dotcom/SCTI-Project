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
  <title>My Classes | SCTI Teacher Portal</title>
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
    .breadcrumb { opacity: 1; font-size: 14px; background: transparent; padding: 0; }
    .breadcrumb a { color: white; text-decoration: none; }
    
    .class-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px; }
    
    .class-card {
      background: white; border-radius: 12px; overflow: hidden;
      box-shadow: 0 3px 15px rgba(0,0,0,0.1);
      transition: all 0.3s;
    }
    .class-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 8px 30px rgba(40,167,69,0.2);
    }
    
    .class-header {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white; padding: 25px; position: relative;
    }
    .class-header h3 { margin: 0 0 10px 0; font-size: 20px; }
    .class-code { opacity: 0.9; font-size: 13px; }
    
    .class-body { padding: 25px; }
    
    .class-info-item {
      display: flex; align-items: center; gap: 10px;
      margin-bottom: 12px; color: #666;
    }
    .class-info-item i { color: #28a745; width: 20px; }
    
    .class-actions {
      display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 20px;
    }
    .btn {
      padding: 12px; border: none; border-radius: 6px;
      cursor: pointer; font-size: 14px; transition: all 0.3s;
      text-decoration: none; text-align: center; display: inline-block;
    }
    .btn-primary {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white;
    }
    .btn-primary:hover {
      background: linear-gradient(135deg, #20c997, #28a745);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(40,167,69,0.3);
    }
    .btn-outline {
      background: white; color: #28a745;
      border: 2px solid #28a745;
    }
    .btn-outline:hover {
      background: #28a745; color: white;
    }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>My Classes - Manage your courses and track student progress</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <h1><i class="fa fa-book-open"></i> My Classes</h1>
    <div class="breadcrumb">
      <a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / My Classes
    </div>
  </div>

  <div class="class-grid">
    
    <div class="class-card">
      <div class="class-header">
        <h3>Programming Fundamentals</h3>
        <div class="class-code">CS101 - B.Tech IT Semester 1</div>
      </div>
      <div class="class-body">
        <div class="class-info-item">
          <i class="fa fa-users"></i>
          <span>35 Students Enrolled</span>
        </div>
        <div class="class-info-item">
          <i class="fa fa-clock"></i>
          <span>Mon, Wed, Fri - 9:00 AM</span>
        </div>
        <div class="class-info-item">
          <i class="fa fa-door-open"></i>
          <span>Room 101 / Lab 1</span>
        </div>
        <div class="class-info-item">
          <i class="fa fa-chart-line"></i>
          <span>Average Grade: B+</span>
        </div>
        
        <div class="class-actions">
          <a href="../pages/teacher-students.php" class="btn btn-primary">
            <i class="fa fa-users"></i> Students
          </a>
          <a href="../pages/teacher-attendance.php" class="btn btn-outline">
            <i class="fa fa-calendar-check"></i> Attendance
          </a>
          <a href="../pages/teacher-grades.php" class="btn btn-outline">
            <i class="fa fa-star"></i> Grades
          </a>
          <a href="../pages/teacher-materials.php" class="btn btn-outline">
            <i class="fa fa-book"></i> Materials
          </a>
        </div>
      </div>
    </div>

    <div class="class-card">
      <div class="class-header">
        <h3>Database Management</h3>
        <div class="class-code">CS201 - B.Tech IT Semester 3</div>
      </div>
      <div class="class-body">
        <div class="class-info-item">
          <i class="fa fa-users"></i>
          <span>28 Students Enrolled</span>
        </div>
        <div class="class-info-item">
          <i class="fa fa-clock"></i>
          <span>Tue, Thu - 11:00 AM</span>
        </div>
        <div class="class-info-item">
          <i class="fa fa-door-open"></i>
          <span>Room 203 / Lab 2</span>
        </div>
        <div class="class-info-item">
          <i class="fa fa-chart-line"></i>
          <span>Average Grade: A-</span>
        </div>
        
        <div class="class-actions">
          <a href="../pages/teacher-students.php" class="btn btn-primary">
            <i class="fa fa-users"></i> Students
          </a>
          <a href="../pages/teacher-attendance.php" class="btn btn-outline">
            <i class="fa fa-calendar-check"></i> Attendance
          </a>
          <a href="../pages/teacher-grades.php" class="btn btn-outline">
            <i class="fa fa-star"></i> Grades
          </a>
          <a href="../pages/teacher-materials.php" class="btn btn-outline">
            <i class="fa fa-book"></i> Materials
          </a>
        </div>
      </div>
    </div>

    <div class="class-card">
      <div class="class-header">
        <h3>Web Development</h3>
        <div class="class-code">CS301 - Diploma Civil Semester 2</div>
      </div>
      <div class="class-body">
        <div class="class-info-item">
          <i class="fa fa-users"></i>
          <span>32 Students Enrolled</span>
        </div>
        <div class="class-info-item">
          <i class="fa fa-clock"></i>
          <span>Mon, Wed - 2:00 PM</span>
        </div>
        <div class="class-info-item">
          <i class="fa fa-door-open"></i>
          <span>Lab 3</span>
        </div>
        <div class="class-info-item">
          <i class="fa fa-chart-line"></i>
          <span>Average Grade: A</span>
        </div>
        
        <div class="class-actions">
          <a href="../pages/teacher-students.php" class="btn btn-primary">
            <i class="fa fa-users"></i> Students
          </a>
          <a href="../pages/teacher-attendance.php" class="btn btn-outline">
            <i class="fa fa-calendar-check"></i> Attendance
          </a>
          <a href="../pages/teacher-grades.php" class="btn btn-outline">
            <i class="fa fa-star"></i> Grades
          </a>
          <a href="../pages/teacher-materials.php" class="btn btn-outline">
            <i class="fa fa-book"></i> Materials
          </a>
        </div>
      </div>
    </div>

    <div class="class-card">
      <div class="class-header">
        <h3>Data Structures</h3>
        <div class="class-code">CS102 - B.Tech IT Semester 2</div>
      </div>
      <div class="class-body">
        <div class="class-info-item">
          <i class="fa fa-users"></i>
          <span>25 Students Enrolled</span>
        </div>
        <div class="class-info-item">
          <i class="fa fa-clock"></i>
          <span>Tue, Thu - 3:30 PM</span>
        </div>
        <div class="class-info-item">
          <i class="fa fa-door-open"></i>
          <span>Room 102 / Lab 1</span>
        </div>
        <div class="class-info-item">
          <i class="fa fa-chart-line"></i>
          <span>Average Grade: B</span>
        </div>
        
        <div class="class-actions">
          <a href="../pages/teacher-students.php" class="btn btn-primary">
            <i class="fa fa-users"></i> Students
          </a>
          <a href="../pages/teacher-attendance.php" class="btn btn-outline">
            <i class="fa fa-calendar-check"></i> Attendance
          </a>
          <a href="../pages/teacher-grades.php" class="btn btn-outline">
            <i class="fa fa-star"></i> Grades
          </a>
          <a href="../pages/teacher-materials.php" class="btn btn-outline">
            <i class="fa fa-book"></i> Materials
          </a>
        </div>
      </div>
    </div>

  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Teacher Portal</p>
</footer>

</body>
</html>
