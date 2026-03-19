<?php
session_start();
require_once('../includes/config.php');
$conn = getDBConnection();

$error = '';
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) || 
          (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
          isset($_POST['ajax']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $userType = trim($_POST['userType'] ?? '');

    if (empty($username) || empty($password) || empty($userType)) {
        $error = "All fields are required!";
    } else {
        $tableMap = ['student'=>'students','teacher'=>'teachers','admin'=>'admins'];
        $redirectMap = [
            'student' => '../dashboards/student-dashboard.php',
            'teacher' => '../dashboards/teacher-dashboard.php',
            'admin'   => '../dashboards/admin-dashboard.php'
        ];

        if (!isset($tableMap[$userType])) {
            $error = "Invalid user type!";
        } else {
            $table    = $tableMap[$userType];
            $redirect = $redirectMap[$userType];
            try {
                $stmt = $conn->prepare("SELECT * FROM $table WHERE username = :username LIMIT 1");
                $stmt->bindParam(':username', $username);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    $passwordOk = password_verify($password, $user['password']) || ($password === $user['password']);
                    if ($passwordOk) {
                        $_SESSION['user_id']   = $user['id'];
                        $_SESSION['username']  = $user['username'];
                        $_SESSION['user_type'] = $userType;
                        $_SESSION['full_name'] = $user['full_name'];
                        $_SESSION['email']     = $user['email'];
                        $_SESSION['logged_in'] = true;

                        if ($isAjax) {
                            header('Content-Type: application/json');
                            echo json_encode(['success'=>true,'redirect'=>$redirect,'userType'=>$userType]);
                            exit();
                        }
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

    if ($isAjax && $error) {
        header('Content-Type: application/json');
        echo json_encode(['success'=>false,'message'=>$error]);
        exit();
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
      border-radius: 8px;
      font-size: 14px;
      animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .alert-danger {
      background: linear-gradient(135deg, #f8d7da, #f5c6cb);
      color: #721c24;
      border-left: 4px solid #dc3545;
      box-shadow: 0 2px 10px rgba(220, 53, 69, 0.2);
    }
    
    .credentials-info {
      background: linear-gradient(135deg, #d1ecf1, #bee5eb);
      color: #0c5460;
      padding: 18px;
      margin-bottom: 20px;
      border-radius: 8px;
      font-size: 13px;
      border-left: 4px solid #17a2b8;
      box-shadow: 0 2px 10px rgba(23, 162, 184, 0.2);
      animation: fadeIn 0.5s ease-out;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    
    .credentials-info strong {
      display: block;
      margin-bottom: 10px;
      font-size: 14px;
    }
    
    .credentials-info p {
      line-height: 1.6;
      margin: 0;
    }
    
    .credentials-info ul {
      margin: 5px 0;
      padding-left: 20px;
    }
    
    /* Enhanced form styling */
    .login-box {
      animation: fadeInUp 0.5s ease-out;
    }
    
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .form-group {
      position: relative;
      margin-bottom: 25px;
    }
    
    .form-group input:focus,
    .form-group select:focus {
      border-color: #004080;
      box-shadow: 0 0 0 3px rgba(0, 64, 128, 0.1);
      transform: translateY(-2px);
    }
    
    .form-group label {
      font-weight: 600;
      color: #333;
      margin-bottom: 8px;
      display: block;
      transition: color 0.3s;
    }
    
    /* Password field with eye icon */
    .password-wrapper {
      position: relative;
    }
    
    .password-wrapper input {
      padding-right: 45px;
      transition: all 0.3s;
    }
    
    .password-toggle {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #666;
      font-size: 18px;
      transition: all 0.3s;
      z-index: 10;
    }
    
    .password-toggle:hover {
      color: #004080;
      transform: translateY(-50%) scale(1.1);
    }
    
    .password-toggle:active {
      transform: translateY(-50%) scale(0.95);
    }
    
    /* Enhanced button */
    .login-btn {
      position: relative;
      overflow: hidden;
      transition: all 0.3s;
    }
    
    .login-btn::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.2);
      transform: translate(-50%, -50%);
      transition: width 0.6s, height 0.6s;
    }
    
    .login-btn:hover::before {
      width: 300px;
      height: 300px;
    }
    
    .login-btn:active {
      transform: translateY(0);
      box-shadow: 0 2px 8px rgba(0, 64, 128, 0.3);
    }
    
    /* Input focus effects */
    .form-group input,
    .form-group select {
      transition: all 0.3s;
    }
    
    .form-group input:hover,
    .form-group select:hover {
      border-color: #0059b3;
    }
    
    /* Form options enhancement */
    .form-options {
      animation: fadeIn 0.5s ease-out 0.2s both;
    }
    
    .forgot-password {
      position: relative;
      transition: all 0.3s;
    }
    
    .forgot-password::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0;
      height: 2px;
      background: #004080;
      transition: width 0.3s;
    }
    
    .forgot-password:hover::after {
      width: 100%;
    }
    
    /* Login footer enhancement */
    .login-footer {
      animation: fadeIn 0.5s ease-out 0.3s both;
    }
    
    .login-footer a {
      position: relative;
      transition: all 0.3s;
    }
    
    .login-footer a::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0;
      height: 2px;
      background: #004080;
      transition: width 0.3s;
    }
    
    .login-footer a:hover::after {
      width: 100%;
    }
    
    /* Responsive enhancements */
    @media (max-width: 768px) {
      .login-container {
        padding: 0 15px;
      }
      
      .password-toggle {
        font-size: 16px;
      }
      
      .credentials-info {
        font-size: 12px;
      }
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
