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
    
    /* Modal Styles */
    .modal {
      display: none; position: fixed; z-index: 1000;
      left: 0; top: 0; width: 100%; height: 100%;
      background: rgba(0,0,0,0.7); animation: fadeIn 0.3s;
    }
    .modal.active { display: flex; align-items: center; justify-content: center; }
    
    .modal-content {
      background: white; border-radius: 15px;
      max-width: 600px; width: 90%; max-height: 90vh;
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
    .form-group textarea {
      resize: vertical; min-height: 80px;
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
    .delete-modal .teacher-name {
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
            <button class="btn-sm btn-info" onclick="viewTeacher(1, 'Mr. Bibek Bhandari', 'Senior Lecturer', 'bibek.bhandari@scti.edu.np', '+977 9841234567', 'Programming, Data Structures', '3 Classes (85 Students)', 'Active', 'Information Technology', '2020-01-15', 'MSc in Computer Science')">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="btn-sm btn-warning" onclick="editTeacher(1, 'Mr. Bibek Bhandari', 'Senior Lecturer', 'bibek.bhandari@scti.edu.np', '+977 9841234567', 'Programming, Data Structures', 'Active', 'Information Technology')">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn-sm btn-danger" onclick="deleteTeacher(1, 'Mr. Bibek Bhandari')">
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
            <button class="btn-sm btn-info" onclick="viewTeacher(2, 'Mr. Santosh Sapkota', 'Lecturer', 'santosh.sapkota@scti.edu.np', '+977 9851234568', 'Database, Web Development', '2 Classes (60 Students)', 'Active', 'Information Technology', '2021-03-20', 'MSc in Information Technology')">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="btn-sm btn-warning" onclick="editTeacher(2, 'Mr. Santosh Sapkota', 'Lecturer', 'santosh.sapkota@scti.edu.np', '+977 9851234568', 'Database, Web Development', 'Active', 'Information Technology')">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn-sm btn-danger" onclick="deleteTeacher(2, 'Mr. Santosh Sapkota')">
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
            <button class="btn-sm btn-info" onclick="viewTeacher(3, 'Mr. Tej Bikram Thapa', 'Assistant Lecturer', 'tej.thapa@scti.edu.np', '+977 9861234569', 'Web Dev, Mobile Apps', '2 Classes (55 Students)', 'Active', 'Information Technology', '2022-07-10', 'BSc in Computer Engineering')">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="btn-sm btn-warning" onclick="editTeacher(3, 'Mr. Tej Bikram Thapa', 'Assistant Lecturer', 'tej.thapa@scti.edu.np', '+977 9861234569', 'Web Dev, Mobile Apps', 'Active', 'Information Technology')">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn-sm btn-danger" onclick="deleteTeacher(3, 'Mr. Tej Bikram Thapa')">
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
            <button class="btn-sm btn-info" onclick="viewTeacher(4, 'Mrs. Sita Poudel', 'Lecturer', 'sita.poudel@scti.edu.np', '+977 9871234570', 'Mathematics, Statistics', '4 Classes (120 Students)', 'Active', 'General Studies', '2019-08-01', 'MSc in Mathematics')">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="btn-sm btn-warning" onclick="editTeacher(4, 'Mrs. Sita Poudel', 'Lecturer', 'sita.poudel@scti.edu.np', '+977 9871234570', 'Mathematics, Statistics', 'Active', 'General Studies')">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn-sm btn-danger" onclick="deleteTeacher(4, 'Mrs. Sita Poudel')">
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
            <button class="btn-sm btn-info" onclick="viewTeacher(5, 'Mr. Ram Prasad Oli', 'Senior Lecturer', 'ram.oli@scti.edu.np', '+977 9881234571', 'Civil Engineering, Surveying', '3 Classes (75 Students)', 'Inactive', 'Civil Engineering', '2018-05-12', 'MSc in Civil Engineering')">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="btn-sm btn-warning" onclick="editTeacher(5, 'Mr. Ram Prasad Oli', 'Senior Lecturer', 'ram.oli@scti.edu.np', '+977 9881234571', 'Civil Engineering, Surveying', 'Inactive', 'Civil Engineering')">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn-sm btn-danger" onclick="deleteTeacher(5, 'Mr. Ram Prasad Oli')">
              <i class="fa fa-trash"></i>
            </button>
          </div>
        </div>
      </div>

    </div>
  </div>

</div>

<!-- View Teacher Modal -->
<div id="viewModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2><i class="fa fa-user-circle"></i> Teacher Details</h2>
      <button class="modal-close" onclick="closeModal('viewModal')">&times;</button>
    </div>
    <div class="modal-body" id="viewModalBody">
      <!-- Content will be populated by JavaScript -->
    </div>
  </div>
</div>

<!-- Edit Teacher Modal -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2><i class="fa fa-edit"></i> Edit Teacher</h2>
      <button class="modal-close" onclick="closeModal('editModal')">&times;</button>
    </div>
    <div class="modal-body">
      <form id="editForm">
        <input type="hidden" id="editTeacherId">
        
        <div class="form-group">
          <label for="editName"><i class="fa fa-user"></i> Full Name</label>
          <input type="text" id="editName" required>
        </div>
        
        <div class="form-group">
          <label for="editPosition"><i class="fa fa-briefcase"></i> Position</label>
          <select id="editPosition" required>
            <option value="Senior Lecturer">Senior Lecturer</option>
            <option value="Lecturer">Lecturer</option>
            <option value="Assistant Lecturer">Assistant Lecturer</option>
            <option value="Professor">Professor</option>
          </select>
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
          <label for="editSubjects"><i class="fa fa-book"></i> Subjects</label>
          <input type="text" id="editSubjects" placeholder="e.g., Programming, Data Structures" required>
        </div>
        
        <div class="form-group">
          <label for="editDepartment"><i class="fa fa-building"></i> Department</label>
          <select id="editDepartment" required>
            <option value="Information Technology">Information Technology</option>
            <option value="Civil Engineering">Civil Engineering</option>
            <option value="Electrical Engineering">Electrical Engineering</option>
            <option value="General Studies">General Studies</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="editStatus"><i class="fa fa-toggle-on"></i> Status</label>
          <select id="editStatus" required>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">
        <i class="fa fa-times"></i> Cancel
      </button>
      <button type="button" class="btn btn-save" onclick="saveTeacher()">
        <i class="fa fa-save"></i> Save Changes
      </button>
    </div>
  </div>
</div>

<!-- Delete Teacher Modal -->
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
      <p>Do you really want to delete <span class="teacher-name" id="deleteTeacherName"></span>?</p>
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

// View Teacher Details
function viewTeacher(id, name, position, email, phone, subjects, classes, status, department, joinDate, qualification) {
  const modalBody = document.getElementById('viewModalBody');
  
  const statusClass = status.toLowerCase() === 'active' ? 'active' : 'inactive';
  
  modalBody.innerHTML = `
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-user"></i> Full Name</div>
      <div class="detail-value">${name}</div>
    </div>
    
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-briefcase"></i> Position</div>
      <div class="detail-value">${position}</div>
    </div>
    
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-building"></i> Department</div>
      <div class="detail-value">${department}</div>
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
      <div class="detail-label"><i class="fa fa-book"></i> Subjects Teaching</div>
      <div class="detail-value">${subjects}</div>
    </div>
    
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-users"></i> Current Load</div>
      <div class="detail-value">Teaching ${classes}</div>
    </div>
    
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-graduation-cap"></i> Qualification</div>
      <div class="detail-value">${qualification}</div>
    </div>
    
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-calendar"></i> Join Date</div>
      <div class="detail-value">${new Date(joinDate).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</div>
    </div>
    
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-toggle-on"></i> Status</div>
      <div class="detail-value"><span class="status-badge ${statusClass}">${status}</span></div>
    </div>
  `;
  
  openModal('viewModal');
}

// Edit Teacher
function editTeacher(id, name, position, email, phone, subjects, status, department) {
  document.getElementById('editTeacherId').value = id;
  document.getElementById('editName').value = name;
  document.getElementById('editPosition').value = position;
  document.getElementById('editEmail').value = email;
  document.getElementById('editPhone').value = phone;
  document.getElementById('editSubjects').value = subjects;
  document.getElementById('editDepartment').value = department;
  document.getElementById('editStatus').value = status;
  
  openModal('editModal');
}

// Save Teacher Changes
function saveTeacher() {
  const id = document.getElementById('editTeacherId').value;
  const name = document.getElementById('editName').value;
  const position = document.getElementById('editPosition').value;
  const email = document.getElementById('editEmail').value;
  const phone = document.getElementById('editPhone').value;
  const subjects = document.getElementById('editSubjects').value;
  const department = document.getElementById('editDepartment').value;
  const status = document.getElementById('editStatus').value;
  
  // Validate form
  if (!name || !position || !email || !phone || !subjects || !department || !status) {
    alert('Please fill in all required fields');
    return;
  }
  
  // Here you would normally send data to server
  // For now, we'll just show success message
  
  closeModal('editModal');
  showSuccess('Teacher updated successfully!');
  
  // In a real application, you would reload the teacher data here
  console.log('Updated teacher:', { id, name, position, email, phone, subjects, department, status });
}

// Delete Teacher
function deleteTeacher(id, name) {
  currentDeleteId = id;
  document.getElementById('deleteTeacherName').textContent = name;
  openModal('deleteModal');
}

// Confirm Delete
function confirmDelete() {
  if (currentDeleteId) {
    // Here you would normally send delete request to server
    // For now, we'll just show success message
    
    closeModal('deleteModal');
    showSuccess('Teacher deleted successfully!');
    
    // In a real application, you would remove the teacher card here
    console.log('Deleted teacher ID:', currentDeleteId);
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
  const cards = document.querySelectorAll('.teacher-card');
  
  cards.forEach(card => {
    const text = card.textContent.toLowerCase();
    card.style.display = text.includes(searchTerm) ? '' : 'none';
  });
});
</script>

</body>
</html>
