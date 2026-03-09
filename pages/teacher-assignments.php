<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../index.php');
    exit();
}
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
    }
    .page-header h1 { margin: 0 0 10px 0; font-size: 28px; }
    .breadcrumb { opacity: 1; font-size: 14px; background: transparent; padding: 0; }
    .breadcrumb a { color: white; text-decoration: none; }
    .assignment-grid { display: grid; gap: 20px; }
    .assignment-card {
      background: white; border-radius: 10px; padding: 25px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-left: 5px solid #28a745;
    }
    .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; }
    .btn-primary { background: linear-gradient(135deg, #28a745, #20c997); color: white; }
  </style>
</head>
<body>
<div class="top-header"><marquee>Assignment Management</marquee></div>
<div class="container">
  <div class="page-header">
    <h1><i class="fa fa-tasks"></i> Assignments</h1>
    <div class="breadcrumb">
      <a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Assignments
    </div>
  </div>
  <div class="assignment-grid">
    <div class="assignment-card">
      <h3>Database Design Project</h3>
      <p>12 submissions pending review</p>
      <button class="btn btn-primary" onclick="alert('Review coming soon!');">Review Submissions</button>
    </div>
  </div>
</div>
</body>
</html>
