<?php
require_once 'includes/config.php';
try {
    $db = getDBConnection();
    $newPass = password_hash('Student@123', PASSWORD_DEFAULT);
    // Reset all student passwords
    $db->exec("UPDATE students SET password='$newPass'");
    $count = $db->query("SELECT COUNT(*) FROM students")->fetchColumn();
    echo "<div style='font-family:sans-serif;max-width:500px;margin:40px auto;padding:30px;background:#d4edda;border-radius:10px'>";
    echo "<h2 style='color:#155724'>✓ Done!</h2>";
    echo "<p>Reset password for <b>$count</b> students.</p>";
    echo "<p>All students can now login with password: <code>Student@123</code></p>";
    echo "</div>";
} catch(Exception $e) {
    echo "Error: ".$e->getMessage();
}
?>
