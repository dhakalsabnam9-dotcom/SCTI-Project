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
    .breadcrumb { opacity: 0.9; font-size: 14px; }
    .breadcrumb a { color: white; text-decoration: none; }
    
    .grade-controls {
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
    
    .grades-table-container {
      background: white; border-radius: 10px; padding: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .grades-table {
      width: 100%; border-collapse: collapse;
    }
    .grades-table th {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white; padding: 15px; text-align: left;
      font-weight: 600; border: 1px solid #20c997;
    }
    .grades-table td {
      padding: 15px; border-bottom: 1px solid #dee2e6;
    }
    
    .grade-input {
      width: 80px; padding: 8px; border: 2px solid #dee2e6;
      border-radius: 4px; text-align: center;
    }
    .grade-input:focus {
      border-color: #28a745;
      outline: none;
    }
    
    .grade-badge {
      padding: 6px 12px; border-radius: 20px; font-size: 12px;
      font-weight: 600; display: inline-block;
    }
    .grade-a { background: #d4edda; color: #155724; }
    .grade-b { background: #d1ecf1; color: #0c5460; }
    .grade-c { background: #fff3cd; color: #856404; }
    .grade-f { background: #f8d7da; color: #721c24; }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Enter Grades - Evaluate student performance</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <h1><i class="fa fa-chart-line"></i> Enter Grades</h1>
    <div class="breadcrumb">
      <a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Enter Grades
    </div>
  </div>

  <div class="grade-controls">
    <div class="form-group">
      <label>Select Class</label>
      <select class="form-control">
        <option>Programming Fundamentals</option>
        <option>Database Management</option>
        <option>Web Development</option>
        <option>Data Structures</option>
      </select>
    </div>
    <div class="form-group">
      <label>Assessment Type</label>
      <select class="form-control">
        <option>Mid-Term Exam</option>
        <option>Final Exam</option>
        <option>Assignment</option>
        <option>Project</option>
        <option>Quiz</option>
      </select>
    </div>
    <div class="form-group">
      <label>Total Marks</label>
      <input type="number" class="form-control" value="100">
    </div>
    <button class="btn-submit" onclick="alert('Grades saved successfully!');">
      <i class="fa fa-save"></i> Save Grades
    </button>
  </div>

  <div class="grades-table-container">
    <table class="grades-table">
      <thead>
        <tr>
          <th>Roll No.</th>
          <th>Student Name</th>
          <th>Student ID</th>
          <th>Marks Obtained</th>
          <th>Grade</th>
          <th>Current GPA</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td>
          <td>Ram Sharma</td>
          <td>STU20251001</td>
          <td><input type="number" class="grade-input" value="85" min="0" max="100"></td>
          <td><span class="grade-badge grade-a">A</span></td>
          <td>3.8</td>
        </tr>
        <tr>
          <td>2</td>
          <td>Sita Poudel</td>
          <td>STU20251002</td>
          <td><input type="number" class="grade-input" value="78" min="0" max="100"></td>
          <td><span class="grade-badge grade-b">B+</span></td>
          <td>3.6</td>
        </tr>
        <tr>
          <td>3</td>
          <td>Hari Thapa</td>
          <td>STU20251003</td>
          <td><input type="number" class="grade-input" value="72" min="0" max="100"></td>
          <td><span class="grade-badge grade-b">B</span></td>
          <td>3.2</td>
        </tr>
        <tr>
          <td>4</td>
          <td>Gita KC</td>
          <td>STU20251004</td>
          <td><input type="number" class="grade-input" value="92" min="0" max="100"></td>
          <td><span class="grade-badge grade-a">A+</span></td>
          <td>3.9</td>
        </tr>
        <tr>
          <td>5</td>
          <td>Prakash Gurung</td>
          <td>STU20251005</td>
          <td><input type="number" class="grade-input" value="68" min="0" max="100"></td>
          <td><span class="grade-badge grade-c">C+</span></td>
          <td>3.4</td>
        </tr>
      </tbody>
    </table>
  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Teacher Portal</p>
</footer>

</body>
</html>
