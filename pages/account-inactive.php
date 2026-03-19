<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login-simple.php'); exit();
}
$name     = $_SESSION['full_name'] ?? 'User';
$userType = $_SESSION['user_type'] ?? 'user';
$typeLabel = ['student'=>'Student','teacher'=>'Teacher','admin'=>'Administrator'][$userType] ?? ucfirst($userType);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Account Inactive | SCTI</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Segoe UI',sans-serif;background:linear-gradient(135deg,#0a1628 0%,#0d2347 50%,#0a1628 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
    .stars{position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;overflow:hidden;z-index:0}
    .star{position:absolute;background:#fff;border-radius:50%;animation:twinkle linear infinite}
    @keyframes twinkle{0%,100%{opacity:0}50%{opacity:1}}
    .card{position:relative;z-index:1;background:rgba(255,255,255,0.05);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,0.12);border-radius:24px;padding:50px 44px;max-width:520px;width:100%;text-align:center;box-shadow:0 25px 60px rgba(0,0,0,0.5)}
    .icon-wrap{width:100px;height:100px;border-radius:50%;background:linear-gradient(135deg,#ff6b6b,#ee5a24);display:flex;align-items:center;justify-content:center;margin:0 auto 28px;font-size:42px;color:#fff;box-shadow:0 8px 32px rgba(238,90,36,0.4);animation:pulse 2s ease-in-out infinite}
    @keyframes pulse{0%,100%{transform:scale(1);box-shadow:0 8px 32px rgba(238,90,36,0.4)}50%{transform:scale(1.06);box-shadow:0 12px 40px rgba(238,90,36,0.6)}}
    .badge{display:inline-flex;align-items:center;gap:6px;background:rgba(238,90,36,0.2);border:1px solid rgba(238,90,36,0.4);color:#ff8c69;font-size:12px;font-weight:700;padding:5px 14px;border-radius:20px;margin-bottom:20px;text-transform:uppercase;letter-spacing:1px}
    h1{font-size:28px;font-weight:800;color:#fff;margin-bottom:12px;line-height:1.2}
    .subtitle{font-size:15px;color:rgba(255,255,255,0.6);margin-bottom:32px;line-height:1.6}
    .user-info{background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.1);border-radius:14px;padding:16px 20px;margin-bottom:32px;display:flex;align-items:center;gap:14px;text-align:left}
    .avatar{width:46px;height:46px;border-radius:50%;background:linear-gradient(135deg,#004080,#0059b3);display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;color:#fff;flex-shrink:0}
    .user-name{font-size:15px;font-weight:700;color:#fff}
    .user-role{font-size:12px;color:rgba(255,255,255,0.5);margin-top:2px}
    .contact-box{background:rgba(0,64,128,0.25);border:1px solid rgba(0,100,200,0.3);border-radius:14px;padding:20px 24px;margin-bottom:32px;text-align:left}
    .contact-box h3{font-size:13px;font-weight:700;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:1px;margin-bottom:14px;display:flex;align-items:center;gap:7px}
    .contact-item{display:flex;align-items:center;gap:12px;padding:9px 0;border-bottom:1px solid rgba(255,255,255,0.06)}
    .contact-item:last-child{border-bottom:none;padding-bottom:0}
    .contact-item i{width:32px;height:32px;border-radius:8px;background:rgba(0,100,200,0.3);display:flex;align-items:center;justify-content:center;font-size:13px;color:#5b9bd5;flex-shrink:0}
    .contact-item span{font-size:13px;color:rgba(255,255,255,0.75)}
    .btn-logout{width:100%;padding:14px;background:linear-gradient(135deg,#ee5a24,#ff6b6b);color:#fff;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:9px;transition:.25s;text-decoration:none}
    .btn-logout:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(238,90,36,0.45)}
    .btn-logout:active{transform:translateY(0)}
    .footer-note{margin-top:20px;font-size:12px;color:rgba(255,255,255,0.3)}
  </style>
</head>
<body>

<div class="stars" id="stars"></div>

<div class="card">
  <div class="icon-wrap"><i class="fa fa-ban"></i></div>
  <div class="badge"><i class="fa fa-circle-xmark"></i> Account Inactive</div>
  <h1>Your Account Has Been Deactivated</h1>
  <p class="subtitle">Your account is currently inactive and cannot access the portal. Please contact the school administration to resolve this.</p>

  <div class="user-info">
    <div class="avatar"><?= strtoupper(substr($name, 0, 1)) ?></div>
    <div>
      <div class="user-name"><?= htmlspecialchars($name) ?></div>
      <div class="user-role"><?= $typeLabel ?> Account</div>
    </div>
  </div>

  <div class="contact-box">
    <h3><i class="fa fa-headset"></i> Contact Administration</h3>
    <div class="contact-item">
      <i class="fa fa-building"></i>
      <span>Sindhuli Community Technical Institute (SCTI)</span>
    </div>
    <div class="contact-item">
      <i class="fa fa-location-dot"></i>
      <span>Sindhuli, Bagmati Province, Nepal</span>
    </div>
    <div class="contact-item">
      <i class="fa fa-phone"></i>
      <span>+977-9841234567</span>
    </div>
    <div class="contact-item">
      <i class="fa fa-envelope"></i>
      <span>admin@scti.edu.np</span>
    </div>
  </div>

  <a href="../includes/logout.php" class="btn-logout">
    <i class="fa fa-right-from-bracket"></i> Logout
  </a>
  <p class="footer-note">© 2025 SCTI &mdash; All rights reserved</p>
</div>

<script>
// Generate stars
var stars = document.getElementById('stars');
for (var i = 0; i < 80; i++) {
  var s = document.createElement('div');
  s.className = 'star';
  var size = Math.random() * 2.5 + 0.5;
  s.style.cssText = 'width:'+size+'px;height:'+size+'px;top:'+Math.random()*100+'%;left:'+Math.random()*100+'%;animation-duration:'+(Math.random()*4+2)+'s;animation-delay:'+(Math.random()*4)+'s';
  stars.appendChild(s);
}
</script>
</body>
</html>
