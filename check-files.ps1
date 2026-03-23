$files = @(
  "pages\login-simple.php",
  "pages\forgot-password.php",
  "pages\student-attendance.php",
  "pages\student-attendance-data.php",
  "pages\student-assignments.php",
  "pages\student-grades.php",
  "pages\student-profile.php",
  "pages\student-list.php",
  "pages\student-save.php",
  "pages\student-delete.php",
  "pages\teacher-attendance.php",
  "pages\teacher-attendance-chart.php",
  "pages\attendance-data.php",
  "pages\attendance-save.php",
  "pages\teacher-assignments.php",
  "pages\assignment-list.php",
  "pages\assignment-save.php",
  "pages\assignment-submit.php",
  "pages\assignment-grade.php",
  "pages\assignment-submissions-list.php",
  "pages\assignment-delete.php",
  "pages\assignment-submission-delete.php",
  "pages\notice-save.php",
  "pages\notice-list.php",
  "pages\notice-widget.php",
  "pages\notice-board.php",
  "pages\manage-students.php",
  "pages\manage-teachers.php",
  "pages\first-login-save.php",
  "dashboards\student-dashboard.php",
  "dashboards\teacher-dashboard.php",
  "dashboards\admin-dashboard.php",
  "includes\config.php",
  "includes\logout.php"
)
$base = "C:\xampp\htdocs\scti-school"
$missing = @()
$ok = @()
foreach ($f in $files) {
  $path = "$base\$f"
  if (Test-Path $path) { $ok += $f } else { $missing += $f }
}
Write-Host "=== EXISTS ($($ok.Count)) ===" -ForegroundColor Green
$ok | ForEach-Object { Write-Host "  OK  $_" -ForegroundColor Green }
if ($missing.Count -gt 0) {
  Write-Host "`n=== MISSING ($($missing.Count)) ===" -ForegroundColor Red
  $missing | ForEach-Object { Write-Host "  MISSING  $_" -ForegroundColor Red }
} else {
  Write-Host "`nALL FILES PRESENT" -ForegroundColor Green
}
