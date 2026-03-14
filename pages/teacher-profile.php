<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../index.php'); exit();
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
    body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; }
    .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
    .page-header {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;
      box-shadow: 0 4px 15px rgba(40,167,69,0.2);
    }
    .page-header h1 { margin: 0 0 8px 0; font-size: 28px; }
    .breadcrumb { background: transparent !important; padding: 0; font-size: 14px; }
    .breadcrumb a { color: white; text-decoration: none; }
    .profile-grid { display: grid; grid-template-columns: 300px 1fr; gap: 25px; }
    .profile-card {
      background: white; border-radius: 10px; padding: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;
    }
    .avatar {
      width: 120px; height: 120px; border-radius: 50%;
      background: linear-gradient(135deg, #28a745, #20c997);
      display: flex; align-items: center; justify-content: center;
      font-size: 48px; color: white; margin: 0 auto 20px;
    }
    .profile-name { font-size: 22px; font-weight: 700; color: #333; margin-bottom: 5px; }
    .profile-role {
      display: inline-block; background: #d4edda; color: #155724;
      padding: 5px 15px; border-radius: 20px; font-size: 13px; margin-bottom: 20px;
    }
    .profile-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 20px; }
    .pstat { background: #f8f9fa; border-radius: 8px; padding: 12px; }
    .pstat .num { font-size: 22px; font-weight: 700; color: #28a745; }
    .pstat .lbl { font-size: 11px; color: #666; }
    .info-card {
      background: white; border-radius: 10px; padding: 25px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px;
    }
    .info-card h3 { color: #28a745; margin-bottom: 20px; font-size: 18px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .info-item label { display: block; font-size: 12px; color: #999; margin-bottom: 4px; text-transform: uppercase; }
    .info-item span { font-size: 15px; color: #333; font-weight: 500; }
    .edit-btn {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white; padding: 10px 20px; border: none;
      border-radius: 6px; cursor: pointer; font-size: 14px;
      transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px;
    }
    .edit-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(40,167,69,0.3); }
    .subject-tags { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 5px; }
    .subject-tag {
      background: #d4edda; color: #155724;
      padding: 5px 12px; border-radius: 20px; font-size: 13px;
    }
  </style>
</head>
<body>
<div class="top-header"><marquee>My Profile - View and manage your personal and professional information</marquee></div>
<div class="container">
  <div class="page-header">
    <h1><i class="fa fa-user-circle"></i> My Profile</h1>
    <div class="breadcrumb">
      <a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / My Profile
    </div>
  </div>

  <div class="profile-grid">
    <div>
      <div class="profile-card">
        <div class="avatar"><i class="fa fa-user-tie"></i></div>
        <div class="profile-name"><?php echo htmlspecialchars($fullName); ?></div>
        <div class="profile-role">Teacher</div>
        <div class="profile-stats">
          <div class="pstat"><div class="num">5</div><div class="lbl">Subjects</div></div>
          <div class="pstat"><div class="num">120</div><div class="lbl">Students</div></div>
          <div class="pstat"><div class="num">8</div><div class="lbl">Yrs Exp.</div></div>
          <div class="pstat"><div class="num">4.8</div><div class="lbl">Rating</div></div>
        </div>
      </div>
    </div>

    <div>
      <div class="info-card">
        <h3><i class="fa fa-id-card"></i> Personal Information</h3>
        <div class="info-grid">
          <div class="info-item"><label>Full Name</label><span><?php echo htmlspecialchars($fullName); ?></span></div>
          <div class="info-item"><label>Username</label><span><?php echo htmlspecialchars($username); ?></span></div>
          <div class="info-item"><label>Email</label><span>teacher@scti.edu.np</span></div>
          <div class="info-item"><label>Phone</label><span>+977-9800000000</span></div>
          <div class="info-item"><label>Date of Birth</label><span>Jan 15, 1985</span></div>
          <div class="info-item"><label>Gender</label><span>Male</span></div>
          <div class="info-item"><label>Address</label><span>Sindhuli, Bagmati Province</span></div>
          <div class="info-item"><label>Joined</label><span>Aug 2018</span></div>
        </div>
      </div>

      <div class="info-card">
        <h3><i class="fa fa-graduation-cap"></i> Professional Information</h3>
        <div class="info-grid">
          <div class="info-item"><label>Employee ID</label><span>TCH-2018-001</span></div>
          <div class="info-item"><label>Department</label><span>Computer Science</span></div>
          <div class="info-item"><label>Qualification</label><span>M.Sc. Computer Science</span></div>
          <div class="info-item"><label>Experience</label><span>8 Years</span></div>
        </div>
        <div style="margin-top:15px;">
          <label style="font-size:12px;color:#999;text-transform:uppercase;">Subjects Teaching</label>
          <div class="subject-tags">
            <span class="subject-tag">Programming Fundamentals</span>
            <span class="subject-tag">Database Management</span>
            <span class="subject-tag">Web Development</span>
            <span class="subject-tag">Data Structures</span>
            <span class="subject-tag">Algorithms</span>
          </div>
        </div>
      </div>

      <div style="text-align:right;">
        <button class="edit-btn" onclick="alert('Edit profile feature coming soon!');">
          <i class="fa fa-edit"></i> Edit Profile
        </button>
      </div>
    </div>
  </div>
</div>
<footer class="footer" style="margin-top:30px;"><p>© 2025 SCTI - Teacher Portal</p></footer>
</body>
</html>
