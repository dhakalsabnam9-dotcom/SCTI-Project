<?php
session_start();

// Check if user is already logged in and redirect to appropriate dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['user_type'])) {
    switch($_SESSION['user_type']) {
        case 'student':
            header("Location: dashboards/student-dashboard.php");
            exit();
        case 'teacher':
            header("Location: dashboards/teacher-dashboard.php");
            exit();
        case 'admin':
            header("Location: dashboards/admin-dashboard.php");
            exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sindhuli Community Technical Institute (SCTI)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

  <!-- External CSS -->
  <link rel="stylesheet" href="assets/css/style.css?v=26">
</head>

<body>

<!-- ===== TOP HEADER ===== -->
<div class="top-header">
  <marquee>
    Examination Notification | Genius 2025 | Enrollment Open 2025–26
  </marquee>
</div>

<!-- ===== HEADER & MENU (STICKY) ===== -->
<header class="header">
  <div class="container header-flex">

    <div class="logo">
      <img src="assets/images/scti logo.jpeg" alt="SCTI Logo">
    </div>

    <div class="menu-toggle" id="menu-toggle">
      <i class="fa fa-bars"></i>
    </div>

    <nav class="menu" id="menu">
      <ul>
        <li><a href="#" onclick="loadPage('home'); return false;">Home</a></li>
        <li><a href="#" onclick="loadPage('programs'); return false;">Programs</a></li> 
        <li><a href="#" onclick="loadPage('gallery'); return false;">Gallery</a></li>
        <li><a href="#" onclick="loadPage('notices'); return false;">Notice Board</a></li>
        <li><a href="#" onclick="loadPage('contact'); return false;">Contact Us</a></li>
        <li><a href="#" onclick="loadPage('login'); return false;">Login</a></li>
        <li><a href="#" onclick="loadPage('signup'); return false;">Sign Up</a></li>
      </ul>
    </nav>

  </div>
</header>

<!-- ===== DYNAMIC CONTENT AREA ===== -->
<div id="content-area">
  <!-- Content will be loaded here dynamically -->
</div>

<!-- ===== FOOTER ===== -->
<footer class="footer">
  <p>© 2025 Sindhuli Community Technical Institute (SCTI)</p>
</footer>

<!-- ===== JAVASCRIPT ===== -->
<script src="assets/js/menu.js"></script>
<script src="assets/js/pages/home.js"></script>
<script src="assets/js/pages/programs.js"></script>
<script src="assets/js/pages/gallery.js?v=30"></script>
<script src="assets/js/pages/notices.js"></script>
<script src="assets/js/pages/contact.js"></script>
<script src="assets/js/pages/login.js"></script>
<script src="assets/js/pages/signup.js"></script>
<script src="assets/js/pages.js"></script>
<script src="assets/js/app.js?v=25"></script>

</body>
</html>
