<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = '';
$success = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $userType = isset($_POST['userType']) ? trim($_POST['userType']) : '';
    
    // Simple validation
    if (empty($username) || empty($password) || empty($userType)) {
        $error = "All fields are required!";
    } else {
        // Check credentials
        $validLogin = false;
        $redirectTo = '';
        
        if ($userType == 'admin' && $username == 'admin' && $password == 'admin123') {
            $validLogin = true;
            $redirectTo = 'Admin-Notice-Board.html';
            $_SESSION['full_name'] = 'Administrator';
        } elseif ($userType == 'teacher' && $username == 'teacher' && $password == 'teacher123') {
            $validLogin = true;
            $redirectTo = 'teacher-dashboard.php';
            $_SESSION['full_name'] = 'Teacher';
        } elseif ($userType == 'student' && $username == 'student' && $password == 'student123') {
            $validLogin = true;
            $redirectTo = 'student-dashboard.php';
            $_SESSION['full_name'] = 'Student';
        }
        
        if ($validLogin) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['user_type'] = $userType;
            
            header("Location: " . $redirectTo);
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
  <title>Login - SCTI</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
    .test-info {
      background: #d1ecf1;
      color: #0c5460;
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 6px;
      font-size: 13px;
      border: 1px solid #bee5eb;
    }
    .test-info strong {
      display: block;
      margin-bottom: 8px;
    }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Examination Notification | Genius 2025 | Enrollment Open 2025–26</marquee>
</div>

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
        <li><a href="login-fixed.php">Login</a></li>
      </ul>
    </nav>
  </div>
</header>

<section class="login-section">
  <div class="login-container">
    <div class="login-box">
      <div class="login-header">
        <i class="fa fa-user-circle"></i>
        <h2>Login to SCTI Portal</h2>
        <p>Access your student/staff account</p>
      </div>

      <form class="login-form" method="POST" action="login-fixed.php">
        
        <div class="test-info">
          <strong><i class="fa fa-info-circle"></i> Test Credentials:</strong>
          <p style="margin: 5px 0;"><strong>Admin:</strong> username: admin, password: admin123</p>
          <p style="margin: 5px 0;"><strong>Teacher:</strong> username: teacher, password: teacher123</p>
          <p style="margin: 5px 0;"><strong>Student:</strong> username: student, password: student123</p>
        </div>
        
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger">
        