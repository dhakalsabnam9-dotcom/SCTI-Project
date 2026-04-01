<?php
require_once 'includes/config.php';
try {
    $db = getDBConnection();
    $stuPass = password_hash('Student@123', PASSWORD_DEFAULT);
    $tchPass = password_hash('Teacher@123', PASSWORD_DEFAULT);

    // Reset all student passwords
    $db->exec("UPDATE students SET password='$stuPass'");

    // Reset all teacher passwords  
    $db->exec("UPDATE teachers SET password='$tchPass'");

    // Get sample credentials
    $stu = $db->query("SELECT username, full_name FROM students ORDER BY full_name LIMIT 3")->fetchAll();
    $tch = $db->query("SELECT username, full_name FROM teachers LIMIT 3")->fetchAll();

    echo "<div style='font-family:sans-serif;max-width:600px;margin:30px auto;padding:24px;background:#fff;border-radius:10px;box-shadow:0 2px 12px rgba(0,0,0,.1)'>";
    echo "<h2 style='color:#28a745'>✓ Auto-fixed!</h2>";
    
    echo "<h3 style='color:#004080;margin-top:20px'>Student Login</h3>";
    echo "<p>Password: <code style='background:#e8f0fe;padding:3px 8px;border-radius:4px'>Student@123</code></p>";
    echo "<table style='width:100%;border-collapse:collapse'>";
    echo "<tr style='background:#004080;color:#fff'><th style='padding:8px;text-align:left'>Username</th><th style='padding:8px;text-align:left'>Full Name</th></tr>";
    foreach($stu as $s) echo "<tr style='border-bottom:1px solid #eee'><td style='padding:8px'><b>".$s['username']."</b></td><td style='padding:8px'>".$s['full_name']."</td></tr>";
    echo "</table>";

    echo "<h3 style='color:#004080;margin-top:20px'>Teacher Login</h3>";
    echo "<p>Password: <code style='background:#e8f0fe;padding:3px 8px;border-radius:4px'>Teacher@123</code></p>";
    if (count($tch)) {
        echo "<table style='width:100%;border-collapse:collapse'>";
        echo "<tr style='background:#004080;color:#fff'><th style='padding:8px;text-align:left'>Username</th><th style='padding:8px;text-align:left'>Full Name</th></tr>";
        foreach($tch as $t) echo "<tr style='border-bottom:1px solid #eee'><td style='padding:8px'><b>".$t['username']."</b></td><td style='padding:8px'>".$t['full_name']."</td></tr>";
        echo "</table>";
    } else {
        echo "<p style='color:#dc3545'>No teachers found in database!</p>";
    }

    echo "<br><a href='http://localhost/scti-school/pages/login-simple.php' style='background:#004080;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;display:inline-block;margin-top:10px'>Go to Login</a>";
    echo "</div>";
} catch(Exception $e) {
    echo "Error: ".$e->getMessage();
}
?>
