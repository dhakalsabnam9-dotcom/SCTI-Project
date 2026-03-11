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
  <title>Assignments | SCTI Teacher Portal</title>
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
      display: flex; justify-content: space-between; align-items: center;
    }
    .page-header h1 { margin: 0 0 10px 0; font-size: 28px; }
    .breadcrumb { opacity: 1; font-size: 14px; background: transparent; padding: 0; }
    .breadcrumb a { color: white; text-decoration: none; }
    
    .btn-create {
      background: rgba(255,255,255,0.2); color: white;
      padding: 12px 24px; border: 2px solid white;
      border-radius: 6px; cursor: pointer; font-size: 14px;
      transition: all 0.3s; text-decoration: none;
      display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-create:hover {
      background: white; color: #28a745;
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
    
    .assignments-grid {
      display: grid; gap: 20px;
    }
    
    .assignment-card {
      background: white; border-radius: 10px; padding: 25px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      border-left: 5px solid #28a745;
      transition: all 0.3s;
    }
    .assignment-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(40,167,69,0.3);
    }
    
    .assignment-header {
      display: flex; justify-content: space-between;
      align-items: flex-start; margin-bottom: 15px;
    }
    .assignment-title {
      font-size: 20px; font-weight: 600; color: #333;
      margin-bottom: 5px;
    }
    .assignment-class {
      color: #666; font-size: 14px;
    }
    
    .status-badge {
      padding: 6px 12px; border-radius: 20px;
      font-size: 12px; font-weight: 600;
    }
    .status-active { background: #d4edda; color: #155724; }
    .status-graded { background: #cce5ff; color: #004085; }
    .status-upcoming { background: #fff3cd; color: #856404; }
    .status-pending { background: #f8d7da; color: #721c24; }
    
    .assignment-meta {
      display: flex; gap: 20px; margin: 15px 0;
      padding: 15px; background: #f8f9fa; border-radius: 6px;
    }
    .meta-item {
      display: flex; align-items: center; gap: 8px;
      color: #666; font-size: 14px;
    }
    .meta-item i { color: #28a745; }
    
    .submission-stats {
      display: grid; grid-template-columns: repeat(3, 1fr);
      gap: 15px; margin: 15px 0;
    }
    .stat-box {
      text-align: center; padding: 15px;
      background: #f8f9fa; border-radius: 6px;
    }
    .stat-number {
      font-size: 24px; font-weight: 700; color: #28a745;
    }
    .stat-label {
      font-size: 12px; color: #666; margin-top: 5px;
    }
    
    .assignment-actions {
      display: flex; gap: 10px; margin-top: 15px;
    }
    .action-btn {
      flex: 1; padding: 10px; border: none;
      border-radius: 6px; cursor: pointer;
      font-size: 14px; transition: all 0.3s;
      display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .btn-view {
      background: #28a745; color: white;
    }
    .btn-view:hover {
      background: #20c997;
    }
    .btn-edit {
      background: #004080; color: white;
    }
    .btn-edit:hover {
      background: #0059b3;
    }
    
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
  <marquee>Assignment Management - Create, manage, and grade assignments</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <div>
      <h1><i class="fa fa-tasks"></i> Assignments</h1>
      <div class="breadcrumb">
        <a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Assignments
      </div>
    </div>
    <button class="btn-create" onclick="alert('Create assignment feature coming soon!');">
      <i class="fa fa-plus"></i> Create Assignment
    </button>
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
      <option>All Status</option>
      <option>Active</option>
      <option>Graded</option>
      <option>Upcoming</option>
      <option>Pending</option>
    </select>
    <input type="text" class="search-input" placeholder="Search assignments...">
  </div>

  <div class="assignments-grid">
    
    <div class="assignment-card">
      <div class="assignment-header">
        <div>
          <div class="assignment-title">Database Design Project</div>
          <div class="assignment-class">Database Management - B.Tech IT Semester 3</div>
        </div>
        <span class="status-badge status-active">Active</span>
      </div>
      
      <div class="assignment-meta">
        <div class="meta-item">
          <i class="fa fa-calendar"></i>
          <span>Due: Mar 15, 2026</span>
        </div>
        <div class="meta-item">
          <i class="fa fa-clock"></i>
          <span>5 days left</span>
        </div>
        <div class="meta-item">
          <i class="fa fa-star"></i>
          <span>100 points</span>
        </div>
      </div>
      
      <div class="submission-stats">
        <div class="stat-box">
          <div class="stat-number">18</div>
          <div class="stat-label">Submitted</div>
        </div>
        <div class="stat-box">
          <div class="stat-number">10</div>
          <div class="stat-label">Pending</div>
        </div>
        <div class="stat-box">
          <div class="stat-number">5</div>
          <div class="stat-label">Graded</div>
        </div>
      </div>
      
      <div class="assignment-actions">
        <button class="action-btn btn-view" onclick="window.location.href='teacher-assignment-submissions.php';">
          <i class="fa fa-eye"></i> View Submissions
        </button>
        <button class="action-btn btn-edit" onclick="window.location.href='teacher-assignment-edit.php';">
          <i class="fa fa-edit"></i> Edit
        </button>
      </div>
    </div>

    <div class="assignment-card">
      <div class="assignment-header">
        <div>
          <div class="assignment-title">Web Development Final Project</div>
          <div class="assignment-class">Web Development - Diploma Civil Semester 2</div>
        </div>
        <span class="status-badge status-active">Active</span>
      </div>
      
      <div class="assignment-meta">
        <div class="meta-item">
          <i class="fa fa-calendar"></i>
          <span>Due: Mar 20, 2026</span>
        </div>
        <div class="meta-item">
          <i class="fa fa-clock"></i>
          <span>10 days left</span>
        </div>
        <div class="meta-item">
          <i class="fa fa-star"></i>
          <span>150 points</span>
        </div>
      </div>
      
      <div class="submission-stats">
        <div class="stat-box">
          <div class="stat-number">25</div>
          <div class="stat-label">Submitted</div>
        </div>
        <div class="stat-box">
          <div class="stat-number">7</div>
          <div class="stat-label">Pending</div>
        </div>
        <div class="stat-box">
          <div class="stat-number">12</div>
          <div class="stat-label">Graded</div>
        </div>
      </div>
      
      <div class="assignment-actions">
        <button class="action-btn btn-view" onclick="window.location.href='teacher-assignment-submissions.php';">
          <i class="fa fa-eye"></i> View Submissions
        </button>
        <button class="action-btn btn-edit" onclick="window.location.href='teacher-assignment-edit.php';">
          <i class="fa fa-edit"></i> Edit
        </button>
      </div>
    </div>

    <div class="assignment-card">
      <div class="assignment-header">
        <div>
          <div class="assignment-title">Data Structures Lab Assignment</div>
          <div class="assignment-class">Data Structures - B.Tech IT Semester 2</div>
        </div>
        <span class="status-badge status-graded">Graded</span>
      </div>
      
      <div class="assignment-meta">
        <div class="meta-item">
          <i class="fa fa-calendar"></i>
          <span>Due: Mar 5, 2026</span>
        </div>
        <div class="meta-item">
          <i class="fa fa-clock"></i>
          <span>Completed</span>
        </div>
        <div class="meta-item">
          <i class="fa fa-star"></i>
          <span>50 points</span>
        </div>
      </div>
      
      <div class="submission-stats">
        <div class="stat-box">
          <div class="stat-number">25</div>
          <div class="stat-label">Submitted</div>
        </div>
        <div class="stat-box">
          <div class="stat-number">0</div>
          <div class="stat-label">Pending</div>
        </div>
        <div class="stat-box">
          <div class="stat-number">25</div>
          <div class="stat-label">Graded</div>
        </div>
      </div>
      
      <div class="assignment-actions">
        <button class="action-btn btn-view" onclick="window.location.href='teacher-assignment-submissions.php';">
          <i class="fa fa-eye"></i> View Submissions
        </button>
        <button class="action-btn btn-edit" onclick="window.location.href='teacher-assignment-edit.php';">
          <i class="fa fa-edit"></i> Edit
        </button>
      </div>
    </div>

    <div class="assignment-card">
      <div class="assignment-header">
        <div>
          <div class="assignment-title">Programming Quiz 3</div>
          <div class="assignment-class">Programming Fundamentals - B.Tech IT Semester 1</div>
        </div>
        <span class="status-badge status-upcoming">Upcoming</span>
      </div>
      
      <div class="assignment-meta">
        <div class="meta-item">
          <i class="fa fa-calendar"></i>
          <span>Due: Mar 25, 2026</span>
        </div>
        <div class="meta-item">
          <i class="fa fa-clock"></i>
          <span>15 days left</span>
        </div>
        <div class="meta-item">
          <i class="fa fa-star"></i>
          <span>30 points</span>
        </div>
      </div>
      
      <div class="submission-stats">
        <div class="stat-box">
          <div class="stat-number">0</div>
          <div class="stat-label">Submitted</div>
        </div>
        <div class="stat-box">
          <div class="stat-number">35</div>
          <div class="stat-label">Pending</div>
        </div>
        <div class="stat-box">
          <div class="stat-number">0</div>
          <div class="stat-label">Graded</div>
        </div>
      </div>
      
      <div class="assignment-actions">
        <button class="action-btn btn-view" onclick="window.location.href='teacher-assignment-submissions.php';">
          <i class="fa fa-eye"></i> View Submissions
        </button>
        <button class="action-btn btn-edit" onclick="window.location.href='teacher-assignment-edit.php';">
          <i class="fa fa-edit"></i> Edit
        </button>
      </div>
    </div>

    <div class="assignment-card">
      <div class="assignment-header">
        <div>
          <div class="assignment-title">Midterm Project Report</div>
          <div class="assignment-class">Database Management - B.Tech IT Semester 3</div>
        </div>
        <span class="status-badge status-pending">Pending Review</span>
      </div>
      
      <div class="assignment-meta">
        <div class="meta-item">
          <i class="fa fa-calendar"></i>
          <span>Due: Mar 8, 2026</span>
        </div>
        <div class="meta-item">
          <i class="fa fa-clock"></i>
          <span>2 days overdue</span>
        </div>
        <div class="meta-item">
          <i class="fa fa-star"></i>
          <span>80 points</span>
        </div>
      </div>
      
      <div class="submission-stats">
        <div class="stat-box">
          <div class="stat-number">22</div>
          <div class="stat-label">Submitted</div>
        </div>
        <div class="stat-box">
          <div class="stat-number">6</div>
          <div class="stat-label">Pending</div>
        </div>
        <div class="stat-box">
          <div class="stat-number">0</div>
          <div class="stat-label">Graded</div>
        </div>
      </div>
      
      <div class="assignment-actions">
        <button class="action-btn btn-view" onclick="window.location.href='teacher-assignment-submissions.php';">
          <i class="fa fa-eye"></i> View Submissions
        </button>
        <button class="action-btn btn-edit" onclick="window.location.href='teacher-assignment-edit.php';">
          <i class="fa fa-edit"></i> Edit
        </button>
      </div>
    </div>

  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Teacher Portal</p>
</footer>

</body>
</html>
