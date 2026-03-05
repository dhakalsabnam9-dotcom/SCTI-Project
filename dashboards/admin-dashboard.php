<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard | SCTI</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .dashboard-container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 20px;
    }
    
    .dashboard-header {
      background: linear-gradient(135deg, #004080 0%, #0059b3 100%);
      color: white;
      padding: 30px;
      border-radius: 10px;
      margin-bottom: 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .dashboard-header h1 {
      margin: 0;
      font-size: 28px;
    }
    
    .user-info {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    
    .logout-btn {
      background: rgba(255,255,255,0.2);
      color: white;
      padding: 10px 20px;
      border-radius: 5px;
      text-decoration: none;
      transition: all 0.3s;
    }
    
    .logout-btn:hover {
      background: rgba(255,255,255,0.3);
    }
    
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    
    .stat-card {
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      display: flex;
      align-items: center;
      gap: 20px;
      transition: transform 0.3s;
    }
    
    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    
    .stat-icon {
      width: 60px;
      height: 60px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      color: white;
    }
    
    .stat-icon.blue { background: linear-gradient(135deg, #004080, #0059b3); }
    .stat-icon.green { background: linear-gradient(135deg, #28a745, #20c997); }
    .stat-icon.orange { background: linear-gradient(135deg, #fd7e14, #ffc107); }
    .stat-icon.purple { background: linear-gradient(135deg, #6f42c1, #e83e8c); }
    
    .stat-info h3 {
      margin: 0;
      font-size: 32px;
      color: #004080;
    }
    
    .stat-info p {
      margin: 5px 0 0 0;
      color: #666;
      font-size: 14px;
    }
    
    .quick-actions {
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }
    
    .quick-actions h2 {
      margin-top: 0;
      color: #004080;
      margin-bottom: 20px;
    }
    
    .action-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
    }
    
    .action-btn {
      background: linear-gradient(135deg, #004080, #0059b3);
      color: white;
      padding: 20px;
      border-radius: 8px;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 15px;
      transition: all 0.3s;
      border: none;
      cursor: pointer;
      font-size: 16px;
    }
    
    .action-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(0,64,128,0.3);
    }
    
    .action-btn i {
      font-size: 24px;
    }
    
    .recent-activity {
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .recent-activity h2 {
      margin-top: 0;
      color: #004080;
      margin-bottom: 20px;
    }
    
    .activity-list {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    
    .activity-item {
      padding: 15px;
      border-bottom: 1px solid #eee;
      display: flex;
      align-items: center;
      gap: 15px;
    }
    
    .activity-item:last-child {
      border-bottom: none;
    }
    
    .activity-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #f0f0f0;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #004080;
    }
    
    .activity-content {
      flex: 1;
    }
    
    .activity-content p {
      margin: 0;
      color: #333;
    }
    
    .activity-time {
      color: #999;
      font-size: 12px;
    }
  </style>
</head>
<body>

<!-- Top Header -->
<div class="top-header">
  <marquee>Welcome to SCTI Admin Dashboard - Manage your institution efficiently</marquee>
</div>

<!-- Main Dashboard -->
<div class="dashboard-container">
  
  <!-- Dashboard Header -->
  <div class="dashboard-header">
    <div>
      <h1><i class="fa fa-tachometer-alt"></i> Admin Dashboard</h1>
      <p style="margin: 5px 0 0 0; opacity: 0.9;">Welcome back, <?php echo htmlspecialchars($username); ?>!</p>
    </div>
    <div class="user-info">
      <span><i class="fa fa-user-shield"></i> Administrator</span>
      <a href="../includes/logout.php" class="logout-btn">
        <i class="fa fa-sign-out-alt"></i> Logout
      </a>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon blue">
        <i class="fa fa-users"></i>
      </div>
      <div class="stat-info">
        <h3>245</h3>
        <p>Total Students</p>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon green">
        <i class="fa fa-chalkboard-teacher"></i>
      </div>
      <div class="stat-info">
        <h3>18</h3>
        <p>Total Teachers</p>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon orange">
        <i class="fa fa-book"></i>
      </div>
      <div class="stat-info">
        <h3>4</h3>
        <p>Active Programs</p>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon purple">
        <i class="fa fa-bullhorn"></i>
      </div>
      <div class="stat-info">
        <h3>12</h3>
        <p>Active Notices</p>
      </div>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="quick-actions">
    <h2><i class="fa fa-bolt"></i> Quick Actions</h2>
    <div class="action-grid">
      <a href="../admin/Admin-Notice-Board.html" class="action-btn">
        <i class="fa fa-bullhorn"></i>
        <span>Manage Notices</span>
      </a>
      <a href="#" class="action-btn" onclick="alert('Student management coming soon!'); return false;">
        <i class="fa fa-user-graduate"></i>
        <span>Manage Students</span>
      </a>
      <a href="#" class="action-btn" onclick="alert('Teacher management coming soon!'); return false;">
        <i class="fa fa-chalkboard-teacher"></i>
        <span>Manage Teachers</span>
      </a>
      <a href="#" class="action-btn" onclick="alert('Program management coming soon!'); return false;">
        <i class="fa fa-graduation-cap"></i>
        <span>Manage Programs</span>
      </a>
      <a href="#" class="action-btn" onclick="alert('Reports coming soon!'); return false;">
        <i class="fa fa-chart-bar"></i>
        <span>View Reports</span>
      </a>
      <a href="#" class="action-btn" onclick="alert('Settings coming soon!'); return false;">
        <i class="fa fa-cog"></i>
        <span>Settings</span>
      </a>
    </div>
  </div>

  <!-- Recent Activity -->
  <div class="recent-activity">
    <h2><i class="fa fa-history"></i> Recent Activity</h2>
    <ul class="activity-list">
      <li class="activity-item">
        <div class="activity-icon">
          <i class="fa fa-user-plus"></i>
        </div>
        <div class="activity-content">
          <p><strong>New student registered:</strong> Ram Sharma</p>
          <span class="activity-time">2 hours ago</span>
        </div>
      </li>
      <li class="activity-item">
        <div class="activity-icon">
          <i class="fa fa-bullhorn"></i>
        </div>
        <div class="activity-content">
          <p><strong>Notice published:</strong> Admission Open 2025/26</p>
          <span class="activity-time">5 hours ago</span>
        </div>
      </li>
      <li class="activity-item">
        <div class="activity-icon">
          <i class="fa fa-edit"></i>
        </div>
        <div class="activity-content">
          <p><strong>Program updated:</strong> B.Tech Ed in IT</p>
          <span class="activity-time">1 day ago</span>
        </div>
      </li>
      <li class="activity-item">
        <div class="activity-icon">
          <i class="fa fa-user-check"></i>
        </div>
        <div class="activity-content">
          <p><strong>Teacher approved:</strong> Sita Poudel</p>
          <span class="activity-time">2 days ago</span>
        </div>
      </li>
      <li class="activity-item">
        <div class="activity-icon">
          <i class="fa fa-calendar"></i>
        </div>
        <div class="activity-content">
          <p><strong>Event scheduled:</strong> Sports Week Dec 20-25</p>
          <span class="activity-time">3 days ago</span>
        </div>
      </li>
    </ul>
  </div>

</div>

<!-- Footer -->
<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 Sindhuli Community Technical Institute (SCTI) - Admin Panel</p>
</footer>

</body>
</html>
