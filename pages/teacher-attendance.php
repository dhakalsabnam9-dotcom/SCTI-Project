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
    .breadcrumb { opacity: 0.9; font-size: 14px; }
    .breadcrumb a { color: white; text-decoration: none; }
    
    .attendance-controls {
      background: white; padding: 25px; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px;
      display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 15px;
    }
    .form-group label { display: block; margin-bottom: 8px; color: #333; font-weight: 600; }
    .form-control {
      width: 100%; padding: 12px; border: 2px solid #dee2e6;
      border-radius: 6px; font-size: 14px;
    }
    .btn-submit {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white; padding: 12px 30px; border: none;
      border-radius: 6px; cursor: pointer; font-size: 14px;
      align-self: end; transition: all 0.3s;
    }
    .btn-submit:hover {
      background: linear-gradient(135deg, #20c997, #28a745);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(40,167,69,0.3);
    }
    
    .attendance-table-container {
      background: white; border-radius: 10px; padding: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .attendance-table {
      width: 100%; border-collapse: collapse;
    }
    .attendance-table th {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white; padding: 15px; text-align: left;
      font-weight: 600; border: 1px solid #20c997;
    }
    .attendance-table td {
      padding: 15px; border-bottom: 1px solid #dee2e6;
    }
    
    .attendance-btn {
      padding: 8px 16px; border: 2px solid; border-radius: 6px;
      cursor: pointer; font-size: 13px; transition: all 0.3s;
      margin-right: 8px; background: white;
    }
    .btn-present { border-color: #28a745; color: #28a745; }
    .btn-present.active { background: #28a745; color: white; }
    .btn-absent { border-color: #dc3545; color: #dc3545; }
    .btn-absent.active { background: #dc3545; color: white; }
    .btn-late { border-color: #ffc107; color: #856404; }
    .btn-late.active { background: #ffc107; color: #856404; }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Mark Attendance - Track student presence efficiently</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <h1><i class="fa fa-calendar-check"></i> Mark Attendance</h1>
    <div class="breadcrumb">
      <a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Mark Attendance
    </div>
  </div>

  <div class="attendance-controls">
    <div class="form-group">
      <label>Select Class</label>
      <select class="form-control" id="classSelect">
        <option>Programming Fundamentals</option>
        <option>Database Management</option>
        <option>Web Development</option>
        <option>Data Structures</option>
      </select>
    </div>
    <div class="form-group">
      <label>Date</label>
      <input type="date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
    </div>
    <div class="form-group">
      <label>Session</label>
      <select class="form-control">
        <option>Morning (9:00 AM - 12:00 PM)</option>
        <option>Afternoon (1:00 PM - 5:00 PM)</option>
      </select>
    </div>
    <button class="btn-submit" onclick="alert('Attendance saved successfully!');">
      <i class="fa fa-save"></i> Save Attendance
    </button>
  </div>

  <div class="attendance-table-container">
    <table class="attendance-table">
      <thead>
        <tr>
          <th>Roll No.</th>
          <th>Student Name</th>
          <th>Student ID</th>
          <th>Mark Attendance</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td>
          <td>Ram Sharma</td>
          <td>STU20251001</td>
          <td>
            <button class="attendance-btn btn-present active" onclick="markAttendance(this, 'present')">
              <i class="fa fa-check"></i> Present
            </button>
            <button class="attendance-btn btn-absent" onclick="markAttendance(this, 'absent')">
              <i class="fa fa-times"></i> Absent
            </button>
            <button class="attendance-btn btn-late" onclick="markAttendance(this, 'late')">
              <i class="fa fa-clock"></i> Late
            </button>
          </td>
        </tr>
        <tr>
          <td>2</td>
          <td>Sita Poudel</td>
          <td>STU20251002</td>
          <td>
            <button class="attendance-btn btn-present active" onclick="markAttendance(this, 'present')">
              <i class="fa fa-check"></i> Present
            </button>
            <button class="attendance-btn btn-absent" onclick="markAttendance(this, 'absent')">
              <i class="fa fa-times"></i> Absent
            </button>
            <button class="attendance-btn btn-late" onclick="markAttendance(this, 'late')">
              <i class="fa fa-clock"></i> Late
            </button>
          </td>
        </tr>
        <tr>
          <td>3</td>
          <td>Hari Thapa</td>
          <td>STU20251003</td>
          <td>
            <button class="attendance-btn btn-present active" onclick="markAttendance(this, 'present')">
              <i class="fa fa-check"></i> Present
            </button>
            <button class="attendance-btn btn-absent" onclick="markAttendance(this, 'absent')">
              <i class="fa fa-times"></i> Absent
            </button>
            <button class="attendance-btn btn-late" onclick="markAttendance(this, 'late')">
              <i class="fa fa-clock"></i> Late
            </button>
          </td>
        </tr>
        <tr>
          <td>4</td>
          <td>Gita KC</td>
          <td>STU20251004</td>
          <td>
            <button class="attendance-btn btn-present active" onclick="markAttendance(this, 'present')">
              <i class="fa fa-check"></i> Present
            </button>
            <button class="attendance-btn btn-absent" onclick="markAttendance(this, 'absent')">
              <i class="fa fa-times"></i> Absent
            </button>
            <button class="attendance-btn btn-late" onclick="markAttendance(this, 'late')">
              <i class="fa fa-clock"></i> Late
            </button>
          </td>
        </tr>
        <tr>
          <td>5</td>
          <td>Prakash Gurung</td>
          <td>STU20251005</td>
          <td>
            <button class="attendance-btn btn-present active" onclick="markAttendance(this, 'present')">
              <i class="fa fa-check"></i> Present
            </button>
            <button class="attendance-btn btn-absent" onclick="markAttendance(this, 'absent')">
              <i class="fa fa-times"></i> Absent
            </button>
            <button class="attendance-btn btn-late" onclick="markAttendance(this, 'late')">
              <i class="fa fa-clock"></i> Late
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Teacher Portal</p>
</footer>

<script>
function markAttendance(btn, status) {
  const row = btn.closest('tr');
  const buttons = row.querySelectorAll('.attendance-btn');
  buttons.forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}
</script>

</body>
</html>
