<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header('Location: ../index.php'); exit();
}
require_once '../includes/config.php';

$studentId = $_SESSION['user_id'] ?? 0;

try {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM attendance WHERE student_id = ? ORDER BY attendance_date DESC LIMIT 60");
    $stmt->execute([$studentId]);
    $records = $stmt->fetchAll();

    $total    = count($records);
    $present  = count(array_filter($records, fn($r) => strtolower($r['status']) === 'present'));
    $absent   = count(array_filter($records, fn($r) => strtolower($r['status']) === 'absent'));
    $late     = count(array_filter($records, fn($r) => strtolower($r['status']) === 'late'));
    $pct      = $total > 0 ? round(($present / $total) * 100) : 0;
} catch(Exception $e) {
    $records = []; $total = 0; $present = 0; $absent = 0; $late = 0; $pct = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Attendance | SCTI Student Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Segoe UI',sans-serif;background:#f5f7fa}
    .top-header{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:10px 20px;font-size:14px}
    .footer{background:#2c3e50;color:white;text-align:center;padding:20px;margin-top:40px}
    .container{max-width:1200px;margin:0 auto;padding:20px}
    .page-header{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:30px;border-radius:10px;margin-bottom:30px;box-shadow:0 4px 15px rgba(40,167,69,.2)}
    .page-header h1{margin:0 0 8px;font-size:28px}
    .breadcrumb{background:transparent;padding:0;font-size:14px}
    .breadcrumb a{color:white;text-decoration:none}
    .stats-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:15px;margin-bottom:25px}
    .stat-box{background:white;padding:18px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.1);text-align:center}
    .stat-box h3{font-size:32px;color:#28a745;margin:8px 0}
    .stat-box p{color:#666;font-size:13px}
    .attendance-card{background:white;padding:25px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.1);margin-bottom:20px}
    .attendance-card h2{color:#28a745;margin-bottom:18px;font-size:18px}
    .attendance-table{width:100%;border-collapse:collapse}
    .attendance-table th{background:#f8f9fa;padding:13px;text-align:left;color:#333;font-weight:600;border-bottom:2px solid #dee2e6}
    .attendance-table td{padding:13px;border-bottom:1px solid #dee2e6}
    .attendance-table tr:hover{background:#f8f9fa}
    .status-badge{padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;display:inline-block}
    .status-present{background:#d4edda;color:#155724}
    .status-absent{background:#f8d7da;color:#721c24}
    .status-late{background:#fff3cd;color:#856404}
    .empty-state{text-align:center;padding:60px 20px;color:#999}
    .empty-state i{font-size:56px;display:block;margin-bottom:15px;color:#ccc}
    .pct-bar{background:#e0e0e0;border-radius:20px;height:12px;margin-top:8px;overflow:hidden}
    .pct-fill{height:100%;border-radius:20px;background:linear-gradient(90deg,#28a745,#20c997);transition:width .5s}
  </style>
</head>
<body>
<div class="top-header"><marquee>Attendance Tracking - Monitor your class attendance and maintain good academic standing</marquee></div>
<div class="container">
  <div class="page-header">
    <h1><i class="fa fa-calendar-check"></i> My Attendance</h1>
    <div class="breadcrumb"><a href="../dashboards/student-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Attendance</div>
  </div>

  <div class="stats-row">
    <div class="stat-box">
      <i class="fa fa-check-circle" style="font-size:28px;color:#28a745"></i>
      <h3><?=$pct?>%</h3>
      <p>Overall Attendance</p>
      <div class="pct-bar"><div class="pct-fill" style="width:<?=$pct?>%"></div></div>
    </div>
    <div class="stat-box">
      <i class="fa fa-calendar-day" style="font-size:28px;color:#004080"></i>
      <h3><?=$present?></h3>
      <p>Classes Attended</p>
    </div>
    <div class="stat-box">
      <i class="fa fa-times-circle" style="font-size:28px;color:#dc3545"></i>
      <h3><?=$absent?></h3>
      <p>Classes Missed</p>
    </div>
    <div class="stat-box">
      <i class="fa fa-clock" style="font-size:28px;color:#ffc107"></i>
      <h3><?=$late?></h3>
      <p>Late Arrivals</p>
    </div>
  </div>

  <div class="attendance-card">
    <h2><i class="fa fa-list"></i> Attendance Records</h2>
    <?php if (empty($records)): ?>
    <div class="empty-state"><i class="fa fa-calendar"></i><p>No attendance records found yet.</p></div>
    <?php else: ?>
    <table class="attendance-table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Subject / Class</th>
          <th>Period</th>
          <th>Status</th>
          <th>Remarks</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($records as $r):
            $st = strtolower($r['status'] ?? 'present');
            $badgeCls = 'status-'.$st;
        ?>
        <tr>
          <td><?=date('M d, Y', strtotime($r['attendance_date']))?></td>
          <td><?=htmlspecialchars($r['class_name'] ?? '—')?></td>
          <td style="font-size:12px;color:#666"><?=htmlspecialchars($r['period'] ?? '—')?></td>
          <td><span class="status-badge <?=$badgeCls?>"><?=ucfirst($st)?></span></td>
          <td><?=htmlspecialchars($r['remarks'] ?? ($st==='present'?'On time':($st==='late'?'Late arrival':'Absent')))?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</div>
<footer class="footer"><p>© 2025 SCTI - Student Portal</p></footer>
</body>
</html>
