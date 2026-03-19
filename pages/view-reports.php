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
  <title>Reports | SCTI Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; }
    .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
    .page-header {
      background: linear-gradient(135deg, #004080 0%, #0059b3 100%);
      color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;
      box-shadow: 0 4px 15px rgba(0,64,128,0.2);
    }
    .page-header h1 { margin: 0 0 8px 0; font-size: 28px; }
    .breadcrumb { background: transparent !important; padding: 0; font-size: 14px; }
    .breadcrumb a { color: white; text-decoration: none; }
    .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .stat-card {
      background: white; border-radius: 12px; padding: 25px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;
      border-top: 5px solid;
    }
    .stat-card.blue { border-color: #004080; }
    .stat-card.green { border-color: #28a745; }
    .stat-card.orange { border-color: #fd7e14; }
    .stat-card.purple { border-color: #6f42c1; }
    .stat-card .icon { font-size: 36px; margin-bottom: 10px; }
    .stat-card.blue .icon { color: #004080; }
    .stat-card.green .icon { color: #28a745; }
    .stat-card.orange .icon { color: #fd7e14; }
    .stat-card.purple .icon { color: #6f42c1; }
    .stat-card .num { font-size: 36px; font-weight: 700; color: #333; }
    .stat-card .lbl { color: #666; font-size: 14px; margin-top: 5px; }
    .stat-card .change { font-size: 12px; margin-top: 8px; }
    .change.up { color: #28a745; }
    .change.down { color: #dc3545; }
    .reports-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px; }
    .report-card { background: white; border-radius: 10px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .report-card h3 { color: #004080; margin-bottom: 20px; font-size: 18px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; }
    .bar-chart { display: flex; flex-direction: column; gap: 12px; }
    .bar-row { display: flex; align-items: center; gap: 12px; }
    .bar-label { min-width: 120px; font-size: 13px; color: #555; }
    .bar-track { flex: 1; background: #f0f0f0; border-radius: 10px; height: 22px; overflow: hidden; }
    .bar-fill { height: 100%; border-radius: 10px; display: flex; align-items: center; padding-left: 8px; color: white; font-size: 12px; font-weight: 600; transition: width 1s ease; }
    .bar-blue { background: linear-gradient(90deg, #004080, #0059b3); }
    .bar-green { background: linear-gradient(90deg, #28a745, #20c997); }
    .bar-orange { background: linear-gradient(90deg, #fd7e14, #ffc107); }
    .bar-purple { background: linear-gradient(90deg, #6f42c1, #e83e8c); }
    .bar-val { min-width: 40px; font-size: 13px; font-weight: 600; color: #333; text-align: right; }
    .report-table { width: 100%; border-collapse: collapse; }
    .report-table th { background: #f8f9fa; padding: 10px 12px; text-align: left; font-size: 13px; color: #666; border-bottom: 2px solid #dee2e6; }
    .report-table td { padding: 10px 12px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
    .report-table tr:hover td { background: #f0f4ff; }
    .badge { padding: 3px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
    .badge-pass { background: #d4edda; color: #155724; }
    .badge-fail { background: #f8d7da; color: #721c24; }
    .export-btns { display: flex; gap: 10px; margin-bottom: 25px; }
    .btn-export {
      padding: 10px 20px; border: none; border-radius: 6px;
      cursor: pointer; font-size: 14px; transition: all 0.3s;
      display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-pdf { background: #dc3545; color: white; }
    .btn-excel { background: #28a745; color: white; }
    .btn-print { background: #004080; color: white; }
    .btn-export:hover { transform: translateY(-2px); opacity: 0.9; }
  </style>
</head>
<body>
<div class="top-header"><marquee>Reports & Analytics - Comprehensive overview of institutional performance</marquee></div>
<div class="container">
  <div class="page-header">
    <h1><i class="fa fa-chart-bar"></i> Reports & Analytics</h1>
    <div class="breadcrumb"><a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Reports</div>
  </div>

  <div class="stats-row">
    <div class="stat-card blue">
      <div class="icon"><i class="fa fa-users"></i></div>
      <div class="num">245</div><div class="lbl">Total Students</div>
      <div class="change up"><i class="fa fa-arrow-up"></i> +12% from last year</div>
    </div>
    <div class="stat-card green">
      <div class="icon"><i class="fa fa-chalkboard-teacher"></i></div>
      <div class="num">18</div><div class="lbl">Total Teachers</div>
      <div class="change up"><i class="fa fa-arrow-up"></i> +2 new this year</div>
    </div>
    <div class="stat-card orange">
      <div class="icon"><i class="fa fa-calendar-check"></i></div>
      <div class="num">87%</div><div class="lbl">Avg Attendance</div>
      <div class="change down"><i class="fa fa-arrow-down"></i> -3% from last month</div>
    </div>
    <div class="stat-card purple">
      <div class="icon"><i class="fa fa-star"></i></div>
      <div class="num">78%</div><div class="lbl">Pass Rate</div>
      <div class="change up"><i class="fa fa-arrow-up"></i> +5% from last term</div>
    </div>
  </div>

  <div class="export-btns">
    <button class="btn-export btn-pdf" onclick="exportPDF()"><i class="fa fa-file-pdf"></i> Export PDF</button>
    <button class="btn-export btn-excel" onclick="exportExcel()"><i class="fa fa-file-excel"></i> Export Excel</button>
    <button class="btn-export btn-print" onclick="window.print()"><i class="fa fa-print"></i> Print</button>
  </div>

  <div class="reports-grid">
    <div class="report-card">
      <h3><i class="fa fa-users"></i> Students by Program</h3>
      <div class="bar-chart">
        <div class="bar-row">
          <span class="bar-label">B.Tech IT</span>
          <div class="bar-track"><div class="bar-fill bar-blue" style="width:49%">120</div></div>
          <span class="bar-val">120</span>
        </div>
        <div class="bar-row">
          <span class="bar-label">Diploma Civil</span>
          <div class="bar-track"><div class="bar-fill bar-green" style="width:31%">75</div></div>
          <span class="bar-val">75</span>
        </div>
        <div class="bar-row">
          <span class="bar-label">Diploma Electrical</span>
          <div class="bar-track"><div class="bar-fill bar-orange" style="width:20%">50</div></div>
          <span class="bar-val">50</span>
        </div>
      </div>
    </div>

    <div class="report-card">
      <h3><i class="fa fa-calendar-check"></i> Attendance by Program</h3>
      <div class="bar-chart">
        <div class="bar-row">
          <span class="bar-label">B.Tech IT</span>
          <div class="bar-track"><div class="bar-fill bar-blue" style="width:89%">89%</div></div>
          <span class="bar-val">89%</span>
        </div>
        <div class="bar-row">
          <span class="bar-label">Diploma Civil</span>
          <div class="bar-track"><div class="bar-fill bar-green" style="width:85%">85%</div></div>
          <span class="bar-val">85%</span>
        </div>
        <div class="bar-row">
          <span class="bar-label">Diploma Electrical</span>
          <div class="bar-track"><div class="bar-fill bar-orange" style="width:82%">82%</div></div>
          <span class="bar-val">82%</span>
        </div>
      </div>
    </div>
  </div>

  <div class="report-card">
    <h3><i class="fa fa-table"></i> Program Performance Summary</h3>
    <table class="report-table">
      <thead>
        <tr><th>Program</th><th>Students</th><th>Avg Attendance</th><th>Pass Rate</th><th>Avg GPA</th><th>Status</th></tr>
      </thead>
      <tbody>
        <tr><td>B.Tech IT</td><td>120</td><td>89%</td><td>82%</td><td>3.2</td><td><span class="badge badge-pass">Good</span></td></tr>
        <tr><td>Diploma Civil</td><td>75</td><td>85%</td><td>78%</td><td>3.0</td><td><span class="badge badge-pass">Good</span></td></tr>
        <tr><td>Diploma Electrical</td><td>50</td><td>82%</td><td>70%</td><td>2.8</td><td><span class="badge badge-fail">Needs Attention</span></td></tr>
      </tbody>
    </table>
  </div>
</div>
<footer class="footer" style="margin-top:30px;"><p>© 2025 SCTI - Admin Panel</p></footer>

<style>
@media print {
  .export-btns, .top-header, footer, .breadcrumb { display: none !important; }
  body { background: white; }
  .page-header { box-shadow: none; border-radius: 0; }
  .report-card, .stat-card { box-shadow: none; border: 1px solid #ddd; }
}
</style>

<script>
function exportPDF() {
  window.print();
}

function exportExcel() {
  var rows = [
    ['Program', 'Students', 'Avg Attendance', 'Pass Rate', 'Avg GPA', 'Status'],
    ['B.Tech IT', '120', '89%', '82%', '3.2', 'Good'],
    ['Diploma Civil', '75', '85%', '78%', '3.0', 'Good'],
    ['Diploma Electrical', '50', '82%', '70%', '2.8', 'Needs Attention']
  ];
  var csv = rows.map(function(r) {
    return r.map(function(c) { return '"' + c + '"'; }).join(',');
  }).join('\n');
  var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  var url  = URL.createObjectURL(blob);
  var a    = document.createElement('a');
  a.href     = url;
  a.download = 'SCTI_Report_' + new Date().toISOString().slice(0,10) + '.csv';
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
}
</script>
</body>
</html>
