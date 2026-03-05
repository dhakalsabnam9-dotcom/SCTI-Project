<?php
session_start();

// Check if user is logged in and is student
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header('Location: ../index.php');
    exit();
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Student';
$fullName = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Dashboard | SCTI</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
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
    
    .logout-btn {
      background: rgba(255,255,255,0.2); color: white; padding: 10px 20px;
      border-radius: 5px; text-decoration: none; transition: all 0.3s;
    }
    .logout-btn:hover { background: rgba(255,255,255,0.3); }
    
    .stats-grid {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px; margin-bottom: 30px;
    }
    
    .stat-card {
      background: white; padding: 25px; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      display: flex; align-items: center; gap: 20px;
      transition: transform 0.3s;
    }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 5px 20px rgba(0,0,0,0.15); }
    
    .stat-icon {
      width: 60px; height: 60px; border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 24px; color: white;
    }
    .stat-icon.blue { background: linear-gradient(135deg, #004080, #0059b3); }
    .stat-icon.green { background: linear-gradient(135deg, #28a745, #20c997); }
    .stat-icon.orange { background: linear-gradient(135deg, #fd7e14, #ffc107); }
    .stat-icon.purple { background: linear-gradient(135deg, #6f42c1, #e83e8c); }
    
    .stat-info h3 { margin: 0; font-size: 32px; color: #004080; }
    .stat-info p { margin: 5px 0 0 0; color: #666; font-size: 14px; }
    
    .content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 30px; }
    
    .card {
      background: white; padding: 30px; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .card h2 { margin-top: 0; color: #004080; margin-bottom: 20px; }
    
    .course-list { list-style: none; padding: 0; margin: 0; }
    .course-item {
      padding: 15px; border-bottom: 1px solid #eee;
      display: flex; justify-content: space-between; align-items: center;
    }
    .course-item:last-child { border-bottom: none; }
    .course-name { font-weight: 600; color: #333; }
    .course-progress {
      width: 100px; height: 8px; background: #e0e0e0;
      border-radius: 4px; overflow: hidden;
    }
    .course-progress-bar {
      height: 100%; background: linear-gradient(90deg, #28a745, #20c997);
      transition: width 0.3s;
    }
    
    .notice-list { list-style: none; padding: 0; margin: 0; }
    .notice-item {
      padding: 15px; border-left: 4px solid #004080;
      background: #f8f9fa; margin-bottom: 10px; border-radius: 4px;
    }
    .notice-title { font-weight: 600; color: #004080; margin-bottom: 5px; }
    .notice-date { font-size: 12px; color: #999; }
    
    .quick-links { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; }
    .quick-link {
      background: linear-gradient(135deg, #004080, #0059b3);
      color: white; padding: 20px; border-radius: 8px;
      text-decoration: none; text-align: center;
      transition: all 0.3s; display: flex;
      flex-direction: column; align-items: center; gap: 10px;
    }
    .quick-link:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,64,128,0.3); }
    .quick-link i { font-size: 24px; }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Welcome to SCTI Student Portal - Your gateway to academic excellence</marquee>
</div>

<div class="dashboard-container">
  
  <div class="dashboard-header">
    <div>
      <h1><i class="fa fa-graduation-cap"></i> Student Dashboard</h1>
      <p style="margin: 5px 0 0 0; opacity: 0.9;">Welcome back, <?php echo htmlspecialchars($fullName); ?>!</p>
    </div>
    <div class="user-info">
      <span><i class="fa fa-user-graduate"></i> Student</span>
      <a href="../includes/logout.php" class="logout-btn">
        <i class="fa fa-sign-out-alt"></i> Logout
      </a>
    </div>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon blue"><i class="fa fa-book"></i></div>
      <div class="stat-info">
        <h3>4</h3>
        <p>Enrolled Courses</p>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon green"><i class="fa fa-check-circle"></i></div>
      <div class="stat-info">
        <h3>85%</h3>
        <p>Attendance</p>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon orange"><i class="fa fa-star"></i></div>
      <div class="stat-info">
        <h3>3.6</h3>
        <p>GPA</p>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon purple"><i class="fa fa-tasks"></i></div>
      <div class="stat-info">
        <h3>3</h3>
        <p>Pending Assignments</p>
      </div>
    </div>
  </div>

  <div class="content-grid">
    <div class="card">
      <h2><i class="fa fa-book-open"></i> My Courses</h2>
      <ul class="course-list">
        <li class="course-item">
          <div>
            <div class="course-name">Programming Fundamentals</div>
            <small style="color: #666;">Instructor: Mr. Bibek Bhandari</small>
          </div>
          <div>
            <div class="course-progress">
              <div class="course-progress-bar" style="width: 75%;"></div>
            </div>
            <small style="color: #666;">75% Complete</small>
          </div>
        </li>
        <li class="course-item">
          <div>
            <div class="course-name">Database Management</div>
            <small style="color: #666;">Instructor: Mr. Santosh Sapkota</small>
          </div>
          <div>
            <div class="course-progress">
              <div class="course-progress-bar" style="width: 60%;"></div>
            </div>
            <small style="color: #666;">60% Complete</small>
          </div>
        </li>
        <li class="course-item">
          <div>
            <div class="course-name">Web Development</div>
            <small style="color: #666;">Instructor: Mr. Tej Bikram Thapa</small>
          </div>
          <div>
            <div class="course-progress">
              <div class="course-progress-bar" style="width: 90%;"></div>
            </div>
            <small style="color: #666;">90% Complete</small>
          </div>
        </li>
        <li class="course-item">
          <div>
            <div class="course-name">Data Structures</div>
            <small style="color: #666;">Instructor: Mr. Bibek Bhandari</small>
          </div>
          <div>
            <div class="course-progress">
              <div class="course-progress-bar" style="width: 45%;"></div>
            </div>
            <small style="color: #666;">45% Complete</small>
          </div>
        </li>
      </ul>
    </div>

    <div class="card">
      <h2><i class="fa fa-bullhorn"></i> Recent Notices</h2>
      <ul class="notice-list">
        <li class="notice-item">
          <div class="notice-title">Mid-term Exam Schedule</div>
          <div class="notice-date">2 days ago</div>
        </li>
        <li class="notice-item">
          <div class="notice-title">Sports Week Registration</div>
          <div class="notice-date">5 days ago</div>
        </li>
        <li class="notice-item">
          <div class="notice-title">Library Hours Extended</div>
          <div class="notice-date">1 week ago</div>
        </li>
      </ul>
    </div>
  </div>

  <div class="card">
    <h2><i class="fa fa-bolt"></i> Quick Links</h2>
    <div class="quick-links">
      <a href="#" class="quick-link" onclick="alert('Attendance coming soon!'); return false;">
        <i class="fa fa-calendar-check"></i>
        <span>Attendance</span>
      </a>
      <a href="#" class="quick-link" onclick="alert('Grades coming soon!'); return false;">
        <i class="fa fa-chart-line"></i>
        <span>Grades</span>
      </a>
      <a href="#" class="quick-link" onclick="alert('Assignments coming soon!'); return false;">
        <i class="fa fa-file-alt"></i>
        <span>Assignments</span>
      </a>
      <a href="#" class="quick-link" onclick="alert('Timetable coming soon!'); return false;">
        <i class="fa fa-clock"></i>
        <span>Timetable</span>
      </a>
      <a href="#" class="quick-link" onclick="alert('Library coming soon!'); return false;">
        <i class="fa fa-book"></i>
        <span>Library</span>
      </a>
      <a href="#" class="quick-link" onclick="alert('Profile coming soon!'); return false;">
        <i class="fa fa-user"></i>
        <span>Profile</span>
      </a>
    </div>
  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 Sindhuli Community Technical Institute (SCTI) - Student Portal</p>
</footer>

</body>
</html>
