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
  <title>Course Materials | SCTI Teacher Portal</title>
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
    
    .btn-upload {
      background: rgba(255,255,255,0.2); color: white;
      padding: 12px 24px; border: 2px solid white;
      border-radius: 6px; cursor: pointer; transition: all 0.3s;
    }
    .btn-upload:hover {
      background: white; color: #28a745;
    }
    
    .filter-bar {
      background: white; padding: 20px; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px;
      display: flex; gap: 15px;
    }
    .filter-select {
      padding: 10px 15px; border: 2px solid #dee2e6;
      border-radius: 6px; font-size: 14px;
    }
    
    .materials-grid {
      display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 20px;
    }
    
    .material-card {
      background: white; border-radius: 10px; padding: 20px;
      box-shadow: 0 3px 15px rgba(0,0,0,0.1);
      transition: all 0.3s;
    }
    .material-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(40,167,69,0.2);
    }
    
    .file-icon {
      width: 60px; height: 60px; border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 28px; color: white; margin-bottom: 15px;
    }
    .icon-pdf { background: linear-gradient(135deg, #dc3545, #c82333); }
    .icon-doc { background: linear-gradient(135deg, #004080, #0059b3); }
    .icon-ppt { background: linear-gradient(135deg, #fd7e14, #e8590c); }
    .icon-video { background: linear-gradient(135deg, #6f42c1, #5a32a3); }
    
    .material-title {
      font-size: 16px; font-weight: 600; color: #333;
      margin-bottom: 8px;
    }
    .material-class {
      color: #666; font-size: 13px; margin-bottom: 12px;
    }
    
    .material-meta {
      display: flex; justify-content: space-between;
      padding: 12px 0; border-top: 1px solid #dee2e6;
      border-bottom: 1px solid #dee2e6; margin: 12px 0;
      font-size: 13px; color: #666;
    }
    
    .material-actions {
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
    .btn-danger {
      background: white; color: #dc3545;
      border: 2px solid #dc3545;
    }
    .btn-danger:hover {
      background: #dc3545; color: white;
    }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Course Materials - Upload and manage learning resources</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <div>
      <h1><i class="fa fa-book"></i> Course Materials</h1>
      <div class="breadcrumb">
        <a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Course Materials
      </div>
    </div>
    <button class="btn-upload" onclick="alert('Upload material form coming soon!');">
      <i class="fa fa-upload"></i> Upload Material
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
      <option>All Types</option>
      <option>Lecture Notes</option>
      <option>Assignments</option>
      <option>Videos</option>
      <option>References</option>
    </select>
  </div>

  <div class="materials-grid">
    
    <div class="material-card">
      <div class="file-icon icon-pdf">
        <i class="fa fa-file-pdf"></i>
      </div>
      <div class="material-title">Introduction to Python Programming</div>
      <div class="material-class">Programming Fundamentals - CS101</div>
      <div class="material-meta">
        <span><i class="fa fa-calendar"></i> Mar 1, 2026</span>
        <span><i class="fa fa-download"></i> 45 downloads</span>
      </div>
      <div class="material-actions">
        <button class="btn btn-primary" onclick="alert('Download coming soon!');">
          <i class="fa fa-download"></i> Download
        </button>
        <button class="btn btn-danger" onclick="alert('Delete coming soon!');">
          <i class="fa fa-trash"></i> Delete
        </button>
      </div>
    </div>

    <div class="material-card">
      <div class="file-icon icon-doc">
        <i class="fa fa-file-word"></i>
      </div>
      <div class="material-title">SQL Queries Tutorial</div>
      <div class="material-class">Database Management - CS201</div>
      <div class="material-meta">
        <span><i class="fa fa-calendar"></i> Feb 28, 2026</span>
        <span><i class="fa fa-download"></i> 32 downloads</span>
      </div>
      <div class="material-actions">
        <button class="btn btn-primary" onclick="alert('Download coming soon!');">
          <i class="fa fa-download"></i> Download
        </button>
        <button class="btn btn-danger" onclick="alert('Delete coming soon!');">
          <i class="fa fa-trash"></i> Delete
        </button>
      </div>
    </div>

    <div class="material-card">
      <div class="file-icon icon-ppt">
        <i class="fa fa-file-powerpoint"></i>
      </div>
      <div class="material-title">HTML & CSS Basics</div>
      <div class="material-class">Web Development - CS301</div>
      <div class="material-meta">
        <span><i class="fa fa-calendar"></i> Mar 5, 2026</span>
        <span><i class="fa fa-download"></i> 28 downloads</span>
      </div>
      <div class="material-actions">
        <button class="btn btn-primary" onclick="alert('Download coming soon!');">
          <i class="fa fa-download"></i> Download
        </button>
        <button class="btn btn-danger" onclick="alert('Delete coming soon!');">
          <i class="fa fa-trash"></i> Delete
        </button>
      </div>
    </div>

    <div class="material-card">
      <div class="file-icon icon-video">
        <i class="fa fa-video"></i>
      </div>
      <div class="material-title">Linked Lists Explained</div>
      <div class="material-class">Data Structures - CS102</div>
      <div class="material-meta">
        <span><i class="fa fa-calendar"></i> Mar 3, 2026</span>
        <span><i class="fa fa-eye"></i> 52 views</span>
      </div>
      <div class="material-actions">
        <button class="btn btn-primary" onclick="alert('Watch video coming soon!');">
          <i class="fa fa-play"></i> Watch
        </button>
        <button class="btn btn-danger" onclick="alert('Delete coming soon!');">
          <i class="fa fa-trash"></i> Delete
        </button>
      </div>
    </div>

    <div class="material-card">
      <div class="file-icon icon-pdf">
        <i class="fa fa-file-pdf"></i>
      </div>
      <div class="material-title">Database Normalization Guide</div>
      <div class="material-class">Database Management - CS201</div>
      <div class="material-meta">
        <span><i class="fa fa-calendar"></i> Feb 25, 2026</span>
        <span><i class="fa fa-download"></i> 38 downloads</span>
      </div>
      <div class="material-actions">
        <button class="btn btn-primary" onclick="alert('Download coming soon!');">
          <i class="fa fa-download"></i> Download
        </button>
        <button class="btn btn-danger" onclick="alert('Delete coming soon!');">
          <i class="fa fa-trash"></i> Delete
        </button>
      </div>
    </div>

    <div class="material-card">
      <div class="file-icon icon-doc">
        <i class="fa fa-file-word"></i>
      </div>
      <div class="material-title">JavaScript ES6 Features</div>
      <div class="material-class">Web Development - CS301</div>
      <div class="material-meta">
        <span><i class="fa fa-calendar"></i> Mar 7, 2026</span>
        <span><i class="fa fa-download"></i> 24 downloads</span>
      </div>
      <div class="material-actions">
        <button class="btn btn-primary" onclick="alert('Download coming soon!');">
          <i class="fa fa-download"></i> Download
        </button>
        <button class="btn btn-danger" onclick="alert('Delete coming soon!');">
          <i class="fa fa-trash"></i> Delete
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
