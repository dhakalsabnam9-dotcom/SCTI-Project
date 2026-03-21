<?php
require_once 'includes/config.php';
$db = getDBConnection();

$results = [];

// Admin
$adminHash = password_hash('admin123', PASSWORD_BCRYPT);
$chk = $db->query("SELECT id FROM admins LIMIT 1")->fetch();
if ($chk) {
    $db->prepare("UPDATE admins SET username='admin', password=?, status='active' WHERE id=?")->execute([$adminHash, $chk['id']]);
    $results[] = "Admin updated (ID: {$chk['id']})";
} else {
    $db->prepare("INSERT INTO admins (username,email,password,full_name,role,status) VALUES ('admin','admin@scti.edu.np',?,'System Administrator','super_admin','active')")->execute([$adminHash]);
    $results[] = "Admin inserted (ID: ".$db->lastInsertId().")";
}

// Teacher
$teacherHash = password_hash('teacher123', PASSWORD_BCRYPT);
$chk = $db->query("SELECT id FROM teachers LIMIT 1")->fetch();
if ($chk) {
    $db->prepare("UPDATE teachers SET username='bibek.bhandari', password=?, status='active' WHERE id=?")->execute([$teacherHash, $chk['id']]);
    $results[] = "Teacher updated (ID: {$chk['id']})";
} else {
    $db->prepare("INSERT INTO teachers (username,email,password,full_name,teacher_id,department,status) VALUES ('bibek.bhandari','bibek@scti.edu.np',?,'Bibek Bhandari','TCH001','Computer Science','active')")->execute([$teacherHash]);
    $results[] = "Teacher inserted (ID: ".$db->lastInsertId().")";
}

// Student
$studentHash = password_hash('student123', PASSWORD_BCRYPT);
$chk = $db->query("SELECT id FROM students LIMIT 1")->fetch();
if ($chk) {
    $db->prepare("UPDATE students SET username='john.doe', password=?, status='active' WHERE id=?")->execute([$studentHash, $chk['id']]);
    $results[] = "Student updated (ID: {$chk['id']})";
} else {
    $db->prepare("INSERT INTO students (username,email,password,full_name,student_id,course,status) VALUES ('john.doe','john@scti.edu.np',?,'John Doe','STU2025001','B.Tech Ed in IT','active')")->execute([$studentHash]);
    $results[] = "Student inserted (ID: ".$db->lastInsertId().")";
}
?>
<!DOCTYPE html>
<html>
<head><title>Reset Credentials</title></head>
<body style="font-family:sans-serif;max-width:500px;margin:40px auto;padding:20px">
  <h2 style="color:#155724">Credentials Reset Done</h2>
  <?php foreach($results as $r): ?>
  <p style="background:#d4edda;padding:10px;border-radius:6px">✅ <?=htmlspecialchars($r)?></p>
  <?php endforeach; ?>
  <hr>
  <table style="width:100%;border-collapse:collapse;font-size:14px">
    <tr style="background:#f8f9fa"><th style="padding:8px;text-align:left">User Type</th><th style="padding:8px;text-align:left">Username</th><th style="padding:8px;text-align:left">Password</th></tr>
    <tr><td style="padding:8px">Administrator</td><td style="padding:8px"><strong>admin</strong></td><td style="padding:8px"><strong>admin123</strong></td></tr>
    <tr style="background:#f8f9fa"><td style="padding:8px">Teacher</td><td style="padding:8px"><strong>bibek.bhandari</strong></td><td style="padding:8px"><strong>teacher123</strong></td></tr>
    <tr><td style="padding:8px">Student</td><td style="padding:8px"><strong>john.doe</strong></td><td style="padding:8px"><strong>student123</strong></td></tr>
  </table>
  <p style="color:#721c24;background:#f8d7da;padding:10px;border-radius:6px;margin-top:20px;font-size:13px">
    ⚠️ Delete this file after use!
  </p>
</body>
</html>
