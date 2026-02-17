<?php
require_once 'db.php';

// Fetch all gallery items
$sql = "SELECT id, title, filename, created_at 
        FROM gallery 
        ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - SCTI Gallery</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<style>
    
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: Arial, sans-serif;
}

.container {
  width: 90%;
  margin: auto;
}

.section {
  padding: 40px 0;
}

.bg-grey {
  background: #f2f2f2;
}

/* ===== TOP HEADER ===== */
.top-header {
  background: #00264d;
  color: white;
  padding: 8px;
}

/* ===== HEADER ===== */
.header {
  background: #004080;
}

.header-flex {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.logo img {
  width: 120px;
}
/* ===== LOGO CIRCLE FIX ===== */
.logo img {
    width: 80px;              /* adjust size if needed */
    height: 80px;
    border-radius: 50%;       /* makes it circular */
    object-fit: cover;        /* prevents distortion */
    border: 2px solid #fff;   /* optional border */
}

.menu ul {
  list-style: none;
  display: flex;
}

.menu ul li {
  margin-left: 20px;
}

.menu ul li a {
  color: white;
  text-decoration: none;
  font-weight: bold;
}

.menu-toggle {
  display: none;
  font-size: 26px;
  color: white;
  cursor: pointer;
}
   
/* ===== GALLERY SECTION ===== */
.gallery {
  padding: 60px 20px;
  background: #f8f9fa;
  text-align: center;
}

/* ===== CENTER H2 ===== */
.gallery h2 {
  font-size: 32px;
  margin-bottom: 40px;
  color: #222;
  display: inline-block;
  position: relative;
  text-align: center;
}

/* Optional underline design */
.gallery h2::after {
  content: "";
  width: 80px;
  height: 4px;
  display: block;
  margin: 10px auto 0;
  border-radius: 5px;
}

/* ===== GRID LAYOUT ===== */
.gallery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

/* ===== GALLERY ITEM ===== */
.gallery-item {
  position: relative;
  overflow: hidden;
  border-radius: 12px;
  box-shadow: 0 6px 15px rgba(0,0,0,0.1);
  background: #fff;
}

.gallery-item img {
  width: 100%;
  height: 220px;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.gallery-item:hover img {
  transform: scale(1.1);
}

/* ===== GALLERY INFO ===== */
.gallery-info {
  padding: 15px;
  background: #fff;
}

.gallery-info h3 {
  font-size: 18px;
  color: #333;
  margin-bottom: 8px;
  font-weight: 600;
}

.gallery-info .gallery-date {
  font-size: 14px;
  color: #666;
  display: flex;
  align-items: center;
  gap: 5px;
}

.gallery-info .gallery-date i {
  color: #004080;
}

/* ===== OVERLAY ===== */
.overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.55);
  color: #fff;
  font-size: 20px;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.4s ease;
}

.gallery-item:hover .overlay {
  opacity: 1;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
  .gallery h2 {
    font-size: 26px;
    margin-bottom: 30px;
  }

  .gallery-item img {
    height: 200px;
  }
}

@media (max-width: 480px) {
  .gallery h2 {
    font-size: 22px;
    margin-bottom: 20px;
  }

  .gallery-item img {
    height: 170px;
  }
}
   
</style>
</head>
<body>

<?php
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

<!-- Gallery Section -->
<section class="gallery">
  <h2>Our Gallery</h2>

  <div class="gallery-grid">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="gallery-item">
          <img src="<?php echo 'uploads/' . htmlspecialchars($row['filename']); ?>" 
               alt="<?php echo htmlspecialchars($row['title']); ?>">
          <div class="overlay">
            <?php echo htmlspecialchars($row['title']); ?>
          </div>
          <div class="gallery-info">
            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
            <div class="gallery-date">
              <i class="fa fa-calendar"></i>
              <span><?php echo date('d M Y', strtotime($row['created_at'])); ?></span>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No gallery images available yet.</p>
    <?php endif; ?>
  </div>
</section>

<!-- ===== JavaScript for Hamburger Menu ===== -->
<script>
  const menuToggle = document.getElementById('menu-toggle');
  const menu = document.getElementById('menu');

  menuToggle.addEventListener('click', () => {
      menu.classList.toggle('show');
  });
</script>

</body>
</html>
