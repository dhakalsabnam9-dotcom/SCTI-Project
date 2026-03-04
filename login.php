<?php
session_start();

// Database configuration
$host = 'localhost';
$dbname = 'scti_school';
$username = 'root';
$password = '';

// Create database connection
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Initialize variables
$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $userType = trim($_POST['userType']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']) ? true : false;
    
    // Validate inputs
    if (empty($userType) || empty($username) || empty($password)) {
        $error = "All fields are required!";
    } else {
        
        // Prepare SQL based on user type
        $table = '';
        switch($userType) {
            case 'student':
                $table = 'students';
                break;
            case 'teacher':
                $table = 'teachers';
                break;
            case 'admin':
                $table = 'admins';
                break;
            default:
                $error = "Invalid user type!";
        }
        
        if (empty($error)) {
            try {
                // Query to check user credentials
                $stmt = $conn->prepare("SELECT * FROM $table WHERE (username = :username OR email = :username) AND status = 'active' LIMIT 1");
                $stmt->bindParam(':username', $username);
                $stmt->execute();
                
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user) {
                    // Verify password
                    if (password_verify($password, $user['password'])) {
                        
                        // Set session variables
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['user_type'] = $userType;
                        $_SESSION['full_name'] = $user['full_name'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['logged_in'] = true;
                        
                        // Set remember me cookie if checked
                        if ($remember) {
                            $token = bin2hex(random_bytes(32));
                            setcookie('remember_token', $token, time() + (86400 * 30), "/"); // 30 days
                            
                            // Store token in database
                            $stmt = $conn->prepare("UPDATE $table SET remember_token = :token WHERE id = :id");
                            $stmt->bindParam(':token', $token);
                            $stmt->bindParam(':id', $user['id']);
                            $stmt->execute();
                        }
                        
                        // Update last login
                        $stmt = $conn->prepare("UPDATE $table SET last_login = NOW() WHERE id = :id");
                        $stmt->bindParam(':id', $user['id']);
                        $stmt->execute();
                        
                        // Redirect based on user type
                        switch($userType) {
                            case 'student':
                                header("Location: student-dashboard.php");
                                break;
                            case 'teacher':
                                header("Location: teacher-dashboard.php");
                                break;
                            case 'admin':
                                header("Location: Admin-Notice-Board.html");
                                break;
                        }
                        exit();
                        
                    } else {
                        $error = "Invalid username or password!";
                    }
                } else {
                    $error = "Invalid username or password!";
                }
                
            } catch(PDOException $e) {
                $error = "Login error: " . $e->getMessage();
            }
        }
    }
}

// Check for remember me cookie
if (!isset($_SESSION['logged_in']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    
    // Check all user tables for the token
    $tables = ['students', 'teachers', 'admins'];
    
    foreach ($tables as $table) {
        try {
            $stmt = $conn->prepare("SELECT * FROM $table WHERE remember_token = :token AND status = 'active' LIMIT 1");
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Auto login
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_type'] = str_replace('s', '', $table); // Remove 's' from table name
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['logged_in'] = true;
                
                // Redirect based on user type
                $userType = str_replace('s', '', $table);
                switch($userType) {
                    case 'student':
                        header("Location: student-dashboard.php");
                        break;
                    case 'teacher':
                        header("Location: teacher-dashboard.php");
                        break;
                    case 'admin':
                        header("Location: Admin-Notice-Board.html");
                        break;
                }
                exit();
            }
        } catch(PDOException $e) {
            // Continue to next table
        }
    }
}

$conn = null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Sindhuli Community Technical Institute (SCTI)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

  <!-- External CSS -->
  <link rel="stylesheet" href="style.css">
  
  <style>
    .alert {
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 6px;
      font-size: 14px;
    }
    
    .alert-danger {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    
    .alert-success {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
  </style>
</head>

<body>

<!-- ===== TOP HEADER ===== -->
<div class="top-header">
  <marquee>
    Examination Notification | Genius 2025 | Enrollment Open 2025–26
  </marquee>
</div>

<!-- ===== HEADER & MENU ===== -->
<header class="header">
  <div class="container header-flex">

    <div class="logo">
      <img src="scti logo.jpeg" alt="SCTI Logo">
    </div>

    <div class="menu-toggle" id="menu-toggle">
      <i class="fa fa-bars"></i>
    </div>

    <nav class="menu" id="menu">
      <ul>
        <li><a href="index.html">Home</a></li>
        <li><a href="Programs.html">Programs</a></li> 
        <li><a href="Gallery.html">Gallery</a></li>
        <li><a href="Notice Board.html">Notice Board</a></li>
        <li><a href="Contact Us.html">Contact Us</a></li>
        <li><a href="login.php">Login</a></li>
      </ul>
    </nav>

  </div>
</header>

<!-- ===== LOGIN SECTION ===== -->
<section class="login-section">
  <div class="login-container">
    
    <div class="login-box">
      <div class="login-header">
        <i class="fa fa-user-circle"></i>
        <h2>Login to SCTI Portal</h2>
        <p>Access your student/staff account</p>
      </div>

      <form class="login-form" method="POST" action="login.php">
        
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger">
            <i class="fa fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
          </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
          <div class="alert alert-success">
            <i class="fa fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
          </div>
        <?php endif; ?>
        
        <div class="form-group">
          <label for="userType">
            <i class="fa fa-users"></i> User Type
          </label>
          <select id="userType" name="userType" required>
            <option value="">Select User Type</option>
            <option value="student" <?php echo (isset($_POST['userType']) && $_POST['userType'] == 'student') ? 'selected' : ''; ?>>Student</option>
            <option value="teacher" <?php echo (isset($_POST['userType']) && $_POST['userType'] == 'teacher') ? 'selected' : ''; ?>>Teacher</option>
            <option value="admin" <?php echo (isset($_POST['userType']) && $_POST['userType'] == 'admin') ? 'selected' : ''; ?>>Administrator</option>
          </select>
        </div>

        <div class="form-group">
          <label for="username">
            <i class="fa fa-user"></i> Username / Email
          </label>
          <input 
            type="text" 
            id="username" 
            name="username" 
            placeholder="Enter your username or email"
            value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
            required
          >
        </div>

        <div class="form-group">
          <label for="password">
            <i class="fa fa-lock"></i> Password
          </label>
          <input 
            type="password" 
            id="password" 
            name="password" 
            placeholder="Enter your password"
            required
          >
        </div>

        <div class="form-options">
          <label class="remember-me">
            <input type="checkbox" name="remember"> Remember Me
          </label>
          <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
        </div>

        <button type="submit" class="login-btn">
          <i class="fa fa-sign-in"></i> Login
        </button>

        <div class="login-footer">
          <p>Don't have an account? <a href="register.php">Register Here</a></p>
        </div>

      </form>
    </div>

  </div>
</section>

<!-- ===== FOOTER ===== -->
<footer class="footer">
  <p>© 2025 Sindhuli Community Technical Institute (SCTI)</p>
</footer>

<!-- ===== JS ===== -->
<script>
  document.getElementById("menu-toggle").onclick = function () {
    document.getElementById("menu").classList.toggle("show");
  };
</script>

</body>
</html>
