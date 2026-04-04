<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header('Location: ../index.php'); exit();
}
require_once '../includes/config.php';

$studentId = $_SESSION['user_id'] ?? 0;

try {
    $db = getDBConnection();
    $stu = $db->prepare("SELECT * FROM students WHERE id=? LIMIT 1");
    $stu->execute([$studentId]);
    $stuData = $stu->fetch() ?: [];
    $semester = $stuData['semester'] ?? '';

    // Get grades for this student
    $stmt = $db->prepare("SELECT g.*, (g.internal_marks + g.external_marks) as total_marks, s.full_name as student_name FROM grades g LEFT JOIN students s ON g.student_id = s.id WHERE g.student_id = ? ORDER BY g.subject ASC");
    $stmt->execute([$studentId]);
    $grades = $stmt->fetchAll();

    // Group by subject
    $bySubject = [];
    foreach ($grades as $g) {
        $bySubject[$g['subject']][$g['exam_type']] = $g;
    }

    // Calculate GPA (simple: avg of total_marks/100 * 4)
    $gpaTotal = 0; $gpaCount = 0;
    foreach ($bySubject as $subj => $exams) {
        foreach ($exams as $et => $g) {
            if (!empty($g['total_marks'])) {
                $gpaTotal += ($g['total_marks'] / 100) * 4;
                $gpaCount++;
            }
        }
    }
    $gpa = $gpaCount > 0 ? round($gpaTotal / $gpaCount, 2) : 0;

} catch(Exception $e) {
    $grades = []; $bySubject = []; $gpa = 0; $stuData = [];
}

function getGradeLetter($marks) {
    if ($marks >= 90) return ['A+','grade-a'];
    if ($marks >= 80) return ['A','grade-a'];
    if ($marks >= 70) return ['B+','grade-b'];
    if ($marks >= 60) return ['B','grade-b'];
    if ($marks >= 50) return ['C','grade-c'];
    return ['D','grade-d'];
}
function getGradePoint($marks) {
    if ($marks >= 90) return 4.0;
    if ($marks >= 80) return 3.7;
    if ($marks >= 70) return 3.3;
    if ($marks >= 60) return 3.0;
    if ($marks >= 50) return 2.0;
    return 1.0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Grades & GPA | SCTI Student Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Segoe UI',sans-serif;background:#f5f7fa}
    .top-header{background:linear-gradient(135deg,#ffc107,#fd7e14);color:white;padding:10px 20px;font-size:14px}
    .footer{background:#2c3e50;color:white;text-align:center;padding:20px;margin-top:40px}
    .container{max-width:1200px;margin:0 auto;padding:20px}
    .page-header{background:linear-gradient(135deg,#ffc107,#fd7e14);color:white;padding:30px;border-radius:10px;margin-bottom:30px;box-shadow:0 4px 15px rgba(255,193,7,.2)}
    .page-header h1{margin:0 0 8px;font-size:28px}
    .breadcrumb{background:transparent;padding:0;font-size:14px}
    .breadcrumb a{color:white;text-decoration:none}
    .gpa-card{background:white;padding:35px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.1);margin-bottom:25px;text-align:center}
    .gpa-display{font-size:72px;font-weight:bold;background:linear-gradient(135deg,#ffc107,#fd7e14);-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin:15px 0}
    .grades-table{width:100%;border-collapse:collapse;background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.1)}
    .grades-table th{background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:14px;text-align:left;font-weight:600}
    .grades-table td{padding:14px;border-bottom:1px solid #dee2e6}
    .grades-table tr:hover{background:#f8f9fa}
    .grade-badge{padding:6px 14px;border-radius:6px;font-weight:bold;display:inline-block;font-size:15px}
    .grade-a{background:#d4edda;color:#155724}
    .grade-b{background:#d1ecf1;color:#0c5460}
    .grade-c{background:#fff3cd;color:#856404}
    .grade-d{background:#f8d7da;color:#721c24}
    .empty-state{text-align:center;padding:60px 20px;color:#999}
    .empty-state i{font-size:56px;display:block;margin-bottom:15px;color:#ccc}
    .info-note{background:#fff3cd;border:1px solid #ffc107;border-radius:8px;padding:15px;margin-bottom:20px;color:#856404;font-size:14px}
  </style>
</head>
<body>
<div class="top-header"><marquee>Academic Performance - Track your grades and maintain excellence</marquee></div>
<div class="container">
  <div class="page-header">
    <h1><i class="fa fa-chart-line"></i> Grades & GPA</h1>
    <div class="breadcrumb"><a href="../dashboards/student-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Grades</div>
  </div>

  <div class="gpa-card">
    <h2 style="color:#666;margin:0">Current GPA</h2>
    <div class="gpa-display"><?=number_format($gpa,1)?></div>
    <p style="color:#666;font-size:15px">Out of 4.0 &mdash; <?=($gpa>=3.5?'&#11088; Excellent Performance!':($gpa>=2.5?'&#128077; Good Performance!':'&#128170; Keep it up!'))?></p>
    <?php if (!empty($semester)): ?>
    <p style="color:#999;font-size:13px;margin-top:8px"><?= is_numeric($semester) ? 'Semester '.$semester : htmlspecialchars($semester) ?></p>
    <?php endif; ?>
  </div>

  <?php if (empty($grades)): ?>
  <div class="empty-state"><i class="fa fa-chart-bar"></i><p>No grades recorded yet. Check back after exams.</p></div>
  <?php else: ?>
  <table class="grades-table">
    <thead>
      <tr>
        <th>Subject</th>
        <th>Exam Type</th>
        <th>Internal Marks</th>
        <th>External Marks</th>
        <th>Total</th>
        <th>Grade</th>
        <th>Grade Point</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($grades as $g):
          $total = intval($g['total_marks'] ?? 0);
          [$letter, $cls] = getGradeLetter($total);
          $gp = getGradePoint($total);
      ?>
      <tr>
        <td><?=htmlspecialchars($g['subject'])?></td>
        <td><?=htmlspecialchars($g['exam_type'])?></td>
        <td><?=htmlspecialchars($g['internal_marks'] ?? '-')?></td>
        <td><?=htmlspecialchars($g['external_marks'] ?? '-')?></td>
        <td><?=$total > 0 ? $total.'/100' : '-'?></td>
        <td><?=$total > 0 ? '<span class="grade-badge '.$cls.'">'.$letter.'</span>' : '-'?></td>
        <td><?=$total > 0 ? $gp : '-'?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
<footer class="footer"><p>&copy; 2025 SCTI - Student Portal</p></footer>
</body>
</html>
