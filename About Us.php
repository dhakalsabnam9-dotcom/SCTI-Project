<?php
require_once 'db.php';
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
  <title>About Us - SCTI</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, Helvetica, sans-serif;
    }

    body {
      line-height: 1.6;
      background: #f5f5f5;
      color: #333;
    }

    .container {
      width: 90%;
      max-width: 1200px;
      margin: auto;
    }

    .top-header {
     color:white;
      background: #00264d;
      padding: 6px 0;
      font-size: 14px;
    }

    .header {
       background: #004080;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      position: relative;
    }

    .header-flex {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 0;
    }

    .logo img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #fff;
    }

    .menu ul {
      list-style: none;
      display: flex;
    }

    .menu ul li {
      margin-left: 25px;
    }

    .menu ul li a {
      text-decoration: none;
      color: #ffffff;
      font-weight: bold;
      transition: 0.3s;
    }

    .menu ul li a:hover {
      color: #8b0000;
    }

    .menu-toggle {
      display: none;
      font-size: 24px;
      cursor: pointer;
      color: white;
    }

    .section {
      padding: 40px 0;
    }

    .section h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #004080;
    }

    .main-banner{
      width:100%;
      height:400px;
      object-fit:cover;
    }

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
        color: #333;
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
        height: 65px;
      }

      .section h2 {
        font-size: 22px;
      }

      .top-header {
        font-size: 12px;
      }
    }
</style>
</head>

<body>

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

<img src="banner.png" class="main-banner" alt="SCTI Banner">

<script>
  document.getElementById("menu-toggle").onclick = function () {
    document.getElementById("menu").classList.toggle("show");
  };
</script>

</body>
</html>
