<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../pages/login-simple.php'); exit();
}
$userType = $_SESSION['user_type'] ?? '';
if (!in_array($userType, ['teacher', 'student'])) {
    header('Location: ../dashboards/'.$userType.'-dashboard.php'); exit();
}

require_once '../includes/config.php';

// If they already have a last_login (not first login), redirect to dashboard
try {
    $db = getDBConnection();
    $table = ($userType === 'teacher') ? 'teachers' : 'students';
    $chk = $db->prepare("SELECT last_login FROM $table WHERE id=? LIMIT 1");
    $chk->execute([$_SESSION['user_id']]);
    $row = $chk->fetch();
    if ($row && !empty($row['last_login'])) {
        // Not first login — redirect to dashboard
        header('Location: ../dashboards/'.$userType.'-dashboard.php'); exit();
    }
} catch(Exception $e) {}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPass     = trim($_POST['new_password'] ?? '');
    $confirmPass = trim($_POST['confirm_password'] ?? '');

    if (empty($newPass) || empty($confirmPass)) {
        $error = 'Both fields are required.';
    } elseif (strlen($newPass) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($newPass !== $confirmPass) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $db    = getDBConnection();
            $table = ($userType === 'teacher') ? 'teachers' : 'students';
            $hashed = password_hash($newPass, PASSWORD_DEFAULT);
            // Update password AND set last_login so this page won't show again
            $stmt = $db->prepare("UPDATE $table SET password=?, last_login=NOW(), updated_at=NOW() WHERE id=?");
            $stmt->execute([$hashed, $_SESSION['user_id']]);
            $success = 'Password changed successfully! Redirecting...';
        } catch(Exception $e) {
            $error = 'Server error. Please try again.';
        }
    }
}

$dashboardUrl = '../dashboards/'.$userType.'-dashboard.php';
$accentColor  = ($userType === 'teacher') ? '#28a745' : '#004080';
$accentGrad   = ($userType === 'teacher')
    ? 'linear-gradient(135deg,#28a745,#20c997)'
    : 'linear-gradient(135deg,#004080,#0059b3)';
$icon         = ($userType === 'teacher') ? 'fa-chalkboard-teacher' : 'fa-user-graduate';
$label        = ($userType === 'teacher') ? 'Teacher' : 'Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Set Your Password | SCTI</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Segoe UI',sans-serif;background:#f0f4f8;min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center}
    .wrap{width:100%;max-width:460px;padding:20px}
    .card{background:white;border-radius:18px;box-shadow:0 8px 40px rgba(0,0,0,.13);overflow:hidden;animation:fadeUp .45s ease}
    @keyframes fadeUp{from{opacity:0;transform:translateY(28px)}to{opacity:1;transform:translateY(0)}}
    .card-top{background:<?=$accentGrad?>;padding:32px 30px 28px;text-align:center;color:white}
    .card-top .big-icon{width:72px;height:72px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:30px;margin:0 auto 14px}
    .card-top h2{font-size:22px;margin-bottom:6px}
    .card-top p{font-size:13px;opacity:.88;line-height:1.5}
    .card-body{padding:28px 30px}
    .notice-box{background:#fff8e1;border-left:4px solid #ffc107;border-radius:8px;padding:12px 14px;font-size:13px;color:#7a5c00;margin-bottom:22px;display:flex;align-items:flex-start;gap:10px}
    .notice-box i{margin-top:2px;flex-shrink:0}
    .form-group{margin-bottom:18px}
    .form-group label{display:block;font-size:13px;font-weight:600;color:#444;margin-bottom:7px}
    .pw-wrap{position:relative}
    .pw-wrap input{width:100%;padding:11px 44px 11px 14px;border:2px solid #e0e0e0;border-radius:10px;font-size:14px;font-family:inherit;transition:.2s;outline:none}
    .pw-wrap input:focus{border-color:<?=$accentColor?>}
    .pw-wrap .eye{position:absolute;right:13px;top:50%;transform:translateY(-50%);cursor:pointer;color:#aaa;font-size:16px;transition:.2s}
    .pw-wrap .eye:hover{color:<?=$accentColor?>}
    .strength-bar{height:4px;border-radius:4px;background:#eee;margin-top:7px;overflow:hidden}
    .strength-fill{height:100%;border-radius:4px;width:0;transition:width .3s,background .3s}
    .strength-label{font-size:11px;color:#999;margin-top:4px}
    .btn{width:100%;padding:13px;background:<?=$accentGrad?>;color:white;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:.25s;margin-top:6px}
    .btn:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,.2)}
    .alert{padding:11px 14px;border-radius:9px;font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:9px}
    .alert-danger{background:#fde8e8;color:#c0392b;border-left:4px solid #e74c3c}
    .alert-success{background:#e8f8ee;color:#1a7a4a;border-left:4px solid #28a745}
    .footer-note{text-align:center;font-size:12px;color:#aaa;margin-top:18px}
  </style>
</head>
<body>
<div class="wrap">
  <div class="card">
    <div class="card-top">
      <div class="big-icon"><i class="fa <?=$icon?>"></i></div>
      <h2>Welcome, <?=htmlspecialchars($_SESSION['full_name'] ?? $label)?>!</h2>
      <p>This is your first login. Please set a new password to continue.</p>
    </div>
    <div class="card-body">
      <div class="notice-box">
        <i class="fa fa-shield-alt"></i>
        <span>For your security, you must change your password before accessing the portal. Choose something strong and memorable.</span>
      </div>

      <?php if ($error): ?>
      <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?=htmlspecialchars($error)?></div>
      <?php endif; ?>
      <?php if ($success): ?>
      <div class="alert alert-success" id="successMsg"><i class="fa fa-check-circle"></i> <?=htmlspecialchars($success)?></div>
      <?php endif; ?>

      <?php if (!$success): ?>
      <form method="POST">
        <div class="form-group">
          <label><i class="fa fa-lock"></i> New Password</label>
          <div class="pw-wrap">
            <input type="password" name="new_password" id="newPass" placeholder="Enter new password" required autocomplete="new-password">
            <i class="fa fa-eye eye" onclick="togglePw('newPass',this)"></i>
          </div>
          <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
          <div class="strength-label" id="strengthLabel"></div>
        </div>
        <div class="form-group">
          <label><i class="fa fa-lock"></i> Confirm Password</label>
          <div class="pw-wrap">
            <input type="password" name="confirm_password" id="confPass" placeholder="Re-enter new password" required autocomplete="new-password">
            <i class="fa fa-eye eye" onclick="togglePw('confPass',this)"></i>
          </div>
        </div>
        <button type="submit" class="btn"><i class="fa fa-key"></i> Set Password & Continue</button>
      </form>
      <?php endif; ?>
    </div>
  </div>
  <div class="footer-note">SCTI &mdash; <?=$label?> Portal &copy; 2025</div>
</div>

<script>
function togglePw(id, icon) {
  var inp = document.getElementById(id);
  if (inp.type === 'password') {
    inp.type = 'text';
    icon.classList.replace('fa-eye','fa-eye-slash');
  } else {
    inp.type = 'password';
    icon.classList.replace('fa-eye-slash','fa-eye');
  }
}

var newPass = document.getElementById('newPass');
if (newPass) {
  newPass.addEventListener('input', function() {
    var v = this.value, score = 0;
    if (v.length >= 6) score++;
    if (v.length >= 10) score++;
    if (/[A-Z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;
    var fill = document.getElementById('strengthFill');
    var lbl  = document.getElementById('strengthLabel');
    var colors = ['#e74c3c','#e67e22','#f1c40f','#2ecc71','#27ae60'];
    var labels = ['Very Weak','Weak','Fair','Strong','Very Strong'];
    fill.style.width = (score * 20) + '%';
    fill.style.background = colors[score-1] || '#eee';
    lbl.textContent = v.length ? labels[score-1] || '' : '';
  });
}

// Auto-redirect after success
<?php if ($success): ?>
setTimeout(function(){ window.location.href = '<?=$dashboardUrl?>'; }, 1800);
<?php endif; ?>
</script>
</body>
</html>
