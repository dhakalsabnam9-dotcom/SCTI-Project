<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>System Settings | SCTI Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
    .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
    
    .page-header {
      background: linear-gradient(135deg, #004080 0%, #0059b3 100%);
      color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;
      box-shadow: 0 4px 15px rgba(0,64,128,0.2);
      display: flex; justify-content: space-between; align-items: center;
    }
    .page-header h1 { margin: 0; font-size: 28px; }
    .breadcrumb { opacity: 1; font-size: 14px; background: transparent; padding: 0; margin-top: 8px; }
    .breadcrumb a { color: white; text-decoration: none; }
    .breadcrumb a:hover { text-decoration: underline; }
    
    .btn {
      padding: 12px 24px; border: none; border-radius: 6px;
      cursor: pointer; font-size: 14px; transition: all 0.3s;
      text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-primary {
      background: white; color: #004080; font-weight: 600;
    }
    .btn-primary:hover {
      background: #f0f0f0; transform: translateY(-2px);
    }
    
    .settings-grid {
      display: grid; gap: 25px;
    }
    
    .settings-card {
      background: white; border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px;
    }
    .settings-card h3 {
      margin: 0 0 20px 0; color: #004080;
      display: flex; align-items: center; gap: 10px;
      padding-bottom: 15px; border-bottom: 2px solid #e0e0e0;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    .form-group label {
      display: block; margin-bottom: 8px;
      color: #333; font-weight: 600; font-size: 14px;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%; padding: 12px 15px;
      border: 2px solid #e0e0e0; border-radius: 6px;
      font-size: 14px; transition: all 0.3s;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      outline: none; border-color: #004080;
    }
    .form-group textarea {
      resize: vertical; min-height: 100px;
    }
    .form-group small {
      display: block; margin-top: 5px;
      color: #666; font-size: 12px;
    }
    
    .form-row {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
    }
    
    .toggle-switch {
      position: relative; display: inline-block;
      width: 50px; height: 24px;
    }
    .toggle-switch input {
      opacity: 0; width: 0; height: 0;
    }
    .slider {
      position: absolute; cursor: pointer;
      top: 0; left: 0; right: 0; bottom: 0;
      background-color: #ccc; transition: .4s;
      border-radius: 24px;
    }
    .slider:before {
      position: absolute; content: "";
      height: 18px; width: 18px; left: 3px; bottom: 3px;
      background-color: white; transition: .4s;
      border-radius: 50%;
    }
    input:checked + .slider {
      background-color: #28a745;
    }
    input:checked + .slider:before {
      transform: translateX(26px);
    }
    
    .toggle-group {
      display: flex; align-items: center;
      justify-content: space-between; padding: 15px 0;
      border-bottom: 1px solid #e0e0e0;
    }
    .toggle-group:last-child {
      border-bottom: none;
    }
    .toggle-info h4 {
      margin: 0 0 5px 0; color: #333; font-size: 15px;
    }
    .toggle-info p {
      margin: 0; color: #666; font-size: 13px;
    }
    
    .btn-save {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white; padding: 12px 30px; border: none;
      border-radius: 6px; cursor: pointer; font-size: 15px;
      font-weight: 600; transition: all 0.3s;
    }
    .btn-save:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(40,167,69,0.3);
    }
    
    .color-picker-group {
      display: flex; gap: 15px; align-items: center;
    }
    .color-preview {
      width: 50px; height: 50px; border-radius: 8px;
      border: 2px solid #e0e0e0;
    }
    input[type="color"] {
      width: 80px; height: 50px; border: none;
      border-radius: 8px; cursor: pointer;
    }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>System Settings - Configure system preferences and settings</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <div>
      <h1><i class="fa fa-cog"></i> System Settings</h1>
      <div class="breadcrumb">
        <a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Settings
      </div>
    </div>
    <a href="../dashboards/admin-dashboard.php" class="btn btn-primary">
      <i class="fa fa-arrow-left"></i> Back
    </a>
  </div>

  <div class="settings-grid">
    
    <div class="settings-card">
      <h3><i class="fa fa-school"></i> Institute Information</h3>
      <form onsubmit="event.preventDefault(); alert('Save functionality coming soon!');">
        <div class="form-row">
          <div class="form-group">
            <label>Institute Name</label>
            <input type="text" value="Sindhuli Community Technical Institute" required>
          </div>
          <div class="form-group">
            <label>Short Name</label>
            <input type="text" value="SCTI" required>
          </div>
        </div>
        <div class="form-group">
          <label>Address</label>
          <input type="text" value="Sindhuli, Nepal" required>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Phone</label>
            <input type="tel" value="+977 9841234567" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" value="info@scti.edu.np" required>
          </div>
        </div>
        <div class="form-group">
          <label>Website</label>
          <input type="url" value="https://scti.edu.np">
        </div>
        <button type="submit" class="btn-save">
          <i class="fa fa-save"></i> Save Changes
        </button>
      </form>
    </div>

    <div class="settings-card">
      <h3><i class="fa fa-calendar-alt"></i> Academic Year Settings</h3>
      <form onsubmit="event.preventDefault(); alert('Save functionality coming soon!');">
        <div class="form-row">
          <div class="form-group">
            <label>Current Academic Year</label>
            <input type="text" value="2024/2025" required>
          </div>
          <div class="form-group">
            <label>Current Semester</label>
            <select required>
              <option>Semester 1</option>
              <option selected>Semester 2</option>
              <option>Semester 3</option>
              <option>Semester 4</option>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Semester Start Date</label>
            <input type="date" value="2025-01-15" required>
          </div>
          <div class="form-group">
            <label>Semester End Date</label>
            <input type="date" value="2025-06-30" required>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Exam Start Date</label>
            <input type="date" value="2025-06-01">
          </div>
          <div class="form-group">
            <label>Exam End Date</label>
            <input type="date" value="2025-06-20">
          </div>
        </div>
        <button type="submit" class="btn-save">
          <i class="fa fa-save"></i> Save Changes
        </button>
      </form>
    </div>

    <div class="settings-card">
      <h3><i class="fa fa-bell"></i> Notification Settings</h3>
      <div class="toggle-group">
        <div class="toggle-info">
          <h4>Email Notifications</h4>
          <p>Send email notifications for important updates</p>
        </div>
        <label class="toggle-switch">
          <input type="checkbox" checked>
          <span class="slider"></span>
        </label>
      </div>
      <div class="toggle-group">
        <div class="toggle-info">
          <h4>SMS Notifications</h4>
          <p>Send SMS alerts for urgent notices</p>
        </div>
        <label class="toggle-switch">
          <input type="checkbox" checked>
          <span class="slider"></span>
        </label>
      </div>
      <div class="toggle-group">
        <div class="toggle-info">
          <h4>Student Notifications</h4>
          <p>Allow students to receive notifications</p>
        </div>
        <label class="toggle-switch">
          <input type="checkbox" checked>
          <span class="slider"></span>
        </label>
      </div>
      <div class="toggle-group">
        <div class="toggle-info">
          <h4>Teacher Notifications</h4>
          <p>Allow teachers to receive notifications</p>
        </div>
        <label class="toggle-switch">
          <input type="checkbox" checked>
          <span class="slider"></span>
        </label>
      </div>
      <button class="btn-save" style="margin-top: 20px;" onclick="alert('Save functionality coming soon!')">
        <i class="fa fa-save"></i> Save Changes
      </button>
    </div>

    <div class="settings-card">
      <h3><i class="fa fa-shield-alt"></i> Security Settings</h3>
      <div class="toggle-group">
        <div class="toggle-info">
          <h4>Two-Factor Authentication</h4>
          <p>Require 2FA for admin accounts</p>
        </div>
        <label class="toggle-switch">
          <input type="checkbox">
          <span class="slider"></span>
        </label>
      </div>
      <div class="toggle-group">
        <div class="toggle-info">
          <h4>Password Expiry</h4>
          <p>Force password change every 90 days</p>
        </div>
        <label class="toggle-switch">
          <input type="checkbox" checked>
          <span class="slider"></span>
        </label>
      </div>
      <div class="toggle-group">
        <div class="toggle-info">
          <h4>Login Attempt Limit</h4>
          <p>Lock account after 5 failed login attempts</p>
        </div>
        <label class="toggle-switch">
          <input type="checkbox" checked>
          <span class="slider"></span>
        </label>
      </div>
      <div class="toggle-group">
        <div class="toggle-info">
          <h4>Session Timeout</h4>
          <p>Auto logout after 30 minutes of inactivity</p>
        </div>
        <label class="toggle-switch">
          <input type="checkbox" checked>
          <span class="slider"></span>
        </label>
      </div>
      <button class="btn-save" style="margin-top: 20px;" onclick="alert('Save functionality coming soon!')">
        <i class="fa fa-save"></i> Save Changes
      </button>
    </div>

    <div class="settings-card">
      <h3><i class="fa fa-palette"></i> Appearance Settings</h3>
      <form onsubmit="event.preventDefault(); alert('Save functionality coming soon!');">
        <div class="form-group">
          <label>Primary Color</label>
          <div class="color-picker-group">
            <input type="color" value="#004080" id="primaryColor">
            <div class="color-preview" style="background: #004080;"></div>
            <span>#004080</span>
          </div>
          <small>Main theme color for the system</small>
        </div>
        <div class="form-group">
          <label>Secondary Color</label>
          <div class="color-picker-group">
            <input type="color" value="#0059b3" id="secondaryColor">
            <div class="color-preview" style="background: #0059b3;"></div>
            <span>#0059b3</span>
          </div>
          <small>Secondary accent color</small>
        </div>
        <div class="form-group">
          <label>Logo Upload</label>
          <input type="file" accept="image/*">
          <small>Recommended size: 200x200px, PNG or JPG</small>
        </div>
        <button type="submit" class="btn-save">
          <i class="fa fa-save"></i> Save Changes
        </button>
      </form>
    </div>

    <div class="settings-card">
      <h3><i class="fa fa-database"></i> System Maintenance</h3>
      <div class="toggle-group">
        <div class="toggle-info">
          <h4>Maintenance Mode</h4>
          <p>Put system in maintenance mode</p>
        </div>
        <label class="toggle-switch">
          <input type="checkbox">
          <span class="slider"></span>
        </label>
      </div>
      <div class="toggle-group">
        <div class="toggle-info">
          <h4>Auto Backup</h4>
          <p>Automatically backup database daily</p>
        </div>
        <label class="toggle-switch">
          <input type="checkbox" checked>
          <span class="slider"></span>
        </label>
      </div>
      <div style="margin-top: 20px; display: flex; gap: 10px;">
        <button class="btn-save" onclick="alert('Backup functionality coming soon!')">
          <i class="fa fa-download"></i> Backup Now
        </button>
        <button class="btn-save" style="background: linear-gradient(135deg, #dc3545, #c82333);" onclick="if(confirm('Clear all cache?')) alert('Clear cache coming soon!')">
          <i class="fa fa-trash"></i> Clear Cache
        </button>
      </div>
    </div>

  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Admin Panel</p>
</footer>

</body>
</html>
