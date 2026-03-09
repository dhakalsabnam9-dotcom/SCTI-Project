<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Notices | SCTI Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
    .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
    
    .page-header {
      background: linear-gradient(135deg, #004080 0%, #0059b3 100%);
      color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;
      box-shadow: 0 4px 15px rgba(0,64,128,0.2);
      display: flex; justify-content: space-between; align-items: center;
    }
    .page-header h1 { margin: 0; font-size: 28px; }
    .breadcrumb { opacity: 1; font-size: 14px; background: transparent; padding: 0; margin-top: 8px; }
    .breadcrumb a { color: white; text-decoration: none; }
    .breadcrumb a:hover { text-decoration: underline; }
    
    .header-actions { display: flex; gap: 10px; }
    .btn {
      padding: 12px 24px; border: none; border-radius: 6px;
      cursor: pointer; font-size: 14px; transition: all 0.3s;
      text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-primary {
      background: white; color: #004080; font-weight: 600;
    }
    .btn-primary:hover {
      background: #f0f0f0; transform: translateY(-2px);
    }
    .btn-success {
      background: #28a745; color: white;
    }
    .btn-success:hover {
      background: #218838; transform: translateY(-2px);
    }
    
    .stats-grid {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px; margin-bottom: 30px;
    }
    .stat-card {
      background: white; padding: 20px; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      display: flex; align-items: center; gap: 15px;
    }
    .stat-icon {
      width: 50px; height: 50px; border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 24px; color: white;
    }
    .stat-icon.blue { background: linear-gradient(135deg, #004080, #0059b3); }
    .stat-icon.green { background: linear-gradient(135deg, #28a745, #20c997); }
    .stat-icon.orange { background: linear-gradient(135deg, #fd7e14, #ffc107); }
    .stat-icon.red { background: linear-gradient(135deg, #dc3545, #c82333); }
    .stat-info h3 { margin: 0; font-size: 28px; color: #004080; }
    .stat-info p { margin: 5px 0 0 0; color: #666; font-size: 13px; }
    
    .content-card {
      background: white; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 25px;
    }
    
    .filters-section {
      display: flex; gap: 15px; margin-bottom: 25px; flex-wrap: wrap;
    }
    .search-box {
      flex: 1; min-width: 250px; position: relative;
    }
    .search-box input {
      width: 100%; padding: 12px 40px 12px 15px;
      border: 2px solid #e0e0e0; border-radius: 6px;
      font-size: 14px; transition: all 0.3s;
    }
    .search-box input:focus {
      outline: none; border-color: #004080;
    }
    .search-box i {
      position: absolute; right: 15px; top: 50%;
      transform: translateY(-50%); color: #999;
    }
    
    .filter-select {
      padding: 12px 15px; border: 2px solid #e0e0e0;
      border-radius: 6px; font-size: 14px; cursor: pointer;
    }
    
    .notice-card {
      background: white; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 20px; overflow: hidden;
      border-left: 5px solid #004080;
      transition: all 0.3s;
    }
    .notice-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    
    .notice-card.urgent {
      border-left-color: #dc3545;
    }
    .notice-card.important {
      border-left-color: #ffc107;
    }
    
    .notice-header {
      display: flex; justify-content: space-between;
      align-items: start; padding: 20px 25px;
      background: #f8f9fa;
    }
    
    .notice-title h3 {
      margin: 0 0 8px 0; color: #004080; font-size: 20px;
    }
    .notice-meta {
      font-size: 13px; color: #666;
    }
    .notice-meta i {
      margin-right: 5px; color: #004080;
    }
    .notice-meta span {
      margin-right: 15px;
    }
    
    .notice-badges {
      display: flex; gap: 8px; flex-direction: column;
      align-items: flex-end;
    }
    
    .status-badge {
      padding: 5px 12px; border-radius: 20px;
      font-size: 12px; font-weight: 600;
      text-transform: uppercase; display: inline-block;
    }
    .status-badge.published { background: #d4edda; color: #155724; }
    .status-badge.draft { background: #fff3cd; color: #856404; }
    .status-badge.archived { background: #f8d7da; color: #721c24; }
    
    .priority-badge {
      padding: 5px 12px; border-radius: 20px;
      font-size: 11px; font-weight: 600;
      text-transform: uppercase;
    }
    .priority-badge.urgent { background: #dc3545; color: white; }
    .priority-badge.important { background: #ffc107; color: #333; }
    .priority-badge.normal { background: #17a2b8; color: white; }
    
    .notice-body {
      padding: 20px 25px;
    }
    .notice-content {
      color: #555; line-height: 1.6; margin-bottom: 15px;
    }
    
    .notice-footer {
      display: flex; justify-content: space-between;
      align-items: center; padding: 15px 25px;
      border-top: 1px solid #e0e0e0;
    }
    
    .notice-date {
      font-size: 13px; color: #666;
    }
    
    .action-buttons {
      display: flex; gap: 8px;
    }
    .btn-sm {
      padding: 8px 15px; font-size: 13px; border-radius: 4px;
      border: none; cursor: pointer; transition: all 0.3s;
    }
    .btn-info { background: #17a2b8; color: white; }
    .btn-info:hover { background: #138496; }
    .btn-warning { background: #ffc107; color: #333; }
    .btn-warning:hover { background: #e0a800; }
    .btn-danger { background: #dc3545; color: white; }
    .btn-danger:hover { background: #c82333; }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Manage Notices - Create, edit, and publish notices for students and teachers</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <div>
      <h1><i class="fa fa-bullhorn"></i> Manage Notices</h1>
      <div class="breadcrumb">
        <a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Manage Notices
      </div>
    </div>
    <div class="header-actions">
      <a href="../dashboards/admin-dashboard.php" class="btn btn-primary">
        <i class="fa fa-arrow-left"></i> Back
      </a>
      <button class="btn btn-success" onclick="alert('Create Notice form coming soon!')">
        <i class="fa fa-plus"></i> Create Notice
      </button>
    </div>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon blue">
        <i class="fa fa-bullhorn"></i>
      </div>
      <div class="stat-info">
        <h3>12</h3>
        <p>Total Notices</p>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon green">
        <i class="fa fa-check-circle"></i>
      </div>
      <div class="stat-info">
        <h3>8</h3>
        <p>Published</p>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon orange">
        <i class="fa fa-file-alt"></i>
      </div>
      <div class="stat-info">
        <h3>3</h3>
        <p>Drafts</p>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon red">
        <i class="fa fa-exclamation-triangle"></i>
      </div>
      <div class="stat-info">
        <h3>2</h3>
        <p>Urgent</p>
      </div>
    </div>
  </div>

  <div class="content-card">
    <div class="filters-section">
      <div class="search-box">
        <input type="text" placeholder="Search notices..." id="searchInput">
        <i class="fa fa-search"></i>
      </div>
      <select class="filter-select" id="statusFilter">
        <option value="">All Status</option>
        <option value="published">Published</option>
        <option value="draft">Draft</option>
        <option value="archived">Archived</option>
      </select>
      <select class="filter-select" id="priorityFilter">
        <option value="">All Priority</option>
        <option value="urgent">Urgent</option>
        <option value="important">Important</option>
        <option value="normal">Normal</option>
      </select>
    </div>

    <div id="noticesContainer">
      
      <div class="notice-card urgent">
        <div class="notice-header">
          <div class="notice-title">
            <h3>Admission Open for Academic Year 2025/26</h3>
            <div class="notice-meta">
              <span><i class="fa fa-user"></i>Posted by Admin</span>
              <span><i class="fa fa-users"></i>For: All Students</span>
            </div>
          </div>
          <div class="notice-badges">
            <span class="status-badge published">Published</span>
            <span class="priority-badge urgent">Urgent</span>
          </div>
        </div>
        <div class="notice-body">
          <div class="notice-content">
            Applications are now open for the academic year 2025/26. Students interested in B.Tech Ed in IT, Diploma in IT, Civil, and Electrical Engineering programs can apply online or visit the admission office. Last date: March 30, 2025.
          </div>
        </div>
        <div class="notice-footer">
          <div class="notice-date">
            <i class="fa fa-calendar"></i> Published: March 1, 2025
          </div>
          <div class="action-buttons">
            <button class="btn-sm btn-info" onclick="alert('View details coming soon!')">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="btn-sm btn-warning" onclick="alert('Edit coming soon!')">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn-sm btn-danger" onclick="if(confirm('Delete this notice?')) alert('Delete coming soon!')">
              <i class="fa fa-trash"></i> Delete
            </button>
          </div>
        </div>
      </div>

      <div class="notice-card important">
        <div class="notice-header">
          <div class="notice-title">
            <h3>Semester Examination Schedule Released</h3>
            <div class="notice-meta">
              <span><i class="fa fa-user"></i>Posted by Admin</span>
              <span><i class="fa fa-users"></i>For: All Students</span>
            </div>
          </div>
          <div class="notice-badges">
            <span class="status-badge published">Published</span>
            <span class="priority-badge important">Important</span>
          </div>
        </div>
        <div class="notice-body">
          <div class="notice-content">
            The semester examination schedule for all programs has been released. Exams will begin from March 15, 2025. Students are advised to check the detailed schedule on the notice board and prepare accordingly.
          </div>
        </div>
        <div class="notice-footer">
          <div class="notice-date">
            <i class="fa fa-calendar"></i> Published: February 28, 2025
          </div>
          <div class="action-buttons">
            <button class="btn-sm btn-info" onclick="alert('View details coming soon!')">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="btn-sm btn-warning" onclick="alert('Edit coming soon!')">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn-sm btn-danger" onclick="if(confirm('Delete this notice?')) alert('Delete coming soon!')">
              <i class="fa fa-trash"></i> Delete
            </button>
          </div>
        </div>
      </div>

      <div class="notice-card">
        <div class="notice-header">
          <div class="notice-title">
            <h3>Sports Week Announcement</h3>
            <div class="notice-meta">
              <span><i class="fa fa-user"></i>Posted by Admin</span>
              <span><i class="fa fa-users"></i>For: All Students</span>
            </div>
          </div>
          <div class="notice-badges">
            <span class="status-badge published">Published</span>
            <span class="priority-badge normal">Normal</span>
          </div>
        </div>
        <div class="notice-body">
          <div class="notice-content">
            SCTI Sports Week will be held from December 20-25, 2024. Various indoor and outdoor games will be organized. Interested students can register with the sports coordinator by December 15.
          </div>
        </div>
        <div class="notice-footer">
          <div class="notice-date">
            <i class="fa fa-calendar"></i> Published: December 1, 2024
          </div>
          <div class="action-buttons">
            <button class="btn-sm btn-info" onclick="alert('View details coming soon!')">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="btn-sm btn-warning" onclick="alert('Edit coming soon!')">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn-sm btn-danger" onclick="if(confirm('Delete this notice?')) alert('Delete coming soon!')">
              <i class="fa fa-trash"></i> Delete
            </button>
          </div>
        </div>
      </div>

      <div class="notice-card">
        <div class="notice-header">
          <div class="notice-title">
            <h3>Library Hours Extended</h3>
            <div class="notice-meta">
              <span><i class="fa fa-user"></i>Posted by Admin</span>
              <span><i class="fa fa-users"></i>For: All Students</span>
            </div>
          </div>
          <div class="notice-badges">
            <span class="status-badge draft">Draft</span>
            <span class="priority-badge normal">Normal</span>
          </div>
        </div>
        <div class="notice-body">
          <div class="notice-content">
            Due to upcoming examinations, library hours have been extended. The library will now be open from 7:00 AM to 8:00 PM on weekdays and 8:00 AM to 6:00 PM on weekends.
          </div>
        </div>
        <div class="notice-footer">
          <div class="notice-date">
            <i class="fa fa-calendar"></i> Created: March 5, 2025
          </div>
          <div class="action-buttons">
            <button class="btn-sm btn-info" onclick="alert('View details coming soon!')">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="btn-sm btn-warning" onclick="alert('Edit coming soon!')">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn-sm btn-danger" onclick="if(confirm('Delete this notice?')) alert('Delete coming soon!')">
              <i class="fa fa-trash"></i> Delete
            </button>
          </div>
        </div>
      </div>

    </div>
  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Admin Panel</p>
</footer>

<script>
// Simple search functionality
document.getElementById('searchInput').addEventListener('input', function(e) {
  const searchTerm = e.target.value.toLowerCase();
  const cards = document.querySelectorAll('.notice-card');
  
  cards.forEach(card => {
    const text = card.textContent.toLowerCase();
    card.style.display = text.includes(searchTerm) ? '' : 'none';
  });
});
</script>

</body>
</html>
