<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php'); exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Students | SCTI Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; }
    .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
    .page-header {
      background: linear-gradient(135deg, #004080 0%, #0059b3 100%);
      color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;
      display: flex; justify-content: space-between; align-items: center;
      box-shadow: 0 4px 15px rgba(0,64,128,0.2);
    }
    .page-header h1 { margin: 0 0 8px 0; font-size: 28px; }
    .breadcrumb { background: transparent !important; padding: 0; font-size: 14px; }
    .breadcrumb a { color: white; text-decoration: none; }
    .btn-add {
      background: rgba(255,255,255,0.2); color: white;
      padding: 12px 24px; border: 2px solid white; border-radius: 6px;
      cursor: pointer; font-size: 14px; transition: all 0.3s;
      text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-add:hover { background: white; color: #004080; }
    .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 15px; margin-bottom: 25px; }
    .stat-box {
      background: white; border-radius: 10px; padding: 20px; text-align: center;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-top: 4px solid #004080;
    }
    .stat-box .num { font-size: 30px; font-weight: 700; color: #004080; }
    .stat-box .lbl { color: #666; font-size: 13px; margin-top: 4px; }
    .filter-bar {
      background: white; padding: 20px; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 25px;
      display: flex; gap: 15px; flex-wrap: wrap; align-items: center;
    }
    .filter-select, .search-input {
      padding: 10px 15px; border: 2px solid #dee2e6; border-radius: 6px; font-size: 14px;
    }
    .search-input { flex: 1; min-width: 220px; }
    .table-card { background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden; }
    .table-card table { width: 100%; border-collapse: collapse; }
    .table-card th {
      background: linear-gradient(135deg, #004080, #0059b3);
      color: white; padding: 14px 15px; text-align: left; font-size: 14px;
    }
    .table-card td { padding: 13px 15px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
    .table-card tr:hover td { background: #f0f4ff; }
    .badge { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
    .badge-active { background: #d4edda; color: #155724; }
    .badge-inactive { background: #f8d7da; color: #721c24; }
    .action-btns { display: flex; gap: 6px; }
    .btn-icon {
      width: 32px; height: 32px; border: none; border-radius: 5px;
      cursor: pointer; display: flex; align-items: center; justify-content: center;
      font-size: 13px; transition: all 0.2s;
    }
    .btn-view-i { background: #cce5ff; color: #004085; }
    .btn-edit-i { background: #d4edda; color: #155724; }
    .btn-del-i { background: #f8d7da; color: #721c24; }
    .btn-icon:hover { transform: scale(1.1); }
    .avatar-sm {
      width: 36px; height: 36px; border-radius: 50%;
      background: linear-gradient(135deg, #004080, #0059b3);
      display: inline-flex; align-items: center; justify-content: center;
      color: white; font-size: 14px; font-weight: 700; margin-right: 8px;
    }
  </style>
</head>
<body>
<div class="top-header"><marquee>Manage Students - View, add, edit and manage all student records</marquee></div>
<div class="container">
  <div class="page-header">
    <div>
      <h1><i class="fa fa-user-graduate"></i> Manage Students</h1>
      <div class="breadcrumb"><a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Manage Students</div>
    </div>
    <button class="btn-add" onclick="alert('Add student feature coming soon!');"><i class="fa fa-plus"></i> Add Student</button>
  </div>

  <div class="stats-row">
    <div class="stat-box"><div class="num">245</div><div class="lbl">Total Students</div></div>
    <div class="stat-box"><div class="num">230</div><div class="lbl">Active</div></div>
    <div class="stat-box"><div class="num">15</div><div class="lbl">Inactive</div></div>
    <div class="stat-box"><div class="num">42</div><div class="lbl">New This Year</div></div>
  </div>

  <div class="filter-bar">
    <select class="filter-select"><option>All Programs</option><option>B.Tech IT</option><option>Diploma Civil</option><option>Diploma Electrical</option></select>
    <select class="filter-select"><option>All Semesters</option><option>Semester 1</option><option>Semester 2</option><option>Semester 3</option><option>Semester 4</option></select>
    <select class="filter-select"><option>All Status</option><option>Active</option><option>Inactive</option></select>
    <input type="text" class="search-input" placeholder="Search by name, ID or email...">
  </div>

  <div class="table-card">
    <table>
      <thead>
        <tr><th>#</th><th>Student</th><th>Student ID</th><th>Program</th><th>Semester</th><th>Contact</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td>
          <td><span class="avatar-sm">R</span>Ram Sharma</td>
          <td>STU-2024-001</td><td>B.Tech IT</td><td>Semester 2</td>
          <td>ram@email.com</td>
          <td><span class="badge badge-active">Active</span></td>
          <td><div class="action-btns"><button class="btn-icon btn-view-i" title="View"><i class="fa fa-eye"></i></button><button class="btn-icon btn-edit-i" title="Edit"><i class="fa fa-edit"></i></button><button class="btn-icon btn-del-i" title="Delete"><i class="fa fa-trash"></i></button></div></td>
        </tr>
        <tr>
          <td>2</td>
          <td><span class="avatar-sm">S</span>Sita Poudel</td>
          <td>STU-2024-002</td><td>Diploma Civil</td><td>Semester 1</td>
          <td>sita@email.com</td>
          <td><span class="badge badge-active">Active</span></td>
          <td><div class="action-btns"><button class="btn-icon btn-view-i"><i class="fa fa-eye"></i></button><button class="btn-icon btn-edit-i"><i class="fa fa-edit"></i></button><button class="btn-icon btn-del-i"><i class="fa fa-trash"></i></button></div></td>
        </tr>
        <tr>
          <td>3</td>
          <td><span class="avatar-sm">B</span>Bikash Thapa</td>
          <td>STU-2023-015</td><td>B.Tech IT</td><td>Semester 4</td>
          <td>bikash@email.com</td>
          <td><span class="badge badge-active">Active</span></td>
          <td><div class="action-btns"><button class="btn-icon btn-view-i"><i class="fa fa-eye"></i></button><button class="btn-icon btn-edit-i"><i class="fa fa-edit"></i></button><button class="btn-icon btn-del-i"><i class="fa fa-trash"></i></button></div></td>
        </tr>
        <tr>
          <td>4</td>
          <td><span class="avatar-sm">A</span>Anita Rai</td>
          <td>STU-2023-022</td><td>Diploma Electrical</td><td>Semester 3</td>
          <td>anita@email.com</td>
          <td><span class="badge badge-inactive">Inactive</span></td>
          <td><div class="action-btns"><button class="btn-icon btn-view-i"><i class="fa fa-eye"></i></button><button class="btn-icon btn-edit-i"><i class="fa fa-edit"></i></button><button class="btn-icon btn-del-i"><i class="fa fa-trash"></i></button></div></td>
        </tr>
        <tr>
          <td>5</td>
          <td><span class="avatar-sm">K</span>Krishna Karki</td>
          <td>STU-2024-008</td><td>B.Tech IT</td><td>Semester 1</td>
          <td>krishna@email.com</td>
          <td><span class="badge badge-active">Active</span></td>
          <td><div class="action-btns"><button class="btn-icon btn-view-i"><i class="fa fa-eye"></i></button><button class="btn-icon btn-edit-i"><i class="fa fa-edit"></i></button><button class="btn-icon btn-del-i"><i class="fa fa-trash"></i></button></div></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<footer class="footer" style="margin-top:30px;"><p>© 2025 SCTI - Admin Panel</p></footer>
</body>
</html>
