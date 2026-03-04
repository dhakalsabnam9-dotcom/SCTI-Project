<?php
// Test database connection
$host = 'localhost';
$dbname = 'scti_school';
$username = 'root';
$password = '';

echo "<h2>XAMPP Connection Test</h2>";
echo "<hr>";

// Test 1: Check if PHP is working
echo "<h3>✅ PHP is working!</h3>";
echo "PHP Version: " . phpversion() . "<br><br>";

// Test 2: Check MySQL connection
try {
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<h3>✅ MySQL connection successful!</h3><br>";
    
    // Test 3: Check if database exists
    $stmt = $conn->query("SHOW DATABASES LIKE 'scti_school'");
    $db_exists = $stmt->rowCount() > 0;
    
    if ($db_exists) {
        echo "<h3>✅ Database 'scti_school' exists!</h3><br>";
        
        // Connect to the database
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Test 4: Check tables
        $tables = ['students', 'teachers', 'admins', 'notices'];
        echo "<h3>Checking Tables:</h3>";
        echo "<ul>";
        foreach ($tables as $table) {
            $stmt = $conn->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                // Count records
                $count_stmt = $conn->query("SELECT COUNT(*) as count FROM $table");
                $count = $count_stmt->fetch(PDO::FETCH_ASSOC)['count'];
                echo "<li>✅ Table '$table' exists ($count records)</li>";
            } else {
                echo "<li>❌ Table '$table' NOT found</li>";
            }
        }
        echo "</ul><br>";
        
        // Test 5: Check admin user
        $stmt = $conn->query("SELECT username, email, full_name FROM admins WHERE username = 'admin'");
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin) {
            echo "<h3>✅ Admin user found!</h3>";
            echo "Username: " . $admin['username'] . "<br>";
            echo "Email: " . $admin['email'] . "<br>";
            echo "Name: " . $admin['full_name'] . "<br><br>";
        } else {
            echo "<h3>❌ Admin user NOT found!</h3>";
            echo "Please import database-setup.sql<br><br>";
        }
        
        echo "<hr>";
        echo "<h2>🎉 Everything is ready!</h2>";
        echo "<p><a href='login-simple.php' style='padding: 10px 20px; background: #004080; color: white; text-decoration: none; border-radius: 5px;'>Go to Login Page</a></p>";
        
    } else {
        echo "<h3>❌ Database 'scti_school' NOT found!</h3>";
        echo "<p><strong>Action Required:</strong></p>";
        echo "<ol>";
        echo "<li>Open phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>";
        echo "<li>Click 'Import' tab</li>";
        echo "<li>Choose file: database-setup.sql</li>";
        echo "<li>Click 'Go' button</li>";
        echo "<li>Refresh this page</li>";
        echo "</ol>";
    }
    
} catch(PDOException $e) {
    echo "<h3>❌ Connection failed!</h3>";
    echo "Error: " . $e->getMessage() . "<br><br>";
    echo "<p><strong>Troubleshooting:</strong></p>";
    echo "<ul>";
    echo "<li>Make sure XAMPP Apache and MySQL are running</li>";
    echo "<li>Check if MySQL is running on port 3306</li>";
    echo "<li>Verify database credentials in config.php</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><small>Test file: test-connection.php</small></p>";
?>
