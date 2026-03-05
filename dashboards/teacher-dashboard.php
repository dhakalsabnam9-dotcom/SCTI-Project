<?php
session_start();

// Check if user is logged in and is teacher
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../index.php');
    exit();
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Teacher';
$fullName = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Teacher';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Teacher Dashboard | SCTI</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
    
    .dashboard-container { max-width: 1400px; margin: 0 auto; padding: 20px; }
    
    .dashboard-header {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;
      display: flex; justify-content: space-between; align-items: center;
      box-shadow: 0 4px 15px rgba(40,167,69,0.2);
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
    .stat-icon.green { background: linear-gradient(135deg, #28a745, #20c997); }
    .stat-icon.blue { background: linear-gradient(135deg, #004080, #0059b3); }
    .stat-icon.orange { background: linear-gradient(135deg, #fd7e14, #ffc107); }
    .stat-icon.purple { background: linear-gradient(135deg, #6f42c1, #e83e8c); }
    
    .stat-info h3 { margin: 0; font-size: 32px; color: #28a745; }
    .stat-info p { margin: 5px 0 0 0; color: #666; font-size: 14px; }
    
    .content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 30px; }
    
    .card {
      background: white; padding: 30px; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .card h2 { margin-top: 0; color: #28a745; margin-bottom: 20px; }
    
    .class-list { list-style: none; padding: 0; margin: 0; }
    .class-item {
      padding: 15px; border-bottom: 1px solid #eee;
      display: flex; justify-content: space-between; align-items: center;
    }
    .class-item:last-child { border-bottom: none; }
    .class-name { font-weight: 600; color: #333; }
    .class-badge {
      background: #28a745; color: white; padding: 5px 10px;
      border-radius: 4px; font-size: 12px;
    }
    
    .schedule-list { list-style: none; padding: 0; margin: 0; }
    .schedule-item {
      padding: 15px; border-left: 4px solid #28a745;
      background: #f8f9fa; margin-bottom: 10px; border-radius: 4px;
    }
    .schedule-time { font-weight: 600; color: #28a745; margin-bottom: 5px; }
    .schedule-class { font-size: 14px; color: #666; }
    
    .quick-links { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; }
    .quick-link {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white; padding: 20px; border-radius: 8px;
      text-decoration: none; text-align: center;
      transition: all 0.3s; display: flex;
      flex-direction: column; align-items: center; gap: 10px;
    }
    .quick-link:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(40,167,69,0.3); }
    .quick-link i { font-size: 24px; }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Welcome to SCTI Teacher Portal - Empowering educators, inspiring students</marquee>
</div>

<div class="dashboard-container">
  
  <div class="dashboard-header">
    <div>
      <h1><i class="fa fa-chalkboard-teacher"></i> Teacher Dashboard</h1>
      <p style="margin: 5px 0 0 0; opacity: 0.9;">Welcome back, <?php echo htmlspecialchars($fullName); ?>!</p>
    </div>
    <div class="user-info">
      <span><i class="fa fa-chalkboard-teacher"></i> Teacher</span>
      <a href="../includes/logout.php" class="logout-btn">
        <i class="fa fa-sign-out-alt"></i> Logout
      </a>
    </div>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon green"><i class="fa fa-users"></i></div>
      <div class="stat-info">
        <h3>120</h3>
        <p>Total Students</p>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon blue"><i class="fa fa-book"></i></div>
      <div class="stat-info">
        <h3>5</h3>
        <p>Classes Teaching</p>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon orange"><i class="fa fa-file-alt"></i></div>
      <div class="stat-info">
        <h3>12</h3>
        <p>Pending Assignments</p>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon purple"><i class="fa fa-calendar"></i></div>
      <div class="stat-info">
        <h3>4</h3>
        <p>Classes Today</p>
      </div>
    </div>
  </div>

  <div class="content-grid">
    <div class="card">
      <h2><i class="fa fa-book-open"></i> My Classes</h2>
      <ul class="class-list">
        <li class="class-item">
          <div>
            <div class="class-name">Programming Fundamentals</div>
            <small style="color: #666;">B.Tech IT - Semester 1</small>
          </div>
          <span class="class-badge">35 Students</span>
        </li>
        <li class="class-item">
          <div>
            <div class="class-name">Database Management</div>
            <small style="color: #666;">B.Tech IT - Semester 3</small>
          </div>
          <span class="class-badge">28 Students</span>
        </li>
        <li class="class-item">
          <div>
            <div class="class-name">Web Development</div>
            <small style="color: #666;">Diploma Civil - Semester 2</small>
          </div>
          <span class="class-badge">32 Students</span>
        </li>
        <li class="class-item">
          <div>
            <div class="class-name">Data Structures</div>
            <small style="color: #666;">B.Tech IT - Semester 2</small>
          </div>
          <span class="class-badge">25 Students</span>
        </li>
      </ul>
    </div>

    <div class="card">
      <h2><i class="fa fa-clock"></i> Today's Schedule</h2>
      <ul class="schedule-list">
        <li class="schedule-item">
          <div class="schedule-time">9:00 AM - 10:30 AM</div>
          <div class="schedule-class">Programming Fundamentals</div>
        </li>
        <li class="schedule-item">
          <div class="schedule-time">11:00 AM - 12:30 PM</div>
          <div class="schedule-class">Database Management</div>
        </li>
        <li class="schedule-item">
          <div class="schedule-time">1:30 PM - 3:00 PM</div>
          <div class="schedule-class">Web Development</div>
        </li>
        <li class="schedule-item">
          <div class="schedule-time">3:30 PM - 5:00 PM</div>
          <div class="schedule-class">Data Structures</div>
        </li>
      </ul>
    </div>
  </div>

  <div class="card">
    <h2><i class="fa fa-bolt"></i> Quick Actions</h2>
    <div class="quick-links">
      <a href="#" class="quick-link" onclick="alert('Attendance coming soon!'); return false;">
        <i class="fa fa-calendar-check"></i>
        <span>Mark Attendance</span>
      </a>
      <a href="#" class="quick-link" onclick="alert('Grades coming soon!'); return false;">
        <i class="fa fa-chart-line"></i>
        <span>Enter Grades</span>
      </a>
      <a href="#" class="quick-link" onclick="alert('Assignments coming soon!'); return false;">
        <i class="fa fa-file-alt"></i>
        <span>Assignments</span>
      </a>
      <a href="#" class="quick-link" onclick="alert('Students coming soon!'); return false;">
        <i class="fa fa-users"></i>
        <span>View Students</span>
      </a>
      <a href="#" class="quick-link" onclick="alert('Materials coming soon!'); return false;">
        <i class="fa fa-book"></i>
        <span>Course Materials</span>
      </a>
      <a href="#" class="quick-link" onclick="alert('Profile coming soon!'); return false;">
        <i class="fa fa-user"></i>
        <span>Profile</span>
      </a>
    </div>
  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 Sindhuli Community Technical Institute (SCTI) - Teacher Portal</p>
</footer>

</body>
</html>
