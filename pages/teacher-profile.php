<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../index.php');
    exit();
}
$fullName = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Teacher';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'teacher';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile | SCTI Teacher Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
    .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
    
    .page-header {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;
      box-shadow: 0 4px 15px rgba(40,167,69,0.2);
    }
    .page-header h1 { margin: 0 0 10px 0; font-size: 28px; }
    .breadcrumb { opacity: 0.9; font-size: 14px; }
    .breadcrumb a { color: white; text-decoration: none; }
    
    .profile-grid { display: grid; grid-template-columns: 350px 1fr; gap: 30px; }
    
    .profile-sidebar {
      background: white; border-radius: 10px; padding: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      text-align: center;
    }
    
    .profile-avatar {
      width: 150px; height: 150px; border-radius: 50%;
      background: linear-gradient(135deg, #28a745, #20c997);
      display: flex; align-items: center; justify-content: center;
      font-size: 60px; color: white; margin: 0 auto 20px;
    }
    
    .profile-name {
      font-size: 24px; font-weight: 600; color: #333;
      margin-bottom: 5px;
    }
    .profile-role {
      color: #666; margin-bottom: 20px;
    }
    
    .profile-stats {
      display: grid; grid-template-columns: 1fr 1fr; gap: 15px;
      margin-top: 20px;
    }
    .stat-box {
      background: #f8f9fa; padding: 15px; border-radius: 8px;
    }
    .stat-number {
      font-size: 24px; font-weight: 600; color: #28a745;
    }
    .stat-label {
      font-size: 12px; color: #666;
    }
    
    .profile-content {
      background: white; border-radius: 10px; padding: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .section-title {
      font-size: 20px; font-weight: 600; color: #28a745;
      margin-bottom: 20px; padding-bottom: 10px;
      border-bottom: 2px solid #28a745;
    }
    
    .info-grid {
      display: grid; grid-template-columns: 1fr 1fr; gap: 20px;
      margin-bottom: 30px;
    }
    .info-item {
      margin-bottom: 15px;
    }
    .info-label {
      font-size: 13px; color: #666; margin-bottom: 5px;
    }
    .info-value {
      font-size: 15px; color: #333; font-weight: 500;
    }
    
    .btn-edit {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white; padding: 12px 30px; border: none;
      border-radius: 6px; cursor: pointer; font-size: 14px;
      transition: all 0.3s;
    }
    .btn-edit:hover {
      background: linear-gradient(135deg, #20c997, #28a745);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(40,167,69,0.3);
    }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>My Profile - View and manage your profile information</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <h1><i class="fa fa-user"></i> My Profile</h1>
    <div class="breadcrumb">
      <a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / My Profile
    </div>
  </div>

  <div class="profile-grid">
    
    <div class="profile-sidebar">
      <div class="profile-avatar">
        <i class="fa fa-user"></i>
      </div>
      <div class="profile-name"><?php echo htmlspecialchars($fullName); ?></div>
      <div class="profile-role"><i class="fa fa-chalkboard-teacher"></i> Teacher</div>
      
      <div class="profile-stats">
        <div class="stat-box">
          <div class="stat-number">5</div>
          <div class="stat-label">Classes</div>
        </div>
        <div class="stat-box">
          <div class="stat-number">120</div>
          <div class="stat-label">Students</div>
        </div>
        <div class="stat-box">
          <div class="stat-number">8</div>
          <div class="stat-label">Years Exp.</div>
        </div>
        <div class="stat-box">
          <div class="stat-number">4.8</div>
          <div class="stat-label">Rating</div>
        </div>
      </div>
      
      <button class="btn-edit" style="margin-top: 20px; width: 100%;" onclick="alert('Edit profile coming soon!');">
        <i class="fa fa-edit"></i> Edit Profile
      </button>
    </div>

    <div class="profile-content">
      
      <div class="section-title">Personal Information</div>
      <div class="info-grid">
        <div class="info-item">
          <div class="info-label">Full Name</div>
          <div class="info-value"><?php echo htmlspecialchars($fullName); ?></div>
        </div>
        <div class="info-item">
          <div class="info-label">Employee ID</div>
          <div class="info-value">TCH20251001</div>
        </div>
        <div class="info-item">
          <div class="info-label">Email</div>
          <div class="info-value"><?php echo htmlspecialchars($username); ?>@scti.edu.np</div>
        </div>
        <div class="info-item">
          <div class="info-label">Phone</div>
          <div class="info-value">+977 98XXXXXXXX</div>
        </div>
        <div class="info-item">
          <div class="info-label">Date of Birth</div>
          <div class="info-value">January 15, 1985</div>
        </div>
        <div class="info-item">
          <div class="info-label">Gender</div>
          <div class="info-value">Male</div>
        </div>
        <div class="info-item">
          <div class="info-label">Address</div>
          <div class="info-value">Sindhuli, Nepal</div>
        </div>
        <div class="info-item">
          <div class="info-label">Joining Date</div>
          <div class="info-value">August 1, 2017</div>
        </div>
      </div>

      <div class="section-title">Professional Information</div>
      <div class="info-grid">
        <div class="info-item">
          <div class="info-label">Department</div>
          <div class="info-value">Computer Science & IT</div>
        </div>
        <div class="info-item">
          <div class="info-label">Designation</div>
          <div class="info-value">Senior Lecturer</div>
        </div>
        <div class="info-item">
          <div class="info-label">Qualification</div>
          <div class="info-value">M.Sc. Computer Science</div>
        </div>
        <div class="info-item">
          <div class="info-label">Specialization</div>
          <div class="info-value">Database Systems, Web Development</div>
        </div>
        <div class="info-item">
          <div class="info-label">Experience</div>
          <div class="info-value">8 Years</div>
        </div>
        <div class="info-item">
          <div class="info-label">Office Room</div>
          <div class="info-value">Faculty Room 3</div>
        </div>
      </div>

      <div class="section-title">Teaching Schedule</div>
      <div class="info-grid">
        <div class="info-item">
          <div class="info-label">Total Classes</div>
          <div class="info-value">5 Classes</div>
        </div>
        <div class="info-item">
          <div class="info-label">Weekly Hours</div>
          <div class="info-value">20 Hours</div>
        </div>
        <div class="info-item">
          <div class="info-label">Office Hours</div>
          <div class="info-value">Mon-Fri, 10:00 AM - 11:00 AM</div>
        </div>
        <div class="info-item">
          <div class="info-label">Consultation</div>
          <div class="info-value">By Appointment</div>
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
