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
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
    
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
      box-shadow: 0 4px 15px rgba(0,64,128,0.2);
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
      transition: all 0.3s;
      cursor: pointer;
      position: relative;
      overflow: hidden;
    }
    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(0,64,128,0.1), transparent);
      transition: left 0.5s;
    }
    .stat-card:hover::before {
      left: 100%;
    }
    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,64,128,0.3);
      border: 2px solid #004080;
    }
    .stat-card:active {
      transform: translateY(-2px) scale(0.98);
      box-shadow: 0 4px 15px rgba(0,64,128,0.4);
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
    .stat-icon.cyan { background: linear-gradient(135deg, #17a2b8, #138496); }
    .stat-icon.red { background: linear-gradient(135deg, #dc3545, #c82333); }
    
    /* Special Gallery Card Styling */
    .stat-card.gallery-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
    }
    
    .stat-card.gallery-card::before {
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    }
    
    .stat-card.gallery-card:hover {
      transform: translateY(-8px) scale(1.02);
      box-shadow: 0 15px 40px rgba(102,126,234,0.4);
      border: 2px solid rgba(255,255,255,0.3);
    }
    
    .stat-card.gallery-card .stat-icon {
      background: rgba(255,255,255,0.2);
      backdrop-filter: blur(10px);
      border: 2px solid rgba(255,255,255,0.3);
      animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
    }
    
    .stat-card.gallery-card .stat-icon i {
      color: white;
      animation: pulse 2s ease-in-out infinite;
    }
    
    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.1); }
    }
    
    .stat-card.gallery-card .stat-info h3 {
      color: white;
      text-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }
    
    .stat-card.gallery-card .stat-info p {
      color: rgba(255,255,255,0.9);
    }
    
    .stat-card.gallery-card::after {
      content: '📸';
      position: absolute;
      right: 20px;
      bottom: 20px;
      font-size: 60px;
      opacity: 0.1;
      transform: rotate(-15deg);
    }
    
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
      position: relative;
      overflow: hidden;
    }
    .action-btn::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255,215,0,0.3);
      transform: translate(-50%, -50%);
      transition: width 0.6s, height 0.6s;
    }
    .action-btn:hover::before {
      width: 300px;
      height: 300px;
    }
    .action-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(0,64,128,0.3);
      background: linear-gradient(135deg, #0059b3, #004080);
    }
    .action-btn:active {
      transform: translateY(-1px) scale(0.95);
      box-shadow: 0 3px 10px rgba(0,64,128,0.4);
    }
    .action-btn i {
      font-size: 24px;
      position: relative;
      z-index: 1;
      transition: transform 0.3s;
    }
    .action-btn:hover i {
      transform: scale(1.2) rotate(5deg);
    }
    .action-btn span {
      position: relative;
      z-index: 1;
    }
    
    /* Special Gallery Action Button */
    .action-btn.gallery-action-btn {
      background: linear-gradient(135deg, #667eea, #764ba2);
    }
    .action-btn.gallery-action-btn:hover {
      background: linear-gradient(135deg, #764ba2, #667eea);
      box-shadow: 0 8px 25px rgba(102,126,234,0.4);
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
      transition: all 0.3s;
      border-radius: 8px;
      margin-bottom: 5px;
    }
    .activity-item:hover {
      background: #f8f9fa;
      transform: translateX(5px);
      border-bottom-color: #004080;
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
      transition: all 0.3s;
    }
    .activity-item:hover .activity-icon {
      background: linear-gradient(135deg, #004080, #0059b3);
      color: white;
      transform: scale(1.1);
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
  <!-- Updated: Gallery card added - Version 2.0 -->
  <div class="stats-grid">
    <!-- GALLERY CARD SHOULD APPEAR AS 6TH CARD -->
    <div class="stat-card" style="cursor: pointer;" onclick="window.location.href='../pages/manage-students.php'">
      <div class="stat-icon blue">
        <i class="fa fa-users"></i>
      </div>
      <div class="stat-info">
        <h3>245</h3>
        <p>Total Students</p>
      </div>
    </div>

    <div class="stat-card" style="cursor: pointer;" onclick="window.location.href='../pages/manage-teachers.php'">
      <div class="stat-icon green">
        <i class="fa fa-chalkboard-teacher"></i>
      </div>
      <div class="stat-info">
        <h3>18</h3>
        <p>Total Teachers</p>
      </div>
    </div>

    <div class="stat-card" style="cursor: pointer;" onclick="window.location.href='../pages/manage-programs.php'">
      <div class="stat-icon orange">
        <i class="fa fa-book"></i>
      </div>
      <div class="stat-info">
        <h3>4</h3>
        <p>Active Programs</p>
      </div>
    </div>

    <div class="stat-card" style="cursor: pointer;" onclick="window.location.href='../pages/manage-notices.php'">
      <div class="stat-icon purple">
        <i class="fa fa-bullhorn"></i>
      </div>
      <div class="stat-info">
        <h3 id="noticeCount">0</h3>
        <p>Active Notices</p>
      </div>
    </div>
    
    <div class="stat-card" style="cursor: pointer;" onclick="window.location.href='../pages/view-contacts.php'">
      <div class="stat-icon red">
        <i class="fa fa-envelope"></i>
      </div>
      <div class="stat-info">
        <h3 id="contactCount">0</h3>
        <p>Contact Messages</p>
      </div>
    </div>

    <div class="stat-card gallery-card" style="cursor: pointer;" onclick="window.location.href='../pages/manage-gallery.php'">
      <div class="stat-icon">
        <i class="fa fa-images"></i>
      </div>
      <div class="stat-info">
        <h3 id="galleryCount">0</h3>
        <p>Gallery Images</p>
      </div>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="quick-actions">
    <h2><i class="fa fa-bolt"></i> Quick Actions</h2>
    <div class="action-grid">
      <a href="../pages/view-contacts.php" class="action-btn" style="background: linear-gradient(135deg, #dc3545, #c82333);">
        <i class="fa fa-envelope"></i>
        <span>View Contact Messages</span>
      </a>
      <a href="../pages/manage-notices.php" class="action-btn">
        <i class="fa fa-bullhorn"></i>
        <span>Manage Notices</span>
      </a>
      <a href="../pages/manage-students.php" class="action-btn">
        <i class="fa fa-user-graduate"></i>
        <span>Manage Students</span>
      </a>
      <a href="../pages/manage-teachers.php" class="action-btn">
        <i class="fa fa-chalkboard-teacher"></i>
        <span>Manage Teachers</span>
      </a>
      <a href="../pages/manage-programs.php" class="action-btn">
        <i class="fa fa-graduation-cap"></i>
        <span>Manage Programs</span>
      </a>
      <a href="../pages/manage-gallery.php" class="action-btn gallery-action-btn">
        <i class="fa fa-images"></i>
        <span>Manage Gallery</span>
      </a>
      <a href="../pages/view-reports.php" class="action-btn">
        <i class="fa fa-chart-bar"></i>
        <span>View Reports</span>
      </a>
      <a href="../pages/settings.php" class="action-btn">
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

<script>
  // Fetch notice count
  fetch('../pages/get-notice-count.php')
    .then(r => r.json())
    .then(d => { if (d.success) document.getElementById('noticeCount').textContent = d.count; })
    .catch(() => {});

  // Fetch contact messages count
  fetch('../pages/get-contact-count.php')
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        document.getElementById('contactCount').textContent = data.count;
      }
    })
    .catch(error => {
      console.error('Error fetching contact count:', error);
    });

  // Fetch gallery images count
  fetch('../pages/get-gallery-count.php')
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        document.getElementById('galleryCount').textContent = data.count;
      }
    })
    .catch(error => {
      console.error('Error fetching gallery count:', error);
    });
</script>

</body>
</html>
