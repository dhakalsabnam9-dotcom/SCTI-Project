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
  <title>Manage Students | SCTI Admin</title>
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
    
    .students-table {
      width: 100%; border-collapse: collapse; margin-top: 20px;
    }
    .students-table thead {
      background: linear-gradient(135deg, #004080, #0059b3);
      color: white;
    }
    .students-table th {
      padding: 15px; text-align: left; font-weight: 600;
    }
    .students-table td {
      padding: 15px; border-bottom: 1px solid #e0e0e0;
    }
    .students-table tbody tr {
      transition: all 0.3s;
    }
    .students-table tbody tr:hover {
      background: #f8f9fa;
      transform: scale(1.01);
    }
    
    .student-info {
      display: flex; align-items: center; gap: 12px;
    }
    .student-avatar {
      width: 45px; height: 45px; border-radius: 50%;
      background: linear-gradient(135deg, #004080, #0059b3);
      display: flex; align-items: center; justify-content: center;
      color: white; font-weight: bold; font-size: 16px;
    }
    .student-details h4 {
      margin: 0 0 3px 0; color: #333; font-size: 15px;
    }
    .student-details p {
      margin: 0; color: #666; font-size: 12px;
    }
    
    .status-badge {
      padding: 5px 12px; border-radius: 20px;
      font-size: 12px; font-weight: 600;
      text-transform: uppercase; display: inline-block;
    }
    .status-badge.active { background: #d4edda; color: #155724; }
    .status-badge.inactive { background: #f8d7da; color: #721c24; }
    .status-badge.suspended { background: #fff3cd; color: #856404; }
    
    .action-buttons {
      display: flex; gap: 8px;
    }
    .btn-sm {
      padding: 6px 12px; font-size: 12px; border-radius: 4px;
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
  <marquee>Manage Students - Add, edit, and monitor student records</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <div>
      <h1><i class="fa fa-user-graduate"></i> Manage Students</h1>
      <div class="breadcrumb">
        <a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Manage Students
      </div>
    </div>
    <div class="header-actions">
      <a href="../dashboards/admin-dashboard.php" class="btn btn-primary">
        <i class="fa fa-arrow-left"></i> Back
      </a>
      <button class="btn btn-success" onclick="alert('Add Student form coming soon!')">
        <i class="fa fa-plus"></i> Add Student
      </button>
    </div>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon blue">
        <i class="fa fa-users"></i>
      </div>
      <div class="stat-info">
        <h3>245</h3>
        <p>Total Students</p>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon green">
        <i class="fa fa-user-check"></i>
      </div>
      <div class="stat-info">
        <h3>230</h3>
        <p>Active Students</p>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon orange">
        <i class="fa fa-user-clock"></i>
      </div>
      <div class="stat-info">
        <h3>12</h3>
        <p>Inactive</p>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon purple">
        <i class="fa fa-user-plus"></i>
      </div>
      <div class="stat-info">
        <h3>18</h3>
        <p>New This Month</p>
      </div>
    </div>
  </div>

  <div class="content-card">
    <div class="filters-section">
      <div class="search-box">
        <input type="text" placeholder="Search by name, email, or student ID..." id="searchInput">
        <i class="fa fa-search"></i>
      </div>
      <select class="filter-select" id="programFilter">
        <option value="">All Programs</option>
        <option value="btech">B.Tech Ed in IT</option>
        <option value="diploma">Diploma in IT</option>
        <option value="civil">Diploma in Civil</option>
        <option value="electrical">Diploma in Electrical</option>
      </select>
      <select class="filter-select" id="statusFilter">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
        <option value="suspended">Suspended</option>
      </select>
      <select class="filter-select" id="semesterFilter">
        <option value="">All Semesters</option>
        <option value="1">Semester 1</option>
        <option value="2">Semester 2</option>
        <option value="3">Semester 3</option>
        <option value="4">Semester 4</option>
      </select>
    </div>

    <table class="students-table">
      <thead>
        <tr>
          <th>Student</th>
          <th>Student ID</th>
          <th>Program</th>
          <th>Semester</th>
          <th>GPA</th>
          <th>Attendance</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="studentsTableBody">
        <tr>
          <td>
            <div class="student-info">
              <div class="student-avatar">RK</div>
              <div class="student-details">
                <h4>Ram Kumar Sharma</h4>
                <p>ram.sharma@scti.edu.np</p>
              </div>
            </div>
          </td>
          <td>STU20251001</td>
          <td>B.Tech Ed in IT</td>
          <td>3</td>
          <td>3.8</td>
          <td>92%</td>
          <td><span class="status-badge active">Active</span></td>
          <td>
            <div class="action-buttons">
              <button class="btn-sm btn-info" onclick="alert('View details coming soon!')">
                <i class="fa fa-eye"></i>
              </button>
              <button class="btn-sm btn-warning" onclick="alert('Edit coming soon!')">
                <i class="fa fa-edit"></i>
              </button>
              <button class="btn-sm btn-danger" onclick="if(confirm('Delete this student?')) alert('Delete coming soon!')">
                <i class="fa fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <div class="student-info">
              <div class="student-avatar">SP</div>
              <div class="student-details">
                <h4>Sita Poudel</h4>
                <p>sita.poudel@scti.edu.np</p>
              </div>
            </div>
          </td>
          <td>STU20251002</td>
          <td>Diploma in IT</td>
          <td>2</td>
          <td>3.6</td>
          <td>88%</td>
          <td><span class="status-badge active">Active</span></td>
          <td>
            <div class="action-buttons">
              <button class="btn-sm btn-info" onclick="alert('View details coming soon!')">
                <i class="fa fa-eye"></i>
              </button>
              <button class="btn-sm btn-warning" onclick="alert('Edit coming soon!')">
                <i class="fa fa-edit"></i>
              </button>
              <button class="btn-sm btn-danger" onclick="if(confirm('Delete this student?')) alert('Delete coming soon!')">
                <i class="fa fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <div class="student-info">
              <div class="student-avatar">HT</div>
              <div class="student-details">
                <h4>Hari Bahadur Thapa</h4>
                <p>hari.thapa@scti.edu.np</p>
              </div>
            </div>
          </td>
          <td>STU20251003</td>
          <td>Diploma in Civil</td>
          <td>4</td>
          <td>3.2</td>
          <td>85%</td>
          <td><span class="status-badge active">Active</span></td>
          <td>
            <div class="action-buttons">
              <button class="btn-sm btn-info" onclick="alert('View details coming soon!')">
                <i class="fa fa-eye"></i>
              </button>
              <button class="btn-sm btn-warning" onclick="alert('Edit coming soon!')">
                <i class="fa fa-edit"></i>
              </button>
              <button class="btn-sm btn-danger" onclick="if(confirm('Delete this student?')) alert('Delete coming soon!')">
                <i class="fa fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <div class="student-info">
              <div class="student-avatar">GA</div>
              <div class="student-details">
                <h4>Gita Adhikari</h4>
                <p>gita.adhikari@scti.edu.np</p>
              </div>
            </div>
          </td>
          <td>STU20251004</td>
          <td>B.Tech Ed in IT</td>
          <td>1</td>
          <td>3.9</td>
          <td>95%</td>
          <td><span class="status-badge active">Active</span></td>
          <td>
            <div class="action-buttons">
              <button class="btn-sm btn-info" onclick="alert('View details coming soon!')">
                <i class="fa fa-eye"></i>
              </button>
              <button class="btn-sm btn-warning" onclick="alert('Edit coming soon!')">
                <i class="fa fa-edit"></i>
              </button>
              <button class="btn-sm btn-danger" onclick="if(confirm('Delete this student?')) alert('Delete coming soon!')">
                <i class="fa fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <div class="student-info">
              <div class="student-avatar">KO</div>
              <div class="student-details">
                <h4>Krishna Prasad Oli</h4>
                <p>krishna.oli@scti.edu.np</p>
              </div>
            </div>
          </td>
          <td>STU20251005</td>
          <td>Diploma in Electrical</td>
          <td>3</td>
          <td>3.1</td>
          <td>78%</td>
          <td><span class="status-badge inactive">Inactive</span></td>
          <td>
            <div class="action-buttons">
              <button class="btn-sm btn-info" onclick="alert('View details coming soon!')">
                <i class="fa fa-eye"></i>
              </button>
              <button class="btn-sm btn-warning" onclick="alert('Edit coming soon!')">
                <i class="fa fa-edit"></i>
              </button>
              <button class="btn-sm btn-danger" onclick="if(confirm('Delete this student?')) alert('Delete coming soon!')">
                <i class="fa fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <div class="student-info">
              <div class="student-avatar">PG</div>
              <div class="student-details">
                <h4>Prakash Gurung</h4>
                <p>prakash.gurung@scti.edu.np</p>
              </div>
            </div>
          </td>
          <td>STU20251006</td>
          <td>B.Tech Ed in IT</td>
          <td>2</td>
          <td>3.5</td>
          <td>90%</td>
          <td><span class="status-badge active">Active</span></td>
          <td>
            <div class="action-buttons">
              <button class="btn-sm btn-info" onclick="alert('View details coming soon!')">
                <i class="fa fa-eye"></i>
              </button>
              <button class="btn-sm btn-warning" onclick="alert('Edit coming soon!')">
                <i class="fa fa-edit"></i>
              </button>
              <button class="btn-sm btn-danger" onclick="if(confirm('Delete this student?')) alert('Delete coming soon!')">
                <i class="fa fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <div class="student-info">
              <div class="student-avatar">MK</div>
              <div class="student-details">
                <h4>Maya KC</h4>
                <p>maya.kc@scti.edu.np</p>
              </div>
            </div>
          </td>
          <td>STU20251007</td>
          <td>Diploma in IT</td>
          <td>3</td>
          <td>3.7</td>
          <td>89%</td>
          <td><span class="status-badge active">Active</span></td>
          <td>
            <div class="action-buttons">
              <button class="btn-sm btn-info" onclick="alert('View details coming soon!')">
                <i class="fa fa-eye"></i>
              </button>
              <button class="btn-sm btn-warning" onclick="alert('Edit coming soon!')">
                <i class="fa fa-edit"></i>
              </button>
              <button class="btn-sm btn-danger" onclick="if(confirm('Delete this student?')) alert('Delete coming soon!')">
                <i class="fa fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <div class="student-info">
              <div class="student-avatar">SB</div>
              <div class="student-details">
                <h4>Suresh Baral</h4>
                <p>suresh.baral@scti.edu.np</p>
              </div>
            </div>
          </td>
          <td>STU20251008</td>
          <td>Diploma in Civil</td>
          <td>2</td>
          <td>3.3</td>
          <td>86%</td>
          <td><span class="status-badge active">Active</span></td>
          <td>
            <div class="action-buttons">
              <button class="btn-sm btn-info" onclick="alert('View details coming soon!')">
                <i class="fa fa-eye"></i>
              </button>
              <button class="btn-sm btn-warning" onclick="alert('Edit coming soon!')">
                <i class="fa fa-edit"></i>
              </button>
              <button class="btn-sm btn-danger" onclick="if(confirm('Delete this student?')) alert('Delete coming soon!')">
                <i class="fa fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Admin Panel</p>
</footer>

<script>
// Simple search functionality
document.getElementById('searchInput').addEventListener('input', function(e) {
  const searchTerm = e.target.value.toLowerCase();
  const rows = document.querySelectorAll('#studentsTableBody tr');
  
  rows.forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(searchTerm) ? '' : 'none';
  });
});
</script>

</body>
</html>
