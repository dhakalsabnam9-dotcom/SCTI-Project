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
  <title>Manage Teachers | SCTI Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
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
      display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-add:hover { background: white; color: #004080; }
    .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 15px; margin-bottom: 25px; }
    .stat-box { background: white; border-radius: 10px; padding: 20px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-top: 4px solid #004080; }
    .stat-box .num { font-size: 30px; font-weight: 700; color: #004080; }
    .stat-box .lbl { color: #666; font-size: 13px; margin-top: 4px; }
    .teachers-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
    .teacher-card {
      background: white; border-radius: 12px; overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); transition: all 0.3s;
    }
    .teacher-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,64,128,0.2); }
    .teacher-card-header {
      background: linear-gradient(135deg, #004080, #0059b3);
      padding: 25px; text-align: center; color: white;
    }
    .teacher-avatar {
      width: 80px; height: 80px; border-radius: 50%;
      background: rgba(255,255,255,0.2); border: 3px solid rgba(255,255,255,0.5);
      display: flex; align-items: center; justify-content: center;
      font-size: 32px; margin: 0 auto 12px;
    }
    .teacher-name { font-size: 18px; font-weight: 700; margin-bottom: 4px; }
    .teacher-dept { opacity: 0.9; font-size: 13px; }
    .teacher-card-body { padding: 20px; }
    .teacher-info { display: flex; flex-direction: column; gap: 8px; margin-bottom: 15px; }
    .info-row { display: flex; align-items: center; gap: 10px; color: #666; font-size: 13px; }
    .info-row i { color: #004080; width: 16px; }
    .subject-tags { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 15px; }
    .subject-tag { background: #e8f0fe; color: #004080; padding: 3px 10px; border-radius: 12px; font-size: 12px; }
    .card-actions { display: flex; gap: 8px; }
    .btn-sm {
      flex: 1; padding: 9px; border: none; border-radius: 6px;
      cursor: pointer; font-size: 13px; transition: all 0.3s;
      display: flex; align-items: center; justify-content: center; gap: 5px;
    }
    .btn-primary { background: linear-gradient(135deg, #004080, #0059b3); color: white; }
    .btn-primary:hover { transform: translateY(-2px); }
    .btn-danger { background: #f8d7da; color: #dc3545; }
    .btn-danger:hover { background: #dc3545; color: white; }
    .badge { padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; }
    .badge-active { background: #d4edda; color: #155724; }
    .filter-bar {
      background: white; padding: 20px; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 25px;
      display: flex; gap: 15px; flex-wrap: wrap;
    }
    .filter-select, .search-input { padding: 10px 15px; border: 2px solid #dee2e6; border-radius: 6px; font-size: 14px; }
    .search-input { flex: 1; min-width: 220px; }
  </style>
</head>
<body>
<div class="top-header"><marquee>Manage Teachers - View and manage all teaching staff records</marquee></div>
<div class="container">
  <div class="page-header">
    <div>
      <h1><i class="fa fa-chalkboard-teacher"></i> Manage Teachers</h1>
      <div class="breadcrumb"><a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Manage Teachers</div>
    </div>
    <button class="btn-add" onclick="alert('Add teacher feature coming soon!');"><i class="fa fa-plus"></i> Add Teacher</button>
  </div>

  <div class="stats-row">
    <div class="stat-box"><div class="num">18</div><div class="lbl">Total Teachers</div></div>
    <div class="stat-box"><div class="num">16</div><div class="lbl">Active</div></div>
    <div class="stat-box"><div class="num">2</div><div class="lbl">On Leave</div></div>
    <div class="stat-box"><div class="num">3</div><div class="lbl">Departments</div></div>
  </div>

  <div class="filter-bar">
    <select class="filter-select"><option>All Departments</option><option>Computer Science</option><option>Civil Engineering</option><option>Electrical</option></select>
    <input type="text" class="search-input" placeholder="Search teachers...">
  </div>

  <div class="teachers-grid">
    <div class="teacher-card">
      <div class="teacher-card-header">
        <div class="teacher-avatar"><i class="fa fa-user-tie"></i></div>
        <div class="teacher-name">Bibek Sharma</div>
        <div class="teacher-dept">Computer Science</div>
      </div>
      <div class="teacher-card-body">
        <div class="teacher-info">
          <div class="info-row"><i class="fa fa-id-badge"></i> TCH-2018-001</div>
          <div class="info-row"><i class="fa fa-envelope"></i> bibek@scti.edu.np</div>
          <div class="info-row"><i class="fa fa-graduation-cap"></i> M.Sc. Computer Science</div>
          <div class="info-row"><i class="fa fa-clock"></i> 8 Years Experience</div>
        </div>
        <div class="subject-tags">
          <span class="subject-tag">Programming</span>
          <span class="subject-tag">Database</span>
          <span class="subject-tag">Web Dev</span>
        </div>
        <div class="card-actions">
          <button class="btn-sm btn-primary"><i class="fa fa-eye"></i> View</button>
          <button class="btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</button>
          <button class="btn-sm btn-danger"><i class="fa fa-trash"></i></button>
        </div>
      </div>
    </div>

    <div class="teacher-card">
      <div class="teacher-card-header">
        <div class="teacher-avatar"><i class="fa fa-user-tie"></i></div>
        <div class="teacher-name">Santosh Karki</div>
        <div class="teacher-dept">Civil Engineering</div>
      </div>
      <div class="teacher-card-body">
        <div class="teacher-info">
          <div class="info-row"><i class="fa fa-id-badge"></i> TCH-2019-002</div>
          <div class="info-row"><i class="fa fa-envelope"></i> santosh@scti.edu.np</div>
          <div class="info-row"><i class="fa fa-graduation-cap"></i> B.E. Civil Engineering</div>
          <div class="info-row"><i class="fa fa-clock"></i> 6 Years Experience</div>
        </div>
        <div class="subject-tags">
          <span class="subject-tag">Surveying</span>
          <span class="subject-tag">Structures</span>
        </div>
        <div class="card-actions">
          <button class="btn-sm btn-primary"><i class="fa fa-eye"></i> View</button>
          <button class="btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</button>
          <button class="btn-sm btn-danger"><i class="fa fa-trash"></i></button>
        </div>
      </div>
    </div>

    <div class="teacher-card">
      <div class="teacher-card-header">
        <div class="teacher-avatar"><i class="fa fa-user-tie"></i></div>
        <div class="teacher-name">Tej Bahadur</div>
        <div class="teacher-dept">Electrical Engineering</div>
      </div>
      <div class="teacher-card-body">
        <div class="teacher-info">
          <div class="info-row"><i class="fa fa-id-badge"></i> TCH-2020-003</div>
          <div class="info-row"><i class="fa fa-envelope"></i> tej@scti.edu.np</div>
          <div class="info-row"><i class="fa fa-graduation-cap"></i> B.E. Electrical</div>
          <div class="info-row"><i class="fa fa-clock"></i> 5 Years Experience</div>
        </div>
        <div class="subject-tags">
          <span class="subject-tag">Circuit Theory</span>
          <span class="subject-tag">Electronics</span>
        </div>
        <div class="card-actions">
          <button class="btn-sm btn-primary"><i class="fa fa-eye"></i> View</button>
          <button class="btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</button>
          <button class="btn-sm btn-danger"><i class="fa fa-trash"></i></button>
        </div>
      </div>
    </div>
  </div>
</div>
<footer class="footer" style="margin-top:30px;"><p>© 2025 SCTI - Admin Panel</p></footer>
</body>
</html>
