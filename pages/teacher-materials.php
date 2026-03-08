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
    }
    .page-header h1 { margin: 0 0 10px 0; font-size: 28px; }
    .breadcrumb { opacity: 0.9; font-size: 14px; }
    .breadcrumb a { color: white; text-decoration: none; }
    .action-bar {
      display: flex; justify-content: space-between; align-items: center;
      margin-bottom: 30px; flex-wrap: wrap; gap: 15px;
    }
    .search-box {
      flex: 1; max-width: 400px;
    }
    .search-box input {
      width: 100%; padding: 12px 20px; border: 2px solid #dee2e6;
      border-radius: 6px; font-size: 14px;
    }
    .search-box input:focus {
      outline: none; border-color: #28a745;
    }
    .btn-upload {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white; padding: 12px 24px; border: none;
      border-radius: 6px; cursor: pointer; font-size: 14px;
      transition: all 0.3s; font-weight: 600;
    }
    .btn-upload:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(40,167,69,0.3);
    }
    .course-tabs {
      display: flex; gap: 10px; margin-bottom: 30px;
      overflow-x: auto; padding-bottom: 10px;
    }
    .course-tab {
      padding: 12px 24px; border: 2px solid #28a745; border-radius: 6px;
      background: white; color: #28a745; cursor: pointer;
      transition: all 0.3s; font-weight: 600; white-space: nowrap;
    }
    .course-tab:hover { background: #f0f0f0; }
    .course-tab.active { background: #28a745; color: white; }
    .materials-grid {
      display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
    }
    .material-card {
      background: white; border-radius: 10px; padding: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      transition: all 0.3s; cursor: pointer;
    }
    .material-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 20px rgba(40,167,69,0.2);
    }
    .file-icon {
      width: 60px; height: 60px; border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 28px; margin-bottom: 15px;
    }
    .icon-pdf { background: #dc3545; color: white; }
    .icon-doc { background: #0d6efd; color: white; }
    .icon-ppt { background: #fd7e14; color: white; }
    .icon-video { background: #6f42c1; color: white; }
    .icon-zip { background: #6c757d; color: white; }
    .material-title {
      font-size: 16px; font-weight: 600; color: #333;
      margin-bottom: 8px; line-height: 1.4;
    }
    .material-meta {
      display: flex; flex-direction: column; gap: 5px;
      color: #666; font-size: 13px; margin-bottom: 15px;
    }
    .material-actions {
      display: flex; gap: 8px; padding-top: 15px;
      border-top: 1px solid #dee2e6;
    }
    .btn-icon {
      flex: 1; padding: 8px; border: none; border-radius: 4px;
      cursor: pointer; font-size: 12px; transition: all 0.3s;
      background: #f8f9fa; color: #28a745;
    }
    .btn-icon:hover {
      background: #28a745; color: white;
    }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Course Materials - Upload and manage lecture notes, presentations, and resources</marquee>
</div>

<div class="container">
  <div class="page-header">
    <h1><i class="fa fa-book"></i> Course Materials</h1>
    <div class="breadcrumb">
      <a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Materials
    </div>
  </div>

  <div class="action-bar">
    <div class="search-box">
      <input type="text" placeholder="Search materials...">
    </div>
    <button class="btn-upload" onclick="alert('Upload material coming soon!');">
      <i class="fa fa-upload"></i> Upload Material
    </button>
  </div>

  <div class="course-tabs">
    <button class="course-tab active">All Materials (24)</button>
    <button class="course-tab">Programming Fundamentals (8)</button>
    <button class="course-tab">Database Management (6)</button>
    <button class="course-tab">Web Development (5)</button>
    <button class="course-tab">Data Structures (5)</button>
  </div>

  <div class="materials-grid">
    
    <div class="material-card">
      <div class="file-icon icon-pdf">
        <i class="fa fa-file-pdf"></i>
      </div>
      <div class="material-title">Introduction to Programming</div>
      <div class="material-meta">
        <span><i class="fa fa-book"></i> Programming Fundamentals</span>
        <span><i class="fa fa-calendar"></i> Uploaded: Mar 1, 2026</span>
        <span><i class="fa fa-download"></i> 45 downloads</span>
        <span><i class="fa fa-file"></i> 2.4 MB</span>
      </div>
      <div class="material-actions">
        <button class="btn-icon" onclick="alert('Download coming soon!');"><i class="fa fa-download"></i> Download</button>
        <button class="btn-icon" onclick="alert('Edit coming soon!');"><i class="fa fa-edit"></i> Edit</button>
        <button class="btn-icon" onclick="alert('Delete coming soon!');"><i class="fa fa-trash"></i> Delete</button>
      </div>
    </div>

    <div class="material-card">
      <div class="file-icon icon-ppt">
        <i class="fa fa-file-powerpoint"></i>
      </div>
      <div class="material-title">Variables and Data Types</div>
      <div class="material-meta">
        <span><i class="fa fa-book"></i> Programming Fundamentals</span>
        <span><i class="fa fa-calendar"></i> Uploaded: Mar 2, 2026</span>
        <span><i class="fa fa-download"></i> 38 downloads</span>
        <span><i class="fa fa-file"></i> 5.1 MB</span>
      </div>
      <div class="material-actions">
        <button class="btn-icon" onclick="alert('Download coming soon!');"><i class="fa fa-download"></i> Download</button>
        <button class="btn-icon" onclick="alert('Edit coming soon!');"><i class="fa fa-edit"></i> Edit</button>
        <button class="btn-icon" onclick="alert('Delete coming soon!');"><i class="fa fa-trash"></i> Delete</button>
      </div>
    </div>

    <div class="material-card">
      <div class="file-icon icon-pdf">
        <i class="fa fa-file-pdf"></i>
      </div>
      <div class="material-title">SQL Fundamentals Guide</div>
      <div class="material-meta">
        <span><i class="fa fa-book"></i> Database Management</span>
        <span><i class="fa fa-calendar"></i> Uploaded: Feb 28, 2026</span>
        <span><i class="fa fa-download"></i> 52 downloads</span>
        <span><i class="fa fa-file"></i> 3.8 MB</span>
      </div>
      <div class="material-actions">
        <button class="btn-icon" onclick="alert('Download coming soon!');"><i class="fa fa-download"></i> Download</button>
        <button class="btn-icon" onclick="alert('Edit coming soon!');"><i class="fa fa-edit"></i> Edit</button>
        <button class="btn-icon" onclick="alert('Delete coming soon!');"><i class="fa fa-trash"></i> Delete</button>
      </div>
    </div>

    <div class="material-card">
      <div class="file-icon icon-video">
        <i class="fa fa-video"></i>
      </div>
      <div class="material-title">Database Normalization Tutorial</div>
      <div class="material-meta">
        <span><i class="fa fa-book"></i> Database Management</span>
        <span><i class="fa fa-calendar"></i> Uploaded: Mar 3, 2026</span>
        <span><i class="fa fa-download"></i> 28 downloads</span>
        <span><i class="fa fa-file"></i> 45.2 MB</span>
      </div>
      <div class="material-actions">
        <button class="btn-icon" onclick="alert('Download coming soon!');"><i class="fa fa-download"></i> Download</button>
        <button class="btn-icon" onclick="alert('Edit coming soon!');"><i class="fa fa-edit"></i> Edit</button>
        <button class="btn-icon" onclick="alert('Delete coming soon!');"><i class="fa fa-trash"></i> Delete</button>
      </div>
    </div>

    <div class="material-card">
      <div class="file-icon icon-doc">
        <i class="fa fa-file-word"></i>
      </div>
      <div class="material-title">HTML5 and CSS3 Basics</div>
      <div class="material-meta">
        <span><i class="fa fa-book"></i> Web Development</span>
        <span><i class="fa fa-calendar"></i> Uploaded: Mar 4, 2026</span>
        <span><i class="fa fa-download"></i> 41 downloads</span>
        <span><i class="fa fa-file"></i> 1.9 MB</span>
      </div>
      <div class="material-actions">
        <button class="btn-icon" onclick="alert('Download coming soon!');"><i class="fa fa-download"></i> Download</button>
        <button class="btn-icon" onclick="alert('Edit coming soon!');"><i class="fa fa-edit"></i> Edit</button>
        <button class="btn-icon" onclick="alert('Delete coming soon!');"><i class="fa fa-trash"></i> Delete</button>
      </div>
    </div>

    <div class="material-card">
      <div class="file-icon icon-zip">
        <i class="fa fa-file-archive"></i>
      </div>
      <div class="material-title">JavaScript Code Examples</div>
      <div class="material-meta">
        <span><i class="fa fa-book"></i> Web Development</span>
        <span><i class="fa fa-calendar"></i> Uploaded: Mar 5, 2026</span>
        <span><i class="fa fa-download"></i> 35 downloads</span>
        <span><i class="fa fa-file"></i> 8.7 MB</span>
      </div>
      <div class="material-actions">
        <button class="btn-icon" onclick="alert('Download coming soon!');"><i class="fa fa-download"></i> Download</button>
        <button class="btn-icon" onclick="alert('Edit coming soon!');"><i class="fa fa-edit"></i> Edit</button>
        <button class="btn-icon" onclick="alert('Delete coming soon!');"><i class="fa fa-trash"></i> Delete</button>
      </div>
    </div>

    <div class="material-card">
      <div class="file-icon icon-pdf">
        <i class="fa fa-file-pdf"></i>
      </div>
      <div class="material-title">Arrays and Linked Lists</div>
      <div class="material-meta">
        <span><i class="fa fa-book"></i> Data Structures</span>
        <span><i class="fa fa-calendar"></i> Uploaded: Mar 6, 2026</span>
        <span><i class="fa fa-download"></i> 33 downloads</span>
        <span><i class="fa fa-file"></i> 2.1 MB</span>
      </div>
      <div class="material-actions">
        <button class="btn-icon" onclick="alert('Download coming soon!');"><i class="fa fa-download"></i> Download</button>
        <button class="btn-icon" onclick="alert('Edit coming soon!');"><i class="fa fa-edit"></i> Edit</button>
        <button class="btn-icon" onclick="alert('Delete coming soon!');"><i class="fa fa-trash"></i> Delete</button>
      </div>
    </div>

    <div class="material-card">
      <div class="file-icon icon-ppt">
        <i class="fa fa-file-powerpoint"></i>
      </div>
      <div class="material-title">Sorting Algorithms</div>
      <div class="material-meta">
        <span><i class="fa fa-book"></i> Data Structures</span>
        <span><i class="fa fa-calendar"></i> Uploaded: Mar 7, 2026</span>
        <span><i class="fa fa-download"></i> 29 downloads</span>
        <span><i class="fa fa-file"></i> 4.3 MB</span>
      </div>
      <div class="material-actions">
        <button class="btn-icon" onclick="alert('Download coming soon!');"><i class="fa fa-download"></i> Download</button>
        <button class="btn-icon" onclick="alert('Edit coming soon!');"><i class="fa fa-edit"></i> Edit</button>
        <button class="btn-icon" onclick="alert('Delete coming soon!');"><i class="fa fa-trash"></i> Delete</button>
      </div>
    </div>

  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Teacher Portal</p>
</footer>

</body>
</html>
