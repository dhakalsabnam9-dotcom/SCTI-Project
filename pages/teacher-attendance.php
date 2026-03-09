<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../index.php');
    exit();
}
$fullName = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Teacher';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Mark Attendance | SCTI Teacher Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
    .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
    
    .page-header {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;
      box-shadow: 0 4px 15px rgba(40,167,69,0.2);
    }
    .page-header h1 { margin: 0 0 10px 0; font-size: 28px; }
    .breadcrumb { opacity: 1; font-size: 14px; background: transparent; padding: 0; }
    .breadcrumb a { color: white; text-decoration: none; }
    
    .attendance-controls {
      background: white; padding: 25px; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px;
      display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;
    }
    .control-group label {
      display: block; margin-bottom: 8px; color: #666;
      font-weight: 600; font-size: 14px;
    }
    .control-select {
      width: 100%; padding: 12px; border: 2px solid #dee2e6;
      border-radius: 6px; font-size: 14px;
    }
    .control-select:focus {
      outline: none; border-color: #28a745;
    }
    
    .attendance-summary {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 15px; margin-bottom: 30px;
    }
    .summary-box {
      background: white; padding: 20px; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;
    }
    .summary-box h3 { font-size: 32px; margin: 10px 0; }
    .summary-box.present h3 { color: #28a745; }
    .summary-box.absent h3 { color: #dc3545; }
    .summary-box.late h3 { color: #ffc107; }
    
    .attendance-table-container {
      background: white; border-radius: 10px; padding: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .attendance-table {
      width: 100%; border-collapse: collapse;
    }
    .attendance-table th {
      background: #f8f9fa; padding: 15px; text-align: left;
      color: #333; font-weight: 600; border-bottom: 2px solid #dee2e6;
    }
    .attendance-table td {
      padding: 15px; border-bottom: 1px solid #dee2e6;
    }
    
    .student-info {
      display: flex; align-items: center; gap: 10px;
    }
    .student-avatar {
      width: 40px; height: 40px; border-radius: 50%;
      background: linear-gradient(135deg, #28a745, #20c997);
      display: flex; align-items: center; justify-content: center;
      color: white; font-weight: bold;
    }
    
    .attendance-buttons {
      display: flex; gap: 8px;
    }
    .attendance-btn {
      padding: 8px 16px; border: none; border-radius: 6px;
      cursor: pointer; font-size: 13px; font-weight: 600;
      transition: all 0.3s;
    }
    .btn-present {
      background: #d4edda; color: #155724;
    }
    .btn-present:hover, .btn-present.active {
      background: #28a745; color: white;
    }
    .btn-absent {
      background: #f8d7da; color: #721c24;
    }
    .btn-absent:hover, .btn-absent.active {
      background: #dc3545; color: white;
    }
    .btn-late {
      background: #fff3cd; color: #856404;
    }
    .btn-late:hover, .btn-late.active {
      background: #ffc107; color: white;
    }
    
    .save-btn {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white; padding: 15px 40px; border: none;
      border-radius: 8px; cursor: pointer; font-size: 16px;
      font-weight: 600; margin-top: 20px;
      transition: all 0.3s;
    }
    .save-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(40,167,69,0.3);
    }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Attendance Management - Mark and track student attendance</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <h1><i class="fa fa-calendar-check"></i> Mark Attendance</h1>
    <div class="breadcrumb">
      <a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Attendance
    </div>
  </div>

  <div class="attendance-controls">
    <div class="control-group">
      <label><i class="fa fa-book"></i> Select Class</label>
      <select class="control-select">
        <option>Programming Fundamentals</option>
        <option>Database Management</option>
        <option>Web Development</option>
        <option>Data Structures</option>
      </select>
    </div>
    <div class="control-group">
      <label><i class="fa fa-calendar"></i> Select Date</label>
      <input type="date" class="control-select" value="2026-03-08">
    </div>
    <div class="control-group">
      <label><i class="fa fa-clock"></i> Select Period</label>
      <select class="control-select">
        <option>9:00 AM - 10:30 AM</option>
        <option>11:00 AM - 12:30 PM</option>
        <option>1:30 PM - 3:00 PM</option>
        <option>3:30 PM - 5:00 PM</option>
      </select>
    </div>
  </div>

  <div class="attendance-summary">
    <div class="summary-box present">
      <i class="fa fa-check-circle" style="font-size: 24px; color: #28a745;"></i>
      <h3>32</h3>
      <p>Present</p>
    </div>
    <div class="summary-box absent">
      <i class="fa fa-times-circle" style="font-size: 24px; color: #dc3545;"></i>
      <h3>2</h3>
      <p>Absent</p>
    </div>
    <div class="summary-box late">
      <i class="fa fa-clock" style="font-size: 24px; color: #ffc107;"></i>
      <h3>1</h3>
      <p>Late</p>
    </div>
    <div class="summary-box">
      <i class="fa fa-percentage" style="font-size: 24px; color: #004080;"></i>
      <h3>91%</h3>
      <p>Attendance Rate</p>
    </div>
  </div>

  <div class="attendance-table-container">
    <table class="attendance-table">
      <thead>
        <tr>
          <th>Roll No.</th>
          <th>Student Name</th>
          <th>Student ID</th>
          <th>Mark Attendance</th>
          <th>Remarks</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td>
          <td>
            <div class="student-info">
              <div class="student-avatar">RS</div>
              <span>Ram Sharma</span>
            </div>
          </td>
          <td>STU20251001</td>
          <td>
            <div class="attendance-buttons">
              <button class="attendance-btn btn-present active">
                <i class="fa fa-check"></i> Present
              </button>
              <button class="attendance-btn btn-absent">
                <i class="fa fa-times"></i> Absent
              </button>
              <button class="attendance-btn btn-late">
                <i class="fa fa-clock"></i> Late
              </button>
            </div>
          </td>
          <td>
            <input type="text" placeholder="Add remarks..." style="padding: 8px; border: 1px solid #dee2e6; border-radius: 4px; width: 100%;">
          </td>
        </tr>
        <tr>
          <td>2</td>
          <td>
            <div class="student-info">
              <div class="student-avatar">SP</div>
              <span>Sita Poudel</span>
            </div>
          </td>
          <td>STU20251002</td>
          <td>
            <div class="attendance-buttons">
              <button class="attendance-btn btn-present active">
                <i class="fa fa-check"></i> Present
              </button>
              <button class="attendance-btn btn-absent">
                <i class="fa fa-times"></i> Absent
              </button>
              <button class="attendance-btn btn-late">
                <i class="fa fa-clock"></i> Late
              </button>
            </div>
          </td>
          <td>
            <input type="text" placeholder="Add remarks..." style="padding: 8px; border: 1px solid #dee2e6; border-radius: 4px; width: 100%;">
          </td>
        </tr>
        <tr>
          <td>3</td>
          <td>
            <div class="student-info">
              <div class="student-avatar">HT</div>
              <span>Hari Thapa</span>
            </div>
          </td>
          <td>STU20251003</td>
          <td>
            <div class="attendance-buttons">
              <button class="attendance-btn btn-present">
                <i class="fa fa-check"></i> Present
              </button>
              <button class="attendance-btn btn-absent active">
                <i class="fa fa-times"></i> Absent
              </button>
              <button class="attendance-btn btn-late">
                <i class="fa fa-clock"></i> Late
              </button>
            </div>
          </td>
          <td>
            <input type="text" placeholder="Add remarks..." value="Sick leave" style="padding: 8px; border: 1px solid #dee2e6; border-radius: 4px; width: 100%;">
          </td>
        </tr>
        <tr>
          <td>4</td>
          <td>
            <div class="student-info">
              <div class="student-avatar">GK</div>
              <span>Gita KC</span>
            </div>
          </td>
          <td>STU20251004</td>
          <td>
            <div class="attendance-buttons">
              <button class="attendance-btn btn-present active">
                <i class="fa fa-check"></i> Present
              </button>
              <button class="attendance-btn btn-absent">
                <i class="fa fa-times"></i> Absent
              </button>
              <button class="attendance-btn btn-late">
                <i class="fa fa-clock"></i> Late
              </button>
            </div>
          </td>
          <td>
            <input type="text" placeholder="Add remarks..." style="padding: 8px; border: 1px solid #dee2e6; border-radius: 4px; width: 100%;">
          </td>
        </tr>
        <tr>
          <td>5</td>
          <td>
            <div class="student-info">
              <div class="student-avatar">PG</div>
              <span>Prakash Gurung</span>
            </div>
          </td>
          <td>STU20251005</td>
          <td>
            <div class="attendance-buttons">
              <button class="attendance-btn btn-present">
                <i class="fa fa-check"></i> Present
              </button>
              <button class="attendance-btn btn-absent">
                <i class="fa fa-times"></i> Absent
              </button>
              <button class="attendance-btn btn-late active">
                <i class="fa fa-clock"></i> Late
              </button>
            </div>
          </td>
          <td>
            <input type="text" placeholder="Add remarks..." value="10 mins late" style="padding: 8px; border: 1px solid #dee2e6; border-radius: 4px; width: 100%;">
          </td>
        </tr>
      </tbody>
    </table>
    
    <div style="text-align: center; margin-top: 30px;">
      <button class="save-btn" onclick="alert('Save attendance coming soon!');">
        <i class="fa fa-save"></i> Save Attendance
      </button>
    </div>
  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Teacher Portal</p>
</footer>

<script>
  // Toggle attendance buttons
  document.querySelectorAll('.attendance-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      // Remove active from siblings
      this.parentElement.querySelectorAll('.attendance-btn').forEach(b => b.classList.remove('active'));
      // Add active to clicked button
      this.classList.add('active');
      
      // Update summary counts (demo)
      updateSummary();
    });
  });
  
  function updateSummary() {
    const presentCount = document.querySelectorAll('.btn-present.active').length;
    const absentCount = document.querySelectorAll('.btn-absent.active').length;
    const lateCount = document.querySelectorAll('.btn-late.active').length;
    const total = presentCount + absentCount + lateCount;
    const rate = total > 0 ? Math.round((presentCount / total) * 100) : 0;
    
    document.querySelector('.summary-box.present h3').textContent = presentCount;
    document.querySelector('.summary-box.absent h3').textContent = absentCount;
    document.querySelector('.summary-box.late h3').textContent = lateCount;
    document.querySelector('.summary-box:last-child h3').textContent = rate + '%';
  }
</script>

</body>
</html>
