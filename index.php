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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- External CSS -->
  <link rel="stylesheet" href="assets/css/style.css?v=32">
</head>

<body>

<!-- ===== TOP HEADER ===== -->
<div class="top-header">
  <span class="ticker-label"><i class="fa fa-bullhorn"></i> NOTICE</span>
  <div class="ticker-outer">
    <div class="ticker-text" id="tickerText">
      📢 Examination form submission deadline is 25th Poush 2081 &nbsp;&nbsp;|&nbsp;&nbsp; 🎓 Annual Sports Day on 1st Magh 2081 — All students must participate &nbsp;&nbsp;|&nbsp;&nbsp; 📝 Enrollment for new batch 2025–26 is now open &nbsp;&nbsp;|&nbsp;&nbsp; 🏫 College closed on 15th Poush for national holiday &nbsp;&nbsp;|&nbsp;&nbsp; 📋 Result of 1st semester internal exam published — check notice board &nbsp;&nbsp;|&nbsp;&nbsp; 🔔 Library books must be returned before Magh 5th &nbsp;&nbsp;|&nbsp;&nbsp; 🎉 Congratulations to all distinction holders of board exam &nbsp;&nbsp;|&nbsp;&nbsp; 📌 Parent-teacher meeting on Magh 10th at 10:00 AM &nbsp;&nbsp;|&nbsp;&nbsp;
    </div>
  </div>
</div>
<script>
(function(){
  var el = document.getElementById('tickerText');
  if (!el) return;
  var pos = el.parentElement.offsetWidth;
  el.style.transform = 'translateX(' + pos + 'px)';
  function tick() {
    pos -= 1;
    if (pos < -(el.offsetWidth)) pos = el.parentElement.offsetWidth;
    el.style.transform = 'translateX(' + pos + 'px)';
    requestAnimationFrame(tick);
  }
  requestAnimationFrame(tick);
})();
</script>

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
<script src="assets/js/pages/programs.js?v=35"></script>
<script src="assets/js/pages/gallery.js?v=36"></script>
<script src="assets/js/pages/notices.js?v=44"></script>
<script src="assets/js/pages/contact.js?v=44"></script>
<script src="assets/js/pages/login.js"></script>
<script src="assets/js/pages/signup.js"></script>
<script src="assets/js/pages.js"></script>
<script src="assets/js/app.js?v=37"></script>
<script>
// Handle ?page= URL parameter
(function(){
  var params = new URLSearchParams(window.location.search);
  var pg = params.get('page');
  if (pg && typeof pages !== 'undefined' && pages[pg]) {
    window.onload = function(){ loadPage(pg); };
  }
})();
</script>

</body>
</html>
