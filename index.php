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
  <link rel="stylesheet" href="assets/css/style.css?v=36">
  <style>
    .header {
      background: #004080 !important;
      position: sticky !important;
      top: 36px !important;
      z-index: 1000 !important;
      box-shadow: 0 2px 10px rgba(0,0,0,0.3) !important;
    }
    .menu ul li a { color: white !important; }
    .top-header {
      background: #c0392b !important;
      display: flex !important;
      height: 36px !important;
      overflow: hidden !important;
      position: sticky !important;
      top: 0 !important;
      z-index: 1001 !important;
    }
    .ticker-label {
      background: #922b21 !important;
      color: white !important;
      padding: 0 14px !important;
      height: 100% !important;
      display: flex !important;
      align-items: center !important;
      gap: 6px !important;
      font-size: 11px !important;
      font-weight: 700 !important;
      text-transform: uppercase !important;
      white-space: nowrap !important;
      flex-shrink: 0 !important;
    }
    .ticker-outer {
      flex: 1 !important;
      overflow: hidden !important;
      height: 100% !important;
      display: flex !important;
      align-items: center !important;
      background: #c0392b !important;
      position: relative !important;
    }
    .ticker-track {
      white-space: nowrap !important;
      color: #ffffff !important;
      font-size: 13px !important;
      position: absolute !important;
      will-change: transform !important;
    }
  </style>
</head>

<body>

<!-- ===== TOP HEADER ===== -->
<div class="top-header">
  <div class="ticker-label"><i class="fa fa-bullhorn"></i> NOTICE</div>
  <div class="ticker-outer" id="tickerOuter">
    <div class="ticker-track" id="tickerTrack">
      📢 Examination form submission deadline is 25th Poush 2081 &nbsp;&nbsp;|&nbsp;&nbsp; 🎓 Annual Sports Day on 1st Magh 2081 — All students must participate &nbsp;&nbsp;|&nbsp;&nbsp; 📝 Enrollment for new batch 2025–26 is now open &nbsp;&nbsp;|&nbsp;&nbsp; 🏫 College closed on 15th Poush for national holiday &nbsp;&nbsp;|&nbsp;&nbsp; 📋 Result of 1st semester internal exam published &nbsp;&nbsp;|&nbsp;&nbsp; 🔔 Library books must be returned before Magh 5th &nbsp;&nbsp;|&nbsp;&nbsp; 🎉 Congratulations to all distinction holders &nbsp;&nbsp;|&nbsp;&nbsp; 📌 Parent-teacher meeting on Magh 10th at 10:00 AM &nbsp;&nbsp;&nbsp;&nbsp;
    </div>
  </div>
</div>
<script>
(function(){
  var outer = document.getElementById('tickerOuter');
  var track = document.getElementById('tickerTrack');
  var pos = outer.offsetWidth;
  track.style.transform = 'translateX(' + pos + 'px)';
  function tick(){
    pos -= 1;
    if(pos < -(track.offsetWidth)) pos = outer.offsetWidth;
    track.style.transform = 'translateX(' + pos + 'px)';
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
      </ul>
    </nav>

  </div>
</header>

<!-- ===== DYNAMIC CONTENT AREA ===== -->
<div id="content-area">
  <!-- ===== BANNER ===== -->
  <div class="banner">
    <img src="assets/images/banner.png" alt="SCTI Banner">
  </div>

  <!-- ===== NOTICE & EVENTS ===== -->
  <section class="section">
    <section class="updates">
      <div class="update-card notice">
        <h3><i class="fa fa-bullhorn"></i> Notice Board</h3>
        <ul>
          <li class="update-item" onclick="loadPage('notices')">
            <span class="date">2025/26</span>
            <span class="item-text">Admission Open</span>
            <i class="fa fa-chevron-right item-arrow"></i>
          </li>
          <li class="update-item" onclick="loadPage('notices')">
            <span class="date">Nov 10</span>
            <span class="item-text">Orientation Program</span>
            <i class="fa fa-chevron-right item-arrow"></i>
          </li>
          <li class="update-item" onclick="loadPage('notices')">
            <span class="date">Nov 12</span>
            <span class="item-text">Classes Begin</span>
            <i class="fa fa-chevron-right item-arrow"></i>
          </li>
        </ul>
        <div class="card-footer-link" onclick="loadPage('notices')">
          <span>View All Notices</span> <i class="fa fa-arrow-right"></i>
        </div>
      </div>
      <div class="update-card events">
        <h3><i class="fa fa-calendar-alt"></i> Recent Events</h3>
        <ul>
          <li class="update-item" onclick="loadPage('notices')">
            <span class="date">Sept 14</span>
            <span class="item-text">AI Workshop</span>
            <i class="fa fa-chevron-right item-arrow"></i>
          </li>
          <li class="update-item" onclick="loadPage('notices')">
            <span class="date">Aug 30</span>
            <span class="item-text">Sports Week</span>
            <i class="fa fa-chevron-right item-arrow"></i>
          </li>
        </ul>
        <div class="card-footer-link" onclick="loadPage('notices')">
          <span>View All Events</span> <i class="fa fa-arrow-right"></i>
        </div>
      </div>
    </section>
  </section>

  <!-- ===== CAMPUS INFO ===== -->
  <section class="section bg-grey">
    <div class="container campus-flex">
      <div class="campus-img">
        <img src="assets/images/work.jpg" alt="Campus Image">
      </div>
      <div class="campus-text">
        <h2>Campus Information</h2>
        <p>Sindhuli Community Technical Institute (SCTI) was established in 2014 AD as a nonprofit community-based technical institution supported by DCC Sindhuli, Kamalamai Municipality, and CTEVT.</p>
      </div>
    </div>
  </section>

  <!-- ===== COURSES ===== -->
  <section class="section">
    <div class="container">
      <h2>Our Popular Courses</h2>
      <div class="course-grid">
        <div class="course">B.Tech Ed in IT</div>
        <div class="course">B.Tech Ed in Civil</div>
        <div class="course">Diploma in Civil Engineering</div>
        <div class="course">Diploma in Animal Science</div>
      </div>
    </div>
  </section>
</div>

<!-- ===== FOOTER ===== -->
<footer class="footer">
  <p>© 2025 Sindhuli Community Technical Institute (SCTI)</p>
</footer>

<!-- ===== JAVASCRIPT ===== -->
<script src="assets/js/menu.js"></script>
<script src="assets/js/pages/home.js"></script>
<script src="assets/js/pages/programs.js"></script>
<script src="assets/js/pages/gallery.js"></script>
<script src="assets/js/pages/notices.js"></script>
<script src="assets/js/pages/contact.js"></script>
<script src="assets/js/pages/login.js"></script>
<script src="assets/js/pages.js"></script>
<script src="assets/js/app.js"></script>
<script>
// Reload content area on nav click since home is pre-rendered
document.addEventListener('DOMContentLoaded', function(){
  // Nav links already work via loadPage()
  // Re-render home content via JS so dynamic switching works
  if(typeof loadPage === 'function') loadPage('home');
});
</script>

</body>
</html>
