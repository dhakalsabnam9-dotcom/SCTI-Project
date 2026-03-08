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
  <title>Manage Assignments | SCTI Teacher Portal</title>
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
    .action-bar {
      display: flex; justify-content: space-between; align-items: center;
      margin-bottom: 30px; flex-wrap: wrap; gap: 15px;
    }
    .filter-tabs {
      display: flex; gap: 10px; flex-wrap: wrap;
    }
    .filter-tab {
      padding: 10px 20px; border: 2px solid #28a745; border-radius: 6px;
      background: white; color: #28a745; cursor: pointer;
      transition: all 0.3s; font-weight: 600;
    }
    .filter-tab:hover { background: #f0f0f0; }
    .filter-tab.active { background: #28a745; color: white; }
    .btn-create {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white; padding: 12px 24px; border: none;
      border-radius: 6px; cursor: pointer; font-size: 14px;
      transition: all 0.3s; font-weight: 600;
    }
    .btn-create:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(40,167,69,0.3);
    }
    .assignment-grid { display: grid; gap: 20px; }
    .assignment-card {
      background: white; border-radius: 10px; padding: 25px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      border-left: 5px solid #28a745;
      transition: all 0.3s;
    }
    .assignment-card:hover {
      transform: translateX(5px);
      box-shadow: 0 5px 20px rgba(40,167,69,0.2);
    }
    .assignment-header {
      display: flex; justify-content: space-between; align-items: start;
      margin-bottom: 15px;
    }
    .assignment-title {
      font-size: 20px; font-weight: 600; color: #333;
      margin-bottom: 5px;
    }
    .assignment-course {
      color: #666; font-size: 14px;
    }
    .status-badge {
      padding: 6px 12px; border-radius: 20px; font-size: 12px;
      font-weight: 600;
    }
    .status-active { background: #d4edda; color: #155724; }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-closed { background: #d6d8db; color: #383d41; }
    .assignment-meta {
      display: flex; gap: 20px; margin: 15px 0;
      flex-wrap: wrap;
    }
    .meta-item {
      display: flex; align-items: center; gap: 8px;
      color: #666; font-size: 14px;
    }
    .meta-item i { color: #28a745; }
    .submission-stats {
      display: flex; gap: 20px; margin: 15px 0;
      padding: 15px; background: #f8f9fa; border-radius: 6px;
    }
    .stat-item {
      flex: 1; text-align: center;
    }
    .stat-number {
      font-size: 24px; font-weight: 700; color: #28a745;
    }
    .stat-label {
      font-size: 12px; color: #666; margin-top: 5px;
    }
    .assignment-actions {
      display: flex; gap: 10px; margin-top: 15px; flex-wrap: wrap;
    }
    .btn {
      padding: 10px 20px; border: none; border-radius: 6px;
      cursor: pointer; font-size: 14px; transition: all 0.3s;
      text-decoration: none; display: inline-block;
    }
    .btn-primary {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white;
    }
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(40,167,69,0.3);
    }
    .btn-outline {
      background: white; color: #28a745;
      border: 2px solid #28a745;
    }
    .btn-outline:hover {
      background: #28a745; color: white;
    }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Assignment Management - Create, review, and grade student assignments</marquee>
</div>

<div class="container">
  <div class="page-header">
    <h1><i class="fa fa-file-alt"></i> Manage Assignments</h1>
    <div class="breadcrumb">
      <a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Assignments
    </div>
  </div>

  <div class="action-bar">
    <div class="filter-tabs">
      <button class="filter-tab active">All (12)</button>
      <button class="filter-tab">Active (8)</button>
      <button class="filter-tab">Pending Review (4)</button>
      <button class="filter-tab">Closed (0)</button>
    </div>
    <button class="btn-create" onclick="alert('Create assignment coming soon!');">
      <i class="fa fa-plus"></i> Create Assignment
    </button>
  </div>

  <div class="assignment-grid">
    
    <div class="assignment-card" style="border-left-color: #ffc107;">
      <div class="assignment-header">
        <div>
          <div class="assignment-title">Database Design Project</div>
          <div class="assignment-course">Database Management - CS201</div>
        </div>
        <span class="status-badge status-pending">Pending Review</span>
      </div>
      <div class="assignment-meta">
        <div class="meta-item">
          <i class="fa fa-calendar"></i>
          <span>Due: Mar 5, 2026</span>
        </div>
        <div class="meta-item">
          <i class="fa fa-clock"></i>
          <span>Overdue by 2 days</span>
        </div>
        <div class="meta-item">
          <i class="fa fa-star"></i>
          <span>20 points</span>
        </div>
      </div>
      <div class="submission-stats">
        <div class="stat-item">
          <div class="stat-number">28</div>
          <div class="stat-label">Total Students</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">22</div>
          <div class="stat-label">Submitted</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">10</div>
          <div class="stat-label">Graded</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">12</div>
          <div class="stat-label">Pending</div>
        </div>
      </div>
      <div class="assignment-actions">
        <a href="#" class="btn btn-primary" onclick="alert('Review submissions coming soon!'); return false;">
          <i class="fa fa-check-circle"></i> Review Submissions
        </a>
        <a href="#" class="btn btn-outline" onclick="alert('Edit assignment coming soon!'); return false;">
          <i class="fa fa-edit"></i> Edit
        </a>
        <a href="#" class="btn btn-outline" onclick="alert('View details coming soon!'); return false;">
          <i class="fa fa-eye"></i> Details
        </a>
      </div>
    </div>

    <div class="assignment-card" style="border-left-color: #28a745;">
      <div class="assignment-header">
        <div>
          <div class="assignment-title">Algorithm Analysis Report</div>
          <div class="assignment-course">Data Structures - CS102</div>
        </div>
        <span class="status-badge status-active">Active</span>
      </div>
      <div class="assignment-meta">
        <div class="meta-item">
          <i class="fa fa-calendar"></i>
          <span>Due: Mar 12, 2026</span>
        </div>
        <div class="meta-item">
          <i class="fa fa-clock"></i>
          <span>4 days left</span>
        </div>
        <div class="meta-item">
          <i class="fa fa-star"></i>
          <span>15 points</span>
        </div>
      </div>
      <div class="submission-stats">
        <div class="stat-item">
          <div class="stat-number">25</div>
          <div class="stat-label">Total Students</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">8</div>
          <div class="stat-label">Submitted</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">5</div>
          <div class="stat-label">Graded</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">17</div>
          <div class="stat-label">Pending</div>
        </div>
      </div>
      <div class="assignment-actions">
        <a href="#" class="btn btn-primary" onclick="alert('Review submissions coming soon!'); return false;">
          <i class="fa fa-check-circle"></i> Review Submissions
        </a>
        <a href="#" class="btn btn-outline" onclick="alert('Edit assignment coming soon!'); return false;">
          <i class="fa fa-edit"></i> Edit
        </a>
        <a href="#" class="btn btn-outline" onclick="alert('View details coming soon!'); return false;">
          <i class="fa fa-eye"></i> Details
        </a>
      </div>
    </div>

    <div class="assignment-card" style="border-left-color: #28a745;">
      <div class="assignment-header">
        <div>
          <div class="assignment-title">Responsive Website Project</div>
          <div class="assignment-course">Web Development - CS301</div>
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
          <span>7 days left</span>
        </div>
        <div class="meta-item">
          <i class="fa fa-star"></i>
          <span>25 points</span>
        </div>
      </div>
      <div class="submission-stats">
        <div class="stat-item">
          <div class="stat-number">32</div>
          <div class="stat-label">Total Students</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">5</div>
          <div class="stat-label">Submitted</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">2</div>
          <div class="stat-label">Graded</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">27</div>
          <div class="stat-label">Pending</div>
        </div>
      </div>
      <div class="assignment-actions">
        <a href="#" class="btn btn-primary" onclick="alert('Review submissions coming soon!'); return false;">
          <i class="fa fa-check-circle"></i> Review Submissions
        </a>
        <a href="#" class="btn btn-outline" onclick="alert('Edit assignment coming soon!'); return false;">
          <i class="fa fa-edit"></i> Edit
        </a>
        <a href="#" class="btn btn-outline" onclick="alert('View details coming soon!'); return false;">
          <i class="fa fa-eye"></i> Details
        </a>
      </div>
    </div>

    <div class="assignment-card" style="border-left-color: #ffc107;">
      <div class="assignment-header">
        <div>
          <div class="assignment-title">OOP Concepts Assignment</div>
          <div class="assignment-course">Programming Fundamentals - CS101</div>
        </div>
        <span class="status-badge status-pending">Pending Review</span>
      </div>
      <div class="assignment-meta">
        <div class="meta-item">
          <i class="fa fa-calendar"></i>
          <span>Due: Mar 3, 2026</span>
        </div>
        <div class="meta-item">
          <i class="fa fa-clock"></i>
          <span>Closed</span>
        </div>
        <div class="meta-item">
          <i class="fa fa-star"></i>
          <span>10 points</span>
        </div>
      </div>
      <div class="submission-stats">
        <div class="stat-item">
          <div class="stat-number">35</div>
          <div class="stat-label">Total Students</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">33</div>
          <div class="stat-label">Submitted</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">25</div>
          <div class="stat-label">Graded</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">8</div>
          <div class="stat-label">Pending</div>
        </div>
      </div>
      <div class="assignment-actions">
        <a href="#" class="btn btn-primary" onclick="alert('Review submissions coming soon!'); return false;">
          <i class="fa fa-check-circle"></i> Review Submissions
        </a>
        <a href="#" class="btn btn-outline" onclick="alert('Edit assignment coming soon!'); return false;">
          <i class="fa fa-edit"></i> Edit
        </a>
        <a href="#" class="btn btn-outline" onclick="alert('View details coming soon!'); return false;">
          <i class="fa fa-eye"></i> Details
        </a>
      </div>
    </div>

  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Teacher Portal</p>
</footer>

</body>
</html>
