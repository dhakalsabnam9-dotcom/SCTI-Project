<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../index.php'); exit();
}
$fullName = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Teacher';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Course Materials | SCTI Teacher Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; }
    .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
    .page-header {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;
      display: flex; justify-content: space-between; align-items: center;
      box-shadow: 0 4px 15px rgba(40,167,69,0.2);
    }
    .page-header h1 { margin: 0 0 8px 0; font-size: 28px; }
    .breadcrumb { background: transparent !important; padding: 0; font-size: 14px; }
    .breadcrumb a { color: white; text-decoration: none; }
    .btn-upload {
      background: rgba(255,255,255,0.2); color: white;
      padding: 12px 24px; border: 2px solid white; border-radius: 6px;
      cursor: pointer; font-size: 14px; transition: all 0.3s;
      text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-upload:hover { background: white; color: #28a745; }
    .filter-bar {
      background: white; padding: 20px; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 25px;
      display: flex; gap: 15px; flex-wrap: wrap; align-items: center;
    }
    .filter-select, .search-input {
      padding: 10px 15px; border: 2px solid #dee2e6;
      border-radius: 6px; font-size: 14px;
    }
    .search-input { flex: 1; min-width: 220px; }
    .materials-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
    .material-card {
      background: white; border-radius: 10px; overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); transition: all 0.3s;
    }
    .material-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(40,167,69,0.2); }
    .material-header {
      padding: 20px; display: flex; align-items: center; gap: 15px;
      border-bottom: 1px solid #f0f0f0;
    }
    .file-icon {
      width: 50px; height: 50px; border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 22px; color: white; flex-shrink: 0;
    }
    .icon-pdf { background: linear-gradient(135deg, #dc3545, #c82333); }
    .icon-doc { background: linear-gradient(135deg, #004080, #0059b3); }
    .icon-ppt { background: linear-gradient(135deg, #fd7e14, #e55a00); }
    .icon-zip { background: linear-gradient(135deg, #6f42c1, #5a32a3); }
    .icon-video { background: linear-gradient(135deg, #e83e8c, #c2185b); }
    .material-title { font-weight: 600; color: #333; font-size: 15px; margin-bottom: 4px; }
    .material-subject { color: #666; font-size: 13px; }
    .material-body { padding: 15px 20px; }
    .material-meta { display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 12px; }
    .meta-tag {
      display: flex; align-items: center; gap: 5px;
      color: #666; font-size: 12px;
    }
    .meta-tag i { color: #28a745; }
    .material-actions { display: flex; gap: 8px; }
    .btn-sm {
      flex: 1; padding: 8px; border: none; border-radius: 5px;
      cursor: pointer; font-size: 13px; transition: all 0.3s;
      display: flex; align-items: center; justify-content: center; gap: 5px;
    }
    .btn-download { background: #28a745; color: white; }
    .btn-download:hover { background: #20c997; }
    .btn-delete { background: #f8d7da; color: #dc3545; }
    .btn-delete:hover { background: #dc3545; color: white; }
  </style>
</head>
<body>
<div class="top-header"><marquee>Course Materials - Upload and manage learning resources for your students</marquee></div>
<div class="container">
  <div class="page-header">
    <div>
      <h1><i class="fa fa-book-open"></i> Course Materials</h1>
      <div class="breadcrumb">
        <a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Course Materials
      </div>
    </div>
    <button class="btn-upload" onclick="alert('Upload feature coming soon!');">
      <i class="fa fa-upload"></i> Upload Material
    </button>
  </div>

  <div class="filter-bar">
    <select class="filter-select">
      <option>All Subjects</option>
      <option>Programming Fundamentals</option>
      <option>Database Management</option>
      <option>Web Development</option>
      <option>Data Structures</option>
    </select>
    <select class="filter-select">
      <option>All Types</option>
      <option>PDF</option><option>Document</option>
      <option>Presentation</option><option>Video</option><option>Archive</option>
    </select>
    <input type="text" class="search-input" placeholder="Search materials...">
  </div>

  <div class="materials-grid">
    <div class="material-card">
      <div class="material-header">
        <div class="file-icon icon-pdf"><i class="fa fa-file-pdf"></i></div>
        <div><div class="material-title">Chapter 1 - Intro to Programming</div><div class="material-subject">Programming Fundamentals</div></div>
      </div>
      <div class="material-body">
        <div class="material-meta">
          <span class="meta-tag"><i class="fa fa-file"></i> PDF, 2.4 MB</span>
          <span class="meta-tag"><i class="fa fa-calendar"></i> Mar 1, 2026</span>
          <span class="meta-tag"><i class="fa fa-download"></i> 28 downloads</span>
        </div>
        <div class="material-actions">
          <button class="btn-sm btn-download"><i class="fa fa-download"></i> Download</button>
          <button class="btn-sm btn-delete"><i class="fa fa-trash"></i> Delete</button>
        </div>
      </div>
    </div>

    <div class="material-card">
      <div class="material-header">
        <div class="file-icon icon-ppt"><i class="fa fa-file-powerpoint"></i></div>
        <div><div class="material-title">Database Normalization Slides</div><div class="material-subject">Database Management</div></div>
      </div>
      <div class="material-body">
        <div class="material-meta">
          <span class="meta-tag"><i class="fa fa-file"></i> PPT, 5.1 MB</span>
          <span class="meta-tag"><i class="fa fa-calendar"></i> Mar 3, 2026</span>
          <span class="meta-tag"><i class="fa fa-download"></i> 22 downloads</span>
        </div>
        <div class="material-actions">
          <button class="btn-sm btn-download"><i class="fa fa-download"></i> Download</button>
          <button class="btn-sm btn-delete"><i class="fa fa-trash"></i> Delete</button>
        </div>
      </div>
    </div>

    <div class="material-card">
      <div class="material-header">
        <div class="file-icon icon-doc"><i class="fa fa-file-word"></i></div>
        <div><div class="material-title">HTML & CSS Reference Guide</div><div class="material-subject">Web Development</div></div>
      </div>
      <div class="material-body">
        <div class="material-meta">
          <span class="meta-tag"><i class="fa fa-file"></i> DOC, 1.8 MB</span>
          <span class="meta-tag"><i class="fa fa-calendar"></i> Mar 5, 2026</span>
          <span class="meta-tag"><i class="fa fa-download"></i> 30 downloads</span>
        </div>
        <div class="material-actions">
          <button class="btn-sm btn-download"><i class="fa fa-download"></i> Download</button>
          <button class="btn-sm btn-delete"><i class="fa fa-trash"></i> Delete</button>
        </div>
      </div>
    </div>

    <div class="material-card">
      <div class="material-header">
        <div class="file-icon icon-zip"><i class="fa fa-file-archive"></i></div>
        <div><div class="material-title">Data Structures Lab Files</div><div class="material-subject">Data Structures</div></div>
      </div>
      <div class="material-body">
        <div class="material-meta">
          <span class="meta-tag"><i class="fa fa-file"></i> ZIP, 3.2 MB</span>
          <span class="meta-tag"><i class="fa fa-calendar"></i> Mar 7, 2026</span>
          <span class="meta-tag"><i class="fa fa-download"></i> 19 downloads</span>
        </div>
        <div class="material-actions">
          <button class="btn-sm btn-download"><i class="fa fa-download"></i> Download</button>
          <button class="btn-sm btn-delete"><i class="fa fa-trash"></i> Delete</button>
        </div>
      </div>
    </div>

    <div class="material-card">
      <div class="material-header">
        <div class="file-icon icon-video"><i class="fa fa-file-video"></i></div>
        <div><div class="material-title">SQL Joins Tutorial Video</div><div class="material-subject">Database Management</div></div>
      </div>
      <div class="material-body">
        <div class="material-meta">
          <span class="meta-tag"><i class="fa fa-file"></i> MP4, 120 MB</span>
          <span class="meta-tag"><i class="fa fa-calendar"></i> Mar 8, 2026</span>
          <span class="meta-tag"><i class="fa fa-download"></i> 15 downloads</span>
        </div>
        <div class="material-actions">
          <button class="btn-sm btn-download"><i class="fa fa-download"></i> Download</button>
          <button class="btn-sm btn-delete"><i class="fa fa-trash"></i> Delete</button>
        </div>
      </div>
    </div>

    <div class="material-card">
      <div class="material-header">
        <div class="file-icon icon-pdf"><i class="fa fa-file-pdf"></i></div>
        <div><div class="material-title">JavaScript Basics Notes</div><div class="material-subject">Web Development</div></div>
      </div>
      <div class="material-body">
        <div class="material-meta">
          <span class="meta-tag"><i class="fa fa-file"></i> PDF, 1.5 MB</span>
          <span class="meta-tag"><i class="fa fa-calendar"></i> Mar 10, 2026</span>
          <span class="meta-tag"><i class="fa fa-download"></i> 26 downloads</span>
        </div>
        <div class="material-actions">
          <button class="btn-sm btn-download"><i class="fa fa-download"></i> Download</button>
          <button class="btn-sm btn-delete"><i class="fa fa-trash"></i> Delete</button>
        </div>
      </div>
    </div>
  </div>
</div>
<footer class="footer" style="margin-top:30px;"><p>© 2025 SCTI - Teacher Portal</p></footer>
</body>
</html>
