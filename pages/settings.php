<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php'); exit();
}
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Settings | SCTI Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; }
    .container { max-width: 1100px; margin: 0 auto; padding: 20px; }
    .page-header {
      background: linear-gradient(135deg, #004080 0%, #0059b3 100%);
      color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;
      box-shadow: 0 4px 15px rgba(0,64,128,0.2);
    }
    .page-header h1 { margin: 0 0 8px 0; font-size: 28px; }
    .breadcrumb a { color: white; text-decoration: none; font-size: 14px; }
    .settings-layout { display: grid; grid-template-columns: 220px 1fr; gap: 25px; }
    .settings-nav { background: white; border-radius: 10px; padding: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); height: fit-content; }
    .nav-item {
      display: flex; align-items: center; gap: 10px;
      padding: 12px 15px; border-radius: 8px; cursor: pointer;
      color: #555; font-size: 14px; transition: all 0.2s; margin-bottom: 4px;
    }
    .nav-item:hover, .nav-item.active { background: #e8f0fe; color: #004080; font-weight: 600; }
    .nav-item i { width: 18px; color: #004080; }
    .settings-content { display: flex; flex-direction: column; gap: 20px; }
    .settings-card { background: white; border-radius: 10px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .settings-card h3 { color: #004080; margin-bottom: 20px; font-size: 18px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; }
    .form-group { margin-bottom: 18px; }
    .form-group label { display: block; font-size: 13px; color: #555; margin-bottom: 6px; font-weight: 600; }
    .form-control {
      width: 100%; padding: 10px 14px; border: 2px solid #dee2e6;
      border-radius: 6px; font-size: 14px; transition: border-color 0.2s;
    }
    .form-control:focus { outline: none; border-color: #004080; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .btn-save {
      background: linear-gradient(135deg, #004080, #0059b3);
      color: white; padding: 12px 28px; border: none; border-radius: 6px;
      cursor: pointer; font-size: 14px; transition: all 0.3s;
      display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,64,128,0.3); }
    .toggle-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f0f0f0; }
    .toggle-row:last-child { border-bottom: none; }
    .toggle-label { font-size: 14px; color: #333; }
    .toggle-desc { font-size: 12px; color: #999; margin-top: 2px; }
    .toggle {
      position: relative; width: 48px; height: 26px;
      background: #dee2e6; border-radius: 13px; cursor: pointer;
      transition: background 0.3s;
    }
    .toggle.on { background: #004080; }
    .toggle::after {
      content: ''; position: absolute; width: 20px; height: 20px;
      background: white; border-radius: 50%; top: 3px; left: 3px;
      transition: left 0.3s; box-shadow: 0 1px 4px rgba(0,0,0,0.2);
    }
    .toggle.on::after { left: 25px; }
    .alert-success { background: #d4edda; color: #155724; padding: 12px 16px; border-radius: 6px; margin-bottom: 15px; display: none; }
  </style>
</head>
<body>
<div class="top-header"><marquee>Settings - Configure system preferences and account settings</marquee></div>
<div class="container">
  <div class="page-header">
    <h1><i class="fa fa-cog"></i> Settings</h1>
    <div class="breadcrumb"><a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Settings</div>
  </div>

  <div class="settings-layout">
    <div class="settings-nav">
      <div class="nav-item active"><i class="fa fa-school"></i> General</div>
      <div class="nav-item"><i class="fa fa-user-shield"></i> Account</div>
      <div class="nav-item"><i class="fa fa-lock"></i> Security</div>
      <div class="nav-item"><i class="fa fa-bell"></i> Notifications</div>
      <div class="nav-item"><i class="fa fa-database"></i> Database</div>
    </div>

    <div class="settings-content">
      <div class="settings-card">
        <h3><i class="fa fa-school"></i> General Settings</h3>
        <div id="saveAlert" class="alert-success"><i class="fa fa-check-circle"></i> Settings saved successfully!</div>
        <div class="form-row">
          <div class="form-group">
            <label>Institution Name</label>
            <input type="text" class="form-control" value="Sindhuli Community Technical Institute (SCTI)">
          </div>
          <div class="form-group">
            <label>Short Name</label>
            <input type="text" class="form-control" value="SCTI">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Contact Email</label>
            <input type="email" class="form-control" value="admin@scti.edu.np">
          </div>
          <div class="form-group">
            <label>Contact Phone</label>
            <input type="text" class="form-control" value="+977-056-XXXXXX">
          </div>
        </div>
        <div class="form-group">
          <label>Address</label>
          <input type="text" class="form-control" value="Sindhuli, Bagmati Province, Nepal">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Academic Year</label>
            <input type="text" class="form-control" value="2025/26">
          </div>
          <div class="form-group">
            <label>Timezone</label>
            <select class="form-control"><option selected>Asia/Kathmandu (NPT +5:45)</option></select>
          </div>
        </div>
        <button class="btn-save" onclick="document.getElementById('saveAlert').style.display='block';setTimeout(()=>document.getElementById('saveAlert').style.display='none',3000);">
          <i class="fa fa-save"></i> Save Changes
        </button>
      </div>

      <div class="settings-card">
        <h3><i class="fa fa-user-shield"></i> Account Settings</h3>
        <div class="form-row">
          <div class="form-group">
            <label>Admin Username</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($username); ?>">
          </div>
          <div class="form-group">
            <label>Admin Email</label>
            <input type="email" class="form-control" value="admin@scti.edu.np">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>New Password</label>
            <input type="password" class="form-control" placeholder="Leave blank to keep current">
          </div>
          <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" class="form-control" placeholder="Confirm new password">
          </div>
        </div>
        <button class="btn-save" onclick="alert('Account update feature coming soon!');"><i class="fa fa-save"></i> Update Account</button>
      </div>

      <div class="settings-card">
        <h3><i class="fa fa-bell"></i> Notification Preferences</h3>
        <div class="toggle-row">
          <div><div class="toggle-label">Email Notifications</div><div class="toggle-desc">Receive email alerts for new registrations</div></div>
          <div class="toggle on" onclick="this.classList.toggle('on')"></div>
        </div>
        <div class="toggle-row">
          <div><div class="toggle-label">Contact Form Alerts</div><div class="toggle-desc">Get notified when new contact messages arrive</div></div>
          <div class="toggle on" onclick="this.classList.toggle('on')"></div>
        </div>
        <div class="toggle-row">
          <div><div class="toggle-label">System Maintenance Alerts</div><div class="toggle-desc">Receive alerts about system updates</div></div>
          <div class="toggle" onclick="this.classList.toggle('on')"></div>
        </div>
        <div class="toggle-row">
          <div><div class="toggle-label">Student Registration Alerts</div><div class="toggle-desc">Notify when new students register</div></div>
          <div class="toggle on" onclick="this.classList.toggle('on')"></div>
        </div>
      </div>
    </div>
  </div>
</div>
<footer class="footer" style="margin-top:30px;"><p>© 2025 SCTI - Admin Panel</p></footer>
</body>
</html>
