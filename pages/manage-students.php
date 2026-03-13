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
    
    /* Modal Styles */
    .modal {
      display: none; position: fixed; z-index: 1000;
      left: 0; top: 0; width: 100%; height: 100%;
      background: rgba(0,0,0,0.7); animation: fadeIn 0.3s;
    }
    .modal.active { display: flex; align-items: center; justify-content: center; }
    
    .modal-content {
      background: white; border-radius: 15px;
      max-width: 700px; width: 90%; max-height: 90vh;
      overflow-y: auto; position: relative;
      animation: slideDown 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
      box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    }
    
    .modal-header {
      background: linear-gradient(135deg, #004080, #0059b3);
      color: white; padding: 25px; border-radius: 15px 15px 0 0;
      display: flex; justify-content: space-between; align-items: center;
    }
    .modal-header h2 {
      margin: 0; font-size: 24px; display: flex; align-items: center; gap: 10px;
    }
    .modal-close {
      background: rgba(255,255,255,0.2); border: none;
      color: white; font-size: 24px; cursor: pointer;
      width: 35px; height: 35px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      transition: all 0.3s;
    }
    .modal-close:hover {
      background: rgba(255,255,255,0.3); transform: rotate(90deg);
    }
    
    .modal-body {
      padding: 30px;
    }
    
    .detail-group {
      margin-bottom: 20px; padding-bottom: 20px;
      border-bottom: 1px solid #e0e0e0;
    }
    .detail-group:last-child { border-bottom: none; }
    .detail-label {
      font-weight: 600; color: #004080;
      font-size: 13px; text-transform: uppercase;
      letter-spacing: 0.5px; margin-bottom: 8px;
      display: flex; align-items: center; gap: 8px;
    }
    .detail-value {
      color: #333; font-size: 15px; line-height: 1.6;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    .form-group label {
      display: block; margin-bottom: 8px;
      font-weight: 600; color: #333; font-size: 14px;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%; padding: 12px; border: 2px solid #e0e0e0;
      border-radius: 6px; font-size: 14px; transition: all 0.3s;
      font-family: inherit;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      outline: none; border-color: #004080;
      box-shadow: 0 0 0 3px rgba(0,64,128,0.1);
    }
    
    .modal-footer {
      padding: 20px 30px; background: #f8f9fa;
      border-radius: 0 0 15px 15px;
      display: flex; gap: 10px; justify-content: flex-end;
    }
    .modal-footer .btn {
      padding: 10px 20px; border-radius: 6px;
      border: none; cursor: pointer; font-size: 14px;
      transition: all 0.3s; display: flex; align-items: center; gap: 8px;
    }
    .btn-secondary {
      background: #6c757d; color: white;
    }
    .btn-secondary:hover {
      background: #5a6268; transform: translateY(-2px);
    }
    .btn-save {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white; font-weight: 600;
    }
    .btn-save:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(40,167,69,0.3);
    }
    
    .delete-modal .modal-content {
      max-width: 450px;
    }
    .delete-modal .modal-body {
      text-align: center; padding: 40px 30px;
    }
    .delete-icon {
      width: 80px; height: 80px; margin: 0 auto 20px;
      background: linear-gradient(135deg, #dc3545, #c82333);
      border-radius: 50%; display: flex;
      align-items: center; justify-content: center;
      font-size: 40px; color: white;
      animation: pulse 2s infinite;
    }
    .delete-modal h3 {
      margin: 0 0 15px 0; color: #333; font-size: 22px;
    }
    .delete-modal p {
      color: #666; font-size: 15px; line-height: 1.6;
      margin-bottom: 25px;
    }
    .delete-modal .student-name {
      font-weight: 600; color: #dc3545;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    @keyframes slideDown {
      from { transform: translateY(-50px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }
    
    .success-message {
      position: fixed; top: 20px; right: 20px; z-index: 2000;
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white; padding: 15px 25px; border-radius: 8px;
      box-shadow: 0 5px 20px rgba(40,167,69,0.3);
      display: none; animation: slideInRight 0.3s;
    }
    .success-message.show { display: flex; align-items: center; gap: 10px; }
    @keyframes slideInRight {
      from { transform: translateX(400px); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }
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

<!-- View Student Modal -->
<div id="viewModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2><i class="fa fa-user-graduate"></i> Student Details</h2>
      <button class="modal-close" onclick="closeModal('viewModal')">&times;</button>
    </div>
    <div class="modal-body" id="viewModalBody">
      <!-- Content will be populated by JavaScript -->
    </div>
  </div>
</div>

<!-- Edit Student Modal -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2><i class="fa fa-edit"></i> Edit Student</h2>
      <button class="modal-close" onclick="closeModal('editModal')">&times;</button>
    </div>
    <div class="modal-body">
      <form id="editForm">
        <input type="hidden" id="editStudentId">
        
        <div class="form-group">
          <label for="editName"><i class="fa fa-user"></i> Full Name</label>
          <input type="text" id="editName" required>
        </div>
        
        <div class="form-group">
          <label for="editStudentID"><i class="fa fa-id-card"></i> Student ID</label>
          <input type="text" id="editStudentID" required>
        </div>
        
        <div class="form-group">
          <label for="editEmail"><i class="fa fa-envelope"></i> Email</label>
          <input type="email" id="editEmail" required>
        </div>
        
        <div class="form-group">
          <label for="editPhone"><i class="fa fa-phone"></i> Phone</label>
          <input type="tel" id="editPhone" required>
        </div>
        
        <div class="form-group">
          <label for="editProgram"><i class="fa fa-graduation-cap"></i> Program</label>
          <select id="editProgram" required>
            <option value="B.Tech Ed in IT">B.Tech Ed in IT</option>
            <option value="Diploma in IT">Diploma in IT</option>
            <option value="Diploma in Civil">Diploma in Civil</option>
            <option value="Diploma in Electrical">Diploma in Electrical</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="editSemester"><i class="fa fa-book"></i> Semester</label>
          <select id="editSemester" required>
            <option value="1">Semester 1</option>
            <option value="2">Semester 2</option>
            <option value="3">Semester 3</option>
            <option value="4">Semester 4</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="editGPA"><i class="fa fa-chart-line"></i> GPA</label>
          <input type="number" id="editGPA" step="0.1" min="0" max="4" required>
        </div>
        
        <div class="form-group">
          <label for="editAttendance"><i class="fa fa-calendar-check"></i> Attendance (%)</label>
          <input type="number" id="editAttendance" min="0" max="100" required>
        </div>
        
        <div class="form-group">
          <label for="editStatus"><i class="fa fa-toggle-on"></i> Status</label>
          <select id="editStatus" required>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
            <option value="Suspended">Suspended</option>
          </select>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">
        <i class="fa fa-times"></i> Cancel
      </button>
      <button type="button" class="btn btn-save" onclick="saveStudent()">
        <i class="fa fa-save"></i> Save Changes
      </button>
    </div>
  </div>
</div>

<!-- Delete Student Modal -->
<div id="deleteModal" class="modal delete-modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2><i class="fa fa-exclamation-triangle"></i> Confirm Delete</h2>
      <button class="modal-close" onclick="closeModal('deleteModal')">&times;</button>
    </div>
    <div class="modal-body">
      <div class="delete-icon">
        <i class="fa fa-trash-alt"></i>
      </div>
      <h3>Are you sure?</h3>
      <p>Do you really want to delete <span class="student-name" id="deleteStudentName"></span>?</p>
      <p>This action cannot be undone.</p>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" onclick="closeModal('deleteModal')">
        <i class="fa fa-times"></i> Cancel
      </button>
      <button type="button" class="btn btn-danger" onclick="confirmDelete()">
        <i class="fa fa-trash"></i> Yes, Delete
      </button>
    </div>
  </div>
</div>

<!-- Success Message -->
<div id="successMessage" class="success-message">
  <i class="fa fa-check-circle"></i>
  <span id="successText">Operation successful!</span>
</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Admin Panel</p>
</footer>

<script>
let currentDeleteId = null;

// View Student Details
function viewStudent(id, name, studentId, email, phone, program, semester, gpa, attendance, status, address, dob, gender, guardian, guardianPhone) {
  const modalBody = document.getElementById('viewModalBody');
  
  const statusClass = status.toLowerCase();
  
  modalBody.innerHTML = `
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-user"></i> Full Name</div>
      <div class="detail-value">${name}</div>
    </div>
    
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-id-card"></i> Student ID</div>
      <div class="detail-value">${studentId}</div>
    </div>
    
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-envelope"></i> Email Address</div>
      <div class="detail-value">${email}</div>
    </div>
    
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-phone"></i> Phone Number</div>
      <div class="detail-value">${phone}</div>
    </div>
    
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-graduation-cap"></i> Program</div>
      <div class="detail-value">${program}</div>
    </div>
    
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-book"></i> Current Semester</div>
      <div class="detail-value">Semester ${semester}</div>
    </div>
    
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-chart-line"></i> GPA</div>
      <div class="detail-value">${gpa} / 4.0</div>
    </div>
    
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-calendar-check"></i> Attendance</div>
      <div class="detail-value">${attendance}</div>
    </div>
    
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-map-marker-alt"></i> Address</div>
      <div class="detail-value">${address || 'Not provided'}</div>
    </div>
    
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-birthday-cake"></i> Date of Birth</div>
      <div class="detail-value">${dob || 'Not provided'}</div>
    </div>
    
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-venus-mars"></i> Gender</div>
      <div class="detail-value">${gender || 'Not provided'}</div>
    </div>
    
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-user-friends"></i> Guardian</div>
      <div class="detail-value">${guardian || 'Not provided'}</div>
    </div>
    
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-phone-alt"></i> Guardian Phone</div>
      <div class="detail-value">${guardianPhone || 'Not provided'}</div>
    </div>
    
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-toggle-on"></i> Status</div>
      <div class="detail-value"><span class="status-badge ${statusClass}">${status}</span></div>
    </div>
  `;
  
  openModal('viewModal');
}

// Edit Student
function editStudent(id, name, studentId, email, phone, program, semester, gpa, attendance, status) {
  document.getElementById('editStudentId').value = id;
  document.getElementById('editName').value = name;
  document.getElementById('editStudentID').value = studentId;
  document.getElementById('editEmail').value = email;
  document.getElementById('editPhone').value = phone;
  document.getElementById('editProgram').value = program;
  document.getElementById('editSemester').value = semester;
  document.getElementById('editGPA').value = gpa;
  document.getElementById('editAttendance').value = attendance.replace('%', '');
  document.getElementById('editStatus').value = status;
  
  openModal('editModal');
}

// Save Student Changes
function saveStudent() {
  const id = document.getElementById('editStudentId').value;
  const name = document.getElementById('editName').value;
  const studentId = document.getElementById('editStudentID').value;
  const email = document.getElementById('editEmail').value;
  const phone = document.getElementById('editPhone').value;
  const program = document.getElementById('editProgram').value;
  const semester = document.getElementById('editSemester').value;
  const gpa = document.getElementById('editGPA').value;
  const attendance = document.getElementById('editAttendance').value;
  const status = document.getElementById('editStatus').value;
  
  // Validate form
  if (!name || !studentId || !email || !phone || !program || !semester || !gpa || !attendance || !status) {
    alert('Please fill in all required fields');
    return;
  }
  
  // Here you would normally send data to server
  // For now, we'll just show success message
  
  closeModal('editModal');
  showSuccess('Student updated successfully!');
  
  // In a real application, you would reload the student data here
  console.log('Updated student:', { id, name, studentId, email, phone, program, semester, gpa, attendance, status });
}

// Delete Student
function deleteStudent(id, name) {
  currentDeleteId = id;
  document.getElementById('deleteStudentName').textContent = name;
  openModal('deleteModal');
}

// Confirm Delete
function confirmDelete() {
  if (currentDeleteId) {
    // Here you would normally send delete request to server
    // For now, we'll just show success message
    
    closeModal('deleteModal');
    showSuccess('Student deleted successfully!');
    
    // In a real application, you would remove the student row here
    console.log('Deleted student ID:', currentDeleteId);
    currentDeleteId = null;
  }
}

// Modal Functions
function openModal(modalId) {
  document.getElementById(modalId).classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
  document.getElementById(modalId).classList.remove('active');
  document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
window.onclick = function(event) {
  if (event.target.classList.contains('modal')) {
    event.target.classList.remove('active');
    document.body.style.overflow = 'auto';
  }
}

// Close modal with ESC key
document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape') {
    const modals = document.querySelectorAll('.modal.active');
    modals.forEach(modal => {
      modal.classList.remove('active');
    });
    document.body.style.overflow = 'auto';
  }
});

// Show Success Message
function showSuccess(message) {
  const successMsg = document.getElementById('successMessage');
  document.getElementById('successText').textContent = message;
  successMsg.classList.add('show');
  
  setTimeout(() => {
    successMsg.classList.remove('show');
  }, 3000);
}

// Simple search functionality
document.getElementById('searchInput').addEventListener('input', function(e) {
  const searchTerm = e.target.value.toLowerCase();
  const rows = document.querySelectorAll('#studentsTableBody tr');
  
  rows.forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(searchTerm) ? '' : 'none';
  });
});

// Update all action buttons to use the new functions
document.addEventListener('DOMContentLoaded', function() {
  const students = [
    {id: 1, name: 'Ram Kumar Sharma', studentId: 'STU20251001', email: 'ram.sharma@scti.edu.np', phone: '+977 9841234567', program: 'B.Tech Ed in IT', semester: '3', gpa: '3.8', attendance: '92%', status: 'Active', address: 'Kathmandu, Nepal', dob: '2005-05-15', gender: 'Male', guardian: 'Father Name', guardianPhone: '+977 9841234568'},
    {id: 2, name: 'Sita Poudel', studentId: 'STU20251002', email: 'sita.poudel@scti.edu.np', phone: '+977 9851234568', program: 'Diploma in IT', semester: '2', gpa: '3.6', attendance: '88%', status: 'Active', address: 'Lalitpur, Nepal', dob: '2006-03-20', gender: 'Female', guardian: 'Mother Name', guardianPhone: '+977 9851234569'},
    {id: 3, name: 'Hari Bahadur Thapa', studentId: 'STU20251003', email: 'hari.thapa@scti.edu.np', phone: '+977 9861234569', program: 'Diploma in Civil', semester: '4', gpa: '3.2', attendance: '85%', status: 'Active', address: 'Bhaktapur, Nepal', dob: '2004-07-10', gender: 'Male', guardian: 'Father Name', guardianPhone: '+977 9861234570'},
    {id: 4, name: 'Gita Adhikari', studentId: 'STU20251004', email: 'gita.adhikari@scti.edu.np', phone: '+977 9871234570', program: 'B.Tech Ed in IT', semester: '1', gpa: '3.9', attendance: '95%', status: 'Active', address: 'Pokhara, Nepal', dob: '2007-01-25', gender: 'Female', guardian: 'Father Name', guardianPhone: '+977 9871234571'},
    {id: 5, name: 'Krishna Prasad Oli', studentId: 'STU20251005', email: 'krishna.oli@scti.edu.np', phone: '+977 9881234571', program: 'Diploma in Electrical', semester: '3', gpa: '3.1', attendance: '78%', status: 'Inactive', address: 'Chitwan, Nepal', dob: '2005-09-12', gender: 'Male', guardian: 'Father Name', guardianPhone: '+977 9881234572'},
    {id: 6, name: 'Prakash Gurung', studentId: 'STU20251006', email: 'prakash.gurung@scti.edu.np', phone: '+977 9891234572', program: 'B.Tech Ed in IT', semester: '2', gpa: '3.5', attendance: '90%', status: 'Active', address: 'Butwal, Nepal', dob: '2006-11-08', gender: 'Male', guardian: 'Father Name', guardianPhone: '+977 9891234573'},
    {id: 7, name: 'Maya KC', studentId: 'STU20251007', email: 'maya.kc@scti.edu.np', phone: '+977 9801234573', program: 'Diploma in IT', semester: '3', gpa: '3.7', attendance: '89%', status: 'Active', address: 'Biratnagar, Nepal', dob: '2005-04-18', gender: 'Female', guardian: 'Mother Name', guardianPhone: '+977 9801234574'},
    {id: 8, name: 'Suresh Baral', studentId: 'STU20251008', email: 'suresh.baral@scti.edu.np', phone: '+977 9811234574', program: 'Diploma in Civil', semester: '2', gpa: '3.3', attendance: '86%', status: 'Active', address: 'Dharan, Nepal', dob: '2006-06-22', gender: 'Male', guardian: 'Father Name', guardianPhone: '+977 9811234575'}
  ];
  
  const rows = document.querySelectorAll('#studentsTableBody tr');
  rows.forEach((row, index) => {
    if (students[index]) {
      const s = students[index];
      const buttons = row.querySelector('.action-buttons');
      buttons.innerHTML = `
        <button class="btn-sm btn-info" onclick="viewStudent(${s.id}, '${s.name}', '${s.studentId}', '${s.email}', '${s.phone}', '${s.program}', '${s.semester}', '${s.gpa}', '${s.attendance}', '${s.status}', '${s.address}', '${s.dob}', '${s.gender}', '${s.guardian}', '${s.guardianPhone}')">
          <i class="fa fa-eye"></i>
        </button>
        <button class="btn-sm btn-warning" onclick="editStudent(${s.id}, '${s.name}', '${s.studentId}', '${s.email}', '${s.phone}', '${s.program}', '${s.semester}', '${s.gpa}', '${s.attendance}', '${s.status}')">
          <i class="fa fa-edit"></i>
        </button>
        <button class="btn-sm btn-danger" onclick="deleteStudent(${s.id}, '${s.name}')">
          <i class="fa fa-trash"></i>
        </button>
      `;
    }
  });
});
</script>

</body>
</html>
