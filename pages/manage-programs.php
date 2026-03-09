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
  <title>Manage Programs | SCTI Admin</title>
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
    
    .programs-grid {
      display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
      gap: 25px;
    }
    
    .program-card {
      background: white; border-radius: 12px; overflow: hidden;
      box-shadow: 0 3px 15px rgba(0,0,0,0.1);
      transition: all 0.3s; border: 2px solid transparent;
    }
    .program-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 8px 30px rgba(0,64,128,0.2);
      border-color: #004080;
    }
    
    .program-header {
      background: linear-gradient(135deg, #004080, #0059b3);
      color: white; padding: 30px; position: relative;
    }
    .program-icon {
      width: 70px; height: 70px; background: rgba(255,255,255,0.2);
      border-radius: 50%; display: flex; align-items: center;
      justify-content: center; font-size: 32px; margin-bottom: 15px;
    }
    .program-header h3 { margin: 0 0 8px 0; font-size: 22px; }
    .program-code { opacity: 0.9; font-size: 13px; }
    
    .program-body { padding: 25px; }
    
    .program-info-item {
      display: flex; align-items: center; gap: 12px;
      margin-bottom: 15px; color: #666; font-size: 14px;
    }
    .program-info-item i { color: #004080; width: 20px; font-size: 16px; }
    
    .program-stats {
      display: grid; grid-template-columns: repeat(3, 1fr);
      gap: 15px; margin: 20px 0; padding: 15px;
      background: #f8f9fa; border-radius: 8px;
    }
    .stat-item {
      text-align: center;
    }
    .stat-item h4 {
      margin: 0; font-size: 24px; color: #004080;
    }
    .stat-item p {
      margin: 5px 0 0 0; font-size: 12px; color: #666;
    }
    
    .program-actions {
      display: flex; gap: 10px; margin-top: 20px;
      padding-top: 20px; border-top: 1px solid #e0e0e0;
    }
    .btn-sm {
      flex: 1; padding: 10px 15px; font-size: 13px; border-radius: 6px;
      border: none; cursor: pointer; transition: all 0.3s;
      text-align: center; font-weight: 600;
    }
    .btn-info { background: #17a2b8; color: white; }
    .btn-info:hover { background: #138496; transform: translateY(-2px); }
    .btn-warning { background: #ffc107; color: #333; }
    .btn-warning:hover { background: #e0a800; transform: translateY(-2px); }
    .btn-danger { background: #dc3545; color: white; }
    .btn-danger:hover { background: #c82333; transform: translateY(-2px); }
    
    .status-badge {
      position: absolute; top: 20px; right: 20px;
      padding: 6px 14px; border-radius: 20px;
      font-size: 11px; font-weight: 600;
      text-transform: uppercase;
    }
    .status-badge.active { background: #28a745; color: white; }
    .status-badge.inactive { background: #dc3545; color: white; }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Manage Programs - Add, edit, and monitor academic programs</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <div>
      <h1><i class="fa fa-graduation-cap"></i> Manage Programs</h1>
      <div class="breadcrumb">
        <a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Manage Programs
      </div>
    </div>
    <div class="header-actions">
      <a href="../dashboards/admin-dashboard.php" class="btn btn-primary">
        <i class="fa fa-arrow-left"></i> Back
      </a>
      <button class="btn btn-success" onclick="alert('Add Program form coming soon!')">
        <i class="fa fa-plus"></i> Add Program
      </button>
    </div>
  </div>

  <div class="programs-grid">
    
    <div class="program-card">
      <div class="program-header">
        <span class="status-badge active">Active</span>
        <div class="program-icon">
          <i class="fa fa-laptop-code"></i>
        </div>
        <h3>B.Tech Ed in Information Technology</h3>
        <div class="program-code">4 Years • Bachelor's Degree</div>
      </div>
      <div class="program-body">
        <div class="program-info-item">
          <i class="fa fa-calendar-alt"></i>
          <span>Duration: 4 Years (8 Semesters)</span>
        </div>
        <div class="program-info-item">
          <i class="fa fa-book"></i>
          <span>Total Courses: 42</span>
        </div>
        <div class="program-info-item">
          <i class="fa fa-certificate"></i>
          <span>Affiliation: Tribhuvan University</span>
        </div>
        
        <div class="program-stats">
          <div class="stat-item">
            <h4>120</h4>
            <p>Students</p>
          </div>
          <div class="stat-item">
            <h4>8</h4>
            <p>Teachers</p>
          </div>
          <div class="stat-item">
            <h4>42</h4>
            <p>Courses</p>
          </div>
        </div>
        
        <div class="program-actions">
          <button class="btn-sm btn-info" onclick="alert('View details coming soon!')">
            <i class="fa fa-eye"></i> View
          </button>
          <button class="btn-sm btn-warning" onclick="alert('Edit coming soon!')">
            <i class="fa fa-edit"></i> Edit
          </button>
          <button class="btn-sm btn-danger" onclick="if(confirm('Delete this program?')) alert('Delete coming soon!')">
            <i class="fa fa-trash"></i>
          </button>
        </div>
      </div>
    </div>

    <div class="program-card">
      <div class="program-header">
        <span class="status-badge active">Active</span>
        <div class="program-icon">
          <i class="fa fa-desktop"></i>
        </div>
        <h3>Diploma in Information Technology</h3>
        <div class="program-code">3 Years • Diploma</div>
      </div>
      <div class="program-body">
        <div class="program-info-item">
          <i class="fa fa-calendar-alt"></i>
          <span>Duration: 3 Years (6 Semesters)</span>
        </div>
        <div class="program-info-item">
          <i class="fa fa-book"></i>
          <span>Total Courses: 32</span>
        </div>
        <div class="program-info-item">
          <i class="fa fa-certificate"></i>
          <span>Affiliation: CTEVT</span>
        </div>
        
        <div class="program-stats">
          <div class="stat-item">
            <h4>85</h4>
            <p>Students</p>
          </div>
          <div class="stat-item">
            <h4>6</h4>
            <p>Teachers</p>
          </div>
          <div class="stat-item">
            <h4>32</h4>
            <p>Courses</p>
          </div>
        </div>
        
        <div class="program-actions">
          <button class="btn-sm btn-info" onclick="alert('View details coming soon!')">
            <i class="fa fa-eye"></i> View
          </button>
          <button class="btn-sm btn-warning" onclick="alert('Edit coming soon!')">
            <i class="fa fa-edit"></i> Edit
          </button>
          <button class="btn-sm btn-danger" onclick="if(confirm('Delete this program?')) alert('Delete coming soon!')">
            <i class="fa fa-trash"></i>
          </button>
        </div>
      </div>
    </div>

    <div class="program-card">
      <div class="program-header">
        <span class="status-badge active">Active</span>
        <div class="program-icon">
          <i class="fa fa-hard-hat"></i>
        </div>
        <h3>Diploma in Civil Engineering</h3>
        <div class="program-code">3 Years • Diploma</div>
      </div>
      <div class="program-body">
        <div class="program-info-item">
          <i class="fa fa-calendar-alt"></i>
          <span>Duration: 3 Years (6 Semesters)</span>
        </div>
        <div class="program-info-item">
          <i class="fa fa-book"></i>
          <span>Total Courses: 30</span>
        </div>
        <div class="program-info-item">
          <i class="fa fa-certificate"></i>
          <span>Affiliation: CTEVT</span>
        </div>
        
        <div class="program-stats">
          <div class="stat-item">
            <h4>45</h4>
            <p>Students</p>
          </div>
          <div class="stat-item">
            <h4>5</h4>
            <p>Teachers</p>
          </div>
          <div class="stat-item">
            <h4>30</h4>
            <p>Courses</p>
          </div>
        </div>
        
        <div class="program-actions">
          <button class="btn-sm btn-info" onclick="alert('View details coming soon!')">
            <i class="fa fa-eye"></i> View
          </button>
          <button class="btn-sm btn-warning" onclick="alert('Edit coming soon!')">
            <i class="fa fa-edit"></i> Edit
          </button>
          <button class="btn-sm btn-danger" onclick="if(confirm('Delete this program?')) alert('Delete coming soon!')">
            <i class="fa fa-trash"></i>
          </button>
        </div>
      </div>
    </div>

    <div class="program-card">
      <div class="program-header">
        <span class="status-badge active">Active</span>
        <div class="program-icon">
          <i class="fa fa-bolt"></i>
        </div>
        <h3>Diploma in Electrical Engineering</h3>
        <div class="program-code">3 Years • Diploma</div>
      </div>
      <div class="program-body">
        <div class="program-info-item">
          <i class="fa fa-calendar-alt"></i>
          <span>Duration: 3 Years (6 Semesters)</span>
        </div>
        <div class="program-info-item">
          <i class="fa fa-book"></i>
          <span>Total Courses: 28</span>
        </div>
        <div class="program-info-item">
          <i class="fa fa-certificate"></i>
          <span>Affiliation: CTEVT</span>
        </div>
        
        <div class="program-stats">
          <div class="stat-item">
            <h4>38</h4>
            <p>Students</p>
          </div>
          <div class="stat-item">
            <h4>4</h4>
            <p>Teachers</p>
          </div>
          <div class="stat-item">
            <h4>28</h4>
            <p>Courses</p>
          </div>
        </div>
        
        <div class="program-actions">
          <button class="btn-sm btn-info" onclick="alert('View details coming soon!')">
            <i class="fa fa-eye"></i> View
          </button>
          <button class="btn-sm btn-warning" onclick="alert('Edit coming soon!')">
            <i class="fa fa-edit"></i> Edit
          </button>
          <button class="btn-sm btn-danger" onclick="if(confirm('Delete this program?')) alert('Delete coming soon!')">
            <i class="fa fa-trash"></i>
          </button>
        </div>
      </div>
    </div>

  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Admin Panel</p>
</footer>

</body>
</html>
