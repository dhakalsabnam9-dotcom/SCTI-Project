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
  <title>Enter Grades | SCTI Teacher Portal</title>
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
    .grades-table-container {
      background: white; border-radius: 10px; padding: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow-x: auto;
    }
    .grades-table { width: 100%; border-collapse: collapse; }
    .grades-table th {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white; padding: 15px; text-align: left; font-weight: 600;
    }
    .grades-table td { padding: 15px; border-bottom: 1px solid #dee2e6; }
    .grade-input {
      width: 80px; padding: 8px; border: 2px solid #dee2e6;
      border-radius: 4px; text-align: center;
    }
    .grade-input:focus { outline: none; border-color: #28a745; }
    .save-btn {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white; padding: 15px 40px; border: none;
      border-radius: 8px; cursor: pointer; font-size: 16px;
      margin-top: 20px; transition: all 0.3s;
    }
    .save-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(40,167,69,0.3);
    }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Grade Management - Enter and manage student grades</marquee>
</div>

<div class="container">
  <div class="page-header">
    <h1><i class="fa fa-star"></i> Enter Grades</h1>
    <div class="breadcrumb">
      <a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Grades
    </div>
  </div>

  <div class="grades-table-container">
    <table class="grades-table">
      <thead>
        <tr>
          <th>Student Name</th>
          <th>Student ID</th>
          <th>Internal (40)</th>
          <th>External (60)</th>
          <th>Total</th>
          <th>Grade</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Ram Sharma</td>
          <td>STU20251001</td>
          <td><input type="number" class="grade-input" value="35" max="40"></td>
          <td><input type="number" class="grade-input" value="52" max="60"></td>
          <td><strong>87</strong></td>
          <td><strong style="color: #28a745;">A</strong></td>
        </tr>
        <tr>
          <td>Sita Poudel</td>
          <td>STU20251002</td>
          <td><input type="number" class="grade-input" value="32" max="40"></td>
          <td><input type="number" class="grade-input" value="48" max="60"></td>
          <td><strong>80</strong></td>
          <td><strong style="color: #28a745;">A-</strong></td>
        </tr>
        <tr>
          <td>Hari Thapa</td>
          <td>STU20251003</td>
          <td><input type="number" class="grade-input" value="28" max="40"></td>
          <td><input type="number" class="grade-input" value="42" max="60"></td>
          <td><strong>70</strong></td>
          <td><strong style="color: #ffc107;">B+</strong></td>
        </tr>
        <tr>
          <td>Gita KC</td>
          <td>STU20251004</td>
          <td><input type="number" class="grade-input" value="38" max="40"></td>
          <td><input type="number" class="grade-input" value="55" max="60"></td>
          <td><strong>93</strong></td>
          <td><strong style="color: #28a745;">A+</strong></td>
        </tr>
        <tr>
          <td>Prakash Gurung</td>
          <td>STU20251005</td>
          <td><input type="number" class="grade-input" value="30" max="40"></td>
          <td><input type="number" class="grade-input" value="45" max="60"></td>
          <td><strong>75</strong></td>
          <td><strong style="color: #28a745;">B+</strong></td>
        </tr>
      </tbody>
    </table>
    <div style="text-align: center;">
      <button class="save-btn" onclick="alert('Save grades coming soon!');">
        <i class="fa fa-save"></i> Save Grades
      </button>
    </div>
  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Teacher Portal</p>
</footer>

</body>
</html>
