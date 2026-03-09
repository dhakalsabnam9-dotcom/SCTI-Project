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
    .breadcrumb { opacity: 1; font-size: 14px; background: transparent; padding: 0; }
    .breadcrumb a { color: white; text-decoration: none; }
    .profile-grid {
      display: grid; grid-template-columns: 300px 1fr; gap: 20px;
      margin-bottom: 30px;
    }
    .profile-card {
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
      color: #28a745; font-size: 14px; margin-bottom: 20px;
    }
    .profile-stats {
      display: grid; grid-template-columns: 1fr 1fr; gap: 15px;
      margin-top: 20px;
    }
    .stat-box {
      padding: 15px; background: #f8f9fa; border-radius: 6px;
    }
    .stat-number {
      font-size: 24px; font-weight: 700; color: #28a745;
    }
    .stat-label {
      font-size: 12px; color: #666; margin-top: 5px;
    }
    .info-card {
      background: white; border-radius: 10px; padding: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .info-card h2 {
      color: #28a745; margin-bottom: 20px;
      padding-bottom: 10px; border-bottom: 2px solid #28a745;
    }
    .info-grid {
      display: grid; grid-template-columns: 1fr 1fr; gap: 20px;
    }
    .info-item {
      margin-bottom: 15px;
    }
    .info-label {
      font-size: 12px; color: #666; margin-bottom: 5px;
      text-transform: uppercase; letter-spacing: 0.5px;
    }
    .info-value {
      font-size: 16px; color: #333; font-weight: 500;
    }
    .section-card {
      background: white; border-radius: 10px; padding: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 20px;
    }
    .section-card h2 {
      color: #28a745; margin-bottom: 20px;
      padding-bottom: 10px; border-bottom: 2px solid #28a745;
    }
    .qualification-item {
      padding: 15px; background: #f8f9fa; border-radius: 6px;
      margin-bottom: 10px; border-left: 4px solid #28a745;
    }
    .qualification-title {
      font-weight: 600; color: #333; margin-bottom: 5px;
    }
    .qualification-details {
      font-size: 14px; color: #666;
    }
    .btn-edit {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white; padding: 12px 30px; border: none;
      border-radius: 6px; cursor: pointer; font-size: 14px;
      transition: all 0.3s; margin-top: 20px;
    }
    .btn-edit:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(40,167,69,0.3);
    }
    @media (max-width: 768px) {
      .profile-grid { grid-template-columns: 1fr; }
      .info-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Teacher Profile - View and manage your professional information</marquee>
</div>

<div class="container">
  <div class="page-header">
    <h1><i class="fa fa-user"></i> My Profile</h1>
    <div class="breadcrumb">
      <a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Profile
    </div>
  </div>

  <div class="profile-grid">
    <div class="profile-card">
      <div class="profile-avatar">
        <i class="fa fa-user"></i>
      </div>
      <div class="profile-name"><?php echo htmlspecialchars($fullName); ?></div>
      <div class="profile-role">Senior Faculty Member</div>
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
      <button class="btn-edit" onclick="alert('Edit profile coming soon!');">
        <i class="fa fa-edit"></i> Edit Profile
      </button>
    </div>

    <div class="info-card">
      <h2><i class="fa fa-info-circle"></i> Personal Information</h2>
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
          <div class="info-label">Email Address</div>
          <div class="info-value">teacher@scti.edu.np</div>
        </div>
        <div class="info-item">
          <div class="info-label">Phone Number</div>
          <div class="info-value">+977 9841234567</div>
        </div>
        <div class="info-item">
          <div class="info-label">Date of Birth</div>
          <div class="info-value">January 15, 1988</div>
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
    </div>
  </div>

  <div class="section-card">
    <h2><i class="fa fa-graduation-cap"></i> Educational Qualifications</h2>
    <div class="qualification-item">
      <div class="qualification-title">Master of Computer Applications (MCA)</div>
      <div class="qualification-details">
        Tribhuvan University | 2015 | First Division
      </div>
    </div>
    <div class="qualification-item">
      <div class="qualification-title">Bachelor of Computer Science (B.Sc. CS)</div>
      <div class="qualification-details">
        Tribhuvan University | 2013 | Distinction
      </div>
    </div>
    <div class="qualification-item">
      <div class="qualification-title">Higher Secondary Education (+2)</div>
      <div class="qualification-details">
        Science Stream | 2010 | First Division
      </div>
    </div>
  </div>

  <div class="section-card">
    <h2><i class="fa fa-briefcase"></i> Professional Experience</h2>
    <div class="qualification-item">
      <div class="qualification-title">Senior Faculty - Computer Science</div>
      <div class="qualification-details">
        SCTI, Sindhuli | August 2017 - Present | 8 years
      </div>
    </div>
    <div class="qualification-item">
      <div class="qualification-title">Junior Lecturer - IT Department</div>
      <div class="qualification-details">
        ABC College, Kathmandu | June 2015 - July 2017 | 2 years
      </div>
    </div>
  </div>

  <div class="section-card">
    <h2><i class="fa fa-certificate"></i> Certifications & Training</h2>
    <div class="qualification-item">
      <div class="qualification-title">Certified Java Programmer</div>
      <div class="qualification-details">
        Oracle | 2018
      </div>
    </div>
    <div class="qualification-item">
      <div class="qualification-title">Web Development Bootcamp</div>
      <div class="qualification-details">
        Udemy | 2019
      </div>
    </div>
    <div class="qualification-item">
      <div class="qualification-title">Database Administration</div>
      <div class="qualification-details">
        Microsoft | 2020
      </div>
    </div>
  </div>

  <div class="section-card">
    <h2><i class="fa fa-book-open"></i> Current Teaching Assignments</h2>
    <div class="info-grid">
      <div class="qualification-item">
        <div class="qualification-title">Programming Fundamentals</div>
        <div class="qualification-details">B.Tech IT - Semester 1 | 35 Students</div>
      </div>
      <div class="qualification-item">
        <div class="qualification-title">Database Management</div>
        <div class="qualification-details">B.Tech IT - Semester 3 | 28 Students</div>
      </div>
      <div class="qualification-item">
        <div class="qualification-title">Web Development</div>
        <div class="qualification-details">Diploma Civil - Semester 2 | 32 Students</div>
      </div>
      <div class="qualification-item">
        <div class="qualification-title">Data Structures</div>
        <div class="qualification-details">B.Tech IT - Semester 2 | 25 Students</div>
      </div>
    </div>
  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Teacher Portal</p>
</footer>

</body>
</html>
