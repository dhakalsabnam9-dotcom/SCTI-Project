<?php
require_once 'includes/config.php';
try {
    $db = getDBConnection();
    $stuPass = password_hash('Student@123', PASSWORD_DEFAULT);
    $tchPass = password_hash('Teacher@123', PASSWORD_DEFAULT);

    $db->exec("UPDATE students SET password='$stuPass'");
    $stuCount = $db->query("SELECT COUNT(*) FROM students")->fetchColumn();

    $db->exec("UPDATE teachers SET password='$tchPass'");
    $tchCount = $db->query("SELECT COUNT(*) FROM teachers")->fetchColumn();

    // Show sample usernames
    $students = $db->query("SELECT username FROM students ORDER BY full_name LIMIT 5")->fetchAll(PDO::FETCH_COLUMN);
    $teachers = $db->query("SELECT username FROM teachers LIMIT 5")->fetchAll(PDO::FETCH_COLUMN);

    echo "<div style='font-family:sans-serif;max-width:600px;margin:40px auto;padding:30px;background:#d4edda;border-radius:10px'>";
    echo "<h2 style='color:#155724'>✓ All passwords reset!</h2>";
    echo "<table style='width:100%;border-collapse:collapse;margin-top:16px'>";
    echo "<tr style='background:#004080;color:#fff'><th style='padding:10px;text-align:left'>Role</th><th style='padding:10px;text-align:left'>Count</th><th style='padding:10px;text-align:left'>Password</th><th style='padding:10px;text-align:left'>Sample Usernames</th></tr>";
    echo "<tr style='background:#fff'><td style='padding:10px'>Student</td><td style='padding:10px'>$stuCount</td><td style='padding:10px'><code>Student@123</code></td><td style='padding:10px'>".implode(', ',$students)."</td></tr>";
    echo "<tr style='background:#f8f9fa'><td style='padding:10px'>Teacher</td><td style='padding:10px'>$tchCount</td><td style='padding:10px'><code>Teacher@123</code></td><td style='padding:10px'>".implode(', ',$teachers)."</td></tr>";
    echo "</table>";
    echo "<br><a href='http://localhost/scti-school/pages/login-simple.php' style='background:#004080;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none'>Go to Login</a>";
    echo "</div>";
} catch(Exception $e) {
    echo "Error: ".$e->getMessage();
}
?>
