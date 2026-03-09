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
  <title>Manage Teachers | SCTI Admin</title>
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
    .stat-icon.purple { background: linear-gradient(135deg, #6f42c1, #e83e8c); }
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
    
    .teachers-grid {
      display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 20px; margin-top: 20px;
    }
    
    .teacher-card {
      background: white; border-radius: 10px;
      box-shadow: 0 3px 15px rgba(0,0,0,0.1);
      overflow: hidden; transition: all 0.3s;
      border: 2px solid #e0e0e0;
    }
    .teacher-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,64,128,0.15);
      border-color: #004080;
    }
    
    .teacher-header {
      background: linear-gradient(135deg, #004080, #0059b3);
      color: white; padding: 20px; text-align: center;
    }
    .teacher-avatar {
      width: 80px; height: 80px; border-radius: 50%;
      background: white; color: #004080;
      display: flex; align-items: center; justify-content: center;
      font-size: 36px; margin: 0 auto 15px;
      border: 4px solid rgba(255,255,255,0.3);
    }
    .teacher-header h3 {
      margin: 0 0 5px 0; font-size: 20px;
    }
    .teacher-header p {
      margin: 0; opacity: 0.9; font-size: 13px;
    }
    
    .teacher-body {
      padding: 20px;
    }
    .teacher-info-item {
      display: flex; align-items: center; gap: 10px;
      margin-bottom: 12px; color: #666; font-size: 14px;
    }
    .teacher-info-item i {
      color: #004080; width: 20px;
    }
    
    .status-badge {
      padding: 5px 12px; border-radius: 20px;
      font-size: 12px; font-weight: 600;
      text-transform: uppercase; display: inline-block;
    }
    .status-badge.active { background: #d4edda; color: #155724; }
    .status-badge.inactive { background: #f8d7da; color: #721c24; }
    
    .teacher-actions {
      display: flex; gap: 8px; margin-top: 15px;
      padding-top: 15px; border-top: 1px solid #e0e0e0;
    }
    .btn-sm {
      flex: 1; padding: 8px 12px; font-size: 13px; border-radius: 4px;
      border: none; cursor: pointer; transition: all 0.3s;
      text-align: center;
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
  <marquee>Manage Teachers - Add, edit, and monitor teacher records</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <div>
      <h1><i class="fa fa-chalkboard-teacher"></i> Manage Teachers</h1>
      <div class="breadcrumb">
        <a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Manage Teachers
      </div>
    </div>
    <div class="header-actions">
      <a href="../dashboards/admin-dashboard.php" class="btn btn-primary">
        <i class="fa fa-arrow-left"></i> Back
      </a>
      <button class="btn btn-success" onclick="alert('Add Teacher form coming soon!')">
        <i class="fa fa-plus"></i> Add Teacher
      </button>
    </div>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon blue">
        <i class="fa fa-chalkboard-teacher"></i>
      </div>
      <div class="stat-info">
        <h3>18</h3>
        <p>Total Teachers</p>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon green">
        <i class="fa fa-user-check"></i>
      </div>
      <div class="stat-info">
        <h3>16</h3>
        <p>Active Teachers</p>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon orange">
        <i class="fa fa-book-open"></i>
      </div>
      <div class="stat-info">
        <h3>42</h3>
        <p>Total Classes</p>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon purple">
        <i class="fa fa-user-plus"></i>
      </div>
      <div class="stat-info">
        <h3>3</h3>
        <p>New This Year</p>
      </div>
    </div>
  </div>

  <div class="content-card">
    <div class="filters-section">
      <div class="search-box">
        <input type="text" placeholder="Search by name, email, or subject..." id="searchInput">
        <i class="fa fa-search"></i>
      </div>
      <select class="filter-select" id="departmentFilter">
        <option value="">All Departments</option>
        <option value="it">Information Technology</option>
        <option value="civil">Civil Engineering</option>
        <option value="electrical">Electrical Engineering</option>
        <option value="general">General Studies</option>
      </select>
      <select class="filter-select" id="statusFilter">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>
    </div>

    <div class="teachers-grid" id="teachersGrid">
      
      <div class="teacher-card">
        <div class="teacher-header">
          <div class="teacher-avatar">
            <i class="fa fa-user"></i>
          </div>
          <h3>Mr. Bibek Bhandari</h3>
          <p>Senior Lecturer</p>
        </div>
        <div class="teacher-body">
          <div class="teacher-info-item">
            <i class="fa fa-envelope"></i>
            <span>bibek.bhandari@scti.edu.np</span>
          </div>
          <div class="teacher-info-item">
            <i class="fa fa-phone"></i>
            <span>+977 9841234567</span>
          </div>
          <div class="teacher-info-item">
            <i class="fa fa-book"></i>
            <span>Programming, Data Structures</span>
          </div>
          <div class="teacher-info-item">
            <i class="fa fa-users"></i>
            <span>Teaching 3 Classes (85 Students)</span>
          </div>
          <div class="teacher-info-item">
            <i class="fa fa-circle"></i>
            <span class="status-badge active">Active</span>
          </div>
          <div class="teacher-actions">
            <button class="btn-sm btn-info" onclick="alert('View details coming soon!')">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="btn-sm btn-warning" onclick="alert('Edit coming soon!')">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn-sm btn-danger" onclick="if(confirm('Delete this teacher?')) alert('Delete coming soon!')">
              <i class="fa fa-trash"></i>
            </button>
          </div>
        </div>
      </div>

      <div class="teacher-card">
        <div class="teacher-header">
          <div class="teacher-avatar">
            <i class="fa fa-user"></i>
          </div>
          <h3>Mr. Santosh Sapkota</h3>
          <p>Lecturer</p>
        </div>
        <div class="teacher-body">
          <div class="teacher-info-item">
            <i class="fa fa-envelope"></i>
            <span>santosh.sapkota@scti.edu.np</span>
          </div>
          <div class="teacher-info-item">
            <i class="fa fa-phone"></i>
            <span>+977 9851234568</span>
          </div>
          <div class="teacher-info-item">
            <i class="fa fa-book"></i>
            <span>Database, Web Development</span>
          </div>
          <div class="teacher-info-item">
            <i class="fa fa-users"></i>
            <span>Teaching 2 Classes (60 Students)</span>
          </div>
          <div class="teacher-info-item">
            <i class="fa fa-circle"></i>
            <span class="status-badge active">Active</span>
          </div>
          <div class="teacher-actions">
            <button class="btn-sm btn-info" onclick="alert('View details coming soon!')">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="btn-sm btn-warning" onclick="alert('Edit coming soon!')">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn-sm btn-danger" onclick="if(confirm('Delete this teacher?')) alert('Delete coming soon!')">
              <i class="fa fa-trash"></i>
            </button>
          </div>
        </div>
      </div>

      <div class="teacher-card">
        <div class="teacher-header">
          <div class="teacher-avatar">
            <i class="fa fa-user"></i>
          </div>
          <h3>Mr. Tej Bikram Thapa</h3>
          <p>Assistant Lecturer</p>
        </div>
        <div class="teacher-body">
          <div class="teacher-info-item">
            <i class="fa fa-envelope"></i>
            <span>tej.thapa@scti.edu.np</span>
          </div>
          <div class="teacher-info-item">
            <i class="fa fa-phone"></i>
            <span>+977 9861234569</span>
          </div>
          <div class="teacher-info-item">
            <i class="fa fa-book"></i>
            <span>Web Dev, Mobile Apps</span>
          </div>
          <div class="teacher-info-item">
            <i class="fa fa-users"></i>
            <span>Teaching 2 Classes (55 Students)</span>
          </div>
          <div class="teacher-info-item">
            <i class="fa fa-circle"></i>
            <span class="status-badge active">Active</span>
          </div>
          <div class="teacher-actions">
            <button class="btn-sm btn-info" onclick="alert('View details coming soon!')">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="btn-sm btn-warning" onclick="alert('Edit coming soon!')">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn-sm btn-danger" onclick="if(confirm('Delete this teacher?')) alert('Delete coming soon!')">
              <i class="fa fa-trash"></i>
            </button>
          </div>
        </div>
      </div>

      <div class="teacher-card">
        <div class="teacher-header">
          <div class="teacher-avatar">
            <i class="fa fa-user"></i>
          </div>
          <h3>Mrs. Sita Poudel</h3>
          <p>Lecturer</p>
        </div>
        <div class="teacher-body">
          <div class="teacher-info-item">
            <i class="fa fa-envelope"></i>
            <span>sita.poudel@scti.edu.np</span>
          </div>
          <div class="teacher-info-item">
            <i class="fa fa-phone"></i>
            <span>+977 9871234570</span>
          </div>
          <div class="teacher-info-item">
            <i class="fa fa-book"></i>
            <span>Mathematics, Statistics</span>
          </div>
          <div class="teacher-info-item">
            <i class="fa fa-users"></i>
            <span>Teaching 4 Classes (120 Students)</span>
          </div>
          <div class="teacher-info-item">
            <i class="fa fa-circle"></i>
            <span class="status-badge active">Active</span>
          </div>
          <div class="teacher-actions">
            <button class="btn-sm btn-info" onclick="alert('View details coming soon!')">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="btn-sm btn-warning" onclick="alert('Edit coming soon!')">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn-sm btn-danger" onclick="if(confirm('Delete this teacher?')) alert('Delete coming soon!')">
              <i class="fa fa-trash"></i>
            </button>
          </div>
        </div>
      </div>

      <div class="teacher-card">
        <div class="teacher-header">
          <div class="teacher-avatar">
            <i class="fa fa-user"></i>
          </div>
          <h3>Mr. Ram Prasad Oli</h3>
          <p>Senior Lecturer</p>
        </div>
        <div class="teacher-body">
          <div class="teacher-info-item">
            <i class="fa fa-envelope"></i>
            <span>ram.oli@scti.edu.np</span>
          </div>
          <div class="teacher-info-item">
            <i class="fa fa-phone"></i>
            <span>+977 9881234571</span>
          </div>
          <div class="teacher-info-item">
            <i class="fa fa-book"></i>
            <span>Civil Engineering, Surveying</span>
          </div>
          <div class="teacher-info-item">
            <i class="fa fa-users"></i>
            <span>Teaching 3 Classes (75 Students)</span>
          </div>
          <div class="teacher-info-item">
            <i class="fa fa-circle"></i>
            <span class="status-badge inactive">Inactive</span>
          </div>
          <div class="teacher-actions">
            <button class="btn-sm btn-info" onclick="alert('View details coming soon!')">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="btn-sm btn-warning" onclick="alert('Edit coming soon!')">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn-sm btn-danger" onclick="if(confirm('Delete this teacher?')) alert('Delete coming soon!')">
              <i class="fa fa-trash"></i>
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
  const cards = document.querySelectorAll('.teacher-card');
  
  cards.forEach(card => {
    const text = card.textContent.toLowerCase();
    card.style.display = text.includes(searchTerm) ? '' : 'none';
  });
});
</script>

</body>
</html>
