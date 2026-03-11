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
  <title>Assignment Submissions | SCTI Teacher Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
    
    .top-header {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white; padding: 10px 0; text-align: center;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .top-header marquee {
      font-size: 14px; font-weight: 500;
    }
    
    .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
    
    .page-header {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;
      box-shadow: 0 4px 15px rgba(40,167,69,0.2);
    }
    .page-header h1 { margin: 0 0 10px 0; font-size: 28px; }
    .page-header p { margin: 5px 0; opacity: 0.95; font-size: 14px; }
    .breadcrumb { opacity: 1; font-size: 14px; background: transparent; padding: 0; margin-top: 10px; }
    .breadcrumb a { color: white; text-decoration: none; }
    
    .stats-bar {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px; margin-bottom: 30px;
    }
    .stat-card {
      background: white; padding: 20px; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      text-align: center;
    }
    .stat-number {
      font-size: 32px; font-weight: 700; color: #28a745;
      margin-bottom: 5px;
    }
    .stat-label {
      font-size: 14px; color: #666;
    }
    
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
    
    .submissions-table-container {
      background: white; border-radius: 10px; padding: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow-x: auto;
    }
    
    .submissions-table {
      width: 100%; border-collapse: collapse;
    }
    .submissions-table th {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white; padding: 15px; text-align: left;
      font-weight: 600; border: 1px solid #20c997;
    }
    .submissions-table td {
      padding: 15px; border-bottom: 1px solid #dee2e6;
    }
    .submissions-table tr:hover {
      background: #f8f9fa;
    }
    
    .student-info {
      display: flex; align-items: center; gap: 10px;
    }
    .student-avatar {
      width: 40px; height: 40px; border-radius: 50%;
      background: linear-gradient(135deg, #28a745, #20c997);
      display: inline-flex; align-items: center; justify-content: center;
      color: white; font-weight: bold; font-size: 14px;
    }
    
    .status-badge {
      padding: 6px 12px; border-radius: 20px;
      font-size: 12px; font-weight: 600; display: inline-block;
    }
    .status-submitted { background: #d4edda; color: #155724; }
    .status-graded { background: #cce5ff; color: #004085; }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-late { background: #f8d7da; color: #721c24; }
    
    .action-btn {
      padding: 8px 16px; border: none; border-radius: 4px;
      cursor: pointer; font-size: 12px; transition: all 0.3s;
      margin-right: 5px; display: inline-flex;
      align-items: center; gap: 5px;
    }
    .btn-grade {
      background: #28a745; color: white;
    }
    .btn-grade:hover {
      background: #20c997;
    }
    .btn-view {
      background: #004080; color: white;
    }
    .btn-view:hover {
      background: #0059b3;
    }
    .btn-download {
      background: #6c757d; color: white;
    }
    .btn-download:hover {
      background: #5a6268;
    }
    
    .grade-display {
      font-weight: 600; font-size: 16px;
    }
    .grade-good { color: #28a745; }
    .grade-average { color: #ffc107; }
    .grade-poor { color: #dc3545; }
    
    .footer {
      background: #2c3e50; color: white;
      text-align: center; padding: 20px;
      border-radius: 10px;
    }
    .footer p {
      margin: 0; font-size: 14px;
    }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Assignment Submissions - Review and grade student submissions</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <h1><i class="fa fa-file-alt"></i> Database Design Project - Submissions</h1>
    <p>Database Management - B.Tech IT Semester 3</p>
    <p><i class="fa fa-calendar"></i> Due: March 15, 2026 | <i class="fa fa-star"></i> 100 points</p>
    <div class="breadcrumb">
      <a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / 
      <a href="teacher-assignments.php">Assignments</a> / Submissions
    </div>
  </div>

  <div class="stats-bar">
    <div class="stat-card">
      <div class="stat-number">28</div>
      <div class="stat-label">Total Students</div>
    </div>
    <div class="stat-card">
      <div class="stat-number">18</div>
      <div class="stat-label">Submitted</div>
    </div>
    <div class="stat-card">
      <div class="stat-number">10</div>
      <div class="stat-label">Pending</div>
    </div>
    <div class="stat-card">
      <div class="stat-number">5</div>
      <div class="stat-label">Graded</div>
    </div>
    <div class="stat-card">
      <div class="stat-number">64%</div>
      <div class="stat-label">Submission Rate</div>
    </div>
  </div>

  <div class="filter-bar">
    <select class="filter-select">
      <option>All Submissions</option>
      <option>Submitted</option>
      <option>Graded</option>
      <option>Pending</option>
      <option>Late Submissions</option>
    </select>
    <select class="filter-select">
      <option>Sort by: Submission Date</option>
      <option>Sort by: Student Name</option>
      <option>Sort by: Grade</option>
      <option>Sort by: Status</option>
    </select>
    <input type="text" class="search-input" placeholder="Search by student name or ID...">
  </div>

  <div class="submissions-table-container">
    <table class="submissions-table">
      <thead>
        <tr>
          <th>Student</th>
          <th>Student ID</th>
          <th>Submitted On</th>
          <th>Status</th>
          <th>Grade</th>
          <th>Feedback</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            <div class="student-info">
              <div class="student-avatar">RS</div>
              <span>Ram Sharma</span>
            </div>
          </td>
          <td>STU20251001</td>
          <td>Mar 10, 2026 2:30 PM</td>
          <td><span class="status-badge status-graded">Graded</span></td>
          <td><span class="grade-display grade-good">92/100</span></td>
          <td><i class="fa fa-check-circle" style="color: #28a745;"></i> Provided</td>
          <td>
            <button class="action-btn btn-view" onclick="alert('View submission coming soon!');">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="action-btn btn-download" onclick="alert('Download coming soon!');">
              <i class="fa fa-download"></i>
            </button>
          </td>
        </tr>
        <tr>
          <td>
            <div class="student-info">
              <div class="student-avatar">SP</div>
              <span>Sita Poudel</span>
            </div>
          </td>
          <td>STU20251002</td>
          <td>Mar 11, 2026 10:15 AM</td>
          <td><span class="status-badge status-submitted">Submitted</span></td>
          <td><span class="grade-display">-</span></td>
          <td>-</td>
          <td>
            <button class="action-btn btn-grade" onclick="alert('Grade submission coming soon!');">
              <i class="fa fa-edit"></i> Grade
            </button>
            <button class="action-btn btn-view" onclick="alert('View submission coming soon!');">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="action-btn btn-download" onclick="alert('Download coming soon!');">
              <i class="fa fa-download"></i>
            </button>
          </td>
        </tr>
        <tr>
          <td>
            <div class="student-info">
              <div class="student-avatar">HT</div>
              <span>Hari Thapa</span>
            </div>
          </td>
          <td>STU20251003</td>
          <td>-</td>
          <td><span class="status-badge status-pending">Pending</span></td>
          <td><span class="grade-display">-</span></td>
          <td>-</td>
          <td>
            <button class="action-btn btn-view" disabled style="opacity: 0.5; cursor: not-allowed;">
              <i class="fa fa-eye"></i> View
            </button>
          </td>
        </tr>
        <tr>
          <td>
            <div class="student-info">
              <div class="student-avatar">GK</div>
              <span>Gita KC</span>
            </div>
          </td>
          <td>STU20251004</td>
          <td>Mar 9, 2026 4:45 PM</td>
          <td><span class="status-badge status-graded">Graded</span></td>
          <td><span class="grade-display grade-good">88/100</span></td>
          <td><i class="fa fa-check-circle" style="color: #28a745;"></i> Provided</td>
          <td>
            <button class="action-btn btn-view" onclick="alert('View submission coming soon!');">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="action-btn btn-download" onclick="alert('Download coming soon!');">
              <i class="fa fa-download"></i>
            </button>
          </td>
        </tr>
        <tr>
          <td>
            <div class="student-info">
              <div class="student-avatar">PG</div>
              <span>Prakash Gurung</span>
            </div>
          </td>
          <td>STU20251005</td>
          <td>Mar 12, 2026 11:20 AM</td>
          <td><span class="status-badge status-submitted">Submitted</span></td>
          <td><span class="grade-display">-</span></td>
          <td>-</td>
          <td>
            <button class="action-btn btn-grade" onclick="alert('Grade submission coming soon!');">
              <i class="fa fa-edit"></i> Grade
            </button>
            <button class="action-btn btn-view" onclick="alert('View submission coming soon!');">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="action-btn btn-download" onclick="alert('Download coming soon!');">
              <i class="fa fa-download"></i>
            </button>
          </td>
        </tr>
        <tr>
          <td>
            <div class="student-info">
              <div class="student-avatar">MR</div>
              <span>Maya Rai</span>
            </div>
          </td>
          <td>STU20251006</td>
          <td>Mar 16, 2026 9:00 AM</td>
          <td><span class="status-badge status-late">Late</span></td>
          <td><span class="grade-display">-</span></td>
          <td>-</td>
          <td>
            <button class="action-btn btn-grade" onclick="alert('Grade submission coming soon!');">
              <i class="fa fa-edit"></i> Grade
            </button>
            <button class="action-btn btn-view" onclick="alert('View submission coming soon!');">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="action-btn btn-download" onclick="alert('Download coming soon!');">
              <i class="fa fa-download"></i>
            </button>
          </td>
        </tr>
        <tr>
          <td>
            <div class="student-info">
              <div class="student-avatar">SK</div>
              <span>Suresh Karki</span>
            </div>
          </td>
          <td>STU20251007</td>
          <td>Mar 10, 2026 8:30 PM</td>
          <td><span class="status-badge status-graded">Graded</span></td>
          <td><span class="grade-display grade-average">75/100</span></td>
          <td><i class="fa fa-check-circle" style="color: #28a745;"></i> Provided</td>
          <td>
            <button class="action-btn btn-view" onclick="alert('View submission coming soon!');">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="action-btn btn-download" onclick="alert('Download coming soon!');">
              <i class="fa fa-download"></i>
            </button>
          </td>
        </tr>
        <tr>
          <td>
            <div class="student-info">
              <div class="student-avatar">AB</div>
              <span>Anita Basnet</span>
            </div>
          </td>
          <td>STU20251008</td>
          <td>Mar 11, 2026 3:15 PM</td>
          <td><span class="status-badge status-submitted">Submitted</span></td>
          <td><span class="grade-display">-</span></td>
          <td>-</td>
          <td>
            <button class="action-btn btn-grade" onclick="alert('Grade submission coming soon!');">
              <i class="fa fa-edit"></i> Grade
            </button>
            <button class="action-btn btn-view" onclick="alert('View submission coming soon!');">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="action-btn btn-download" onclick="alert('Download coming soon!');">
              <i class="fa fa-download"></i>
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
