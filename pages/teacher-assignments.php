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
      display: flex; justify-content: space-between; align-items: center;
    }
    .page-header h1 { margin: 0 0 10px 0; font-size: 28px; }
    .breadcrumb { opacity: 0.9; font-size: 14px; }
    .breadcrumb a { color: white; text-decoration: none; }
    
    .btn-create {
      background: rgba(255,255,255,0.2); color: white;
      padding: 12px 24px; border: 2px solid white;
      border-radius: 6px; cursor: pointer; transition: all 0.3s;
    }
    .btn-create:hover {
      background: white; color: #28a745;
    }
    
    .assignment-grid {
      display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
      gap: 25px;
    }
    
    .assignment-card {
      background: white; border-radius: 10px; padding: 25px;
      box-shadow: 0 3px 15px rgba(0,0,0,0.1);
      transition: all 0.3s;
    }
    .assignment-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(40,167,69,0.2);
    }
    
    .assignment-header {
      display: flex; justify-content: space-between; align-items: start;
      margin-bottom: 15px;
    }
    .assignment-title {
      font-size: 18px; font-weight: 600; color: #333;
      margin-bottom: 5px;
    }
    .assignment-class {
      color: #666; font-size: 13px;
    }
    
    .status-badge {
      padding: 6px 12px; border-radius: 20px; font-size: 12px;
      font-weight: 600;
    }
    .status-active { background: #d4edda; color: #155724; }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-graded { background: #d1ecf1; color: #0c5460; }
    
    .assignment-info {
      margin: 15px 0;
    }
    .info-item {
      display: flex; align-items: center; gap: 10px;
      margin-bottom: 10px; color: #666; font-size: 14px;
    }
    .info-item i { color: #28a745; width: 20px; }
    
    .submission-stats {
      display: flex; justify-content: space-between;
      padding: 15px; background: #f8f9fa; border-radius: 6px;
      margin: 15px 0;
    }
    .stat-item {
      text-align: center;
    }
    .stat-number {
      font-size: 20px; font-weight: 600; color: #28a745;
    }
    .stat-label {
      font-size: 12px; color: #666;
    }
    
    .assignment-actions {
      display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
    }
    .btn {
      padding: 10px; border: none; border-radius: 6px;
      cursor: pointer; font-size: 13px; transition: all 0.3s;
      text-align: center;
    }
    .btn-primary {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white;
    }
    .btn-primary:hover {
      background: linear-gradient(135deg, #20c997, #28a745);
      transform: translateY(-2px);
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
  <marquee>Assignments - Create and manage student assignments</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <div>
      <h1><i class="fa fa-file-alt"></i> Assignments</h1>
      <div class="breadcrumb">
        <a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Assignments
      </div>
    </div>
    <button class="btn-create" onclick="alert('Create assignment form coming soon!');">
      <i class="fa fa-plus"></i> Create Assignment
    </button>
  </div>

  <div class="assignment-grid">
    
    <div class="assignment-card">
      <div class="assignment-header">
        <div>
          <div class="assignment-title">Database Design Project</div>
          <div class="assignment-class">Database Management - CS201</div>
        </div>
        <span class="status-badge status-active">Active</span>
      </div>
      
      <div class="assignment-info">
        <div class="info-item">
          <i class="fa fa-calendar"></i>
          <span>Due: March 15, 2026</span>
        </div>
        <div class="info-item">
          <i class="fa fa-clock"></i>
          <span>Posted: March 1, 2026</span>
        </div>
        <div class="info-item">
          <i class="fa fa-star"></i>
          <span>Total Points: 100</span>
        </div>
      </div>
      
      <div class="submission-stats">
        <div class="stat-item">
          <div class="stat-number">18</div>
          <div class="stat-label">Submitted</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">10</div>
          <div class="stat-label">Pending</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">12</div>
          <div class="stat-label">Graded</div>
        </div>
      </div>
      
      <div class="assignment-actions">
        <button class="btn btn-primary" onclick="alert('View submissions coming soon!');">
          <i class="fa fa-eye"></i> View Submissions
        </button>
        <button class="btn btn-outline" onclick="alert('Edit assignment coming soon!');">
          <i class="fa fa-edit"></i> Edit
        </button>
      </div>
    </div>

    <div class="assignment-card">
      <div class="assignment-header">
        <div>
          <div class="assignment-title">Sorting Algorithms Implementation</div>
          <div class="assignment-class">Data Structures - CS102</div>
        </div>
        <span class="status-badge status-graded">Graded</span>
      </div>
      
      <div class="assignment-info">
        <div class="info-item">
          <i class="fa fa-calendar"></i>
          <span>Due: March 5, 2026</span>
        </div>
        <div class="info-item">
          <i class="fa fa-clock"></i>
          <span>Posted: February 20, 2026</span>
        </div>
        <div class="info-item">
          <i class="fa fa-star"></i>
          <span>Total Points: 50</span>
        </div>
      </div>
      
      <div class="submission-stats">
        <div class="stat-item">
          <div class="stat-number">25</div>
          <div class="stat-label">Submitted</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">0</div>
          <div class="stat-label">Pending</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">25</div>
          <div class="stat-label">Graded</div>
        </div>
      </div>
      
      <div class="assignment-actions">
        <button class="btn btn-primary" onclick="alert('View submissions coming soon!');">
          <i class="fa fa-eye"></i> View Submissions
        </button>
        <button class="btn btn-outline" onclick="alert('Edit assignment coming soon!');">
          <i class="fa fa-edit"></i> Edit
        </button>
      </div>
    </div>

    <div class="assignment-card">
      <div class="assignment-header">
        <div>
          <div class="assignment-title">Responsive Website Design</div>
          <div class="assignment-class">Web Development - CS301</div>
        </div>
        <span class="status-badge status-active">Active</span>
      </div>
      
      <div class="assignment-info">
        <div class="info-item">
          <i class="fa fa-calendar"></i>
          <span>Due: March 20, 2026</span>
        </div>
        <div class="info-item">
          <i class="fa fa-clock"></i>
          <span>Posted: March 5, 2026</span>
        </div>
        <div class="info-item">
          <i class="fa fa-star"></i>
          <span>Total Points: 150</span>
        </div>
      </div>
      
      <div class="submission-stats">
        <div class="stat-item">
          <div class="stat-number">8</div>
          <div class="stat-label">Submitted</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">24</div>
          <div class="stat-label">Pending</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">5</div>
          <div class="stat-label">Graded</div>
        </div>
      </div>
      
      <div class="assignment-actions">
        <button class="btn btn-primary" onclick="alert('View submissions coming soon!');">
          <i class="fa fa-eye"></i> View Submissions
        </button>
        <button class="btn btn-outline" onclick="alert('Edit assignment coming soon!');">
          <i class="fa fa-edit"></i> Edit
        </button>
      </div>
    </div>

    <div class="assignment-card">
      <div class="assignment-header">
        <div>
          <div class="assignment-title">Python Functions Quiz</div>
          <div class="assignment-class">Programming Fundamentals - CS101</div>
        </div>
        <span class="status-badge status-pending">Upcoming</span>
      </div>
      
      <div class="assignment-info">
        <div class="info-item">
          <i class="fa fa-calendar"></i>
          <span>Due: March 25, 2026</span>
        </div>
        <div class="info-item">
          <i class="fa fa-clock"></i>
          <span>Posted: March 8, 2026</span>
        </div>
        <div class="info-item">
          <i class="fa fa-star"></i>
          <span>Total Points: 30</span>
        </div>
      </div>
      
      <div class="submission-stats">
        <div class="stat-item">
          <div class="stat-number">0</div>
          <div class="stat-label">Submitted</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">35</div>
          <div class="stat-label">Pending</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">0</div>
          <div class="stat-label">Graded</div>
        </div>
      </div>
      
      <div class="assignment-actions">
        <button class="btn btn-primary" onclick="alert('View submissions coming soon!');">
          <i class="fa fa-eye"></i> View Submissions
        </button>
        <button class="btn btn-outline" onclick="alert('Edit assignment coming soon!');">
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
