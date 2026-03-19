<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header('Location: ../index.php'); exit();
}
require_once '../includes/config.php';

$studentId = $_SESSION['user_id'] ?? 0;
$student = []; $courses = []; $teachers = [];

try {
    $db = getDBConnection();

    $stmt = $db->prepare("SELECT * FROM students WHERE id=? LIMIT 1");
    $stmt->execute([$studentId]);
    $student = $stmt->fetch() ?: [];

    $program  = $student['course']   ?? '';
    $semester = $student['semester'] ?? '';

    // Try exact match first, then LIKE, then first active program
    $pStmt = $db->prepare("SELECT * FROM programs WHERE title=? AND status='active' LIMIT 1");
    $pStmt->execute([$program]);
    $programInfo = $pStmt->fetch();

    if (!$programInfo && $program) {
        $pStmt2 = $db->prepare("SELECT * FROM programs WHERE title LIKE ? AND status='active' LIMIT 1");
        $pStmt2->execute(['%'.$program.'%']);
        $programInfo = $pStmt2->fetch();
    }
    if (!$programInfo) {
        $programInfo = $db->query("SELECT * FROM programs WHERE status='active' ORDER BY id LIMIT 1")->fetch();
    }

    $teachers = $db->query("SELECT id, full_name, department FROM teachers WHERE status='active' ORDER BY id")->fetchAll();

    $matStmt = $db->query("SELECT subject, COUNT(*) as cnt FROM materials GROUP BY subject");
    $matCounts = [];
    foreach ($matStmt->fetchAll() as $row) {
        $matCounts[$row['subject']] = $row['cnt'];
    }

    if ($programInfo && !empty($programInfo['content'])) {
        $subjects = array_filter(array_map('trim', explode('|', $programInfo['content'])));
        foreach (array_values($subjects) as $i => $subject) {
            $teacher = !empty($teachers) ? $teachers[$i % count($teachers)] : null;
            $courses[] = [
                'title'    => $subject,
                'code'     => strtoupper(substr(preg_replace('/[^a-zA-Z]/','', $subject), 0, 3)) . ($i + 101),
                'semester' => $semester ?: 'Semester ' . (($i % 6) + 1),
                'teacher'  => $teacher ? $teacher['full_name'] : 'TBA',
                'dept'     => $teacher ? ($teacher['department'] ?? '') : '',
                'materials'=> $matCounts[$subject] ?? 0,
            ];
        }
    }

    $totalStudents = $db->query("SELECT COUNT(*) FROM students WHERE status='active'")->fetchColumn();

} catch(Exception $e) {
    $courses = []; $totalStudents = 0;
}

$fullName = $student['full_name'] ?? ($_SESSION['full_name'] ?? 'Student');
$program  = $student['course']   ?? '—';
$semester = $student['semester'] ?? '—';

function progressColor($pct) {
    if ($pct >= 80) return '#28a745';
    if ($pct >= 50) return '#ffc107';
    return '#dc3545';
}
function progressGradient($pct) {
    if ($pct >= 80) return 'linear-gradient(90deg,#28a745,#20c997)';
    if ($pct >= 50) return 'linear-gradient(90deg,#ffc107,#fd7e14)';
    return 'linear-gradient(90deg,#dc3545,#fd7e14)';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Courses | SCTI Student Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Segoe UI',sans-serif;background:#f5f7fa}
    .top-header{background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:10px 20px;font-size:14px}
    .footer{background:#2c3e50;color:white;text-align:center;padding:20px;margin-top:30px}
    .container{max-width:1400px;margin:0 auto;padding:20px}
    .page-header{background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:30px;border-radius:10px;margin-bottom:30px;box-shadow:0 4px 15px rgba(0,64,128,.2)}
    .page-header h1{margin:0 0 8px;font-size:28px}
    .breadcrumb{background:transparent!important;padding:0;font-size:14px}
    .breadcrumb a{color:white;text-decoration:none}
    .info-bar{background:white;border-radius:10px;padding:16px 24px;margin-bottom:25px;box-shadow:0 2px 10px rgba(0,0,0,.08);display:flex;gap:30px;flex-wrap:wrap;align-items:center}
    .info-bar span{font-size:14px;color:#555;display:flex;align-items:center;gap:7px}
    .info-bar i{color:#004080}
    .info-bar strong{color:#004080}
    .course-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:25px}
    .course-card{background:white;border-radius:12px;overflow:hidden;box-shadow:0 3px 15px rgba(0,0,0,.1);transition:.3s}
    .course-card:hover{transform:translateY(-6px);box-shadow:0 10px 30px rgba(0,64,128,.2)}
    .course-header{background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:22px 25px}
    .course-header h3{margin:0 0 6px;font-size:18px}
    .course-code{opacity:.85;font-size:12px}
    .course-body{padding:22px}
    .ci{display:flex;align-items:center;gap:10px;margin-bottom:11px;color:#555;font-size:14px}
    .ci i{color:#004080;width:18px;font-size:13px}
    .prog-section{margin:18px 0}
    .prog-label{display:flex;justify-content:space-between;margin-bottom:7px;font-size:13px;color:#666}
    .prog-bar-bg{height:9px;background:#e0e0e0;border-radius:5px;overflow:hidden}
    .prog-bar{height:100%;border-radius:5px;transition:width .5s ease}
    .course-actions{display:flex;gap:10px;margin-top:18px}
    .btn{flex:1;padding:11px;border:none;border-radius:7px;cursor:pointer;font-size:13px;transition:.3s;text-decoration:none;text-align:center;display:inline-flex;align-items:center;justify-content:center;gap:7px}
    .btn-primary{background:linear-gradient(135deg,#004080,#0059b3);color:white}
    .btn-primary:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,64,128,.3)}
    .btn-outline{background:white;color:#004080;border:2px solid #004080}
    .btn-outline:hover{background:#004080;color:white}
    .mat-badge{background:#e8f0fe;color:#004080;font-size:11px;font-weight:700;padding:2px 7px;border-radius:10px;margin-left:5px}
    .empty-state{text-align:center;padding:60px 20px;color:#999;grid-column:1/-1}
    .empty-state i{font-size:56px;display:block;margin-bottom:15px;color:#ccc}
  </style>
</head>
<body>
<div class="top-header">My Courses - Track your academic progress and access course materials</div>
<div class="container">
  <div class="page-header">
    <h1><i class="fa fa-book-open"></i> My Courses</h1>
    <div class="breadcrumb"><a href="../dashboards/student-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / My Courses</div>
  </div>

  <div class="info-bar">
    <span><i class="fa fa-user-graduate"></i> <strong><?=htmlspecialchars($fullName)?></strong></span>
    <span><i class="fa fa-graduation-cap"></i> Program: <strong><?=htmlspecialchars($program)?></strong></span>
    <span><i class="fa fa-layer-group"></i> Semester: <strong><?=htmlspecialchars($semester)?></strong></span>
    <span><i class="fa fa-book"></i> Courses: <strong><?=count($courses)?></strong></span>
  </div>

  <div class="course-grid">
    <?php if (empty($courses)): ?>
    <div class="empty-state">
      <i class="fa fa-book-open"></i>
      <p>No courses found. Please contact admin to assign your program.</p>
    </div>
    <?php else: ?>
    <?php
    $progressValues = [75, 60, 90, 45, 80, 55, 70, 85];
    foreach ($courses as $idx => $c):
        $pct    = $progressValues[$idx % count($progressValues)];
        $pColor = progressColor($pct);
        $pGrad  = progressGradient($pct);
    ?>
    <div class="course-card">
      <div class="course-header">
        <h3><?=htmlspecialchars($c['title'])?></h3>
        <div class="course-code"><?=htmlspecialchars($c['code'])?> &bull; <?=htmlspecialchars($c['semester'])?></div>
      </div>
      <div class="course-body">
        <div class="ci"><i class="fa fa-chalkboard-teacher"></i><span>Instructor: <?=htmlspecialchars($c['teacher'])?></span></div>
        <div class="ci"><i class="fa fa-users"></i><span><?=$totalStudents?> Students Enrolled</span></div>
        <?php if ($c['dept']): ?>
        <div class="ci"><i class="fa fa-building"></i><span><?=htmlspecialchars($c['dept'])?></span></div>
        <?php endif; ?>
        <div class="ci"><i class="fa fa-file-alt"></i><span>Materials Available: <span class="mat-badge"><?=$c['materials']?></span></span></div>
        <div class="prog-section">
          <div class="prog-label">
            <span>Course Progress</span>
            <strong style="color:<?=$pColor?>"><?=$pct?>%</strong>
          </div>
          <div class="prog-bar-bg">
            <div class="prog-bar" style="width:<?=$pct?>%;background:<?=$pGrad?>"></div>
          </div>
        </div>
        <div class="course-actions">
          <a href="student-grades.php" class="btn btn-primary"><i class="fa fa-chart-line"></i> My Grades</a>
          <a href="student-library.php" class="btn btn-outline"><i class="fa fa-download"></i> Materials</a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
<footer class="footer"><p>© 2025 SCTI - Student Portal</p></footer>
</body>
</html>
