<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
// In real implementation, get student ID from URL parameter
$studentId = isset($_GET['id']) ? $_GET['id'] : 'STU20251001';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Details | SCTI Admin</title>
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
      display: flex; justify-content: space-between; align-items: center;
    }
    .page-header h1 { margin: 0; font-size: 28px; }
    .breadcrumb { opacity: 1; font-size: 14px; background: transparent; padding: 0; margin-top: 8px; }
    .breadcrumb a { color: white; text-decoration: none; }
    
    .btn {
      padding: 12px 24px; border: none; border-radius: 6px;
      cursor: pointer; font-size: 14px; transition: all 0.3s;
      text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-primary {
      background: white; color: #004080; font-weight: 600;
    }
    .btn-primary:hover {
      background: #f0f0f0; transform: translateY(-2px);
    }
    .btn-warning {
      background: #ffc107; color: #333; font-weight: 600;
    }
    .btn-warning:hover {
      background: #e0a800; transform: translateY(-2px);
    }
    
    .profile-grid { display: grid; grid-template-columns: 350px 1fr; gap: 25px; }
    
    .profile-sidebar {
      background: white; border-radius: 10px; padding: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      text-align: center;
    }
    
    .profile-avatar {
      width: 150px; height: 150px; border-radius: 50%;
      background: linear-gradient(135deg, #004080, #0059b3);
      display: flex; align-items: center; justify-content: center;
      font-size: 60px; color: white; margin: 0 auto 20px;
      border: 5px solid #f0f0f0;
    }
    
    .profile-name {
      font-size: 24px; font-weight: 600; color: #333;
      margin-bottom: 5px;
    }
    .profile-id {
      color: #666; margin-bottom: 15px; font-size: 14px;
    }
    
    .status-badge {
      padding: 8px 16px; border-radius: 20px;
      font-size: 13px; font-weight: 600;
      text-transform: uppercase; display: inline-block;
      margin-bottom: 20px;
    }
    .status-badge.active { background: #d4edda; color: #155724; }
    
    .profile-stats {
      display: grid; grid-template-columns: 1fr 1fr; gap: 15px;
      margin-top: 20px;
    }
    .stat-box {
      background: #f8f9fa; padding: 15px; border-radius: 8px;
    }
    .stat-number {
      font-size: 24px; font-weight: 600; color: #004080;
    }
    .stat-label {
      font-size: 12px; color: #666; margin-top: 5px;
    }
    
    .profile-actions {
      margin-top: 20px; display: flex; flex-direction: column; gap: 10px;
    }
    
    .profile-content {
      display: flex; flex-direction: column; gap: 25px;
    }
    
    .info-card {
      background: white; border-radius: 10px; padding: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .section-title {
      font-size: 20px; font-weight: 600; color: #004080;
      margin-bottom: 20px; padding-bottom: 10px;
      border-bottom: 2px solid #004080;
      display: flex; align-items: center; gap: 10px;
    }
    
    .info-grid {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
    }
    .info-item {
      margin-bottom: 15px;
    }
    .info-label {
      font-size: 13px; color: #666; margin-bottom: 5px;
      font-weight: 600;
    }
    .info-value {
      font-size: 15px; color: #333;
    }
    
    .courses-table {
      width: 100%; border-collapse: collapse;
    }
    .courses-table thead {
      background: linear-gradient(135deg, #004080, #0059b3);
      color: white;
    }
    .courses-table th {
      padding: 12px; text-align: left; font-weight: 600;
      font-size: 14px;
    }
    .courses-table td {
      padding: 12px; border-bottom: 1px solid #e0e0e0;
    }
    .courses-table tbody tr:hover {
      background: #f8f9fa;
    }
    
    .grade-badge {
      padding: 5px 12px; border-radius: 20px;
      font-size: 12px; font-weight: 600;
      display: inline-block;
    }
    .grade-a { background: #d4edda; color: #155724; }
    .grade-b { background: #d1ecf1; color: #0c5460; }
    .grade-c { background: #fff3cd; color: #856404; }
    
    .attendance-record {
      display: flex; gap: 15px; margin-bottom: 15px;
      padding: 15px; background: #f8f9fa; border-radius: 8px;
    }
    .attendance-date {
      font-weight: 600; color: #004080; min-width: 120px;
    }
    .attendance-status {
      padding: 4px 10px; border-radius: 15px;
      font-size: 11px; font-weight: 600;
    }
    .status-present { background: #d4edda; color: #155724; }
    .status-absent { background: #f8d7da; color: #721c24; }
    .status-late { background: #fff3cd; color: #856404; }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Student Details - View comprehensive student information</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <div>
      <h1><i class="fa fa-user-graduate"></i> Student Details</h1>
      <div class="breadcrumb">
        <a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / 
        <a href="../pages/manage-students.php">Manage Students</a> / Student Details
      </div>
    </div>
    <div style="display: flex; gap: 10px;">
      <a href="../pages/manage-students.php" class="btn btn-primary">
        <i class="fa fa-arrow-left"></i> Back
      </a>
      <button class="btn btn-warning" onclick="alert('Edit student form coming soon!')">
        <i class="fa fa-edit"></i> Edit Student
      </button>
    </div>
  </div>

  <div class="profile-grid">
    
    <div class="profile-sidebar">
      <div class="profile-avatar">
        <i class="fa fa-user"></i>
      </div>
      <div class="profile-name">Ram Kumar Sharma</div>
      <div class="profile-id">STU20251001</div>
      <span class="status-badge active">Active</span>
      
      <div class="profile-stats">
        <div class="stat-box">
          <div class="stat-number">3.8</div>
          <div class="stat-label">GPA</div>
        </div>
        <div class="stat-box">
          <div class="stat-number">92%</div>
          <div class="stat-label">Attendance</div>
        </div>
        <div class="stat-box">
          <div class="stat-number">6</div>
          <div class="stat-label">Courses</div>
        </div>
        <div class="stat-box">
          <div class="stat-number">3</div>
          <div class="stat-label">Semester</div>
        </div>
      </div>
      
      <div class="profile-actions">
        <button class="btn btn-primary" style="width: 100%;" onclick="alert('Send message coming soon!')">
          <i class="fa fa-envelope"></i> Send Message
        </button>
        <button class="btn btn-warning" style="width: 100%;" onclick="alert('Generate report coming soon!')">
          <i class="fa fa-file-pdf"></i> Generate Report
        </button>
      </div>
    </div>

    <div class="profile-content">
      
      <div class="info-card">
        <div class="section-title">
          <i class="fa fa-user"></i> Personal Information
        </div>
        <div class="info-grid">
          <div class="info-item">
            <div class="info-label">Full Name</div>
            <div class="info-value">Ram Kumar Sharma</div>
          </div>
          <div class="info-item">
            <div class="info-label">Date of Birth</div>
            <div class="info-value">January 15, 2005</div>
          </div>
          <div class="info-item">
            <div class="info-label">Gender</div>
            <div class="info-value">Male</div>
          </div>
          <div class="info-item">
            <div class="info-label">Blood Group</div>
            <div class="info-value">O+</div>
          </div>
          <div class="info-item">
            <div class="info-label">Email</div>
            <div class="info-value">ram.sharma@scti.edu.np</div>
          </div>
          <div class="info-item">
            <div class="info-label">Phone</div>
            <div class="info-value">+977 9841234567</div>
          </div>
          <div class="info-item">
            <div class="info-label">Address</div>
            <div class="info-value">Sindhuli, Nepal</div>
          </div>
          <div class="info-item">
            <div class="info-label">Guardian Name</div>
            <div class="info-value">Mr. Krishna Sharma</div>
          </div>
          <div class="info-item">
            <div class="info-label">Guardian Phone</div>
            <div class="info-value">+977 9851234568</div>
          </div>
        </div>
      </div>

      <div class="info-card">
        <div class="section-title">
          <i class="fa fa-graduation-cap"></i> Academic Information
        </div>
        <div class="info-grid">
          <div class="info-item">
            <div class="info-label">Student ID</div>
            <div class="info-value">STU20251001</div>
          </div>
          <div class="info-item">
            <div class="info-label">Program</div>
            <div class="info-value">B.Tech Ed in Information Technology</div>
          </div>
          <div class="info-item">
            <div class="info-label">Current Semester</div>
            <div class="info-value">Semester 3</div>
          </div>
          <div class="info-item">
            <div class="info-label">Admission Date</div>
            <div class="info-value">August 15, 2023</div>
          </div>
          <div class="info-item">
            <div class="info-label">Expected Graduation</div>
            <div class="info-value">June 2027</div>
          </div>
          <div class="info-item">
            <div class="info-label">Current GPA</div>
            <div class="info-value">3.8 / 4.0</div>
          </div>
          <div class="info-item">
            <div class="info-label">Overall Attendance</div>
            <div class="info-value">92%</div>
          </div>
          <div class="info-item">
            <div class="info-label">Academic Status</div>
            <div class="info-value"><span class="status-badge active">Active</span></div>
          </div>
        </div>
      </div>

      <div class="info-card">
        <div class="section-title">
          <i class="fa fa-book"></i> Enrolled Courses
        </div>
        <table class="courses-table">
          <thead>
            <tr>
              <th>Course Code</th>
              <th>Course Name</th>
              <th>Teacher</th>
              <th>Credits</th>
              <th>Grade</th>
              <th>Attendance</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>CS301</td>
              <td>Web Development</td>
              <td>Mr. Tej Bikram Thapa</td>
              <td>4</td>
              <td><span class="grade-badge grade-a">A</span></td>
              <td>95%</td>
            </tr>
            <tr>
              <td>CS302</td>
              <td>Software Engineering</td>
              <td>Mr. Bibek Bhandari</td>
              <td>3</td>
              <td><span class="grade-badge grade-a">A-</span></td>
              <td>90%</td>
            </tr>
            <tr>
              <td>CS303</td>
              <td>Computer Networks</td>
              <td>Mr. Santosh Sapkota</td>
              <td>4</td>
              <td><span class="grade-badge grade-b">B+</span></td>
              <td>88%</td>
            </tr>
            <tr>
              <td>MATH301</td>
              <td>Mathematics III</td>
              <td>Mrs. Sita Poudel</td>
              <td>3</td>
              <td><span class="grade-badge grade-a">A</span></td>
              <td>94%</td>
            </tr>
            <tr>
              <td>CS304</td>
              <td>Operating Systems</td>
              <td>Mr. Bibek Bhandari</td>
              <td>4</td>
              <td><span class="grade-badge grade-b">B+</span></td>
              <td>92%</td>
            </tr>
            <tr>
              <td>CS305</td>
              <td>Database Systems</td>
              <td>Mr. Santosh Sapkota</td>
              <td>3</td>
              <td><span class="grade-badge grade-a">A</span></td>
              <td>96%</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="info-card">
        <div class="section-title">
          <i class="fa fa-calendar-check"></i> Recent Attendance
        </div>
        <div class="attendance-record">
          <div class="attendance-date">March 8, 2026</div>
          <div style="flex: 1;">
            <span class="attendance-status status-present">Present</span>
            <span style="color: #666; font-size: 13px; margin-left: 10px;">All Classes</span>
          </div>
        </div>
        <div class="attendance-record">
          <div class="attendance-date">March 7, 2026</div>
          <div style="flex: 1;">
            <span class="attendance-status status-present">Present</span>
            <span style="color: #666; font-size: 13px; margin-left: 10px;">All Classes</span>
          </div>
        </div>
        <div class="attendance-record">
          <div class="attendance-date">March 6, 2026</div>
          <div style="flex: 1;">
            <span class="attendance-status status-late">Late</span>
            <span style="color: #666; font-size: 13px; margin-left: 10px;">CS301 - 10 mins late</span>
          </div>
        </div>
        <div class="attendance-record">
          <div class="attendance-date">March 5, 2026</div>
          <div style="flex: 1;">
            <span class="attendance-status status-present">Present</span>
            <span style="color: #666; font-size: 13px; margin-left: 10px;">All Classes</span>
          </div>
        </div>
        <div class="attendance-record">
          <div class="attendance-date">March 4, 2026</div>
          <div style="flex: 1;">
            <span class="attendance-status status-absent">Absent</span>
            <span style="color: #666; font-size: 13px; margin-left: 10px;">CS303 - Medical Leave</span>
          </div>
        </div>
      </div>

      <div class="info-card">
        <div class="section-title">
          <i class="fa fa-chart-line"></i> Academic Performance
        </div>
        <div class="info-grid">
          <div class="info-item">
            <div class="info-label">Semester 1 GPA</div>
            <div class="info-value">3.7</div>
          </div>
          <div class="info-item">
            <div class="info-label">Semester 2 GPA</div>
            <div class="info-value">3.9</div>
          </div>
          <div class="info-item">
            <div class="info-label">Current Semester GPA</div>
            <div class="info-value">3.8</div>
          </div>
          <div class="info-item">
            <div class="info-label">Cumulative GPA</div>
            <div class="info-value">3.8 / 4.0</div>
          </div>
          <div class="info-item">
            <div class="info-label">Credits Completed</div>
            <div class="info-value">62 / 120</div>
          </div>
          <div class="info-item">
            <div class="info-label">Class Rank</div>
            <div class="info-value">5 / 35</div>
          </div>
        </div>
      </div>

      <div class="info-card">
        <div class="section-title">
          <i class="fa fa-file-alt"></i> Documents & Records
        </div>
        <div style="display: flex; flex-direction: column; gap: 12px;">
          <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #f8f9fa; border-radius: 6px;">
            <div>
              <i class="fa fa-file-pdf" style="color: #dc3545; margin-right: 10px;"></i>
              <span>Admission Form</span>
            </div>
            <button class="btn-primary" style="padding: 6px 12px; font-size: 12px;" onclick="alert('Download coming soon!')">
              <i class="fa fa-download"></i> Download
            </button>
          </div>
          <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #f8f9fa; border-radius: 6px;">
            <div>
              <i class="fa fa-file-pdf" style="color: #dc3545; margin-right: 10px;"></i>
              <span>ID Card Copy</span>
            </div>
            <button class="btn-primary" style="padding: 6px 12px; font-size: 12px;" onclick="alert('Download coming soon!')">
              <i class="fa fa-download"></i> Download
            </button>
          </div>
          <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #f8f9fa; border-radius: 6px;">
            <div>
              <i class="fa fa-file-pdf" style="color: #dc3545; margin-right: 10px;"></i>
              <span>Transcript</span>
            </div>
            <button class="btn-primary" style="padding: 6px 12px; font-size: 12px;" onclick="alert('Download coming soon!')">
              <i class="fa fa-download"></i> Download
            </button>
          </div>
        </div>
      </div>

    </div>

  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Admin Panel</p>
</footer>

</body>
</html>
