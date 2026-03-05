<?php
session_start();

// Database configuration
require_once('../includes/config.php');

// Get database connection
$conn = getDBConnection();

// Initialize variables
$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $userType = trim($_POST['userType']);
    
    // Validate inputs
    if (empty($username) || empty($password) || empty($userType)) {
        $error = "All fields are required!";
    } else {
        
        // Determine table based on user type
        $table = '';
        $redirect = '';
        switch($userType) {
            case 'student':
                $table = 'students';
                $redirect = '../dashboards/student-dashboard.php';
                break;
            case 'teacher':
                $table = 'teachers';
                $redirect = '../dashboards/teacher-dashboard.php';
                break;
            case 'admin':
                $table = 'admins';
                $redirect = '../dashboards/admin-dashboard.php';
                break;
            default:
                $error = "Invalid user type!";
        }
        
        if (empty($error)) {
            try {
                // Query database for user
                $stmt = $conn->prepare("SELECT * FROM $table WHERE username = :username LIMIT 1");
                $stmt->bindParam(':username', $username);
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Verify password
                    if (password_verify($password, $user['password'])) {
                        // Password is correct - set session variables
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['user_type'] = $userType;
                        $_SESSION['full_name'] = $user['full_name'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['logged_in'] = true;
                        
                        // Redirect to appropriate dashboard
                        header("Location: " . $redirect);
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
  <link rel="stylesheet" href="../assets/css/style.css">
  
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
    
    /* Password field with eye icon */
    .password-wrapper {
      position: relative;
    }
    
    .password-wrapper input {
      padding-right: 45px;
    }
    
    .password-toggle {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #666;
      font-size: 18px;
      transition: color 0.3s;
    }
    
    .password-toggle:hover {
      color: #004080;
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
      <img src="../assets/images/scti logo.jpeg" alt="SCTI Logo">
    </div>

    <div class="menu-toggle" id="menu-toggle">
      <i class="fa fa-bars"></i>
    </div>

    <nav class="menu" id="menu">
      <ul>
        <li><a href="../index.html">Home</a></li>
        <li><a href="../index.html#programs">Programs</a></li> 
        <li><a href="../index.html#gallery">Gallery</a></li>
        <li><a href="../index.html#notices">Notice Board</a></li>
        <li><a href="../index.html#contact">Contact Us</a></li>
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
          <strong><i class="fa fa-info-circle"></i> Login Information:</strong>
          <p>Use the username and password you created during signup. If you don't have an account, please register first.</p>
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
          <div class="password-wrapper">
            <input 
              type="password" 
              id="password" 
              name="password" 
              placeholder="Enter your password"
              required
            >
            <i class="fa fa-eye password-toggle" id="togglePassword" onclick="togglePasswordVisibility()"></i>
          </div>
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
  
  // Toggle password visibility
  function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('togglePassword');
    
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      toggleIcon.classList.remove('fa-eye');
      toggleIcon.classList.add('fa-eye-slash');
    } else {
      passwordInput.type = 'password';
      toggleIcon.classList.remove('fa-eye-slash');
      toggleIcon.classList.add('fa-eye');
    }
  }
</script>

</body>
</html>
