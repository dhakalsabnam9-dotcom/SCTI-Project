<?php
require_once 'includes/config.php';
try {
    $db = getDBConnection();
    $stmt = $db->prepare("UPDATE teachers SET username='teacher123', password='123456' LIMIT 1");
    $stmt->execute();
    $rows = $stmt->rowCount();
    if ($rows > 0) {
        echo "Done! Username: teacher123 | Password: 123456 <br><a href='pages/login-simple.php'>Go to Login</a>";
    } else {
        $db->exec("INSERT INTO teachers (full_name,username,password,email,department,status) VALUES ('Demo Teacher','teacher123','123456','t@t.com','General','active')");
        echo "Teacher created! Username: teacher123 | Password: 123456 <br><a href='pages/login-simple.php'>Go to Login</a>";
    }
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
