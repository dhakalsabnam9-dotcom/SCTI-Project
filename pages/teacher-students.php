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
  <title>Students | SCTI Teacher Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
    .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
    
    .page-header {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;
      box-shadow: 0 4px 15px rgba(40,167,69,0.2);
    }
    .page-header h1 { margin: 0 0 10px 0; font-size: 28px; }
    .breadcrumb { opacity: 0.9; font-size: 14px; }
    .breadcrumb a { color: white; text-decoration: none; }
    
    .filter-bar {
      background: white; padding: 20px; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px;
      display: flex; gap: 15px; flex-wrap: wrap; align-items: center;
    }
    .filter-select {
      padding: 10px 15px; border: 2px solid #dee2e6;
      border-radius: 6px; font-size: 14px;
    }
    .search-input {
      flex: 1; padding: 10px 15px; border: 2px solid #dee2e6;
      border-radius: 6px; font-size: 14px; min-width: 250px;
    }
    
    .students-table-container {
      background: white; border-radius: 10px; padding: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow-x: auto;
    }
    
    .students-table {
      width: 100%; border-collapse: collapse;
    }
    .students-table th {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white; padding: 15px; text-align: left;
      font-weight: 600; border: 1px solid #20c997;
    }
    .students-table td {
      padding: 15px; border-bottom: 1px solid #dee2e6;
    }
    .students-table tr:hover {
      background: #f8f9fa;
    }
    
    .student-avatar {
      width: 40px; height: 40px; border-radius: 50%;
      background: linear-gradient(135deg, #28a745, #20c997);
      display: inline-flex; align-items: center; justify-content: center;
      color: white; font-weight: bold;
    }
    
    .status-badge {
      padding: 6px 12px; border-radius: 20px; font-size: 12px;
      font-weight: 600; display: inline-block;
    }
    .status-active { background: #d4edda; color: #155724; }
    .status-inactive { background: #f8d7da; color: #721c24; }
    
    .action-btn {
      padding: 6px 12px; border: none; border-radius: 4px;
      cursor: pointer; font-size: 12px; transition: all 0.3s;
      margin-right: 5px;
    }
    .btn-view {
      background: #004080; color: white;
    }
    .btn-view:hover {
      background: #0059b3;
    }
    .btn-message {
      background: #28a745; color: white;
    }
    .btn-message:hover {
      background: #20c997;
    }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Student Management - View and manage your students</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <h1><i class="fa fa-users"></i> My Students</h1>
    <div class="breadcrumb">
      <a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Students
    </div>
  </div>

  <div class="filter-bar">
    <select class="filter-select">
      <option>All Classes</option>
      <option>Programming Fundamentals</option>
      <option>Database Management</option>
      <option>Web Development</option>
      <option>Data Structures</option>
    </select>
    <select class="filter-select">
      <option>All Semesters</option>
      <option>Semester 1</option>
      <option>Semester 2</option>
      <option>Semester 3</option>
    </select>
    <input type="text" class="search-input" placeholder="Search by name, ID, or email...">
  </div>

  <div class="students-table-container">
    <table class="students-table">
      <thead>
        <tr>
          <th>Student</th>
          <th>Student ID</th>
          <th>Email</th>
          <th>Program</th>
          <th>Attendance</th>
          <th>GPA</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            <div style="display: flex; align-items: center; gap: 10px;">
              <div class="student-avatar">RS</div>
              <span>Ram Sharma</span>
            </div>
          </td>
          <td>STU20251001</td>
          <td>ram.sharma@scti.edu.np</td>
          <td>B.Tech IT</td>
          <td>92%</td>
          <td>3.8</td>
          <td><span class="status-badge status-active">Active</span></td>
          <td>
            <button class="action-btn btn-view" onclick="alert('View profile coming soon!');">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="action-btn btn-message" onclick="alert('Message coming soon!');">
              <i class="fa fa-envelope"></i>
            </button>
          </td>
        </tr>
        <tr>
          <td>
            <div style="display: flex; align-items: center; gap: 10px;">
              <div class="student-avatar">SP</div>
              <span>Sita Poudel</span>
            </div>
          </td>
          <td>STU20251002</td>
          <td>sita.poudel@scti.edu.np</td>
          <td>B.Tech IT</td>
          <td>88%</td>
          <td>3.6</td>
          <td><span class="status-badge status-active">Active</span></td>
          <td>
            <button class="action-btn btn-view" onclick="alert('View profile coming soon!');">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="action-btn btn-message" onclick="alert('Message coming soon!');">
              <i class="fa fa-envelope"></i>
            </button>
          </td>
        </tr>
        <tr>
          <td>
            <div style="display: flex; align-items: center; gap: 10px;">
              <div class="student-avatar">HT</div>
              <span>Hari Thapa</span>
            </div>
          </td>
          <td>STU20251003</td>
          <td>hari.thapa@scti.edu.np</td>
          <td>B.Tech IT</td>
          <td>75%</td>
          <td>3.2</td>
          <td><span class="status-badge status-active">Active</span></td>
          <td>
            <button class="action-btn btn-view" onclick="alert('View profile coming soon!');">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="action-btn btn-message" onclick="alert('Message coming soon!');">
              <i class="fa fa-envelope"></i>
            </button>
          </td>
        </tr>
        <tr>
          <td>
            <div style="display: flex; align-items: center; gap: 10px;">
              <div class="student-avatar">GK</div>
              <span>Gita KC</span>
            </div>
          </td>
          <td>STU20251004</td>
          <td>gita.kc@scti.edu.np</td>
          <td>B.Tech IT</td>
          <td>95%</td>
          <td>3.9</td>
          <td><span class="status-badge status-active">Active</span></td>
          <td>
            <button class="action-btn btn-view" onclick="alert('View profile coming soon!');">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="action-btn btn-message" onclick="alert('Message coming soon!');">
              <i class="fa fa-envelope"></i>
            </button>
          </td>
        </tr>
        <tr>
          <td>
            <div style="display: flex; align-items: center; gap: 10px;">
              <div class="student-avatar">PG</div>
              <span>Prakash Gurung</span>
            </div>
          </td>
          <td>STU20251005</td>
          <td>prakash.gurung@scti.edu.np</td>
          <td>B.Tech IT</td>
          <td>82%</td>
          <td>3.4</td>
          <td><span class="status-badge status-active">Active</span></td>
          <td>
            <button class="action-btn btn-view" onclick="alert('View profile coming soon!');">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="action-btn btn-message" onclick="alert('Message coming soon!');">
              <i class="fa fa-envelope"></i>
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Teacher Portal</p>
</footer>

</body>
</html>
