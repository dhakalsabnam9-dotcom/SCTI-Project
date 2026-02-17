<?php
// admin/login.php
// Simple login page for admin panel using hard-coded credentials.

session_start();

// If already logged in, go to dashboard
if (!empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin_index.php');
    exit;
}

$message = '';

// Change these credentials as needed
$VALID_USERNAME = 'admin';
$VALID_PASSWORD = 'admin123'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $message = 'Please enter both username and password.';
    } elseif ($username === $VALID_USERNAME && $password === $VALID_PASSWORD) {
        // Successful login
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: admin_index.php');
        exit;
    } else {
        $message = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - SCTI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin.css">
    <style>
        /* Extra small styles just for the centered login box */
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f4f4f4;
        }
        .login-box {
            background: #ffffff;
            padding: 25px 20px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
            max-width: 360px;
            width: 100%;
        }
        .login-box h1 {
            font-size: 20px;
            text-align: center;
            margin-bottom: 15px;
        }
        .login-box .form-group {
            margin-bottom: 12px;
        }
        .login-box label {
            display: block;
            margin-bottom: 4px;
            font-size: 14px;
        }
        .login-box input[type="text"],
        .login-box input[type="password"] {
            width: 100%;
            padding: 8px;
            font-size: 14px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .login-box button {
            width: 100%;
            margin-top: 8px;
        }
        .login-note {
            font-size: 12px;
            color: #555;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-box">
        <h1>SCTI Admin Login</h1>

        <?php if ($message !== ''): ?>
            <div class="message error" style="margin-bottom:10px;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit" class="button">Login</button>
        </form>

        <p class="login-note">
            Default: <strong>admin / admin123</strong> (change in <code>login.php</code>).
        </p>
    </div>
</div>
</body>
</html>

