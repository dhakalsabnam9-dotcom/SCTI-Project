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
      transform: translateY(-8px) scale(1.02);
      box-shadow: 0 10px 30px rgba(0,64,128,0.25);
      border: 2px solid #004080;
    }
    .stat-card:active {
      transform: translateY(-4px) scale(0.98);
    }
    .stat-card::after {
      content: '\f054';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      position: absolute;
      right: 20px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 20px;
      color: #004080;
      opacity: 0;
      transition: all 0.3s;
    }
    .stat-card:hover::after {
      opacity: 0.3;
      right: 15px;
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
      cursor: pointer;
      transition: all 0.3s;
      position: relative;
      overflow: hidden;
    }
    .chart-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(0,64,128,0.05), transparent);
      transition: left 0.5s;
      z-index: 1;
    }
    .chart-card:hover::before {
      left: 100%;
    }
    .chart-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,64,128,0.2);
      border: 2px solid #004080;
    }
    .chart-card:active {
      transform: translateY(-2px);
    }
    .chart-card::after {
      content: '\f065';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      position: absolute;
      top: 25px;
      right: 25px;
      font-size: 16px;
      color: #004080;
      opacity: 0;
      transition: all 0.3s;
      z-index: 2;
    }
    .chart-card:hover::after {
      opacity: 0.5;
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
    <div class="stat-card" onclick="showDetailedReport('students')">
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
    <div class="stat-card" onclick="showDetailedReport('attendance')">
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
    <div class="stat-card" onclick="showDetailedReport('grades')">
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
    <div class="stat-card" onclick="showDetailedReport('fees')">
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
    
    <div class="chart-card" onclick="expandChart('enrollment')">
      <h3><i class="fa fa-chart-line"></i> Student Enrollment Trend</h3>
      <div class="chart-container">
        <canvas id="enrollmentChart"></canvas>
      </div>
    </div>

    <div class="chart-card" onclick="expandChart('program')">
      <h3><i class="fa fa-chart-pie"></i> Students by Program</h3>
      <div class="chart-container">
        <canvas id="programChart"></canvas>
      </div>
    </div>

    <div class="chart-card" onclick="expandChart('attendance')">
      <h3><i class="fa fa-chart-bar"></i> Monthly Attendance</h3>
      <div class="chart-container">
        <canvas id="attendanceChart"></canvas>
      </div>
    </div>

    <div class="chart-card" onclick="expandChart('grade')">
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

<!-- Modal for Detailed Reports -->
<div id="reportModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 10000; align-items: center; justify-content: center;">
  <div style="background: white; border-radius: 15px; max-width: 800px; width: 90%; max-height: 90vh; overflow-y: auto; padding: 30px; position: relative; animation: slideIn 0.3s;">
    <button onclick="closeModal()" style="position: absolute; top: 20px; right: 20px; background: #dc3545; color: white; border: none; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; font-size: 20px; transition: all 0.3s;">×</button>
    <div id="modalContent"></div>
  </div>
</div>

<style>
@keyframes slideIn {
  from { transform: translateY(-50px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
</style>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Admin Panel</p>
</footer>

<script>
// Show detailed report
function showDetailedReport(type) {
  const modal = document.getElementById('reportModal');
  const content = document.getElementById('modalContent');
  
  let html = '';
  
  switch(type) {
    case 'students':
      html = `
        <h2 style="color: #004080; margin-bottom: 20px;"><i class="fa fa-users"></i> Student Report</h2>
        <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
          <h3 style="margin: 0 0 15px 0;">Summary</h3>
          <p><strong>Total Students:</strong> 245</p>
          <p><strong>New This Month:</strong> 32 (+12%)</p>
          <p><strong>Active Students:</strong> 238</p>
          <p><strong>On Leave:</strong> 7</p>
        </div>
        <div style="background: #e7f3ff; padding: 20px; border-radius: 10px;">
          <h3 style="margin: 0 0 15px 0;">Program Distribution</h3>
          <p>• B.Tech Ed in IT: 120 students (49%)</p>
          <p>• Diploma in IT: 85 students (35%)</p>
          <p>• Diploma in Civil: 45 students (18%)</p>
          <p>• Diploma in Electrical: 38 students (16%)</p>
        </div>
      `;
      break;
    case 'attendance':
      html = `
        <h2 style="color: #28a745; margin-bottom: 20px;"><i class="fa fa-percentage"></i> Attendance Report</h2>
        <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
          <h3 style="margin: 0 0 15px 0;">Overall Statistics</h3>
          <p><strong>Average Attendance:</strong> 87%</p>
          <p><strong>Highest:</strong> 95% (B.Tech Ed IT)</p>
          <p><strong>Lowest:</strong> 78% (Diploma Civil)</p>
          <p><strong>Trend:</strong> +3% from last month</p>
        </div>
        <div style="background: #d4edda; padding: 20px; border-radius: 10px;">
          <h3 style="margin: 0 0 15px 0;">Monthly Breakdown</h3>
          <p>• January: 85%</p>
          <p>• February: 87%</p>
          <p>• March: 86%</p>
          <p>• April: 89%</p>
          <p>• May: 88%</p>
          <p>• June: 90%</p>
        </div>
      `;
      break;
    case 'grades':
      html = `
        <h2 style="color: #fd7e14; margin-bottom: 20px;"><i class="fa fa-graduation-cap"></i> Grade Report</h2>
        <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
          <h3 style="margin: 0 0 15px 0;">Academic Performance</h3>
          <p><strong>Average GPA:</strong> 3.4</p>
          <p><strong>Highest GPA:</strong> 4.0</p>
          <p><strong>Pass Rate:</strong> 94%</p>
          <p><strong>Honor Roll:</strong> 68 students (28%)</p>
        </div>
        <div style="background: #fff3cd; padding: 20px; border-radius: 10px;">
          <h3 style="margin: 0 0 15px 0;">Grade Distribution</h3>
          <p>• A+ (4.0): 45 students (18%)</p>
          <p>• A (3.7-3.9): 68 students (28%)</p>
          <p>• B+ (3.3-3.6): 52 students (21%)</p>
          <p>• B (3.0-3.2): 38 students (16%)</p>
          <p>• C+ (2.7-2.9): 25 students (10%)</p>
          <p>• C (2.3-2.6): 12 students (5%)</p>
          <p>• Below C: 5 students (2%)</p>
        </div>
      `;
      break;
    case 'fees':
      html = `
        <h2 style="color: #6f42c1; margin-bottom: 20px;"><i class="fa fa-dollar-sign"></i> Fee Collection Report</h2>
        <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
          <h3 style="margin: 0 0 15px 0;">Collection Summary</h3>
          <p><strong>Total Expected:</strong> NPR 12,250,000</p>
          <p><strong>Total Collected:</strong> NPR 11,270,000 (92%)</p>
          <p><strong>Pending:</strong> NPR 980,000 (8%)</p>
          <p><strong>Overdue:</strong> NPR 245,000 (2%)</p>
        </div>
        <div style="background: #e7e3fc; padding: 20px; border-radius: 10px;">
          <h3 style="margin: 0 0 15px 0;">Program-wise Collection</h3>
          <p>• B.Tech Ed IT: 95% collected</p>
          <p>• Diploma IT: 91% collected</p>
          <p>• Diploma Civil: 88% collected</p>
          <p>• Diploma Electrical: 90% collected</p>
        </div>
      `;
      break;
  }
  
  content.innerHTML = html;
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

// Expand chart
function expandChart(type) {
  const modal = document.getElementById('reportModal');
  const content = document.getElementById('modalContent');
  
  let title = '';
  switch(type) {
    case 'enrollment': title = 'Student Enrollment Trend'; break;
    case 'program': title = 'Students by Program'; break;
    case 'attendance': title = 'Monthly Attendance'; break;
    case 'grade': title = 'Grade Distribution'; break;
  }
  
  content.innerHTML = `
    <h2 style="color: #004080; margin-bottom: 20px;"><i class="fa fa-chart-bar"></i> ${title}</h2>
    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; text-align: center;">
      <p style="font-size: 18px; color: #666;">Detailed chart view coming soon!</p>
      <p style="margin-top: 15px;">This will show an expanded, interactive version of the ${title.toLowerCase()} chart with more data points and filtering options.</p>
    </div>
  `;
  
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

// Close modal
function closeModal() {
  document.getElementById('reportModal').style.display = 'none';
  document.body.style.overflow = '';
}

// Close modal on outside click
document.getElementById('reportModal').addEventListener('click', function(e) {
  if (e.target === this) {
    closeModal();
  }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeModal();
  }
});
</script>

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
