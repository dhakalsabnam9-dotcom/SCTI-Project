<?php
require_once 'includes/config.php';
try {
    $db = getDBConnection();
    
    // Check if abhi5 exists
    $stmt = $db->prepare("SELECT id, username, password, status FROM students WHERE username=? LIMIT 1");
    $stmt->execute(['abhi5']);
    $user = $stmt->fetch();
    
    echo "<div style='font-family:sans-serif;padding:20px'>";
    
    if ($user) {
        echo "<h3 style='color:green'>✓ User 'abhi5' found</h3>";
        echo "<p>Status: <b>".$user['status']."</b></p>";
        echo "<p>Password hash starts with: <b>".substr($user['password'],0,10)."...</b></p>";
        
        // Test password
        $testPass = 'Student@123';
        $ok = password_verify($testPass, $user['password']);
        echo "<p>Password 'Student@123' matches: <b style='color:".($ok?'green':'red')."'>".($ok?'YES':'NO')."</b></p>";
        
        if (!$ok) {
            // Reset it now
            $newHash = password_hash('Student@123', PASSWORD_DEFAULT);
            $db->prepare("UPDATE students SET password=? WHERE username='abhi5'")->execute([$newHash]);
            echo "<p style='color:green'><b>✓ Password reset to Student@123</b></p>";
        }
    } else {
        echo "<h3 style='color:red'>✗ User 'abhi5' NOT found in database</h3>";
        // Show first 5 students
        $rows = $db->query("SELECT username, status FROM students LIMIT 5")->fetchAll();
        echo "<p>First 5 students in DB:</p><ul>";
        foreach($rows as $r) echo "<li>".$r['username']." (".$r['status'].")</li>";
        echo "</ul>";
    }
    echo "</div>";
} catch(Exception $e) {
    echo "Error: ".$e->getMessage();
}
?>
