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
  <title>Reports & Analytics | SCTI Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    
    .stats-grid {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px; margin-bottom: 30px;
    }
    .stat-card {
      background: white; padding: 25px; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      display: flex; align-items: center; gap: 15px;
      transition: all 0.3s;
    }
    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    .stat-icon {
      width: 60px; height: 60px; border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 28px; color: white;
    }
    .stat-icon.blue { background: linear-gradient(135deg, #004080, #0059b3); }
    .stat-icon.green { background: linear-gradient(135deg, #28a745, #20c997); }
    .stat-icon.orange { background: linear-gradient(135deg, #fd7e14, #ffc107); }
    .stat-icon.purple { background: linear-gradient(135deg, #6f42c1, #e83e8c); }
    .stat-info h3 { margin: 0; font-size: 32px; color: #004080; }
    .stat-info p { margin: 5px 0 0 0; color: #666; font-size: 13px; }
    .stat-change {
      font-size: 12px; margin-top: 5px;
    }
    .stat-change.positive { color: #28a745; }
    .stat-change.negative { color: #dc3545; }
    
    .charts-grid {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
      gap: 25px; margin-bottom: 30px;
    }
    
    .chart-card {
      background: white; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 25px;
    }
    .chart-card h3 {
      margin: 0 0 20px 0; color: #004080;
      display: flex; align-items: center; gap: 10px;
    }
    .chart-container {
      position: relative; height: 300px;
    }
    
    .report-actions {
      background: white; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 25px;
      margin-bottom: 30px;
    }
    .report-actions h3 {
      margin: 0 0 20px 0; color: #004080;
    }
    .action-grid {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
    }
    .action-btn {
      background: linear-gradient(135deg, #004080, #0059b3);
      color: white; padding: 15px 20px; border-radius: 8px;
      text-decoration: none; display: flex; align-items: center;
      gap: 12px; transition: all 0.3s; border: none;
      cursor: pointer; font-size: 14px;
    }
    .action-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(0,64,128,0.3);
    }
    .action-btn i { font-size: 20px; }
    
    .table-card {
      background: white; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 25px;
    }
    .table-card h3 {
      margin: 0 0 20px 0; color: #004080;
    }
    table {
      width: 100%; border-collapse: collapse;
    }
    thead {
      background: linear-gradient(135deg, #004080, #0059b3);
      color: white;
    }
    th {
      padding: 12px; text-align: left; font-weight: 600;
    }
    td {
      padding: 12px; border-bottom: 1px solid #e0e0e0;
    }
    tbody tr:hover {
      background: #f8f9fa;
    }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Reports & Analytics - View comprehensive reports and statistics</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <div>
      <h1><i class="fa fa-chart-bar"></i> Reports & Analytics</h1>
      <div class="breadcrumb">
        <a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Reports
      </div>
    </div>
    <a href="../dashboards/admin-dashboard.php" class="btn btn-primary">
      <i class="fa fa-arrow-left"></i> Back
    </a>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon blue">
        <i class="fa fa-users"></i>
      </div>
      <div class="stat-info">
        <h3>245</h3>
        <p>Total Students</p>
        <div class="stat-change positive">
          <i class="fa fa-arrow-up"></i> +12% from last month
        </div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon green">
        <i class="fa fa-percentage"></i>
      </div>
      <div class="stat-info">
        <h3>87%</h3>
        <p>Avg Attendance</p>
        <div class="stat-change positive">
          <i class="fa fa-arrow-up"></i> +3% from last month
        </div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon orange">
        <i class="fa fa-graduation-cap"></i>
      </div>
      <div class="stat-info">
        <h3>3.4</h3>
        <p>Avg GPA</p>
        <div class="stat-change positive">
          <i class="fa fa-arrow-up"></i> +0.2 from last sem
        </div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon purple">
        <i class="fa fa-dollar-sign"></i>
      </div>
      <div class="stat-info">
        <h3>92%</h3>
        <p>Fee Collection</p>
        <div class="stat-change negative">
          <i class="fa fa-arrow-down"></i> -5% from target
        </div>
      </div>
    </div>
  </div>

  <div class="report-actions">
    <h3><i class="fa fa-download"></i> Export Reports</h3>
    <div class="action-grid">
      <button class="action-btn" onclick="alert('Student Report export coming soon!')">
        <i class="fa fa-file-pdf"></i>
        <span>Student Report</span>
      </button>
      <button class="action-btn" onclick="alert('Attendance Report export coming soon!')">
        <i class="fa fa-file-excel"></i>
        <span>Attendance Report</span>
      </button>
      <button class="action-btn" onclick="alert('Grade Report export coming soon!')">
        <i class="fa fa-file-csv"></i>
        <span>Grade Report</span>
      </button>
      <button class="action-btn" onclick="alert('Financial Report export coming soon!')">
        <i class="fa fa-file-invoice-dollar"></i>
        <span>Financial Report</span>
      </button>
    </div>
  </div>

  <div class="charts-grid">
    
    <div class="chart-card">
      <h3><i class="fa fa-chart-line"></i> Student Enrollment Trend</h3>
      <div class="chart-container">
        <canvas id="enrollmentChart"></canvas>
      </div>
    </div>

    <div class="chart-card">
      <h3><i class="fa fa-chart-pie"></i> Students by Program</h3>
      <div class="chart-container">
        <canvas id="programChart"></canvas>
      </div>
    </div>

    <div class="chart-card">
      <h3><i class="fa fa-chart-bar"></i> Monthly Attendance</h3>
      <div class="chart-container">
        <canvas id="attendanceChart"></canvas>
      </div>
    </div>

    <div class="chart-card">
      <h3><i class="fa fa-chart-area"></i> Grade Distribution</h3>
      <div class="chart-container">
        <canvas id="gradeChart"></canvas>
      </div>
    </div>

  </div>

  <div class="table-card">
    <h3><i class="fa fa-table"></i> Program-wise Performance</h3>
    <table>
      <thead>
        <tr>
          <th>Program</th>
          <th>Students</th>
          <th>Avg Attendance</th>
          <th>Avg GPA</th>
          <th>Pass Rate</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>B.Tech Ed in IT</td>
          <td>120</td>
          <td>89%</td>
          <td>3.6</td>
          <td>94%</td>
        </tr>
        <tr>
          <td>Diploma in IT</td>
          <td>85</td>
          <td>86%</td>
          <td>3.4</td>
          <td>91%</td>
        </tr>
        <tr>
          <td>Diploma in Civil</td>
          <td>45</td>
          <td>84%</td>
          <td>3.2</td>
          <td>88%</td>
        </tr>
        <tr>
          <td>Diploma in Electrical</td>
          <td>38</td>
          <td>87%</td>
          <td>3.3</td>
          <td>90%</td>
        </tr>
      </tbody>
    </table>
  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Admin Panel</p>
</footer>

<script>
// Enrollment Trend Chart
const enrollmentCtx = document.getElementById('enrollmentChart').getContext('2d');
new Chart(enrollmentCtx, {
  type: 'line',
  data: {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    datasets: [{
      label: 'New Enrollments',
      data: [15, 22, 18, 28, 25, 32],
      borderColor: '#004080',
      backgroundColor: 'rgba(0, 64, 128, 0.1)',
      tension: 0.4
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: true }
    }
  }
});

// Program Distribution Chart
const programCtx = document.getElementById('programChart').getContext('2d');
new Chart(programCtx, {
  type: 'doughnut',
  data: {
    labels: ['B.Tech Ed IT', 'Diploma IT', 'Diploma Civil', 'Diploma Electrical'],
    datasets: [{
      data: [120, 85, 45, 38],
      backgroundColor: ['#004080', '#0059b3', '#28a745', '#ffc107']
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { position: 'bottom' }
    }
  }
});

// Attendance Chart
const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
new Chart(attendanceCtx, {
  type: 'bar',
  data: {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    datasets: [{
      label: 'Attendance %',
      data: [85, 87, 86, 89, 88, 90],
      backgroundColor: '#28a745'
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      y: {
        beginAtZero: true,
        max: 100
      }
    }
  }
});

// Grade Distribution Chart
const gradeCtx = document.getElementById('gradeChart').getContext('2d');
new Chart(gradeCtx, {
  type: 'bar',
  data: {
    labels: ['A+', 'A', 'B+', 'B', 'C+', 'C', 'D', 'F'],
    datasets: [{
      label: 'Number of Students',
      data: [45, 68, 52, 38, 25, 12, 4, 1],
      backgroundColor: ['#28a745', '#20c997', '#17a2b8', '#0059b3', '#ffc107', '#fd7e14', '#dc3545', '#c82333']
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false }
    }
  }
});
</script>

</body>
</html>
