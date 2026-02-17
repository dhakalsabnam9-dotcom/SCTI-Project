<?php
require_once 'db.php';

// Fetch all notices ordered by published_at (latest first)
$sql = "SELECT id, title, content, published_at 
        FROM notices 
        ORDER BY published_at DESC, created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Notice Board - SCTI</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<style>
     * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
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
    width: 80px;
    /* adjust size if needed */
    height: 80px;
    border-radius: 50%;
    /* makes it circular */
    object-fit: cover;
    /* prevents distortion */
    border: 2px solid #fff;
    /* optional border */
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

  /* ===== RESPONSIVE ===== */
  @media (max-width: 768px) {

    .menu-toggle {
      display: block;
    }

    .menu {
      display: none;
      width: 100%;
    }

    .menu ul {
      flex-direction: column;
      background: #004080;
      text-align: center;
    }

    .menu ul li {
      padding: 10px;
      border-bottom: 1px solid #ccc;
    }

    .menu.show {
      display: block;
    }

  }

  .container {
    width: 90%;
   
    margin: auto;
  }

    /* ===== SECTION ===== */
    .section {
      padding: 40px 0;
    }

    .section h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #004080;
      
    }

    /* ===== NOTICE BOARD ===== */
    .notice-list {
      background: #ffffff;
      padding: 25px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .notice-list ul {
      list-style: none;
    }

    .notice-list ul li {
      padding: 12px 0;
      border-bottom: 1px solid #ddd;
    }

    .notice-list ul li:last-child {
      border-bottom: none;
    }

    .notice-title {
      font-weight: bold;
    }

    .notice-date {
      font-size: 12px;
      color: #666;
      display: block;
      margin-top: 4px;
    }

    /* ===== FOOTER ===== */
    .footer {
       background: #00264d;
      color: #fff;
      text-align: center;
      padding: 15px 0;
      margin-top: 40px;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
      .menu ul {
        flex-direction: column;
        background: #ffffff;
        position: absolute;
        top: 100%;
        right: 0;
        width: 200px;
        display: none;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
      }

      .menu ul li {
        margin: 0;
        border-bottom: 1px solid #eee;
      }

      .menu ul li a {
        display: block;
        padding: 12px;
      }

      .menu-toggle {
        display: block;
      }

      .menu.show ul {
        display: block;
      }
    }

    @media (max-width: 480px) {
      .logo img {
        width: 65px;
      }

      .section h2 {
        font-size: 22px;
      }

      .notice-list {
        padding: 18px;
      }

      .top-header {
        font-size: 12px;
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

<!-- ===== HEADER ===== -->
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
        <li><a href="About Us.php">About Us</a></li>
        <li><a href="Gallery.php">Gallery</a></li>
        <li><a href="Notice Board.php">Notice Board</a></li>
        <li><a href="Contact Us.php">Contact Us</a></li>
      </ul>
    </nav>

  </div>
</header>

<!-- ===== NOTICE BOARD ===== -->
<section class="section">
  <div class="container">
    <h2>Notice Board</h2>

    <div class="notice-list">
      <ul>
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <li>
              <span class="notice-title">
                <?php echo htmlspecialchars($row['title']); ?>
              </span>
              <span class="notice-date">
                <?php
                  $date = date('d M Y, h:i A', strtotime($row['published_at']));
                  echo htmlspecialchars($date);
                ?>
              </span>
              <div class="notice-content">
                <?php echo nl2br(htmlspecialchars($row['content'])); ?>
              </div>
            </li>
          <?php endwhile; ?>
        <?php else: ?>
          <li>No notices available at the moment.</li>
        <?php endif; ?>
      </ul>
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
