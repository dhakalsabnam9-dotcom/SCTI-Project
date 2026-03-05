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
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
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
                                header("Location: ../admin/Admin-Notice-Board.html");
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
          <input 
            type="password" 
            id="password" 
            name="password" 
            placeholder="Create a password (min 6 characters)"
            required
          >
        </div>

        <div class="form-group">
          <label for="confirmPassword">
            <i class="fa fa-lock"></i> Confirm Password
          </label>
          <input 
            type="password" 
            id="confirmPassword" 
            name="confirmPassword" 
            placeholder="Confirm your password"
            required
          >
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
</script>

</body>
</html>
