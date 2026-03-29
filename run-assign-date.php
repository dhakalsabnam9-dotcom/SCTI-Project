<?php
require_once 'includes/config.php';
try {
    $db = getDBConnection();
    $db->exec("ALTER TABLE assignments ADD COLUMN IF NOT EXISTS assign_date DATETIME DEFAULT NULL");
    echo "<h2 style='color:green'>✓ assign_date column added!</h2>";
    echo "<a href='http://localhost/scti-school/pages/teacher-assignments.php'>Go to Assignments</a>";
} catch(Exception $e) {
    echo "<h2 style='color:orange'>Note: " . $e->getMessage() . "</h2>";
    echo "<p>Column may already exist. <a href='http://localhost/scti-school/pages/teacher-assignments.php'>Go to Assignments</a></p>";
}
?>
