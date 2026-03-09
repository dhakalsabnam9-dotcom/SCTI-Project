<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header('Location: ../index.php');
    exit();
}
$fullName = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Assignments | SCTI Student Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
    .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
    
    .page-header {
      background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
      color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;
      box-shadow: 0 4px 15px rgba(111,66,193,0.2);
    }
    .page-header h1 { margin: 0 0 10px 0; font-size: 28px; }
    .breadcrumb { opacity: 1; font-size: 14px; background: transparent; padding: 0; }
    .breadcrumb a { color: white; text-decoration: none; }
    
    .filter-tabs {
      display: flex; gap: 10px; margin-bottom: 30px; flex-wrap: wrap;
    }
    .filter-tab {
      padding: 12px 24px; border: 2px solid #6f42c1; border-radius: 6px;
      background: white; color: #6f42c1; cursor: pointer;
      transition: all 0.3s; font-weight: 600;
    }
    .filter-tab:hover { background: #f0f0f0; }
    .filter-tab.active { background: #6f42c1; color: white; }
    
    .assignment-grid {
      display: grid; gap: 20px;
    }
    
    .assignment-card {
      background: white; border-radius: 10px; padding: 25px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      border-left: 5px solid #6f42c1;
      transition: all 0.3s;
    }
    .assignment-card:hover {
      transform: translateX(5px);
      box-shadow: 0 5px 20px rgba(111,66,193,0.2);
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
    .status-pending { background: #fff3cd; color: #856404; }
    .status-submitted { background: #d1ecf1; color: #0c5460; }
    .status-graded { background: #d4edda; color: #155724; }
    .status-overdue { background: #f8d7da; color: #721c24; }
    
    .assignment-meta {
      display: flex; gap: 20px; margin: 15px 0;
      flex-wrap: wrap;
    }
    .meta-item {
      display: flex; align-items: center; gap: 8px;
      color: #666; font-size: 14px;
    }
    .meta-item i { color: #6f42c1; }
    
    .assignment-description {
      color: #666; line-height: 1.6; margin: 15px 0;
    }
    
    .assignment-actions {
      display: flex; gap: 10px; margin-top: 15px;
    }
    .btn {
      padding: 10px 20px; border: none; border-radius: 6px;
      cursor: pointer; font-size: 14px; transition: all 0.3s;
      text-decoration: none; display: inline-block;
    }
    .btn-primary {
      background: linear-gradient(135deg, #6f42c1, #e83e8c);
      color: white;
    }
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(111,66,193,0.3);
    }
    .btn-outline {
      background: white; color: #6f42c1;
      border: 2px solid #6f42c1;
    }
    .btn-outline:hover {
      background: #6f42c1; color: white;
    }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Assignments - Stay on top of your coursework and submit on time</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <h1><i class="fa fa-tasks"></i> My Assignments</h1>
    <div class="breadcrumb">
      <a href="../dashboards/student-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Assignments
    </div>
  </div>

  <div class="filter-tabs">
    <button class="filter-tab active">All (8)</button>
    <button class="filter-tab">Pending (3)</button>
    <button class="filter-tab">Submitted (3)</button>
    <button class="filter-tab">Graded (2)</button>
  </div>

  <div class="assignment-grid">
    
    <div class="assignment-card" style="border-left-color: #dc3545;">
      <div class="assignment-header">
        <div>
          <div class="assignment-title">Database Design Project</div>
          <div class="assignment-course">Database Management - CS201</div>
        </div>
        <span class="status-badge status-overdue">Overdue</span>
      </div>
      <div class="assignment-meta">
        <div class="meta-item">
          <i class="fa fa-calendar"></i>
          <span>Due: Mar 5, 2026</span>
        </div>
        <div class="meta-item">
          <i class="fa fa-clock"></i>
          <span>2 days overdue</span>
        </div>
        <div class="meta-item">
          <i class="fa fa-star"></i>
          <span>20 points</span>
        </div>
      </div>
      <div class="assignment-description">
        Design a complete database schema for a library management system including ER diagrams, normalization, and SQL queries.
      </div>
      <div class="assignment-actions">
        <a href="#" class="btn btn-primary" onclick="alert('Submit assignment coming soon!'); return false;">
          <i class="fa fa-upload"></i> Submit Now
        </a>
        <a href="#" class="btn btn-outline" onclick="alert('View details coming soon!'); return false;">
          <i class="fa fa-eye"></i> View Details
        </a>
      </div>
    </div>

    <div class="assignment-card" style="border-left-color: #ffc107;">
      <div class="assignment-header">
        <div>
          <div class="assignment-title">Algorithm Analysis Report</div>
          <div class="assignment-course">Data Structures - CS102</div>
        </div>
        <span class="status-badge status-pending">Pending</span>
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
      <div class="assignment-description">
        Analyze time and space complexity of sorting algorithms (Bubble, Quick, Merge) with practical examples and benchmarks.
      </div>
      <div class="assignment-actions">
        <a href="#" class="btn btn-primary" onclick="alert('Submit assignment coming soon!'); return false;">
          <i class="fa fa-upload"></i> Submit
        </a>
        <a href="#" class="btn btn-outline" onclick="alert('View details coming soon!'); return false;">
          <i class="fa fa-eye"></i> View Details
        </a>
      </div>
    </div>

    <div class="assignment-card" style="border-left-color: #ffc107;">
      <div class="assignment-header">
        <div>
          <div class="assignment-title">Responsive Website Project</div>
          <div class="assignment-course">Web Development - CS301</div>
        </div>
        <span class="status-badge status-pending">Pending</span>
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
      <div class="assignment-description">
        Create a fully responsive website using HTML5, CSS3, and JavaScript with mobile-first approach and modern design principles.
      </div>
      <div class="assignment-actions">
        <a href="#" class="btn btn-primary" onclick="alert('Submit assignment coming soon!'); return false;">
          <i class="fa fa-upload"></i> Submit
        </a>
        <a href="#" class="btn btn-outline" onclick="alert('View details coming soon!'); return false;">
          <i class="fa fa-eye"></i> View Details
        </a>
      </div>
    </div>

    <div class="assignment-card" style="border-left-color: #0dcaf0;">
      <div class="assignment-header">
        <div>
          <div class="assignment-title">OOP Concepts Assignment</div>
          <div class="assignment-course">Programming Fundamentals - CS101</div>
        </div>
        <span class="status-badge status-submitted">Submitted</span>
      </div>
      <div class="assignment-meta">
        <div class="meta-item">
          <i class="fa fa-calendar"></i>
          <span>Submitted: Mar 3, 2026</span>
        </div>
        <div class="meta-item">
          <i class="fa fa-check"></i>
          <span>On time</span>
        </div>
        <div class="meta-item">
          <i class="fa fa-star"></i>
          <span>10 points</span>
        </div>
      </div>
      <div class="assignment-description">
        Implement inheritance, polymorphism, and encapsulation concepts with practical examples in your preferred programming language.
      </div>
      <div class="assignment-actions">
        <a href="#" class="btn btn-outline" onclick="alert('View submission coming soon!'); return false;">
          <i class="fa fa-file"></i> View Submission
        </a>
      </div>
    </div>

    <div class="assignment-card" style="border-left-color: #28a745;">
      <div class="assignment-header">
        <div>
          <div class="assignment-title">SQL Queries Practice</div>
          <div class="assignment-course">Database Management - CS201</div>
        </div>
        <span class="status-badge status-graded">Graded: 18/20</span>
      </div>
      <div class="assignment-meta">
        <div class="meta-item">
          <i class="fa fa-calendar"></i>
          <span>Submitted: Feb 28, 2026</span>
        </div>
        <div class="meta-item">
          <i class="fa fa-trophy"></i>
          <span>Grade: A-</span>
        </div>
        <div class="meta-item">
          <i class="fa fa-comment"></i>
          <span>Feedback available</span>
        </div>
      </div>
      <div class="assignment-description">
        Write complex SQL queries including joins, subqueries, and aggregate functions for given scenarios.
      </div>
      <div class="assignment-actions">
        <a href="#" class="btn btn-primary" onclick="alert('View feedback coming soon!'); return false;">
          <i class="fa fa-comment-dots"></i> View Feedback
        </a>
        <a href="#" class="btn btn-outline" onclick="alert('View submission coming soon!'); return false;">
          <i class="fa fa-file"></i> View Submission
        </a>
      </div>
    </div>

  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Student Portal</p>
</footer>

</body>
</html>
