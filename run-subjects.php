<?php
require_once 'includes/config.php';
try {
    $db = getDBConnection();
    $sql = file_get_contents('create-subjects-table.sql');
    // Remove USE statement (already connected)
    $sql = preg_replace('/USE\s+\w+;\s*/i', '', $sql);
    // Split and run each statement
    $stmts = array_filter(array_map('trim', explode(';', $sql)));
    $count = 0;
    foreach ($stmts as $s) {
        if ($s) { $db->exec($s); $count++; }
    }
    echo "<div style='font-family:sans-serif;max-width:500px;margin:40px auto;padding:30px;background:#d4edda;border-radius:10px'>";
    echo "<h2 style='color:#155724'>✓ Subjects table created!</h2>";
    echo "<p>Executed $count statements.</p>";
    echo "<a href='http://localhost/scti-school/pages/teacher-attendance.php' style='background:#004080;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;display:inline-block;margin-top:10px'>Go to Attendance</a>";
    echo "</div>";
} catch(Exception $e) {
    echo "<div style='color:red;font-family:sans-serif;padding:20px'>Error: ".$e->getMessage()."</div>";
}
?>
