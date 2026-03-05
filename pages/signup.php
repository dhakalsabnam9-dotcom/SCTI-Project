<?php
session_start();

// Database configuration
require_once('../includes/config.php');

// Get database connection
$conn = getDBConnection();

// Initialize variables
$error = '';
$success = '';

// Handle signup form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $userType = trim($_POST['userType']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);
    $fullName = trim($_POST['fullName']);
    
    // Validate inputs
    if (empty($userType) || empty($username) || empty($email) || empty($password) || empty($confirmPassword) || empty($fullName)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 7 || strlen($password) > 12) {
        $error = "Password must be between 7 and 12 characters!";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $error = "Password must contain at least one uppercase letter!";
    } elseif (!preg_match('/[a-z]/', $password)) {
        $error = "Password must contain at least one lowercase letter!";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $error = "Password must contain at least one number!";
    } elseif (!preg_match('/[!@#$%^&*]/', $password)) {
        $error = "Password must contain at least one special character (!@#$%^&*)!";
    } else {
        
        // Determine table based on user type
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
                // Check if username already exists
                $stmt = $conn->prepare("SELECT * FROM $table WHERE username = :username LIMIT 1");
                $stmt->bindParam(':username', $username);
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    $error = "Username already exists! Please choose a different username.";
                } else {
                    // Check if email already exists
                    $stmt = $conn->prepare("SELECT * FROM $table WHERE email = :email LIMIT 1");
                    $stmt->bindParam(':email', $email);
                    $stmt->execute();
                    
                    if ($stmt->rowCount() > 0) {
                        $error = "Email already registered! Please use a different email.";
                    } else {
                        // Hash password
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        
                        // Generate unique ID based on user type
                        $uniqueId = '';
                        if ($userType == 'student') {
                            $uniqueId = 'STU' . date('Y') . rand(1000, 9999);
                        } elseif ($userType == 'teacher') {
                            $uniqueId = 'TCH' . rand(100, 999);
                        }
                        
                        // Insert new user
                        if ($userType == 'student') {
                            $stmt = $conn->prepare("INSERT INTO students (username, email, password, full_name, student_id, status) VALUES (:username, :email, :password, :full_name, :student_id, 'active')");
                            $stmt->bindParam(':student_id', $uniqueId);
                        } elseif ($userType == 'teacher') {
                            $stmt = $conn->prepare("INSERT INTO teachers (username, email, password, full_name, teacher_id, status) VALUES (:username, :email, :password, :full_name, :teacher_id, 'active')");
                            $stmt->bindParam(':teacher_id', $uniqueId);
                        } else {
                            $stmt = $conn->prepare("INSERT INTO admins (username, email, password, full_name, status) VALUES (:username, :email, :password, :full_name, 'active')");
                        }
                        
                        $stmt->bindParam(':username', $username);
                        $stmt->bindParam(':email', $email);
                        $stmt->bindParam(':password', $hashedPassword);
                        $stmt->bindParam(':full_name', $fullName);
                        $stmt->execute();
                        
                        // Get the new user ID
                        $userId = $conn->lastInsertId();
                        
                        // Set session variables (auto login after signup)
                        $_SESSION['user_id'] = $userId;
                        $_SESSION['username'] = $username;
                        $_SESSION['user_type'] = $userType;
                        $_SESSION['full_name'] = $fullName;
                        $_SESSION['email'] = $email;
                        $_SESSION['logged_in'] = true;
                        
                        // Redirect based on user type
                        switch($userType) {
                            case 'student':
                                header("Location: ../dashboards/student-dashboard.php");
                                break;
                            case 'teacher':
                                header("Location: ../dashboards/teacher-dashboard.php");
                                break;
                            case 'admin':
                                header("Location: ../dashboards/admin-dashboard.php");
                                break;
                        }
                        exit();
                    }
                }
                
            } catch(PDOException $e) {
                $error = "Signup error: " . $e->getMessage();
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
  <title>Sign Up - Sindhuli Community Technical Institute (SCTI)</title>
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
    
    .alert-success {
      background: linear-gradient(135deg, #d4edda, #c3e6cb);
      color: #155724;
      border-left: 4px solid #28a745;
      box-shadow: 0 2px 10px rgba(40, 167, 69, 0.2);
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
    
    .form-group input:focus + label,
    .form-group select:focus + label {
      color: #004080;
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
    
    /* Password strength indicator */
    .password-strength {
      margin-top: 10px;
      font-size: 12px;
      animation: fadeIn 0.3s ease-out;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    
    .strength-bar {
      height: 6px;
      background: #e0e0e0;
      border-radius: 3px;
      margin-top: 8px;
      overflow: hidden;
      box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .strength-bar-fill {
      height: 100%;
      transition: all 0.4s ease;
      width: 0%;
      border-radius: 3px;
    }
    
    .strength-weak { 
      background: linear-gradient(90deg, #dc3545, #ff6b6b);
      width: 33%;
      box-shadow: 0 0 10px rgba(220, 53, 69, 0.5);
    }
    
    .strength-medium { 
      background: linear-gradient(90deg, #ffc107, #ffdd57);
      width: 66%;
      box-shadow: 0 0 10px rgba(255, 193, 7, 0.5);
    }
    
    .strength-strong { 
      background: linear-gradient(90deg, #28a745, #5cb85c);
      width: 100%;
      box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
    }
    
    /* Validation messages */
    .validation-list {
      margin-top: 12px;
      padding: 15px;
      background: linear-gradient(135deg, #f8f9fa, #e9ecef);
      border-radius: 8px;
      font-size: 12px;
      border: 1px solid #dee2e6;
      animation: fadeIn 0.3s ease-out;
    }
    
    .validation-list li {
      padding: 6px 0;
      color: #666;
      transition: all 0.3s;
      display: flex;
      align-items: center;
    }
    
    .validation-list li.valid {
      color: #28a745;
      font-weight: 600;
    }
    
    .validation-list li.invalid {
      color: #dc3545;
    }
    
    .validation-list li i {
      margin-right: 8px;
      font-size: 14px;
      transition: transform 0.3s;
    }
    
    .validation-list li.valid i {
      animation: checkBounce 0.5s ease-out;
    }
    
    @keyframes checkBounce {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.2); }
    }
    
    /* Password match message */
    #matchMessage {
      font-size: 13px;
      margin-top: 8px;
      display: block;
      font-weight: 600;
      animation: fadeIn 0.3s ease-out;
    }
    
    #matchMessage i {
      margin-right: 5px;
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
    
    /* Floating label effect */
    .form-group {
      position: relative;
    }
    
    .form-group input::placeholder,
    .form-group select::placeholder {
      transition: all 0.3s;
    }
    
    .form-group input:focus::placeholder {
      opacity: 0.5;
      transform: translateX(5px);
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
      
      .validation-list {
        font-size: 11px;
      }
      
      .password-toggle {
        font-size: 16px;
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

<!-- ===== SIGNUP SECTION ===== -->
<section class="login-section">
  <div class="login-container">
    
    <div class="login-box">
      <div class="login-header">
        <i class="fa fa-user-plus"></i>
        <h2>Create SCTI Account</h2>
        <p>Register as a new user</p>
      </div>

      <form class="login-form" method="POST" action="signup.php">
        
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
          <label for="fullName">
            <i class="fa fa-id-card"></i> Full Name
          </label>
          <input 
            type="text" 
            id="fullName" 
            name="fullName" 
            placeholder="Enter your full name"
            value="<?php echo isset($_POST['fullName']) ? htmlspecialchars($_POST['fullName']) : ''; ?>"
            required
          >
        </div>

        <div class="form-group">
          <label for="username">
            <i class="fa fa-user"></i> Username
          </label>
          <input 
            type="text" 
            id="username" 
            name="username" 
            placeholder="Choose a username"
            value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
            required
          >
        </div>

        <div class="form-group">
          <label for="email">
            <i class="fa fa-envelope"></i> Email
          </label>
          <input 
            type="email" 
            id="email" 
            name="email" 
            placeholder="Enter your email"
            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
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
              placeholder="Create a password (7-12 characters)"
              required
            >
            <i class="fa fa-eye password-toggle" id="togglePassword" onclick="togglePasswordVisibility('password', 'togglePassword')"></i>
          </div>
          <div class="password-strength" id="passwordStrength" style="display: none;">
            <div class="strength-bar">
              <div class="strength-bar-fill" id="strengthBar"></div>
            </div>
            <span id="strengthText"></span>
          </div>
          <ul class="validation-list" id="passwordValidation">
            <li id="lengthCheck"><i class="fa fa-circle"></i> 7-12 characters</li>
            <li id="uppercaseCheck"><i class="fa fa-circle"></i> At least one uppercase letter</li>
            <li id="lowercaseCheck"><i class="fa fa-circle"></i> At least one lowercase letter</li>
            <li id="numberCheck"><i class="fa fa-circle"></i> At least one number</li>
            <li id="specialCheck"><i class="fa fa-circle"></i> At least one special character (!@#$%^&*)</li>
          </ul>
        </div>

        <div class="form-group">
          <label for="confirmPassword">
            <i class="fa fa-lock"></i> Confirm Password
          </label>
          <div class="password-wrapper">
            <input 
              type="password" 
              id="confirmPassword" 
              name="confirmPassword" 
              placeholder="Confirm your password"
              required
            >
            <i class="fa fa-eye password-toggle" id="toggleConfirmPassword" onclick="togglePasswordVisibility('confirmPassword', 'toggleConfirmPassword')"></i>
          </div>
          <span id="matchMessage" style="font-size: 12px; margin-top: 5px; display: block;"></span>
        </div>

        <button type="submit" class="login-btn">
          <i class="fa fa-user-plus"></i> Sign Up
        </button>

        <div class="login-footer">
          <p>Already have an account? <a href="login-simple.php">Login Here</a></p>
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
  function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    } else {
      input.type = 'password';
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    }
  }
  
  // Password validation
  const passwordInput = document.getElementById('password');
  const confirmPasswordInput = document.getElementById('confirmPassword');
  
  // Validation checks
  const checks = {
    length: { element: document.getElementById('lengthCheck'), regex: /^.{7,12}$/ },
    uppercase: { element: document.getElementById('uppercaseCheck'), regex: /[A-Z]/ },
    lowercase: { element: document.getElementById('lowercaseCheck'), regex: /[a-z]/ },
    number: { element: document.getElementById('numberCheck'), regex: /[0-9]/ },
    special: { element: document.getElementById('specialCheck'), regex: /[!@#$%^&*]/ }
  };
  
  // Real-time password validation
  passwordInput.addEventListener('input', function() {
    const password = this.value;
    let validCount = 0;
    
    // Show strength indicator
    document.getElementById('passwordStrength').style.display = 'block';
    
    // Check each validation rule
    for (let key in checks) {
      const check = checks[key];
      const isValid = check.regex.test(password);
      
      if (isValid) {
        check.element.classList.remove('invalid');
        check.element.classList.add('valid');
        check.element.querySelector('i').classList.remove('fa-circle');
        check.element.querySelector('i').classList.add('fa-check-circle');
        validCount++;
      } else {
        check.element.classList.remove('valid');
        check.element.classList.add('invalid');
        check.element.querySelector('i').classList.remove('fa-check-circle');
        check.element.querySelector('i').classList.add('fa-circle');
      }
    }
    
    // Update strength bar
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    
    strengthBar.className = 'strength-bar-fill';
    
    if (validCount <= 2) {
      strengthBar.classList.add('strength-weak');
      strengthText.textContent = 'Weak';
      strengthText.style.color = '#dc3545';
    } else if (validCount <= 4) {
      strengthBar.classList.add('strength-medium');
      strengthText.textContent = 'Medium';
      strengthText.style.color = '#ffc107';
    } else {
      strengthBar.classList.add('strength-strong');
      strengthText.textContent = 'Strong';
      strengthText.style.color = '#28a745';
    }
    
    // Check password match
    checkPasswordMatch();
  });
  
  // Check password match
  confirmPasswordInput.addEventListener('input', checkPasswordMatch);
  
  function checkPasswordMatch() {
    const password = passwordInput.value;
    const confirmPassword = confirmPasswordInput.value;
    const matchMessage = document.getElementById('matchMessage');
    
    if (confirmPassword === '') {
      matchMessage.textContent = '';
      return;
    }
    
    if (password === confirmPassword) {
      matchMessage.innerHTML = '<i class="fa fa-check-circle" style="color: #28a745;"></i> Passwords match';
      matchMessage.style.color = '#28a745';
    } else {
      matchMessage.innerHTML = '<i class="fa fa-times-circle" style="color: #dc3545;"></i> Passwords do not match';
      matchMessage.style.color = '#dc3545';
    }
  }
  
  // Form validation before submit
  document.querySelector('.login-form').addEventListener('submit', function(e) {
    const password = passwordInput.value;
    const confirmPassword = confirmPasswordInput.value;
    const email = document.getElementById('email').value;
    
    // Check all fields are filled
    const requiredFields = ['userType', 'fullName', 'username', 'email', 'password', 'confirmPassword'];
    for (let field of requiredFields) {
      const input = document.getElementById(field);
      if (!input.value.trim()) {
        e.preventDefault();
        alert('Please fill in all fields!');
        input.focus();
        return false;
      }
    }
    
    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      e.preventDefault();
      alert('Please enter a valid email address!');
      document.getElementById('email').focus();
      return false;
    }
    
    // Validate password length
    if (password.length < 7 || password.length > 12) {
      e.preventDefault();
      alert('Password must be between 7 and 12 characters!');
      passwordInput.focus();
      return false;
    }
    
    // Validate password requirements
    if (!/[A-Z]/.test(password)) {
      e.preventDefault();
      alert('Password must contain at least one uppercase letter!');
      passwordInput.focus();
      return false;
    }
    
    if (!/[a-z]/.test(password)) {
      e.preventDefault();
      alert('Password must contain at least one lowercase letter!');
      passwordInput.focus();
      return false;
    }
    
    if (!/[0-9]/.test(password)) {
      e.preventDefault();
      alert('Password must contain at least one number!');
      passwordInput.focus();
      return false;
    }
    
    if (!/[!@#$%^&*]/.test(password)) {
      e.preventDefault();
      alert('Password must contain at least one special character (!@#$%^&*)!');
      passwordInput.focus();
      return false;
    }
    
    // Check passwords match
    if (password !== confirmPassword) {
      e.preventDefault();
      alert('Passwords do not match!');
      confirmPasswordInput.focus();
      return false;
    }
    
    return true;
  });
</script>

</body>
</html>
