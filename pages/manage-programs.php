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
  <title>Manage Programs | SCTI Admin</title>
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
    .breadcrumb a { color: white; text-decoration: none; font-size: 14px; }
    .btn-add {
      background: rgba(255,255,255,0.2); color: white;
      padding: 12px 24px; border: 2px solid white; border-radius: 6px;
      cursor: pointer; font-size: 14px; transition: all 0.3s;
      display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-add:hover { background: white; color: #004080; }
    .programs-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 25px; }
    .program-card {
      background: white; border-radius: 12px; overflow: hidden;
      box-shadow: 0 3px 15px rgba(0,0,0,0.1); transition: all 0.3s;
    }
    .program-card:hover { transform: translateY(-8px); box-shadow: 0 10px 30px rgba(0,64,128,0.2); }
    .program-banner {
      height: 8px;
    }
    .banner-blue { background: linear-gradient(90deg, #004080, #0059b3); }
    .banner-green { background: linear-gradient(90deg, #28a745, #20c997); }
    .banner-orange { background: linear-gradient(90deg, #fd7e14, #ffc107); }
    .banner-purple { background: linear-gradient(90deg, #6f42c1, #e83e8c); }
    .program-body { padding: 25px; }
    .program-icon {
      width: 60px; height: 60px; border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 26px; color: white; margin-bottom: 15px;
    }
    .icon-blue { background: linear-gradient(135deg, #004080, #0059b3); }
    .icon-green { background: linear-gradient(135deg, #28a745, #20c997); }
    .icon-orange { background: linear-gradient(135deg, #fd7e14, #ffc107); }
    .icon-purple { background: linear-gradient(135deg, #6f42c1, #e83e8c); }
    .program-title { font-size: 20px; font-weight: 700; color: #333; margin-bottom: 5px; }
    .program-code { color: #666; font-size: 13px; margin-bottom: 15px; }
    .program-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin: 15px 0; }
    .pstat { background: #f8f9fa; border-radius: 8px; padding: 10px; text-align: center; }
    .pstat .num { font-size: 20px; font-weight: 700; color: #004080; }
    .pstat .lbl { font-size: 11px; color: #666; }
    .program-desc { color: #666; font-size: 14px; line-height: 1.6; margin-bottom: 15px; }
    .badge { padding: 5px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; }
    .badge-active { background: #d4edda; color: #155724; }
    .card-actions { display: flex; gap: 8px; margin-top: 15px; }
    .btn-sm {
      flex: 1; padding: 10px; border: none; border-radius: 6px;
      cursor: pointer; font-size: 13px; transition: all 0.3s;
      display: flex; align-items: center; justify-content: center; gap: 5px;
    }
    .btn-primary { background: linear-gradient(135deg, #004080, #0059b3); color: white; }
    .btn-primary:hover { transform: translateY(-2px); }
    .btn-outline { background: white; color: #004080; border: 2px solid #004080; }
    .btn-outline:hover { background: #004080; color: white; }
  </style>
</head>
<body>
<div class="top-header"><marquee>Manage Programs - Oversee all academic programs offered at SCTI</marquee></div>
<div class="container">
  <div class="page-header">
    <div>
      <h1><i class="fa fa-graduation-cap"></i> Manage Programs</h1>
      <div class="breadcrumb"><a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Manage Programs</div>
    </div>
    <button class="btn-add" onclick="alert('Add program feature coming soon!');"><i class="fa fa-plus"></i> Add Program</button>
  </div>

  <div class="programs-grid">
    <div class="program-card">
      <div class="program-banner banner-blue"></div>
      <div class="program-body">
        <div class="program-icon icon-blue"><i class="fa fa-laptop-code"></i></div>
        <div class="program-title">B.Tech in IT</div>
        <div class="program-code">BTIT | 4 Years | Affiliated: TU</div>
        <div class="program-stats">
          <div class="pstat"><div class="num">120</div><div class="lbl">Students</div></div>
          <div class="pstat"><div class="num">8</div><div class="lbl">Semesters</div></div>
          <div class="pstat"><div class="num">6</div><div class="lbl">Teachers</div></div>
        </div>
        <p class="program-desc">Bachelor of Technology in Information Technology covering programming, databases, networking and software engineering.</p>
        <span class="badge badge-active">Active</span>
        <div class="card-actions">
          <button class="btn-sm btn-primary"><i class="fa fa-eye"></i> View</button>
          <button class="btn-sm btn-outline"><i class="fa fa-edit"></i> Edit</button>
        </div>
      </div>
    </div>

    <div class="program-card">
      <div class="program-banner banner-green"></div>
      <div class="program-body">
        <div class="program-icon icon-green"><i class="fa fa-hard-hat"></i></div>
        <div class="program-title">Diploma in Civil Engineering</div>
        <div class="program-code">DCE | 3 Years | Affiliated: CTEVT</div>
        <div class="program-stats">
          <div class="pstat"><div class="num">75</div><div class="lbl">Students</div></div>
          <div class="pstat"><div class="num">6</div><div class="lbl">Semesters</div></div>
          <div class="pstat"><div class="num">5</div><div class="lbl">Teachers</div></div>
        </div>
        <p class="program-desc">Diploma program in Civil Engineering covering construction, surveying, structural design and project management.</p>
        <span class="badge badge-active">Active</span>
        <div class="card-actions">
          <button class="btn-sm btn-primary"><i class="fa fa-eye"></i> View</button>
          <button class="btn-sm btn-outline"><i class="fa fa-edit"></i> Edit</button>
        </div>
      </div>
    </div>

    <div class="program-card">
      <div class="program-banner banner-orange"></div>
      <div class="program-body">
        <div class="program-icon icon-orange"><i class="fa fa-bolt"></i></div>
        <div class="program-title">Diploma in Electrical Engineering</div>
        <div class="program-code">DEE | 3 Years | Affiliated: CTEVT</div>
        <div class="program-stats">
          <div class="pstat"><div class="num">50</div><div class="lbl">Students</div></div>
          <div class="pstat"><div class="num">6</div><div class="lbl">Semesters</div></div>
          <div class="pstat"><div class="num">4</div><div class="lbl">Teachers</div></div>
        </div>
        <p class="program-desc">Diploma program in Electrical Engineering covering circuit theory, power systems, electronics and industrial automation.</p>
        <span class="badge badge-active">Active</span>
        <div class="card-actions">
          <button class="btn-sm btn-primary"><i class="fa fa-eye"></i> View</button>
          <button class="btn-sm btn-outline"><i class="fa fa-edit"></i> Edit</button>
        </div>
      </div>
    </div>

    <div class="program-card">
      <div class="program-banner banner-purple"></div>
      <div class="program-body">
        <div class="program-icon icon-purple"><i class="fa fa-tools"></i></div>
        <div class="program-title">Diploma in Mechanical Engineering</div>
        <div class="program-code">DME | 3 Years | Affiliated: CTEVT</div>
        <div class="program-stats">
          <div class="pstat"><div class="num">0</div><div class="lbl">Students</div></div>
          <div class="pstat"><div class="num">6</div><div class="lbl">Semesters</div></div>
          <div class="pstat"><div class="num">0</div><div class="lbl">Teachers</div></div>
        </div>
        <p class="program-desc">Diploma program in Mechanical Engineering. Admissions opening for 2026/27 academic year.</p>
        <span class="badge" style="background:#fff3cd;color:#856404;">Upcoming</span>
        <div class="card-actions">
          <button class="btn-sm btn-primary"><i class="fa fa-eye"></i> View</button>
          <button class="btn-sm btn-outline"><i class="fa fa-edit"></i> Edit</button>
        </div>
      </div>
    </div>
  </div>
</div>
<footer class="footer" style="margin-top:30px;"><p>© 2025 SCTI - Admin Panel</p></footer>
</body>
</html>
