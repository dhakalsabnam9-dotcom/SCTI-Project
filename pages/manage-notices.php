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
  <title>Manage Notices | SCTI Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; }
    .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
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
    .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 25px; }
    .stat-box { background: white; border-radius: 10px; padding: 18px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-top: 4px solid #004080; }
    .stat-box .num { font-size: 28px; font-weight: 700; color: #004080; }
    .stat-box .lbl { color: #666; font-size: 13px; margin-top: 4px; }
    .notice-list { display: flex; flex-direction: column; gap: 15px; }
    .notice-card {
      background: white; border-radius: 10px; padding: 25px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-left: 5px solid #004080;
      transition: all 0.3s;
    }
    .notice-card:hover { transform: translateX(5px); box-shadow: 0 5px 20px rgba(0,64,128,0.2); }
    .notice-card.urgent { border-left-color: #dc3545; }
    .notice-card.info { border-left-color: #17a2b8; }
    .notice-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
    .notice-title { font-size: 18px; font-weight: 700; color: #333; }
    .notice-badges { display: flex; gap: 8px; align-items: center; }
    .badge { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
    .badge-active { background: #d4edda; color: #155724; }
    .badge-urgent { background: #f8d7da; color: #721c24; }
    .badge-info { background: #cce5ff; color: #004085; }
    .notice-meta { display: flex; gap: 20px; margin-bottom: 12px; flex-wrap: wrap; }
    .meta-item { display: flex; align-items: center; gap: 6px; color: #666; font-size: 13px; }
    .meta-item i { color: #004080; }
    .notice-body { color: #555; font-size: 14px; line-height: 1.6; margin-bottom: 15px; }
    .notice-actions { display: flex; gap: 8px; }
    .btn-sm {
      padding: 8px 16px; border: none; border-radius: 5px;
      cursor: pointer; font-size: 13px; transition: all 0.3s;
      display: inline-flex; align-items: center; gap: 5px;
    }
    .btn-edit { background: #cce5ff; color: #004085; }
    .btn-edit:hover { background: #004080; color: white; }
    .btn-delete { background: #f8d7da; color: #dc3545; }
    .btn-delete:hover { background: #dc3545; color: white; }
    .btn-toggle { background: #d4edda; color: #155724; }
    .btn-toggle:hover { background: #28a745; color: white; }
  </style>
</head>
<body>
<div class="top-header"><marquee>Manage Notices - Create and manage announcements for students and staff</marquee></div>
<div class="container">
  <div class="page-header">
    <div>
      <h1><i class="fa fa-bullhorn"></i> Manage Notices</h1>
      <div class="breadcrumb"><a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Manage Notices</div>
    </div>
    <button class="btn-add" onclick="alert('Create notice feature coming soon!');"><i class="fa fa-plus"></i> Create Notice</button>
  </div>

  <div class="stats-row">
    <div class="stat-box"><div class="num">12</div><div class="lbl">Active Notices</div></div>
    <div class="stat-box"><div class="num">3</div><div class="lbl">Urgent</div></div>
    <div class="stat-box"><div class="num">5</div><div class="lbl">Archived</div></div>
    <div class="stat-box"><div class="num">2</div><div class="lbl">Drafts</div></div>
  </div>

  <div class="notice-list">
    <div class="notice-card urgent">
      <div class="notice-header">
        <div class="notice-title">Admission Open 2025/26 - B.Tech IT & Diploma Programs</div>
        <div class="notice-badges">
          <span class="badge badge-urgent">Urgent</span>
          <span class="badge badge-active">Active</span>
        </div>
      </div>
      <div class="notice-meta">
        <span class="meta-item"><i class="fa fa-calendar"></i> Published: Mar 1, 2026</span>
        <span class="meta-item"><i class="fa fa-user"></i> By: Admin</span>
        <span class="meta-item"><i class="fa fa-eye"></i> 245 views</span>
      </div>
      <div class="notice-body">Applications are now open for the 2025/26 academic year. Eligible candidates can apply online or visit the campus. Last date for application is April 30, 2026.</div>
      <div class="notice-actions">
        <button class="btn-sm btn-edit"><i class="fa fa-edit"></i> Edit</button>
        <button class="btn-sm btn-toggle"><i class="fa fa-archive"></i> Archive</button>
        <button class="btn-sm btn-delete"><i class="fa fa-trash"></i> Delete</button>
      </div>
    </div>

    <div class="notice-card">
      <div class="notice-header">
        <div class="notice-title">Mid-Term Examination Schedule - March 2026</div>
        <div class="notice-badges"><span class="badge badge-active">Active</span></div>
      </div>
      <div class="notice-meta">
        <span class="meta-item"><i class="fa fa-calendar"></i> Published: Mar 5, 2026</span>
        <span class="meta-item"><i class="fa fa-user"></i> By: Admin</span>
        <span class="meta-item"><i class="fa fa-eye"></i> 189 views</span>
      </div>
      <div class="notice-body">Mid-term examinations for all programs will be held from March 20-28, 2026. Students are advised to check the detailed schedule on the notice board.</div>
      <div class="notice-actions">
        <button class="btn-sm btn-edit"><i class="fa fa-edit"></i> Edit</button>
        <button class="btn-sm btn-toggle"><i class="fa fa-archive"></i> Archive</button>
        <button class="btn-sm btn-delete"><i class="fa fa-trash"></i> Delete</button>
      </div>
    </div>

    <div class="notice-card info">
      <div class="notice-header">
        <div class="notice-title">Sports Week 2026 - Registration Open</div>
        <div class="notice-badges"><span class="badge badge-info">Info</span><span class="badge badge-active">Active</span></div>
      </div>
      <div class="notice-meta">
        <span class="meta-item"><i class="fa fa-calendar"></i> Published: Mar 8, 2026</span>
        <span class="meta-item"><i class="fa fa-user"></i> By: Admin</span>
        <span class="meta-item"><i class="fa fa-eye"></i> 132 views</span>
      </div>
      <div class="notice-body">Annual Sports Week will be held from March 25-30, 2026. Students can register for various sports events. Registration deadline is March 20, 2026.</div>
      <div class="notice-actions">
        <button class="btn-sm btn-edit"><i class="fa fa-edit"></i> Edit</button>
        <button class="btn-sm btn-toggle"><i class="fa fa-archive"></i> Archive</button>
        <button class="btn-sm btn-delete"><i class="fa fa-trash"></i> Delete</button>
      </div>
    </div>

    <div class="notice-card">
      <div class="notice-header">
        <div class="notice-title">Library Timing Change - Effective March 15</div>
        <div class="notice-badges"><span class="badge badge-active">Active</span></div>
      </div>
      <div class="notice-meta">
        <span class="meta-item"><i class="fa fa-calendar"></i> Published: Mar 10, 2026</span>
        <span class="meta-item"><i class="fa fa-user"></i> By: Admin</span>
        <span class="meta-item"><i class="fa fa-eye"></i> 98 views</span>
      </div>
      <div class="notice-body">Library will now be open from 8:00 AM to 6:00 PM on weekdays and 9:00 AM to 2:00 PM on Saturdays, effective March 15, 2026.</div>
      <div class="notice-actions">
        <button class="btn-sm btn-edit"><i class="fa fa-edit"></i> Edit</button>
        <button class="btn-sm btn-toggle"><i class="fa fa-archive"></i> Archive</button>
        <button class="btn-sm btn-delete"><i class="fa fa-trash"></i> Delete</button>
      </div>
    </div>
  </div>
</div>
<footer class="footer" style="margin-top:30px;"><p>© 2025 SCTI - Admin Panel</p></footer>
</body>
</html>
