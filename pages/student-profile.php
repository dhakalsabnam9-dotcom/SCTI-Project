<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header('Location: ../index.php');
    exit();
}
$fullName = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Student';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'student';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'student@scti.edu.np';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile | SCTI Student Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
    .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
    
    .page-header {
      background: linear-gradient(135deg, #004080 0%, #0059b3 100%);
      color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;
      box-shadow: 0 4px 15px rgba(0,64,128,0.2);
    }
    .page-header h1 { margin: 0 0 10px 0; font-size: 28px; }
    .breadcrumb { opacity: 1; font-size: 14px; background: transparent; padding: 0; }
    .breadcrumb a { color: white; text-decoration: none; }
    
    .profile-grid {
      display: grid; grid-template-columns: 1fr 2fr; gap: 25px;
    }
    
    .profile-sidebar {
      background: white; padding: 30px; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      text-align: center;
    }
    .profile-avatar {
      width: 150px; height: 150px; border-radius: 50%;
      background: linear-gradient(135deg, #004080, #0059b3);
      display: flex; align-items: center; justify-content: center;
      color: white; font-size: 60px; margin: 0 auto 20px;
      box-shadow: 0 4px 15px rgba(0,64,128,0.2);
    }
    .profile-name {
      font-size: 24px; font-weight: 600; color: #333;
      margin-bottom: 5px;
    }
    .profile-role {
      color: #666; margin-bottom: 20px;
    }
    .profile-stats {
      margin-top: 30px; text-align: left;
    }
    .stat-item {
      padding: 15px 0; border-bottom: 1px solid #eee;
      display: flex; justify-content: space-between;
    }
    .stat-item:last-child { border-bottom: none; }
    .stat-label { color: #666; }
    .stat-value { font-weight: 600; color: #004080; }
    
    .profile-main {
      background: white; padding: 30px; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .section-title {
      color: #004080; margin-bottom: 20px; padding-bottom: 10px;
      border-bottom: 2px solid #004080;
    }
    
    .info-grid {
      display: grid; grid-template-columns: repeat(2, 1fr);
      gap: 20px; margin-bottom: 30px;
    }
    .info-item {
      padding: 15px; background: #f8f9fa; border-radius: 8px;
    }
    .info-label {
      font-size: 12px; color: #666; margin-bottom: 5px;
      text-transform: uppercase; letter-spacing: 0.5px;
    }
    .info-value {
      font-size: 16px; color: #333; font-weight: 600;
    }
    
    .btn-edit {
      background: linear-gradient(135deg, #004080, #0059b3);
      color: white; padding: 12px 30px; border: none;
      border-radius: 6px; cursor: pointer; font-size: 16px;
      transition: all 0.3s; margin-top: 20px;
    }
    .btn-edit:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,64,128,0.3);
    }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>My Profile - View and manage your personal information</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <h1><i class="fa fa-user"></i> My Profile</h1>
    <div class="breadcrumb">
      <a href="../dashboards/student-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Profile
    </div>
  </div>

  <div class="profile-grid">
    
    <div class="profile-sidebar">
      <div class="profile-avatar">
        <i class="fa fa-user"></i>
      </div>
      <div class="profile-name"><?php echo htmlspecialchars($fullName); ?></div>
      <div class="profile-role">Student - B.Tech IT</div>
      
      <div class="profile-stats">
        <div class="stat-item">
          <span class="stat-label">Student ID</span>
          <span class="stat-value">STU20251234</span>
        </div>
        <div class="stat-item">
          <span class="stat-label">Semester</span>
          <span class="stat-value">3rd Semester</span>
        </div>
        <div class="stat-item">
          <span class="stat-label">Batch</span>
          <span class="stat-value">2024-2028</span>
        </div>
        <div class="stat-item">
          <span class="stat-label">Status</span>
          <span class="stat-value" style="color: #28a745;">Active</span>
        </div>
      </div>
    </div>

    <div class="profile-main">
      <h2 class="section-title"><i class="fa fa-info-circle"></i> Personal Information</h2>
      
      <div class="info-grid">
        <div class="info-item">
          <div class="info-label">Full Name</div>
          <div class="info-value"><?php echo htmlspecialchars($fullName); ?></div>
        </div>
        <div class="info-item">
          <div class="info-label">Username</div>
          <div class="info-value"><?php echo htmlspecialchars($username); ?></div>
        </div>
        <div class="info-item">
          <div class="info-label">Email Address</div>
          <div class="info-value"><?php echo htmlspecialchars($email); ?></div>
        </div>
        <div class="info-item">
          <div class="info-label">Phone Number</div>
          <div class="info-value">+977-9841234567</div>
        </div>
        <div class="info-item">
          <div class="info-label">Date of Birth</div>
          <div class="info-value">Jan 15, 2005</div>
        </div>
        <div class="info-item">
          <div class="info-label">Gender</div>
          <div class="info-value">Male</div>
        </div>
        <div class="info-item">
          <div class="info-label">Address</div>
          <div class="info-value">Kamalamai-5, Sindhuli</div>
        </div>
        <div class="info-item">
          <div class="info-label">Guardian Contact</div>
          <div class="info-value">+977-9841234568</div>
        </div>
      </div>

      <h2 class="section-title" style="margin-top: 30px;"><i class="fa fa-graduation-cap"></i> Academic Information</h2>
      
      <div class="info-grid">
        <div class="info-item">
          <div class="info-label">Program</div>
          <div class="info-value">B.Tech Ed in IT</div>
        </div>
        <div class="info-item">
          <div class="info-label">Enrollment Date</div>
          <div class="info-value">Nov 12, 2024</div>
        </div>
        <div class="info-item">
          <div class="info-label">Current GPA</div>
          <div class="info-value">3.6 / 4.0</div>
        </div>
        <div class="info-item">
          <div class="info-label">Credits Completed</div>
          <div class="info-value">45 / 120</div>
        </div>
      </div>

      <button class="btn-edit" onclick="alert('Edit profile coming soon!');">
        <i class="fa fa-edit"></i> Edit Profile
      </button>
    </div>

  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Student Portal</p>
</footer>

</body>
</html>
