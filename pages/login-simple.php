<?php
session_start();
require_once('../includes/config.php');
$conn = getDBConnection();

$error = '';
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) || 
          (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
          isset($_POST['ajax']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $userType = trim($_POST['userType'] ?? '');

    if (empty($username) || empty($password) || empty($userType)) {
        $error = "All fields are required!";
    } else {
        $tableMap = ['student'=>'students','teacher'=>'teachers','admin'=>'admins'];
        $redirectMap = [
            'student' => '../dashboards/student-dashboard.php',
            'teacher' => '../dashboards/teacher-dashboard.php',
            'admin'   => '../dashboards/admin-dashboard.php'
        ];

        if (!isset($tableMap[$userType])) {
            $error = "Invalid user type!";
        } else {
            $table    = $tableMap[$userType];
            $redirect = $redirectMap[$userType];
            try {
                $stmt = $conn->prepare("SELECT * FROM $table WHERE username = :username LIMIT 1");
                $stmt->bindParam(':username', $username);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    $passwordOk = password_verify($password, $user['password']) || ($password === $user['password']);
                    if ($passwordOk) {
                        // Check account status
                        if (isset($user['status']) && $user['status'] !== 'active') {
                            $_SESSION['user_id']   = $user['id'];
                            $_SESSION['username']  = $user['username'];
                            $_SESSION['user_type'] = $userType;
                            $_SESSION['full_name'] = $user['full_name'];
                            $_SESSION['email']     = $user['email'];
                            $_SESSION['logged_in'] = true;
                            if ($isAjax) {
                                header('Content-Type: application/json');
                                echo json_encode(['success'=>true,'redirect'=>'../pages/account-inactive.php','userType'=>$userType,'inactive'=>true]);
                                exit();
                            }
                            header("Location: account-inactive.php"); exit();
                        }
                        $_SESSION['user_id']   = $user['id'];
                        $_SESSION['username']  = $user['username'];
                        $_SESSION['user_type'] = $userType;
                        $_SESSION['full_name'] = $user['full_name'];
                        $_SESSION['email']     = $user['email'];
                        $_SESSION['logged_in'] = true;

                        // Update last_login and updated_at
                        $updateResult = false;
                        $updateError  = '';
                        try {
                            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                            if ($mysqli->connect_error) {
                                $updateError = 'Connect error: ' . $mysqli->connect_error;
                            } else {
                                $uid = (int)$user['id'];
                                $sql = "UPDATE `$table` SET `last_login`=NOW(), `updated_at`=NOW() WHERE `id`=$uid";
                                $updateResult = $mysqli->query($sql);
                                $updateError  = $mysqli->error;
                                $mysqli->close();
                            }
                        } catch(Exception $ex) {
                            $updateError = $ex->getMessage();
                        }

                        // First-login check: if last_login was NULL before update, set session flag
                        $isFirstLogin = in_array($userType, ['student','teacher']) && empty($user['last_login']);
                        if ($isFirstLogin) {
                            $_SESSION['first_login'] = true;
                        }

                        if ($isAjax) {
                            header('Content-Type: application/json');
                            echo json_encode([
                                'success'       => true,
                                'redirect'      => $redirect,
                                'userType'      => $userType,
                                'first_login'   => $isFirstLogin,
                                'update_result' => $updateResult,
                                'update_error'  => $updateError,
                                'user_id'       => $user['id'],
                                'table'         => $table
                            ]);
                            exit();
                        }
                        header("Location: " . $redirect);
                        exit();
                    } else {
                        $error = "Invalid username or password!";
                    }
                } else {
                    $error = "Invalid username or password!";
                }
            } catch(PDOException $e) {
                $error = "Login error: " . $e->getMessage();
            }
        }
    }

    if ($isAjax && $error) {
        header('Content-Type: application/json');
        echo json_encode(['success'=>false,'message'=>$error]);
        exit();
    }
}
$conn = null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Sindhuli Community Technical Institute (SCTI)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- External CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
  
  <style>
    .alert {
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 8px;
      font-size: 14px;
      animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .alert-danger {
      background: linear-gradient(135deg, #f8d7da, #f5c6cb);
      color: #721c24;
      border-left: 4px solid #dc3545;
      box-shadow: 0 2px 10px rgba(220, 53, 69, 0.2);
    }
    
    .credentials-info {
      background: linear-gradient(135deg, #d1ecf1, #bee5eb);
      color: #0c5460;
      padding: 18px;
      margin-bottom: 20px;
      border-radius: 8px;
      font-size: 13px;
      border-left: 4px solid #17a2b8;
      box-shadow: 0 2px 10px rgba(23, 162, 184, 0.2);
      animation: fadeIn 0.5s ease-out;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    
    .credentials-info strong {
      display: block;
      margin-bottom: 10px;
      font-size: 14px;
    }
    
    .credentials-info p {
      line-height: 1.6;
      margin: 0;
    }
    
    .credentials-info ul {
      margin: 5px 0;
      padding-left: 20px;
    }
    
    /* Enhanced form styling */
    .login-box {
      animation: fadeInUp 0.5s ease-out;
    }
    
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .form-group {
      position: relative;
      margin-bottom: 25px;
    }
    
    .form-group input:focus,
    .form-group select:focus {
      border-color: #004080;
      box-shadow: 0 0 0 3px rgba(0, 64, 128, 0.1);
      transform: translateY(-2px);
    }
    
    .form-group label {
      font-weight: 600;
      color: #333;
      margin-bottom: 8px;
      display: block;
      transition: color 0.3s;
    }
    
    /* Password field with eye icon */
    .password-wrapper {
      position: relative;
    }
    
    .password-wrapper input {
      padding-right: 45px;
      transition: all 0.3s;
    }
    
    .password-toggle {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #666;
      font-size: 18px;
      transition: all 0.3s;
      z-index: 10;
    }
    
    .password-toggle:hover {
      color: #004080;
      transform: translateY(-50%) scale(1.1);
    }
    
    .password-toggle:active {
      transform: translateY(-50%) scale(0.95);
    }
    
    /* Enhanced button */
    .login-btn {
      position: relative;
      overflow: hidden;
      transition: all 0.3s;
    }
    
    .login-btn::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.2);
      transform: translate(-50%, -50%);
      transition: width 0.6s, height 0.6s;
    }
    
    .login-btn:hover::before {
      width: 300px;
      height: 300px;
    }
    
    .login-btn:active {
      transform: translateY(0);
      box-shadow: 0 2px 8px rgba(0, 64, 128, 0.3);
    }
    
    /* Input focus effects */
    .form-group input,
    .form-group select {
      transition: all 0.3s;
    }
    
    .form-group input:hover,
    .form-group select:hover {
      border-color: #0059b3;
    }
    
    /* Form options enhancement */
    .form-options {
      animation: fadeIn 0.5s ease-out 0.2s both;
    }
    
    .forgot-password {
      position: relative;
      transition: all 0.3s;
    }
    
    .forgot-password::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0;
      height: 2px;
      background: #004080;
      transition: width 0.3s;
    }
    
    .forgot-password:hover::after {
      width: 100%;
    }
    
    /* Login footer enhancement */
    .login-footer {
      animation: fadeIn 0.5s ease-out 0.3s both;
    }
    
    .login-footer a {
      position: relative;
      transition: all 0.3s;
    }
    
    .login-footer a::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0;
      height: 2px;
      background: #004080;
      transition: width 0.3s;
    }
    
    .login-footer a:hover::after {
      width: 100%;
    }
    
    /* Responsive enhancements */
    @media (max-width: 768px) {
      .login-container {
        padding: 0 15px;
      }
      
      .password-toggle {
        font-size: 16px;
      }
      
      .credentials-info {
        font-size: 12px;
      }
    }
  </style>
</head>

<body>

<!-- ===== TOP HEADER ===== -->
<div class="top-header">
  <marquee>
    Examination Notification | Genius 2025 | Enrollment Open 2025–26
  </marquee>
</div>

<!-- ===== HEADER & MENU ===== -->
<header class="header">
  <div class="container header-flex">

    <div class="logo">
      <img src="../assets/images/scti logo.jpeg" alt="SCTI Logo">
    </div>

    <div class="menu-toggle" id="menu-toggle">
      <i class="fa fa-bars"></i>
    </div>

    <nav class="menu" id="menu">
      <ul>
        <li><a href="../index.html">Home</a></li>
        <li><a href="../index.html#programs">Programs</a></li> 
        <li><a href="../index.html#gallery">Gallery</a></li>
        <li><a href="../index.html#notices">Notice Board</a></li>
        <li><a href="../index.html#contact">Contact Us</a></li>
        <li><a href="login-simple.php">Login</a></li>
      </ul>
    </nav>

  </div>
</header>

<!-- ===== LOGIN SECTION ===== -->
<section class="login-section">
  <div class="login-container">
    
    <div class="login-box">
      <div class="login-header">
        <i class="fa fa-user-circle"></i>
        <h2>Login to SCTI Portal</h2>
        <p>Access your student/staff account</p>
      </div>

      <form class="login-form" method="POST" action="login-simple.php">
        
        <div class="credentials-info">
          <strong><i class="fa fa-info-circle"></i> Login Information:</strong>
          <p>Use your username and password provided by the administration to login.</p>
        </div>
        
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger">
            <i class="fa fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
          </div>
        <?php endif; ?>
        
        <div class="form-group">
          <label for="userType">
            <i class="fa fa-users"></i> User Type
          </label>
          <select id="userType" name="userType" required>
            <option value="">Select User Type</option>
            <option value="student">Student</option>
            <option value="teacher">Teacher</option>
            <option value="admin">Administrator</option>
          </select>
        </div>

        <div class="form-group">
          <label for="username">
            <i class="fa fa-user"></i> Username
          </label>
          <input 
            type="text" 
            id="username" 
            name="username" 
            placeholder="Enter your username"
            value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
            required
          >
        </div>

        <div class="form-group">
          <label for="password">
            <i class="fa fa-lock"></i> Password
          </label>
          <div class="password-wrapper">
            <input 
              type="password" 
              id="password" 
              name="password" 
              placeholder="Enter your password"
              required
            >
            <i class="fa fa-eye password-toggle" id="togglePassword" onclick="togglePasswordVisibility()"></i>
          </div>
        </div>

        <div class="form-options">
          <label style="display:flex;align-items:center;gap:7px;cursor:pointer;font-size:14px;color:#555">
            <input type="checkbox" name="remember" id="rememberMe" style="width:15px;height:15px;accent-color:#004080"> Remember Me
          </label>
          <a href="#" class="forgot-password" onclick="openForgotModal();return false;">Forgot Password?</a>
        </div>

        <button type="submit" class="login-btn">
          <i class="fa fa-sign-in"></i> Login
        </button>

        <div class="login-footer">
          <p>Contact your administrator if you need access.</p>
        </div>

      </form>
    </div>

  </div>
</section>

<!-- ===== FOOTER ===== -->
<footer class="footer">
  <p>© 2025 Sindhuli Community Technical Institute (SCTI)</p>
</footer>

<!-- ===== FORGOT PASSWORD MODAL ===== -->
<div id="fpOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;align-items:center;justify-content:center;backdrop-filter:blur(3px)">
  <div style="background:white;border-radius:16px;width:100%;max-width:420px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,.3);overflow:hidden;animation:fpPop .35s cubic-bezier(.175,.885,.32,1.275)">
    <style>@keyframes fpPop{from{opacity:0;transform:scale(.85)}to{opacity:1;transform:scale(1)}}</style>

    <!-- Header -->
    <div style="background:linear-gradient(135deg,#004080,#0059b3);padding:22px 24px;color:white;display:flex;justify-content:space-between;align-items:center">
      <div style="display:flex;align-items:center;gap:10px">
        <div style="width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:18px"><i class="fa fa-key"></i></div>
        <div>
          <div style="font-size:16px;font-weight:700">Forgot Password</div>
          <div style="font-size:12px;opacity:.8">Reset your account password</div>
        </div>
      </div>
      <button onclick="closeForgotModal()" style="background:rgba(255,255,255,.15);border:none;color:white;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center">&times;</button>
    </div>

    <!-- Body -->
    <div style="padding:24px">

      <!-- Step indicator -->
      <div style="display:flex;align-items:center;gap:0;margin-bottom:22px">
        <div id="fpStep1Dot" style="width:28px;height:28px;border-radius:50%;background:#004080;color:white;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0">1</div>
        <div style="flex:1;height:3px;background:#e0e6ef"><div id="fpStepLine" style="height:100%;width:0;background:#004080;transition:.4s"></div></div>
        <div id="fpStep2Dot" style="width:28px;height:28px;border-radius:50%;background:#e0e6ef;color:#aaa;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0">2</div>
      </div>

      <!-- Alert -->
      <div id="fpAlert" style="display:none;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:14px;border-left:4px solid"></div>

      <!-- STEP 1: Verify identity -->
      <div id="fpStep1">
        <p style="font-size:13px;color:#666;margin-bottom:16px">Enter your username and full name to verify your identity.</p>
        <div style="margin-bottom:14px">
          <label style="display:block;font-size:12px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px"><i class="fa fa-users"></i> User Type</label>
          <select id="fpUserType" style="width:100%;padding:10px 13px;border:2px solid #e0e6ef;border-radius:9px;font-size:13px;font-family:inherit;outline:none" onfocus="this.style.borderColor='#004080'" onblur="this.style.borderColor='#e0e6ef'">
            <option value="">— Select —</option>
            <option value="student">Student</option>
            <option value="teacher">Teacher</option>
            <option value="admin">Administrator</option>
          </select>
        </div>
        <div style="margin-bottom:14px">
          <label style="display:block;font-size:12px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px"><i class="fa fa-user"></i> Username</label>
          <input type="text" id="fpUsername" placeholder="Enter your username" style="width:100%;padding:10px 13px;border:2px solid #e0e6ef;border-radius:9px;font-size:13px;font-family:inherit;outline:none" onfocus="this.style.borderColor='#004080'" onblur="this.style.borderColor='#e0e6ef'">
        </div>
        <div style="margin-bottom:18px">
          <label style="display:block;font-size:12px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px"><i class="fa fa-id-card"></i> Full Name</label>
          <input type="text" id="fpFullName" placeholder="Enter your full name exactly" style="width:100%;padding:10px 13px;border:2px solid #e0e6ef;border-radius:9px;font-size:13px;font-family:inherit;outline:none" onfocus="this.style.borderColor='#004080'" onblur="this.style.borderColor='#e0e6ef'">
        </div>
        <button onclick="fpVerify()" id="fpVerifyBtn" style="width:100%;padding:12px;background:linear-gradient(135deg,#004080,#0059b3);color:white;border:none;border-radius:9px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px">
          <i class="fa fa-arrow-right"></i> Verify Identity
        </button>
      </div>

      <!-- STEP 2: Set new password -->
      <div id="fpStep2" style="display:none">
        <div id="fpPhoneHint" style="background:#e8f4fd;border-left:4px solid #004080;border-radius:8px;padding:10px 14px;font-size:13px;color:#004080;margin-bottom:16px;display:flex;gap:8px;align-items:center">
          <i class="fa fa-check-circle"></i> <span>Identity verified. Set your new password below.</span>
        </div>
        <div style="margin-bottom:14px">
          <label style="display:block;font-size:12px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px"><i class="fa fa-lock"></i> New Password</label>
          <div style="position:relative">
            <input type="password" id="fpNewPass" placeholder="Min. 6 characters" style="width:100%;padding:10px 44px 10px 13px;border:2px solid #e0e6ef;border-radius:9px;font-size:13px;font-family:inherit;outline:none" onfocus="this.style.borderColor='#004080'" onblur="this.style.borderColor='#e0e6ef'" oninput="fpStrength(this.value)">
            <i class="fa fa-eye" onclick="fpToggle('fpNewPass',this)" style="position:absolute;right:13px;top:50%;transform:translateY(-50%);cursor:pointer;color:#aaa;font-size:14px"></i>
          </div>
          <div style="height:4px;background:#eee;border-radius:4px;margin-top:5px;overflow:hidden"><div id="fpStrBar" style="height:100%;width:0;border-radius:4px;transition:.3s"></div></div>
          <div id="fpStrLbl" style="font-size:11px;color:#999;margin-top:2px"></div>
        </div>
        <div style="margin-bottom:18px">
          <label style="display:block;font-size:12px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px"><i class="fa fa-lock"></i> Confirm Password</label>
          <div style="position:relative">
            <input type="password" id="fpConfPass" placeholder="Re-enter new password" style="width:100%;padding:10px 44px 10px 13px;border:2px solid #e0e6ef;border-radius:9px;font-size:13px;font-family:inherit;outline:none" onfocus="this.style.borderColor='#004080'" onblur="this.style.borderColor='#e0e6ef'">
            <i class="fa fa-eye" onclick="fpToggle('fpConfPass',this)" style="position:absolute;right:13px;top:50%;transform:translateY(-50%);cursor:pointer;color:#aaa;font-size:14px"></i>
          </div>
        </div>
        <div style="display:flex;gap:10px">
          <button onclick="fpBack()" style="flex:1;padding:12px;background:#f0f4f8;color:#555;border:none;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer"><i class="fa fa-arrow-left"></i> Back</button>
          <button onclick="fpReset()" id="fpResetBtn" style="flex:2;padding:12px;background:linear-gradient(135deg,#004080,#0059b3);color:white;border:none;border-radius:9px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px">
            <i class="fa fa-key"></i> Reset Password
          </button>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- ===== JS ===== -->
<script>
  document.getElementById("menu-toggle").onclick = function () {
    document.getElementById("menu").classList.toggle("show");
  };
  
  function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('togglePassword');
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      toggleIcon.classList.replace('fa-eye','fa-eye-slash');
    } else {
      passwordInput.type = 'password';
      toggleIcon.classList.replace('fa-eye-slash','fa-eye');
    }
  }

  // ── Forgot Password Modal ────────────────────────────────────────────────
  function openForgotModal(){
    document.getElementById('fpOverlay').style.display='flex';
    fpShowAlert('','');
    // Pre-fill user type from login form if selected
    var lt = document.getElementById('userType').value;
    if(lt) document.getElementById('fpUserType').value = lt;
  }
  function closeForgotModal(){
    document.getElementById('fpOverlay').style.display='none';
    fpReset_form();
  }
  // Close on backdrop click
  document.getElementById('fpOverlay').addEventListener('click',function(e){
    if(e.target===this) closeForgotModal();
  });

  function fpShowAlert(msg, type){
    var el=document.getElementById('fpAlert');
    if(!msg){el.style.display='none';return;}
    var styles={error:{bg:'#fde8e8',border:'#e74c3c',color:'#c0392b'},success:{bg:'#d4edda',border:'#28a745',color:'#155724'},info:{bg:'#e8f4fd',border:'#004080',color:'#004080'}};
    var s=styles[type]||styles.info;
    el.style.cssText='display:block;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:14px;border-left:4px solid '+s.border+';background:'+s.bg+';color:'+s.color;
    el.innerHTML=msg;
  }

  function fpVerify(){
    var ut=document.getElementById('fpUserType').value;
    var un=document.getElementById('fpUsername').value.trim();
    var fn=document.getElementById('fpFullName').value.trim();
    if(!ut||!un||!fn){fpShowAlert('<i class="fa fa-exclamation-circle"></i> All fields are required.','error');return;}
    var btn=document.getElementById('fpVerifyBtn');
    btn.disabled=true; btn.innerHTML='<i class="fa fa-spinner fa-spin"></i> Verifying...';
    fpShowAlert('','');
    var fd=new FormData();
    fd.append('step','verify'); fd.append('user_type',ut); fd.append('username',un); fd.append('full_name',fn);
    fetch('forgot-password.php',{method:'POST',body:fd})
      .then(function(r){return r.json();})
      .then(function(d){
        btn.disabled=false; btn.innerHTML='<i class="fa fa-arrow-right"></i> Verify Identity';
        if(d.success){
          document.getElementById('fpStep1').style.display='none';
          document.getElementById('fpStep2').style.display='block';
          document.getElementById('fpStep2Dot').style.background='#004080';
          document.getElementById('fpStep2Dot').style.color='white';
          document.getElementById('fpStepLine').style.width='100%';
          if(d.phone_hint){
            document.getElementById('fpPhoneHint').innerHTML='<i class="fa fa-check-circle"></i> <span>Identity verified. Registered phone: <strong>'+d.phone_hint+'</strong>. Set your new password below.</span>';
          }
        } else {
          fpShowAlert('<i class="fa fa-times-circle"></i> '+d.message,'error');
        }
      })
      .catch(function(){btn.disabled=false;btn.innerHTML='<i class="fa fa-arrow-right"></i> Verify Identity';fpShowAlert('<i class="fa fa-wifi"></i> Network error. Try again.','error');});
  }

  function fpBack(){
    document.getElementById('fpStep2').style.display='none';
    document.getElementById('fpStep1').style.display='block';
    document.getElementById('fpStep2Dot').style.background='#e0e6ef';
    document.getElementById('fpStep2Dot').style.color='#aaa';
    document.getElementById('fpStepLine').style.width='0';
    fpShowAlert('','');
  }

  function fpReset(){
    var np=document.getElementById('fpNewPass').value;
    var cp=document.getElementById('fpConfPass').value;
    fpShowAlert('','');
    if(np.length<6){fpShowAlert('<i class="fa fa-exclamation-circle"></i> Password must be at least 6 characters.','error');return;}
    if(np!==cp){fpShowAlert('<i class="fa fa-exclamation-circle"></i> Passwords do not match.','error');return;}
    var btn=document.getElementById('fpResetBtn');
    btn.disabled=true; btn.innerHTML='<i class="fa fa-spinner fa-spin"></i> Resetting...';
    var fd=new FormData();
    fd.append('step','reset'); fd.append('new_password',np); fd.append('confirm_password',cp);
    fetch('forgot-password.php',{method:'POST',body:fd})
      .then(function(r){return r.json();})
      .then(function(d){
        btn.disabled=false; btn.innerHTML='<i class="fa fa-key"></i> Reset Password';
        if(d.success){
          document.getElementById('fpStep2').innerHTML='<div style="text-align:center;padding:20px 0"><div style="width:64px;height:64px;border-radius:50%;background:#d4edda;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:28px;color:#28a745"><i class="fa fa-check"></i></div><h3 style="color:#155724;margin:0 0 8px">Password Reset!</h3><p style="color:#666;font-size:13px;margin-bottom:18px">Your password has been updated. You can now log in.</p><button onclick="closeForgotModal()" style="padding:10px 28px;background:linear-gradient(135deg,#004080,#0059b3);color:white;border:none;border-radius:9px;font-size:14px;font-weight:700;cursor:pointer"><i class="fa fa-sign-in"></i> Go to Login</button></div>';
        } else {
          fpShowAlert('<i class="fa fa-times-circle"></i> '+d.message,'error');
        }
      })
      .catch(function(){btn.disabled=false;btn.innerHTML='<i class="fa fa-key"></i> Reset Password';fpShowAlert('<i class="fa fa-wifi"></i> Network error. Try again.','error');});
  }

  function fpReset_form(){
    document.getElementById('fpStep1').style.display='block';
    document.getElementById('fpStep2').style.display='none';
    document.getElementById('fpStep2Dot').style.background='#e0e6ef';
    document.getElementById('fpStep2Dot').style.color='#aaa';
    document.getElementById('fpStepLine').style.width='0';
    document.getElementById('fpUsername').value='';
    document.getElementById('fpFullName').value='';
    document.getElementById('fpNewPass').value='';
    document.getElementById('fpConfPass').value='';
    fpShowAlert('','');
  }

  function fpToggle(id,icon){
    var inp=document.getElementById(id);
    inp.type=inp.type==='password'?'text':'password';
    icon.classList.toggle('fa-eye'); icon.classList.toggle('fa-eye-slash');
  }

  function fpStrength(v){
    var s=0;
    if(v.length>=6)s++;if(v.length>=10)s++;if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[^A-Za-z0-9]/.test(v))s++;
    var c=['#e74c3c','#e67e22','#f1c40f','#2ecc71','#27ae60'],l=['Very Weak','Weak','Fair','Strong','Very Strong'];
    document.getElementById('fpStrBar').style.width=(s*20)+'%';
    document.getElementById('fpStrBar').style.background=c[s-1]||'#eee';
    document.getElementById('fpStrLbl').textContent=v.length?(l[s-1]||''):'';
  }

  // Enter key support
  document.addEventListener('keydown',function(e){
    if(e.key==='Escape') closeForgotModal();
  });
</script>

</body>
</html>
