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
      transition: all 0.3s;
      cursor: pointer;
      position: relative;
      overflow: hidden;
    }
    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(0,64,128,0.1), transparent);
      transition: left 0.5s;
    }
    .stat-card:hover::before {
      left: 100%;
    }
    .stat-card:hover {
      transform: translateY(-5px) scale(1.02);
      box-shadow: 0 8px 25px rgba(0,64,128,0.2);
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
      border: 2px solid #e0e0e0; border-radius: 8px;
      font-size: 14px; transition: all 0.3s;
      background: white;
    }
    .search-box input:focus {
      outline: none; border-color: #004080;
      box-shadow: 0 0 0 3px rgba(0,64,128,0.1);
      transform: translateY(-2px);
    }
    .search-box i {
      position: absolute; right: 15px; top: 50%;
      transform: translateY(-50%); color: #999;
      transition: all 0.3s;
    }
    .search-box input:focus + i {
      color: #004080;
      transform: translateY(-50%) scale(1.2);
    }
    
    .filter-select {
      padding: 12px 15px; border: 2px solid #e0e0e0;
      border-radius: 8px; font-size: 14px; cursor: pointer;
      transition: all 0.3s;
      background: white;
    }
    .filter-select:hover {
      border-color: #004080;
      transform: translateY(-2px);
    }
    .filter-select:focus {
      outline: none;
      border-color: #004080;
      box-shadow: 0 0 0 3px rgba(0,64,128,0.1);
    }
    
    /* Floating Action Button */
    .fab {
      position: fixed;
      bottom: 30px;
      right: 30px;
      width: 60px;
      height: 60px;
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white;
      border-radius: 50%;
      border: none;
      font-size: 24px;
      cursor: pointer;
      box-shadow: 0 4px 20px rgba(40,167,69,0.4);
      transition: all 0.3s;
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .fab:hover {
      transform: scale(1.1) rotate(90deg);
      box-shadow: 0 6px 30px rgba(40,167,69,0.6);
    }
    .fab:active {
      transform: scale(0.95);
    }
    
    .notice-card {
      background: white; border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
      margin-bottom: 20px; overflow: hidden;
      border-left: 5px solid #004080;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      position: relative;
    }
    .notice-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, #004080, #0059b3, #17a2b8);
      transform: scaleX(0);
      transform-origin: left;
      transition: transform 0.4s;
    }
    .notice-card:hover::before {
      transform: scaleX(1);
    }
    .notice-card:hover {
      transform: translateY(-8px) scale(1.01);
      box-shadow: 0 12px 35px rgba(0,64,128,0.2);
    }
    
    .notice-card.urgent {
      border-left-color: #dc3545;
    }
    .notice-card.urgent::before {
      background: linear-gradient(90deg, #dc3545, #c82333, #bd2130);
    }
    .notice-card.important {
      border-left-color: #ffc107;
    }
    .notice-card.important::before {
      background: linear-gradient(90deg, #ffc107, #ff9800, #f57c00);
    }
    
    .notice-header {
      display: flex; justify-content: space-between;
      align-items: start; padding: 25px;
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      position: relative;
    }
    .notice-header::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(0,64,128,0.2), transparent);
    }
    
    .notice-title h3 {
      margin: 0 0 10px 0; color: #004080; font-size: 20px;
      font-weight: 700;
      transition: color 0.3s;
    }
    .notice-card:hover .notice-title h3 {
      color: #0059b3;
    }
    .notice-meta {
      font-size: 13px; color: #666;
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
    }
    .notice-meta i {
      margin-right: 5px; color: #004080;
      transition: transform 0.3s;
    }
    .notice-card:hover .notice-meta i {
      transform: scale(1.2);
    }
    .notice-meta span {
      display: inline-flex;
      align-items: center;
    }
    
    .notice-badges {
      display: flex; gap: 8px; flex-direction: column;
      align-items: flex-end;
    }
    
    .status-badge {
      padding: 6px 14px; border-radius: 20px;
      font-size: 11px; font-weight: 700;
      text-transform: uppercase; display: inline-block;
      letter-spacing: 0.5px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      animation: fadeIn 0.5s;
    }
    .status-badge.published { background: linear-gradient(135deg, #d4edda, #c3e6cb); color: #155724; }
    .status-badge.draft { background: linear-gradient(135deg, #fff3cd, #ffeaa7); color: #856404; }
    .status-badge.archived { background: linear-gradient(135deg, #f8d7da, #f5c6cb); color: #721c24; }
    
    .priority-badge {
      padding: 6px 14px; border-radius: 20px;
      font-size: 11px; font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.15);
      animation: fadeIn 0.5s 0.1s backwards;
    }
    .priority-badge.urgent { 
      background: linear-gradient(135deg, #dc3545, #c82333); 
      color: white;
      animation: pulse 2s infinite;
    }
    .priority-badge.important { 
      background: linear-gradient(135deg, #ffc107, #ff9800); 
      color: white; 
    }
    .priority-badge.normal { 
      background: linear-gradient(135deg, #17a2b8, #138496); 
      color: white; 
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }
    
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
      padding: 8px 15px; font-size: 13px; border-radius: 6px;
      border: none; cursor: pointer; transition: all 0.3s;
      display: inline-flex; align-items: center; gap: 6px;
      font-weight: 500;
      position: relative;
      overflow: hidden;
    }
    .btn-sm::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255,255,255,0.3);
      transform: translate(-50%, -50%);
      transition: width 0.4s, height 0.4s;
    }
    .btn-sm:hover::before {
      width: 200px;
      height: 200px;
    }
    .btn-sm i {
      position: relative;
      z-index: 1;
    }
    .btn-info { background: linear-gradient(135deg, #17a2b8, #138496); color: white; }
    .btn-info:hover { background: linear-gradient(135deg, #138496, #117a8b); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(23,162,184,0.4); }
    .btn-warning { background: linear-gradient(135deg, #ffc107, #ff9800); color: white; }
    .btn-warning:hover { background: linear-gradient(135deg, #ff9800, #f57c00); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(255,193,7,0.4); }
    .btn-danger { background: linear-gradient(135deg, #dc3545, #c82333); color: white; }
    .btn-danger:hover { background: linear-gradient(135deg, #c82333, #bd2130); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(220,53,69,0.4); }
    
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
    .form-group textarea {
      resize: vertical; min-height: 120px;
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
    .delete-modal .notice-title-text {
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
            <button class="btn-sm btn-info" onclick="viewNotice(1, 'Admission Open for Academic Year 2025/26', 'Applications are now open for the academic year 2025/26. Students interested in B.Tech Ed in IT, Diploma in IT, Civil, and Electrical Engineering programs can apply online or visit the admission office. Last date: March 30, 2025.', 'Admin', 'All Students', 'Published', 'Urgent', 'March 1, 2025')">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="btn-sm btn-warning" onclick="editNotice(1, 'Admission Open for Academic Year 2025/26', 'Applications are now open for the academic year 2025/26. Students interested in B.Tech Ed in IT, Diploma in IT, Civil, and Electrical Engineering programs can apply online or visit the admission office. Last date: March 30, 2025.', 'All Students', 'Published', 'Urgent')">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn-sm btn-danger" onclick="deleteNotice(1, 'Admission Open for Academic Year 2025/26')">
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
            <button class="btn-sm btn-info" onclick="viewNotice(2, 'Semester Examination Schedule Released', 'The semester examination schedule for all programs has been released. Exams will begin from March 15, 2025. Students are advised to check the detailed schedule on the notice board and prepare accordingly.', 'Admin', 'All Students', 'Published', 'Important', 'February 28, 2025')">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="btn-sm btn-warning" onclick="editNotice(2, 'Semester Examination Schedule Released', 'The semester examination schedule for all programs has been released. Exams will begin from March 15, 2025. Students are advised to check the detailed schedule on the notice board and prepare accordingly.', 'All Students', 'Published', 'Important')">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn-sm btn-danger" onclick="deleteNotice(2, 'Semester Examination Schedule Released')">
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
            <button class="btn-sm btn-info" onclick="viewNotice(3, 'Sports Week Announcement', 'SCTI Sports Week will be held from December 20-25, 2024. Various indoor and outdoor games will be organized. Interested students can register with the sports coordinator by December 15.', 'Admin', 'All Students', 'Published', 'Normal', 'December 1, 2024')">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="btn-sm btn-warning" onclick="editNotice(3, 'Sports Week Announcement', 'SCTI Sports Week will be held from December 20-25, 2024. Various indoor and outdoor games will be organized. Interested students can register with the sports coordinator by December 15.', 'All Students', 'Published', 'Normal')">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn-sm btn-danger" onclick="deleteNotice(3, 'Sports Week Announcement')">
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
            <button class="btn-sm btn-info" onclick="viewNotice(4, 'Library Hours Extended', 'Due to upcoming examinations, library hours have been extended. The library will now be open from 7:00 AM to 8:00 PM on weekdays and 8:00 AM to 6:00 PM on weekends.', 'Admin', 'All Students', 'Draft', 'Normal', 'March 5, 2025')">
              <i class="fa fa-eye"></i> View
            </button>
            <button class="btn-sm btn-warning" onclick="editNotice(4, 'Library Hours Extended', 'Due to upcoming examinations, library hours have been extended. The library will now be open from 7:00 AM to 8:00 PM on weekdays and 8:00 AM to 6:00 PM on weekends.', 'All Students', 'Draft', 'Normal')">
              <i class="fa fa-edit"></i> Edit
            </button>
            <button class="btn-sm btn-danger" onclick="deleteNotice(4, 'Library Hours Extended')">
              <i class="fa fa-trash"></i> Delete
            </button>
          </div>
        </div>
      </div>

    </div>
  </div>

</div>

<!-- Floating Action Button -->
<button class="fab" onclick="alert('Create Notice form coming soon!')" title="Create New Notice">
  <i class="fa fa-plus"></i>
</button>

<!-- View Notice Modal -->
<div id="viewModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2><i class="fa fa-bullhorn"></i> Notice Details</h2>
      <button class="modal-close" onclick="closeModal('viewModal')">&times;</button>
    </div>
    <div class="modal-body" id="viewModalBody">
      <!-- Content will be populated by JavaScript -->
    </div>
  </div>
</div>

<!-- Edit Notice Modal -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2><i class="fa fa-edit"></i> Edit Notice</h2>
      <button class="modal-close" onclick="closeModal('editModal')">&times;</button>
    </div>
    <div class="modal-body">
      <form id="editForm">
        <input type="hidden" id="editNoticeId">
        
        <div class="form-group">
          <label for="editTitle"><i class="fa fa-heading"></i> Notice Title</label>
          <input type="text" id="editTitle" required>
        </div>
        
        <div class="form-group">
          <label for="editContent"><i class="fa fa-align-left"></i> Content</label>
          <textarea id="editContent" required></textarea>
        </div>
        
        <div class="form-group">
          <label for="editTargetAudience"><i class="fa fa-users"></i> Target Audience</label>
          <select id="editTargetAudience" required>
            <option value="All Students">All Students</option>
            <option value="All Teachers">All Teachers</option>
            <option value="All Staff">All Staff</option>
            <option value="Everyone">Everyone</option>
            <option value="IT Department">IT Department</option>
            <option value="Civil Department">Civil Department</option>
            <option value="Electrical Department">Electrical Department</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="editStatus"><i class="fa fa-toggle-on"></i> Status</label>
          <select id="editStatus" required>
            <option value="Published">Published</option>
            <option value="Draft">Draft</option>
            <option value="Archived">Archived</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="editPriority"><i class="fa fa-flag"></i> Priority</label>
          <select id="editPriority" required>
            <option value="Urgent">Urgent</option>
            <option value="Important">Important</option>
            <option value="Normal">Normal</option>
          </select>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">
        <i class="fa fa-times"></i> Cancel
      </button>
      <button type="button" class="btn btn-save" onclick="saveNotice()">
        <i class="fa fa-save"></i> Save Changes
      </button>
    </div>
  </div>
</div>

<!-- Delete Notice Modal -->
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
      <p>Do you really want to delete this notice?</p>
      <p class="notice-title-text" id="deleteNoticeTitle"></p>
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

// View Notice Details
function viewNotice(id, title, content, postedBy, targetAudience, status, priority, date) {
  const modalBody = document.getElementById('viewModalBody');
  
  const statusClass = status.toLowerCase();
  const priorityClass = priority.toLowerCase();
  
  modalBody.innerHTML = `
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-heading"></i> Notice Title</div>
      <div class="detail-value">${title}</div>
    </div>
    
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-align-left"></i> Content</div>
      <div class="detail-value">${content}</div>
    </div>
    
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-user"></i> Posted By</div>
      <div class="detail-value">${postedBy}</div>
    </div>
    
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-users"></i> Target Audience</div>
      <div class="detail-value">${targetAudience}</div>
    </div>
    
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-calendar"></i> Date</div>
      <div class="detail-value">${date}</div>
    </div>
    
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-toggle-on"></i> Status</div>
      <div class="detail-value"><span class="status-badge ${statusClass}">${status}</span></div>
    </div>
    
    <div class="detail-group">
      <div class="detail-label"><i class="fa fa-flag"></i> Priority</div>
      <div class="detail-value"><span class="priority-badge ${priorityClass}">${priority}</span></div>
    </div>
  `;
  
  openModal('viewModal');
}

// Edit Notice
function editNotice(id, title, content, targetAudience, status, priority) {
  document.getElementById('editNoticeId').value = id;
  document.getElementById('editTitle').value = title;
  document.getElementById('editContent').value = content;
  document.getElementById('editTargetAudience').value = targetAudience;
  document.getElementById('editStatus').value = status;
  document.getElementById('editPriority').value = priority;
  
  openModal('editModal');
}

// Save Notice Changes
function saveNotice() {
  const id = document.getElementById('editNoticeId').value;
  const title = document.getElementById('editTitle').value;
  const content = document.getElementById('editContent').value;
  const targetAudience = document.getElementById('editTargetAudience').value;
  const status = document.getElementById('editStatus').value;
  const priority = document.getElementById('editPriority').value;
  
  // Validate form
  if (!title || !content || !targetAudience || !status || !priority) {
    alert('Please fill in all required fields');
    return;
  }
  
  // Here you would normally send data to server
  // For now, we'll just show success message
  
  closeModal('editModal');
  showSuccess('Notice updated successfully!');
  
  // In a real application, you would reload the notice data here
  console.log('Updated notice:', { id, title, content, targetAudience, status, priority });
}

// Delete Notice
function deleteNotice(id, title) {
  currentDeleteId = id;
  document.getElementById('deleteNoticeTitle').textContent = `"${title}"`;
  openModal('deleteModal');
}

// Confirm Delete
function confirmDelete() {
  if (currentDeleteId) {
    // Here you would normally send delete request to server
    // For now, we'll just show success message
    
    closeModal('deleteModal');
    showSuccess('Notice deleted successfully!');
    
    // In a real application, you would remove the notice card here
    console.log('Deleted notice ID:', currentDeleteId);
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

// Simple search functionality with animation
document.getElementById('searchInput').addEventListener('input', function(e) {
  const searchTerm = e.target.value.toLowerCase();
  const cards = document.querySelectorAll('.notice-card');
  
  cards.forEach((card, index) => {
    const text = card.textContent.toLowerCase();
    if (text.includes(searchTerm)) {
      card.style.display = '';
      card.style.animation = `fadeIn 0.5s ${index * 0.1}s backwards`;
    } else {
      card.style.display = 'none';
    }
  });
});

// Filter by status
document.getElementById('statusFilter').addEventListener('change', function(e) {
  const status = e.target.value.toLowerCase();
  const cards = document.querySelectorAll('.notice-card');
  
  cards.forEach(card => {
    if (!status) {
      card.style.display = '';
    } else {
      const badge = card.querySelector('.status-badge');
      const hasStatus = badge && badge.textContent.toLowerCase().includes(status);
      card.style.display = hasStatus ? '' : 'none';
    }
  });
});

// Filter by priority
document.getElementById('priorityFilter').addEventListener('change', function(e) {
  const priority = e.target.value.toLowerCase();
  const cards = document.querySelectorAll('.notice-card');
  
  cards.forEach(card => {
    if (!priority) {
      card.style.display = '';
    } else {
      const badge = card.querySelector('.priority-badge');
      const hasPriority = badge && badge.textContent.toLowerCase().includes(priority);
      card.style.display = hasPriority ? '' : 'none';
    }
  });
});

// Add smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    if (target) {
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});
</script>

</body>
</html>
