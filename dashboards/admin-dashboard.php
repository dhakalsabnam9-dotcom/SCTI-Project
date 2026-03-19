<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php'); exit();
}
require_once '../includes/config.php';

$username = $_SESSION['username'] ?? 'Admin';

// Real DB counts
try {
    $db = getDBConnection();
    $totalStudents  = $db->query("SELECT COUNT(*) FROM students")->fetchColumn();
    $totalTeachers  = $db->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
    $totalPrograms  = $db->query("SELECT COUNT(*) FROM programs WHERE status='active'")->fetchColumn();

    // Recent activity: last 5 students + last 5 teachers + last 5 notices combined, sorted by date
    $activities = [];

    $newStudents = $db->query("SELECT full_name, created_at, 'student' as type FROM students ORDER BY created_at DESC LIMIT 3")->fetchAll();
    foreach ($newStudents as $r) {
        $activities[] = ['icon'=>'fa-user-plus','color'=>'#004080','label'=>'New student registered','name'=>$r['full_name'],'time'=>$r['created_at'],'link'=>'../pages/manage-students.php'];
    }

    $newTeachers = $db->query("SELECT full_name, created_at, 'teacher' as type FROM teachers ORDER BY created_at DESC LIMIT 2")->fetchAll();
    foreach ($newTeachers as $r) {
        $activities[] = ['icon'=>'fa-chalkboard-teacher','color'=>'#28a745','label'=>'Teacher added','name'=>$r['full_name'],'time'=>$r['created_at'],'link'=>'../pages/manage-teachers.php'];
    }

    $newNotices = $db->query("SELECT title, created_at FROM notices ORDER BY created_at DESC LIMIT 2")->fetchAll();
    foreach ($newNotices as $r) {
        $activities[] = ['icon'=>'fa-bullhorn','color'=>'#fd7e14','label'=>'Notice published','name'=>$r['title'],'time'=>$r['created_at'],'link'=>'../pages/manage-notices.php'];
    }

    // Sort by time desc
    usort($activities, function($a,$b){ return strtotime($b['time']) - strtotime($a['time']); });
    $activities = array_slice($activities, 0, 6);

} catch(Exception $e) {
    $totalStudents = $totalTeachers = $totalPrograms = 0;
    $activities = [];
}

function timeAgo($datetime) {
    $diff = time() - strtotime($datetime);
    if ($diff < 60)     return 'Just now';
    if ($diff < 3600)   return floor($diff/60).' minutes ago';
    if ($diff < 86400)  return floor($diff/3600).' hours ago';
    if ($diff < 604800) return floor($diff/86400).' days ago';
    return date('M d, Y', strtotime($datetime));
}
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
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      display: flex;
      align-items: center;
      gap: 20px;
      transition: all 0.3s cubic-bezier(.25,.8,.25,1);
      cursor: pointer;
      position: relative;
      overflow: hidden;
      border: 2px solid transparent;
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
      transform: translateY(-6px);
      box-shadow: 0 12px 30px rgba(0,64,128,0.3);
      border-color: #004080;
    }
    .stat-card:active {
      transform: translateY(-2px) scale(0.98);
      box-shadow: 0 5px 15px rgba(0,64,128,0.4);
    }
    .stat-card .card-arrow {
      position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
      color: #ccc; font-size: 13px; transition: all .3s; opacity: 0;
    }
    .stat-card:hover .card-arrow { opacity: 1; color: #004080; right: 10px; }
    
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
      <a href="../pages/admin-profile.php" style="color:white;text-decoration:none;display:flex;align-items:center;gap:6px;background:rgba(255,255,255,0.15);padding:8px 16px;border-radius:5px;transition:.2s" onmouseover="this.style.background='rgba(255,255,255,0.28)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'"><i class="fa fa-user-shield"></i> Administrator</a>
      <a href="../includes/logout.php" class="logout-btn">
        <i class="fa fa-sign-out-alt"></i> Logout
      </a>
    </div>
  </div>

  <!-- Statistics Cards -->
  <!-- Updated: Gallery card added - Version 2.0 -->
  <div class="stats-grid">
    <!-- GALLERY CARD SHOULD APPEAR AS 6TH CARD -->
    <div class="stat-card" onclick="window.location.href='../pages/manage-students.php'">
      <div class="stat-icon blue"><i class="fa fa-users"></i></div>
      <div class="stat-info">
        <h3><?=intval($totalStudents)?></h3>
        <p>Total Students</p>
      </div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>

    <div class="stat-card" onclick="window.location.href='../pages/manage-teachers.php'">
      <div class="stat-icon green"><i class="fa fa-chalkboard-teacher"></i></div>
      <div class="stat-info">
        <h3><?=intval($totalTeachers)?></h3>
        <p>Total Teachers</p>
      </div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>

    <div class="stat-card" onclick="window.location.href='../pages/manage-programs.php'">
      <div class="stat-icon orange"><i class="fa fa-book"></i></div>
      <div class="stat-info">
        <h3><?=intval($totalPrograms)?></h3>
        <p>Active Programs</p>
      </div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>

    <div class="stat-card" style="cursor: pointer;" onclick="window.location.href='../pages/manage-notices.php'">
      <div class="stat-icon purple">
        <i class="fa fa-bullhorn"></i>
      </div>
      <div class="stat-info">
        <h3 id="noticeCount">0</h3>
        <p>Active Notices</p>
      </div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>
    
    <div class="stat-card" style="cursor: pointer;" onclick="window.location.href='../pages/view-contacts.php'">
      <div class="stat-icon red">
        <i class="fa fa-envelope"></i>
      </div>
      <div class="stat-info">
        <h3 id="contactCount">0</h3>
        <p>Contact Messages</p>
      </div>
      <i class="fa fa-chevron-right card-arrow"></i>
    </div>

    <div class="stat-card gallery-card" style="cursor: pointer;" onclick="window.location.href='../pages/manage-gallery.php'">
      <div class="stat-icon">
        <i class="fa fa-images"></i>
      </div>
      <div class="stat-info">
        <h3 id="galleryCount">0</h3>
        <p>Gallery Images</p>
      </div>
      <i class="fa fa-chevron-right card-arrow" style="color:rgba(255,255,255,.5)"></i>
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
      <?php if (empty($activities)): ?>
      <li class="activity-item">
        <div class="activity-icon"><i class="fa fa-info-circle"></i></div>
        <div class="activity-content"><p>No recent activity found.</p></div>
      </li>
      <?php else: ?>
      <?php foreach ($activities as $act): ?>
      <li class="activity-item" onclick="window.location.href='<?=htmlspecialchars($act['link'])?>'" style="cursor:pointer">
        <div class="activity-icon">
          <i class="fa <?=htmlspecialchars($act['icon'])?>" style="color:<?=htmlspecialchars($act['color'])?>"></i>
        </div>
        <div class="activity-content">
          <p><strong><?=htmlspecialchars($act['label'])?>:</strong> <?=htmlspecialchars($act['name'])?></p>
          <span class="activity-time"><?=timeAgo($act['time'])?></span>
        </div>
        <i class="fa fa-chevron-right" style="color:#ccc;font-size:12px"></i>
      </li>
      <?php endforeach; ?>
      <?php endif; ?>
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
