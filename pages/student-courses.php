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
  <title>My Courses | SCTI Student Portal</title>
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
    .breadcrumb a:hover { text-decoration: underline; }
    
    .course-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px; }
    
    .course-card {
      background: white; border-radius: 12px; overflow: hidden;
      box-shadow: 0 3px 15px rgba(0,0,0,0.1);
      transition: all 0.3s;
    }
    .course-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 8px 30px rgba(0,64,128,0.2);
    }
    
    .course-header {
      background: linear-gradient(135deg, #004080, #0059b3);
      color: white; padding: 25px; position: relative;
    }
    .course-header h3 { margin: 0 0 10px 0; font-size: 20px; }
    .course-code { opacity: 0.9; font-size: 13px; }
    
    .course-body { padding: 25px; }
    
    .course-info-item {
      display: flex; align-items: center; gap: 10px;
      margin-bottom: 12px; color: #666;
    }
    .course-info-item i { color: #004080; width: 20px; }
    
    .progress-section { margin: 20px 0; }
    .progress-label {
      display: flex; justify-content: space-between;
      margin-bottom: 8px; font-size: 14px; color: #666;
    }
    .progress-bar-container {
      height: 10px; background: #e0e0e0; border-radius: 5px; overflow: hidden;
    }
    .progress-bar {
      height: 100%; background: linear-gradient(90deg, #28a745, #20c997);
      transition: width 0.5s ease;
    }
    
    .course-actions {
      display: flex; gap: 10px; margin-top: 20px;
    }
    .btn {
      flex: 1; padding: 12px; border: none; border-radius: 6px;
      cursor: pointer; font-size: 14px; transition: all 0.3s;
      text-decoration: none; text-align: center; display: inline-block;
    }
    .btn-primary {
      background: linear-gradient(135deg, #004080, #0059b3);
      color: white;
    }
    .btn-primary:hover {
      background: linear-gradient(135deg, #0059b3, #004080);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,64,128,0.3);
    }
    .btn-outline {
      background: white; color: #004080;
      border: 2px solid #004080;
    }
    .btn-outline:hover {
      background: #004080; color: white;
    }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>My Courses - Track your academic progress and access course materials</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <h1><i class="fa fa-book-open"></i> My Courses</h1>
    <div class="breadcrumb">
      <a href="../dashboards/student-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / My Courses
    </div>
  </div>

  <div class="course-grid">
    
    <div class="course-card">
      <div class="course-header">
        <h3>Programming Fundamentals</h3>
        <div class="course-code">CS101 - Semester 1</div>
      </div>
      <div class="course-body">
        <div class="course-info-item">
          <i class="fa fa-chalkboard-teacher"></i>
          <span>Instructor: Mr. Bibek Bhandari</span>
        </div>
        <div class="course-info-item">
          <i class="fa fa-users"></i>
          <span>35 Students Enrolled</span>
        </div>
        <div class="course-info-item">
          <i class="fa fa-clock"></i>
          <span>Mon, Wed, Fri - 9:00 AM</span>
        </div>
        
        <div class="progress-section">
          <div class="progress-label">
            <span>Course Progress</span>
            <strong style="color: #28a745;">75%</strong>
          </div>
          <div class="progress-bar-container">
            <div class="progress-bar" style="width: 75%;"></div>
          </div>
        </div>
        
        <div class="course-actions">
          <a href="#" class="btn btn-primary" onclick="alert('Course details coming soon!'); return false;">
            <i class="fa fa-eye"></i> View Details
          </a>
          <a href="#" class="btn btn-outline" onclick="alert('Materials coming soon!'); return false;">
            <i class="fa fa-download"></i> Materials
          </a>
        </div>
      </div>
    </div>

    <div class="course-card">
      <div class="course-header">
        <h3>Database Management</h3>
        <div class="course-code">CS201 - Semester 3</div>
      </div>
      <div class="course-body">
        <div class="course-info-item">
          <i class="fa fa-chalkboard-teacher"></i>
          <span>Instructor: Mr. Santosh Sapkota</span>
        </div>
        <div class="course-info-item">
          <i class="fa fa-users"></i>
          <span>28 Students Enrolled</span>
        </div>
        <div class="course-info-item">
          <i class="fa fa-clock"></i>
          <span>Tue, Thu - 11:00 AM</span>
        </div>
        
        <div class="progress-section">
          <div class="progress-label">
            <span>Course Progress</span>
            <strong style="color: #ffc107;">60%</strong>
          </div>
          <div class="progress-bar-container">
            <div class="progress-bar" style="width: 60%; background: linear-gradient(90deg, #ffc107, #fd7e14);"></div>
          </div>
        </div>
        
        <div class="course-actions">
          <a href="#" class="btn btn-primary" onclick="alert('Course details coming soon!'); return false;">
            <i class="fa fa-eye"></i> View Details
          </a>
          <a href="#" class="btn btn-outline" onclick="alert('Materials coming soon!'); return false;">
            <i class="fa fa-download"></i> Materials
          </a>
        </div>
      </div>
    </div>

    <div class="course-card">
      <div class="course-header">
        <h3>Web Development</h3>
        <div class="course-code">CS301 - Semester 2</div>
      </div>
      <div class="course-body">
        <div class="course-info-item">
          <i class="fa fa-chalkboard-teacher"></i>
          <span>Instructor: Mr. Tej Bikram Thapa</span>
        </div>
        <div class="course-info-item">
          <i class="fa fa-users"></i>
          <span>32 Students Enrolled</span>
        </div>
        <div class="course-info-item">
          <i class="fa fa-clock"></i>
          <span>Mon, Wed - 2:00 PM</span>
        </div>
        
        <div class="progress-section">
          <div class="progress-label">
            <span>Course Progress</span>
            <strong style="color: #28a745;">90%</strong>
          </div>
          <div class="progress-bar-container">
            <div class="progress-bar" style="width: 90%;"></div>
          </div>
        </div>
        
        <div class="course-actions">
          <a href="#" class="btn btn-primary" onclick="alert('Course details coming soon!'); return false;">
            <i class="fa fa-eye"></i> View Details
          </a>
          <a href="#" class="btn btn-outline" onclick="alert('Materials coming soon!'); return false;">
            <i class="fa fa-download"></i> Materials
          </a>
        </div>
      </div>
    </div>

    <div class="course-card">
      <div class="course-header">
        <h3>Data Structures</h3>
        <div class="course-code">CS102 - Semester 2</div>
      </div>
      <div class="course-body">
        <div class="course-info-item">
          <i class="fa fa-chalkboard-teacher"></i>
          <span>Instructor: Mr. Bibek Bhandari</span>
        </div>
        <div class="course-info-item">
          <i class="fa fa-users"></i>
          <span>25 Students Enrolled</span>
        </div>
        <div class="course-info-item">
          <i class="fa fa-clock"></i>
          <span>Tue, Thu - 3:30 PM</span>
        </div>
        
        <div class="progress-section">
          <div class="progress-label">
            <span>Course Progress</span>
            <strong style="color: #dc3545;">45%</strong>
          </div>
          <div class="progress-bar-container">
            <div class="progress-bar" style="width: 45%; background: linear-gradient(90deg, #dc3545, #fd7e14);"></div>
          </div>
        </div>
        
        <div class="course-actions">
          <a href="#" class="btn btn-primary" onclick="alert('Course details coming soon!'); return false;">
            <i class="fa fa-eye"></i> View Details
          </a>
          <a href="#" class="btn btn-outline" onclick="alert('Materials coming soon!'); return false;">
            <i class="fa fa-download"></i> Materials
          </a>
        </div>
      </div>
    </div>

  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Student Portal</p>
</footer>

</body>
</html>
