<?php
session_start();

// Initialize variables
$error = '';

// Simple hardcoded credentials (for testing without database)
$users = [
    'admin' => [
        'password' => 'admin123',
        'type' => 'admin',
        'name' => 'Administrator',
        'redirect' => 'Admin-Notice-Board.html'
    ],
    'teacher' => [
        'password' => 'teacher123',
        'type' => 'teacher',
        'name' => 'Teacher',
        'redirect' => 'teacher-dashboard.php'
    ],
    'student' => [
        'password' => 'student123',
        'type' => 'student',
        'name' => 'Student',
        'redirect' => 'student-dashboard.php'
    ]
];

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $userType = trim($_POST['userType']);
    
    // Validate inputs
    if (empty($username) || empty($password) || empty($userType)) {
        $error = "All fields are required!";
    } else {
        // Check credentials
        if (isset($users[$username]) && $users[$username]['password'] === $password && $users[$username]['type'] === $userType) {
            // Set session variables
            $_SESSION['user_id'] = 1;
            $_SESSION['username'] = $username;
            $_SESSION['user_type'] = $userType;
            $_SESSION['full_name'] = $users[$username]['name'];
            $_SESSION['logged_in'] = true;
            
            // Redirect to dashboard
            header("Location: " . $users[$username]['redirect']);
            exit();
        } else {
            $error = "Invalid username, password, or user type!";
        }
    }
}
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
    
    .credentials-info {
      background: #d1ecf1;
      color: #0c5460;
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 6px;
      font-size: 13px;
      border: 1px solid #bee5eb;
    }
    
    .credentials-info strong {
      display: block;
      margin-bottom: 8px;
    }
    
    .credentials-info ul {
      margin: 5px 0;
      padding-left: 20px;
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
        <li><a href="login-simple.php">Login</a></li>
        <li><a href="signup.php">Sign Up</a></li>
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

      <form class="login-form" method="POST" action="login-simple.php">
        
        <div class="credentials-info">
          <strong><i class="fa fa-info-circle"></i> Test Credentials:</strong>
          <ul>
            <li><strong>Admin:</strong> username: admin, password: admin123</li>
            <li><strong>Teacher:</strong> username: teacher, password: teacher123</li>
            <li><strong>Student:</strong> username: student, password: student123</li>
          </ul>
        </div>
        
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger">
            <i class="fa fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
          </div>
        <?php endif; ?>
        
        <div class="form-group">
          <label for="userType">
            <i class="fa fa-users"></i> User Type
          </label>
          <select id="userType" name="userType" required>
            <option value="">Select User Type</option>
            <option value="student">Student</option>
            <option value="teacher">Teacher</option>
            <option value="admin">Administrator</option>
          </select>
        </div>

        <div class="form-group">
          <label for="username">
            <i class="fa fa-user"></i> Username
          </label>
          <input 
            type="text" 
            id="username" 
            name="username" 
            placeholder="Enter your username"
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
          <a href="#" class="forgot-password">Forgot Password?</a>
        </div>

        <button type="submit" class="login-btn">
          <i class="fa fa-sign-in"></i> Login
        </button>

        <div class="login-footer">
          <p>Don't have an account? <a href="signup.php">Register Here</a></p>
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
