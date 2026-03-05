<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'scti_school');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site Configuration
define('SITE_NAME', 'Sindhuli Community Technical Institute (SCTI)');
define('SITE_URL', 'http://localhost/scti');
define('ADMIN_EMAIL', 'admin@scti.edu.np');

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

// Timezone
date_default_timezone_set('Asia/Kathmandu');

// Error Reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Connection Singleton
class Database {
    private static $instance = null;
    private $connection;
    
    // Private constructor to prevent direct instantiation
    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    
    // Prevent cloning of the instance
    private function __clone() {}
    
    // Prevent unserialization of the instance
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
    
    // Get the singleton instance
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    // Get the PDO connection
    public function getConnection() {
        return $this->connection;
    }
}

// Helper function for backward compatibility
function getDBConnection() {
    return Database::getInstance()->getConnection();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Check user type
function checkUserType($allowedTypes) {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
    
    if (!in_array($_SESSION['user_type'], $allowedTypes)) {
        header("Location: unauthorized.php");
        exit();
    }
}

// Sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Generate CSRF Token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF Token
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Format date
function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

// Get user full name
function getUserFullName() {
    return isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'User';
}

// Get user type display name
function getUserTypeDisplay() {
    if (!isset($_SESSION['user_type'])) return '';
    
    $types = [
        'student' => 'Student',
        'teacher' => 'Teacher',
        'admin' => 'Administrator'
    ];
    
    return $types[$_SESSION['user_type']] ?? '';
}
?>
