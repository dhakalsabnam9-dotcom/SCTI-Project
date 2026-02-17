<?php
require_once 'db.php';

// Fetch latest notices for home page (limit to 4)
$noticeSql = "SELECT title, published_at FROM notices 
              ORDER BY published_at DESC, created_at DESC 
              LIMIT 4";
$noticeResult = $conn->query($noticeSql);

// Fetch latest events for home page (limit to 4)
$eventSql = "SELECT title, event_date FROM events 
             ORDER BY event_date DESC, created_at DESC 
             LIMIT 4";
$eventResult = $conn->query($eventSql);

// Fetch notices for marquee
$marqueeSql = "SELECT title FROM notices 
               ORDER BY published_at DESC, created_at DESC 
               LIMIT 5";
$marqueeResult = $conn->query($marqueeSql);
$marqueeNotices = [];
if ($marqueeResult && $marqueeResult->num_rows > 0) {
    while ($row = $marqueeResult->fetch_assoc()) {
        $marqueeNotices[] = htmlspecialchars($row['title']);
    }
}
if (empty($marqueeNotices)) {
    $marqueeNotices = ['Welcome to SCTI - Sindhuli Community Technical Institute'];
}
$marqueeText = implode(' | ', $marqueeNotices);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sindhuli Community Technical Institute (SCTI)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

  <!-- External CSS -->
  <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ===== TOP HEADER ===== -->
<div class="top-header">
  <marquee>
    <?php echo $marqueeText; ?>
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
        <li><a href="index.php">Home</a></li>
        <li><a href="Programs.php">Programs</a></li> 
        <li><a href="Gallery.php">Gallery</a></li>
        <li><a href="Notice Board.php">Notice Board</a></li>
        <li><a href="Contact Us.php">Contact Us</a></li>
      </ul>
    </nav>

  </div>
</header>

<!-- ===== BANNER ===== -->
<div class="banner">
  <img src="banner.png" alt="SCTI Banner">
</div>

<!-- ===== NOTICE & EVENTS ===== -->
<section class="section">
<section class="updates">
  <div class="update-card notice">
    <h3><i class="fa fa-bullhorn"></i> Notice Board</h3>
    <ul>
      <?php if ($noticeResult && $noticeResult->num_rows > 0): ?>
        <?php while ($row = $noticeResult->fetch_assoc()): ?>
          <li>
            <span class="date"><?php echo date('M d', strtotime($row['published_at'])); ?></span>
            <?php echo htmlspecialchars($row['title']); ?>
          </li>
        <?php endwhile; ?>
      <?php else: ?>
        <li>No notices available at the moment.</li>
      <?php endif; ?>
    </ul>
  </div>

  <div class="update-card events">
    <h3><i class="fa fa-calendar"></i> Recent Events</h3>
    <ul>
      <?php if ($eventResult && $eventResult->num_rows > 0): ?>
        <?php while ($row = $eventResult->fetch_assoc()): ?>
          <li>
            <span class="date"><?php echo date('M d', strtotime($row['event_date'])); ?></span>
            <?php echo htmlspecialchars($row['title']); ?>
          </li>
        <?php endwhile; ?>
      <?php else: ?>
        <li>No events available at the moment.</li>
      <?php endif; ?>
    </ul>
  </div>
</section>

</section>

<!-- ===== CAMPUS INFO ===== -->
<section class="section bg-grey">
  <div class="container campus-flex">

    <div class="campus-img">
      <img src="work.jpg" alt="Campus Image">
    </div>

    <div class="campus-text">
      <h2>Campus Information</h2>
      <p>
        Sindhuli Community Technical Institute (SCTI) was established in 2014 AD as a
        nonprofit community-based technical institution supported by DCC Sindhuli,
        Kamalamai Municipality, and CTEVT. The institute provides quality technical
        education in engineering, agriculture, and health with a strong focus on
        practical skills and community needs.
      </p>
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

<!-- ===== TEACHERS ===== -->
<section class="section bg-grey">
  <div class="container">
    <h2>Meet Our Teachers</h2>

    <div class="teacher-grid">
      <div class="teacher">
        <img src="tej.png">
        <h4>Tej Bikram Thapa</h4>
        <p>Chairman</p>
      </div>

      <div class="teacher">
        <img src="santosh.png">
        <h4>Santosh Sapkota</h4>
        <p>Administrative</p>
      </div>

      <div class="teacher">
        <img src="bibek sir.jpg">
        <h4>Bibek Bhandari</h4>
        <p>Invigilator</p>
      </div>
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
