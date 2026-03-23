$php = "C:\xampp\php\php.exe"
$base = "C:\xampp\htdocs\scti-school"
$files = @(
  "pages\login-simple.php","pages\forgot-password.php",
  "pages\student-attendance.php","pages\student-attendance-data.php",
  "pages\student-assignments.php","pages\student-save.php",
  "pages\teacher-attendance.php","pages\teacher-attendance-chart.php",
  "pages\attendance-data.php","pages\attendance-save.php",
  "pages\teacher-assignments.php","pages\assignment-save.php",
  "pages\assignment-submit.php","pages\assignment-grade.php",
  "pages\assignment-submissions-list.php","pages\notice-save.php",
  "pages\notice-widget.php","pages\first-login-save.php",
  "pages\manage-students.php","pages\manage-teachers.php",
  "dashboards\student-dashboard.php","dashboards\teacher-dashboard.php",
  "includes\config.php"
)
$errors = @()
$ok = @()
foreach ($f in $files) {
  $result = & $php -l "$base\$f" 2>&1
  if ($result -match "No syntax errors") {
    $ok += $f
  } else {
    $errors += "$f => $result"
  }
}
Write-Host "SYNTAX OK: $($ok.Count)/$($files.Count)" -ForegroundColor Green
if ($errors.Count -gt 0) {
  Write-Host "`nSYNTAX ERRORS:" -ForegroundColor Red
  foreach ($e in $errors) { Write-Host "  $e" -ForegroundColor Red }
} else {
  Write-Host "ALL PHP FILES SYNTAX CLEAN" -ForegroundColor Green
}
