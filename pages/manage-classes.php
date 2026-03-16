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
  <title>Manage Classes | SCTI Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
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
    
    .classes-grid {
      display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
      gap: 25px;
    }
    
    .class-card {
      background: white; border-radius: 12px; overflow: hidden;
      box-shadow: 0 3px 15px rgba(0,0,0,0.1);
      transition: all 0.3s; border: 2px solid transparent;
    }
    .class-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 8px 30px rgba(0,64,128,0.2);
      border-color: #004080;
    }
    
    .class-header {
      background: linear-gradient(135deg, #004080, #0059b3);
      color: white; padding: 25px; position: relative;
    }
    .class-header h3 { margin: 0 0 8px 0; font-size: 20px; }
    .class-code { opacity: 0.9; font-size: 13px; margin-bottom: 5px; }
    .class-teacher { opacity: 0.85; font-size: 13px; }
    .class-teacher i { margin-right: 5px; }
    
    .class-body { padding: 25px; }
    
    .class-info-item {
      display: flex; align-items: center; gap: 10px;
      margin-bottom: 12px; color: #666; font-size: 14px;
    }
    .class-info-item i { color: #004080; width: 20px; }
    
    .class-stats {
      display: grid; grid-template-columns: repeat(3, 1fr);
      gap: 12px; margin: 20px 0; padding: 15px;
      background: #f8f9fa; border-radius: 8px;
    }
    .stat-item {
      text-align: center;
    }
    .stat-item h4 {
      margin: 0; font-size: 22px; color: #004080;
    }
    .stat-item p {
      margin: 5px 0 0 0; font-size: 11px; color: #666;
    }
    
    .class-actions {
      display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 20px;
    }
    .btn-action {
      padding: 10px; border: none; border-radius: 6px;
      cursor: pointer; font-size: 13px; transition: all 0.3s;
      text-align: center; font-weight: 600;
    }
    .btn-info { background: #17a2b8; color: white; }
    .btn-info:hover { background: #138496; transform: translateY(-2px); }
    .btn-warning { background: #ffc107; color: #333; }
    .btn-warning:hover { background: #e0a800; transform: translateY(-2px); }
    .btn-danger { background: #dc3545; color: white; }
    .btn-danger:hover { background: #c82333; transform: translateY(-2px); }
    .btn-outline {
      background: white; color: #004080;
      border: 2px solid #004080;
    }
    .btn-outline:hover {
      background: #004080; color: white;
    }
    
    .status-badge {
      position: absolute; top: 20px; right: 20px;
      padding: 6px 14px; border-radius: 20px;
      font-size: 11px; font-weight: 600;
      text-transform: uppercase;
    }
    .status-badge.active { background: #28a745; color: white; }
    .status-badge.completed { background: #6c757d; color: white; }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Manage Classes - View and manage all classes across programs</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <div>
      <h1><i class="fa fa-book-open"></i> All Classes</h1>
      <div class="breadcrumb">
        <a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Manage Classes
      </div>
    </div>
    <div class="header-actions">
      <a href="../dashboards/admin-dashboard.php" class="btn btn-primary">
        <i class="fa fa-arrow-left"></i> Back
      </a>
      <button class="btn btn-success" onclick="alert('Add Class form coming soon!')">
        <i class="fa fa-plus"></i> Add Class
      </button>
    </div>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon blue">
        <i class="fa fa-book"></i>
      </div>
      <div class="stat-info">
        <h3>42</h3>
        <p>Total Classes</p>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon green">
        <i class="fa fa-check-circle"></i>
      </div>
      <div class="stat-info">
        <h3>38</h3>
        <p>Active Classes</p>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon orange">
        <i class="fa fa-users"></i>
      </div>
      <div class="stat-info">
        <h3>245</h3>
        <p>Total Students</p>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon purple">
        <i class="fa fa-chalkboard-teacher"></i>
      </div>
      <div class="stat-info">
        <h3>18</h3>
        <p>Teachers</p>
      </div>
    </div>
  </div>

  <div class="content-card">
    <div class="filters-section">
      <div class="search-box">
        <input type="text" placeholder="Search by class name, code, or teacher..." id="searchInput">
        <i class="fa fa-search"></i>
      </div>
      <select class="filter-select" id="programFilter">
        <option value="">All Programs</option>
        <option value="btech">B.Tech Ed in IT</option>
        <option value="diploma-it">Diploma in IT</option>
        <option value="civil">Diploma in Civil</option>
        <option value="electrical">Diploma in Electrical</option>
      </select>
      <select class="filter-select" id="semesterFilter">
        <option value="">All Semesters</option>
        <option value="1">Semester 1</option>
        <option value="2">Semester 2</option>
        <option value="3">Semester 3</option>
        <option value="4">Semester 4</option>
      </select>
      <select class="filter-select" id="statusFilter">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="completed">Completed</option>
      </select>
    </div>

    <div class="classes-grid" id="classesGrid">
      
      <div class="class-card">
        <div class="class-header">
          <span class="status-badge active">Active</span>
          <h3>Programming Fundamentals</h3>
          <div class="class-code">CS101 - B.Tech IT Semester 1</div>
          <div class="class-teacher"><i class="fa fa-user"></i>Mr. Bibek Bhandari</div>
        </div>
        <div class="class-body">
          <div class="class-info-item">
            <i class="fa fa-clock"></i>
            <span>Mon, Wed, Fri - 9:00 AM - 10:30 AM</span>
          </div>
          <div class="class-info-item">
            <i class="fa fa-door-open"></i>
            <span>Room 101 / Lab 1</span>
          </div>
          <div class="class-info-item">
            <i class="fa fa-calendar"></i>
            <span>Jan 15, 2025 - Jun 30, 2025</span>
          </div>
          
          <div class="class-stats">
            <div class="stat-item">
              <h4>35</h4>
              <p>Students</p>
            </div>
            <div class="stat-item">
              <h4>87%</h4>
              <p>Attendance</p>
            </div>
            <div class="stat-item">
              <h4>B+</h4>
              <p>Avg Grade</p>
            </div>
          </div>
          
          <div class="class-actions">
            <button class="btn-action btn-info" onclick="alert('View details coming soon!')">
              <i class="fa fa-eye"></i> View Details
            </button>
            <button class="btn-action btn-warning" onclick="alert('Edit coming soon!')">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn-action btn-outline" onclick="alert('View students coming soon!')">
              <i class="fa fa-users"></i> Students
            </button>
            <button class="btn-action btn-danger" onclick="if(confirm('Delete this class?')) alert('Delete coming soon!')">
              <i class="fa fa-trash"></i> Delete
            </button>
          </div>
        </div>
      </div>

      <div class="class-card">
        <div class="class-header">
          <span class="status-badge active">Active</span>
          <h3>Database Management</h3>
          <div class="class-code">CS201 - B.Tech IT Semester 3</div>
          <div class="class-teacher"><i class="fa fa-user"></i>Mr. Santosh Sapkota</div>
        </div>
        <div class="class-body">
          <div class="class-info-item">
            <i class="fa fa-clock"></i>
            <span>Tue, Thu - 11:00 AM - 12:30 PM</span>
          </div>
          <div class="class-info-item">
            <i class="fa fa-door-open"></i>
            <span>Room 203 / Lab 2</span>
          </div>
          <div class="class-info-item">
            <i class="fa fa-calendar"></i>
            <span>Jan 15, 2025 - Jun 30, 2025</span>
          </div>
          
          <div class="class-stats">
            <div class="stat-item">
              <h4>28</h4>
              <p>Students</p>
            </div>
            <div class="stat-item">
              <h4>91%</h4>
              <p>Attendance</p>
            </div>
            <div class="stat-item">
              <h4>A-</h4>
              <p>Avg Grade</p>
            </div>
          </div>
          
          <div class="class-actions">
            <button class="btn-action btn-info" onclick="alert('View details coming soon!')">
              <i class="fa fa-eye"></i> View Details
            </button>
            <button class="btn-action btn-warning" onclick="alert('Edit coming soon!')">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn-action btn-outline" onclick="alert('View students coming soon!')">
              <i class="fa fa-users"></i> Students
            </button>
            <button class="btn-action btn-danger" onclick="if(confirm('Delete this class?')) alert('Delete coming soon!')">
              <i class="fa fa-trash"></i> Delete
            </button>
          </div>
        </div>
      </div>

      <div class="class-card">
        <div class="class-header">
          <span class="status-badge active">Active</span>
          <h3>Web Development</h3>
          <div class="class-code">CS301 - Diploma Civil Semester 2</div>
          <div class="class-teacher"><i class="fa fa-user"></i>Mr. Tej Bikram Thapa</div>
        </div>
        <div class="class-body">
          <div class="class-info-item">
            <i class="fa fa-clock"></i>
            <span>Mon, Wed - 2:00 PM - 3:30 PM</span>
          </div>
          <div class="class-info-item">
            <i class="fa fa-door-open"></i>
            <span>Lab 3</span>
          </div>
          <div class="class-info-item">
            <i class="fa fa-calendar"></i>
            <span>Jan 15, 2025 - Jun 30, 2025</span>
          </div>
          
          <div class="class-stats">
            <div class="stat-item">
              <h4>32</h4>
              <p>Students</p>
            </div>
            <div class="stat-item">
              <h4>89%</h4>
              <p>Attendance</p>
            </div>
            <div class="stat-item">
              <h4>A</h4>
              <p>Avg Grade</p>
            </div>
          </div>
          
          <div class="class-actions">
            <button class="btn-action btn-info" onclick="alert('View details coming soon!')">
              <i class="fa fa-eye"></i> View Details
            </button>
            <button class="btn-action btn-warning" onclick="alert('Edit coming soon!')">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn-action btn-outline" onclick="alert('View students coming soon!')">
              <i class="fa fa-users"></i> Students
            </button>
            <button class="btn-action btn-danger" onclick="if(confirm('Delete this class?')) alert('Delete coming soon!')">
              <i class="fa fa-trash"></i> Delete
            </button>
          </div>
        </div>
      </div>

      <div class="class-card">
        <div class="class-header">
          <span class="status-badge active">Active</span>
          <h3>Data Structures</h3>
          <div class="class-code">CS102 - B.Tech IT Semester 2</div>
          <div class="class-teacher"><i class="fa fa-user"></i>Mr. Bibek Bhandari</div>
        </div>
        <div class="class-body">
          <div class="class-info-item">
            <i class="fa fa-clock"></i>
            <span>Tue, Thu - 3:30 PM - 5:00 PM</span>
          </div>
          <div class="class-info-item">
            <i class="fa fa-door-open"></i>
            <span>Room 102 / Lab 1</span>
          </div>
          <div class="class-info-item">
            <i class="fa fa-calendar"></i>
            <span>Jan 15, 2025 - Jun 30, 2025</span>
          </div>
          
          <div class="class-stats">
            <div class="stat-item">
              <h4>25</h4>
              <p>Students</p>
            </div>
            <div class="stat-item">
              <h4>84%</h4>
              <p>Attendance</p>
            </div>
            <div class="stat-item">
              <h4>B</h4>
              <p>Avg Grade</p>
            </div>
          </div>
          
          <div class="class-actions">
            <button class="btn-action btn-info" onclick="alert('View details coming soon!')">
              <i class="fa fa-eye"></i> View Details
            </button>
            <button class="btn-action btn-warning" onclick="alert('Edit coming soon!')">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn-action btn-outline" onclick="alert('View students coming soon!')">
              <i class="fa fa-users"></i> Students
            </button>
            <button class="btn-action btn-danger" onclick="if(confirm('Delete this class?')) alert('Delete coming soon!')">
              <i class="fa fa-trash"></i> Delete
            </button>
          </div>
        </div>
      </div>

      <div class="class-card">
        <div class="class-header">
          <span class="status-badge active">Active</span>
          <h3>Mathematics I</h3>
          <div class="class-code">MATH101 - All Programs Semester 1</div>
          <div class="class-teacher"><i class="fa fa-user"></i>Mrs. Sita Poudel</div>
        </div>
        <div class="class-body">
          <div class="class-info-item">
            <i class="fa fa-clock"></i>
            <span>Mon, Wed, Fri - 11:00 AM - 12:00 PM</span>
          </div>
          <div class="class-info-item">
            <i class="fa fa-door-open"></i>
            <span>Room 201</span>
          </div>
          <div class="class-info-item">
            <i class="fa fa-calendar"></i>
            <span>Jan 15, 2025 - Jun 30, 2025</span>
          </div>
          
          <div class="class-stats">
            <div class="stat-item">
              <h4>65</h4>
              <p>Students</p>
            </div>
            <div class="stat-item">
              <h4>88%</h4>
              <p>Attendance</p>
            </div>
            <div class="stat-item">
              <h4>B+</h4>
              <p>Avg Grade</p>
            </div>
          </div>
          
          <div class="class-actions">
            <button class="btn-action btn-info" onclick="alert('View details coming soon!')">
              <i class="fa fa-eye"></i> View Details
            </button>
            <button class="btn-action btn-warning" onclick="alert('Edit coming soon!')">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn-action btn-outline" onclick="alert('View students coming soon!')">
              <i class="fa fa-users"></i> Students
            </button>
            <button class="btn-action btn-danger" onclick="if(confirm('Delete this class?')) alert('Delete coming soon!')">
              <i class="fa fa-trash"></i> Delete
            </button>
          </div>
        </div>
      </div>

      <div class="class-card">
        <div class="class-header">
          <span class="status-badge active">Active</span>
          <h3>Engineering Drawing</h3>
          <div class="class-code">CE101 - Diploma Civil Semester 1</div>
          <div class="class-teacher"><i class="fa fa-user"></i>Mr. Ram Prasad Oli</div>
        </div>
        <div class="class-body">
          <div class="class-info-item">
            <i class="fa fa-clock"></i>
            <span>Tue, Thu - 9:00 AM - 11:00 AM</span>
          </div>
          <div class="class-info-item">
            <i class="fa fa-door-open"></i>
            <span>Drawing Hall</span>
          </div>
          <div class="class-info-item">
            <i class="fa fa-calendar"></i>
            <span>Jan 15, 2025 - Jun 30, 2025</span>
          </div>
          
          <div class="class-stats">
            <div class="stat-item">
              <h4>22</h4>
              <p>Students</p>
            </div>
            <div class="stat-item">
              <h4>82%</h4>
              <p>Attendance</p>
            </div>
            <div class="stat-item">
              <h4>B</h4>
              <p>Avg Grade</p>
            </div>
          </div>
          
          <div class="class-actions">
            <button class="btn-action btn-info" onclick="alert('View details coming soon!')">
              <i class="fa fa-eye"></i> View Details
            </button>
            <button class="btn-action btn-warning" onclick="alert('Edit coming soon!')">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn-action btn-outline" onclick="alert('View students coming soon!')">
              <i class="fa fa-users"></i> Students
            </button>
            <button class="btn-action btn-danger" onclick="if(confirm('Delete this class?')) alert('Delete coming soon!')">
              <i class="fa fa-trash"></i> Delete
            </button>
          </div>
        </div>
      </div>

      <div class="class-card">
        <div class="class-header">
          <span class="status-badge completed">Completed</span>
          <h3>Circuit Analysis</h3>
          <div class="class-code">EE201 - Diploma Electrical Semester 2</div>
          <div class="class-teacher"><i class="fa fa-user"></i>Mr. Rajesh Shrestha</div>
        </div>
        <div class="class-body">
          <div class="class-info-item">
            <i class="fa fa-clock"></i>
            <span>Mon, Wed - 1:00 PM - 2:30 PM</span>
          </div>
          <div class="class-info-item">
            <i class="fa fa-door-open"></i>
            <span>Lab 4</span>
          </div>
          <div class="class-info-item">
            <i class="fa fa-calendar"></i>
            <span>Aug 1, 2024 - Dec 20, 2024</span>
          </div>
          
          <div class="class-stats">
            <div class="stat-item">
              <h4>18</h4>
              <p>Students</p>
            </div>
            <div class="stat-item">
              <h4>90%</h4>
              <p>Attendance</p>
            </div>
            <div class="stat-item">
              <h4>A-</h4>
              <p>Avg Grade</p>
            </div>
          </div>
          
          <div class="class-actions">
            <button class="btn-action btn-info" onclick="alert('View details coming soon!')">
              <i class="fa fa-eye"></i> View Details
            </button>
            <button class="btn-action btn-warning" onclick="alert('Edit coming soon!')">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn-action btn-outline" onclick="alert('View students coming soon!')">
              <i class="fa fa-users"></i> Students
            </button>
            <button class="btn-action btn-danger" onclick="if(confirm('Delete this class?')) alert('Delete coming soon!')">
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
  const cards = document.querySelectorAll('.class-card');
  
  cards.forEach(card => {
    const text = card.textContent.toLowerCase();
    card.style.display = text.includes(searchTerm) ? '' : 'none';
  });
});
</script>

</body>
</html>
