<?php
session_start();

// Clear remember me cookie if exists
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, "/");
    
    // Database configuration
    $host = 'localhost';
    $dbname = 'scti_school';
    $username = 'root';
    $password = '';
    
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Clear remember token from database
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_type'])) {
            $table = $_SESSION['user_type'] . 's';
            $stmt = $conn->prepare("UPDATE $table SET remember_token = NULL WHERE id = :id");
            $stmt->bindParam(':id', $_SESSION['user_id']);
            $stmt->execute();
        }
        
        $conn = null;
    } catch(PDOException $e) {
        // Silent fail
    }
}

// Destroy session
session_unset();
session_destroy();

// Redirect to home page
header("Location: ../index.php");
exit();
?>
