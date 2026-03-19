<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php'); exit();
}
$username = $_SESSION['username'] ?? 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Settings | SCTI Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Segoe UI',sans-serif;background:#f0f4f8;min-height:100vh}
    .top-bar{background:#00264d;color:white;padding:7px 20px;font-size:13px}
    .pg-header{background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:22px 30px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 18px rgba(0,64,128,.25)}
    .pg-header h1{font-size:24px;font-weight:700;display:flex;align-items:center;gap:10px;margin:0 0 3px}
    .pg-header .bc{font-size:12px;color:rgba(255,255,255,.75)}
    .pg-header .bc a{color:#fff;text-decoration:none}
    .btn-back{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;border:none;background:rgba(255,255,255,.18);color:#fff;text-decoration:none;transition:.2s}
    .btn-back:hover{background:rgba(255,255,255,.32)}
    .container{max-width:1100px;margin:0 auto;padding:24px}
    .layout{display:grid;grid-template-columns:230px 1fr;gap:24px;align-items:start}

    /* NAV */
    .nav-panel{background:white;border-radius:14px;padding:14px;box-shadow:0 2px 12px rgba(0,0,0,.08);position:sticky;top:20px}
    .nav-section{font-size:10px;font-weight:700;color:#aaa;text-transform:uppercase;letter-spacing:.8px;padding:10px 12px 6px}
    .nav-item{display:flex;align-items:center;gap:11px;padding:11px 14px;border-radius:9px;cursor:pointer;color:#555;font-size:14px;font-weight:500;transition:.2s;margin-bottom:2px;text-decoration:none}
    .nav-item:hover{background:#f0f4ff;color:#004080}
    .nav-item.active{background:linear-gradient(135deg,#004080,#0059b3);color:white;font-weight:700;box-shadow:0 4px 12px rgba(0,64,128,.3)}
    .nav-item.active i{color:white}
    .nav-item i{width:18px;color:#004080;font-size:14px}
    .nav-item.active i{color:white}

    /* CARDS */
    .settings-content{display:flex;flex-direction:column;gap:20px}
    .s-card{background:white;border-radius:14px;padding:28px;box-shadow:0 2px 12px rgba(0,0,0,.08);transition:.3s;border-left:4px solid transparent}
    .s-card:hover{box-shadow:0 6px 24px rgba(0,64,128,.15);border-left-color:#004080;transform:translateY(-2px)}
    .s-card h3{color:#004080;margin-bottom:22px;font-size:17px;display:flex;align-items:center;gap:9px;padding-bottom:12px;border-bottom:2px solid #f0f4ff}
    .s-card h3 i{background:linear-gradient(135deg,#004080,#0059b3);color:white;width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0}

    /* FORM */
    .fg{margin-bottom:16px}
    .fg label{display:block;font-size:12px;font-weight:700;color:#555;margin-bottom:6px;text-transform:uppercase;letter-spacing:.4px}
    .fc{width:100%;padding:10px 14px;border:2px solid #e9ecef;border-radius:8px;font-size:14px;font-family:inherit;transition:.2s;background:#fafbfc}
    .fc:focus{outline:none;border-color:#004080;background:white;box-shadow:0 0 0 3px rgba(0,64,128,.08)}
    .fc[readonly]{background:#f8f9fa;color:#888;cursor:default}
    .frow{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .btn-save{background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:11px 26px;border:none;border-radius:8px;cursor:pointer;font-size:14px;font-weight:700;display:inline-flex;align-items:center;gap:8px;transition:.25s;margin-top:4px}
    .btn-save:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,64,128,.35)}
    .btn-danger{background:linear-gradient(135deg,#dc3545,#c82333);color:white;padding:11px 26px;border:none;border-radius:8px;cursor:pointer;font-size:14px;font-weight:700;display:inline-flex;align-items:center;gap:8px;transition:.25s;margin-top:4px}
    .btn-danger:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(220,53,69,.35)}

    /* TOGGLE */
    .toggle-row{display:flex;justify-content:space-between;align-items:center;padding:14px 0;border-bottom:1px solid #f5f5f5;cursor:pointer;border-radius:8px;transition:.2s;padding-left:8px;padding-right:8px}
    .toggle-row:last-child{border-bottom:none}
    .toggle-row:hover{background:#f8f9fa}
    .toggle-lbl{font-size:14px;color:#333;font-weight:500}
    .toggle-desc{font-size:12px;color:#999;margin-top:2px}
    .toggle{position:relative;width:50px;height:27px;background:#dee2e6;border-radius:14px;cursor:pointer;transition:.3s;flex-shrink:0}
    .toggle.on{background:linear-gradient(135deg,#004080,#0059b3)}
    .toggle::after{content:'';position:absolute;width:21px;height:21px;background:white;border-radius:50%;top:3px;left:3px;transition:.3s;box-shadow:0 2px 5px rgba(0,0,0,.2)}
    .toggle.on::after{left:26px}

    /* DB INFO */
    .db-row{display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid #f5f5f5;font-size:14px}
    .db-row:last-child{border-bottom:none}
    .db-key{color:#666;font-weight:500}
    .db-val{color:#004080;font-weight:700;font-family:monospace}
    .db-badge{padding:3px 10px;border-radius:10px;font-size:12px;font-weight:700}
    .db-ok{background:#d4edda;color:#155724}
    .db-warn{background:#fff3cd;color:#856404}

    /* ALERT */
    .alert{padding:11px 16px;border-radius:8px;font-size:13px;margin-bottom:14px;display:none;align-items:center;gap:8px}
    .alert.show{display:flex}
    .alert-ok{background:#d4edda;color:#155724;border:1px solid #c3e6cb}
    .alert-err{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb}

    /* TOAST */
    .toast{position:fixed;bottom:24px;right:24px;padding:13px 20px;border-radius:10px;font-size:14px;font-weight:600;z-index:99999;display:none;align-items:center;gap:10px;box-shadow:0 6px 20px rgba(0,0,0,.2)}
    .toast.show{display:flex;animation:tIn .3s ease}
    .toast-ok{background:#28a745;color:white}
    .toast-err{background:#dc3545;color:white}
    @keyframes tIn{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}

    footer{background:#00264d;color:white;text-align:center;padding:14px;font-size:13px;margin-top:30px}
    @media(max-width:768px){.layout{grid-template-columns:1fr}.nav-panel{position:static}.frow{grid-template-columns:1fr}}
  </style>
</head>
<body>
<div class="top-bar"><marquee>Settings — Configure system preferences and account settings</marquee></div>
<div class="pg-header">
  <div>
    <h1><i class="fa fa-cog"></i> Settings</h1>
    <div class="bc"><a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Settings</div>
  </div>
  <a href="../dashboards/admin-dashboard.php" class="btn-back"><i class="fa fa-arrow-left"></i> Back</a>
</div>

<div class="container">
  <div class="layout">

    <!-- SIDEBAR NAV -->
    <div class="nav-panel">
      <div class="nav-section">Configuration</div>
      <a class="nav-item active" onclick="navScrollTo('sec-general')" href="#sec-general"><i class="fa fa-school"></i> General</a>
      <a class="nav-item" onclick="navScrollTo('sec-account')" href="#sec-account"><i class="fa fa-user-shield"></i> Account</a>
      <a class="nav-item" onclick="navScrollTo('sec-security')" href="#sec-security"><i class="fa fa-lock"></i> Security</a>
      <a class="nav-item" onclick="navScrollTo('sec-notif')" href="#sec-notif"><i class="fa fa-bell"></i> Notifications</a>
      <div class="nav-section">System</div>
      <a class="nav-item" onclick="navScrollTo('sec-db')" href="#sec-db"><i class="fa fa-database"></i> Database</a>
    </div>

    <!-- CONTENT -->
    <div class="settings-content">

      <!-- GENERAL -->
      <div class="s-card" id="sec-general">
        <h3><i class="fa fa-school"></i> General Settings</h3>
        <div id="genAlert" class="alert alert-ok"><i class="fa fa-check-circle"></i> Settings saved successfully!</div>
        <div class="frow">
          <div class="fg"><label>Institution Name</label><input type="text" id="gName" class="fc" value="Sindhuli Community Technical Institute (SCTI)"></div>
          <div class="fg"><label>Short Name</label><input type="text" id="gShort" class="fc" value="SCTI"></div>
        </div>
        <div class="frow">
          <div class="fg"><label>Contact Email</label><input type="email" id="gEmail" class="fc" value="admin@scti.edu.np"></div>
          <div class="fg"><label>Contact Phone</label><input type="text" id="gPhone" class="fc" value="+977-056-XXXXXX"></div>
        </div>
        <div class="fg"><label>Address</label><input type="text" id="gAddr" class="fc" value="Sindhuli, Bagmati Province, Nepal"></div>
        <div class="frow">
          <div class="fg"><label>Academic Year</label><input type="text" id="gYear" class="fc" value="2025/26"></div>
          <div class="fg"><label>Timezone</label>
            <select class="fc"><option selected>Asia/Kathmandu (NPT +5:45)</option><option>UTC</option><option>Asia/Kolkata (IST +5:30)</option></select>
          </div>
        </div>
        <button class="btn-save" onclick="saveGeneral()"><i class="fa fa-save"></i> Save Changes</button>
      </div>

      <!-- ACCOUNT -->
      <div class="s-card" id="sec-account">
        <h3><i class="fa fa-user-shield"></i> Account Settings</h3>
        <div id="accAlert" class="alert"></div>
        <div class="frow">
          <div class="fg"><label>Admin Username</label><input type="text" class="fc" value="<?=htmlspecialchars($username)?>" readonly></div>
          <div class="fg"><label>Admin Email</label><input type="email" class="fc" value="admin@scti.edu.np" readonly></div>
        </div>
        <p style="font-size:13px;color:#888;margin-bottom:14px"><i class="fa fa-info-circle"></i> Use the Security section below to change your password.</p>
        <a href="admin-profile.php" class="btn-save" style="text-decoration:none"><i class="fa fa-user"></i> View Full Profile</a>
      </div>

      <!-- SECURITY -->
      <div class="s-card" id="sec-security">
        <h3><i class="fa fa-lock"></i> Security — Change Password</h3>
        <div id="cpAlert" class="alert"></div>
        <div class="fg"><label>Current Password</label><input type="password" id="cpCurrent" class="fc" placeholder="Enter current password"></div>
        <div class="frow">
          <div class="fg"><label>New Password</label><input type="password" id="cpNew" class="fc" placeholder="Min. 6 characters"></div>
          <div class="fg"><label>Confirm New Password</label><input type="password" id="cpConfirm" class="fc" placeholder="Repeat new password"></div>
        </div>
        <button class="btn-save" onclick="changePassword()"><i class="fa fa-key"></i> Update Password</button>
      </div>

      <!-- NOTIFICATIONS -->
      <div class="s-card" id="sec-notif">
        <h3><i class="fa fa-bell"></i> Notification Preferences</h3>
        <div class="toggle-row" onclick="this.querySelector('.toggle').classList.toggle('on')">
          <div><div class="toggle-lbl">Email Notifications</div><div class="toggle-desc">Receive email alerts for new registrations</div></div>
          <div class="toggle on"></div>
        </div>
        <div class="toggle-row" onclick="this.querySelector('.toggle').classList.toggle('on')">
          <div><div class="toggle-lbl">Contact Form Alerts</div><div class="toggle-desc">Get notified when new contact messages arrive</div></div>
          <div class="toggle on"></div>
        </div>
        <div class="toggle-row" onclick="this.querySelector('.toggle').classList.toggle('on')">
          <div><div class="toggle-lbl">System Maintenance Alerts</div><div class="toggle-desc">Receive alerts about system updates</div></div>
          <div class="toggle"></div>
        </div>
        <div class="toggle-row" onclick="this.querySelector('.toggle').classList.toggle('on')">
          <div><div class="toggle-lbl">Student Registration Alerts</div><div class="toggle-desc">Notify when new students register</div></div>
          <div class="toggle on"></div>
        </div>
        <div style="margin-top:16px">
          <button class="btn-save" onclick="showToast('ok','Notification preferences saved!')"><i class="fa fa-save"></i> Save Preferences</button>
        </div>
      </div>

      <!-- DATABASE -->
      <div class="s-card" id="sec-db">
        <h3><i class="fa fa-database"></i> Database Information</h3>
        <div id="dbAlert" class="alert"></div>
        <div class="db-row"><span class="db-key"><i class="fa fa-server"></i> Host</span><span class="db-val">localhost</span></div>
        <div class="db-row"><span class="db-key"><i class="fa fa-database"></i> Database</span><span class="db-val">scti_school</span></div>
        <div class="db-row"><span class="db-key"><i class="fa fa-user"></i> User</span><span class="db-val">root</span></div>
        <div class="db-row">
          <span class="db-key"><i class="fa fa-circle-check"></i> Status</span>
          <span class="db-badge db-ok" id="dbStatus"><i class="fa fa-check"></i> Connected</span>
        </div>
        <div class="db-row"><span class="db-key"><i class="fa fa-code-branch"></i> Engine</span><span class="db-val">MySQL / MariaDB</span></div>
        <div class="db-row"><span class="db-key"><i class="fa fa-table"></i> Tables</span><span class="db-val" id="dbTables">—</span></div>
        <div class="db-row"><span class="db-key"><i class="fa fa-users"></i> Students</span><span class="db-val" id="dbStudents">—</span></div>
        <div class="db-row"><span class="db-key"><i class="fa fa-chalkboard-teacher"></i> Teachers</span><span class="db-val" id="dbTeachers">—</span></div>
        <div class="db-row"><span class="db-key"><i class="fa fa-book"></i> Materials</span><span class="db-val" id="dbMaterials">—</span></div>
        <div class="db-row"><span class="db-key"><i class="fa fa-tasks"></i> Assignments</span><span class="db-val" id="dbAssignments">—</span></div>
        <div style="margin-top:18px;display:flex;gap:10px;flex-wrap:wrap">
          <button class="btn-save" onclick="testDB()"><i class="fa fa-plug"></i> Test Connection</button>
          <button class="btn-save" style="background:linear-gradient(135deg,#28a745,#20c997)" onclick="loadDBStats()"><i class="fa fa-sync"></i> Refresh Stats</button>
          <button class="btn-danger" onclick="if(confirm('Clear all cache? This cannot be undone.')) showToast('ok','Cache cleared successfully!')"><i class="fa fa-trash"></i> Clear Cache</button>
        </div>
      </div>

    </div>
  </div>
</div>

<footer>© 2025 SCTI — Admin Panel</footer>

<div class="toast toast-ok" id="toastOk"><i class="fa fa-check-circle"></i><span id="toastOkMsg"></span></div>
<div class="toast toast-err" id="toastErr"><i class="fa fa-times-circle"></i><span id="toastErrMsg"></span></div>

<script>
// Smooth scroll + active nav — renamed to avoid conflict with window.scrollTo
function navScrollTo(id) {
  document.getElementById(id).scrollIntoView({behavior:'smooth', block:'start'});
  document.querySelectorAll('.nav-item').forEach(function(n){ n.classList.remove('active'); });
  event.currentTarget.classList.add('active');
}

// Highlight nav on scroll
window.addEventListener('scroll', function() {
  var sections = ['sec-general','sec-account','sec-security','sec-notif','sec-db'];
  var navItems = document.querySelectorAll('.nav-item');
  sections.forEach(function(id, i) {
    var el = document.getElementById(id);
    if (!el) return;
    var rect = el.getBoundingClientRect();
    if (rect.top <= 120 && rect.bottom >= 120) {
      navItems.forEach(function(n){ n.classList.remove('active'); });
      if (navItems[i]) navItems[i].classList.add('active');
    }
  });
});

function saveGeneral() {
  showAlert('genAlert','ok','<i class="fa fa-check-circle"></i> General settings saved successfully!');
  showToast('ok','Settings saved!');
}

function changePassword() {
  var current = document.getElementById('cpCurrent').value.trim();
  var newPw   = document.getElementById('cpNew').value.trim();
  var confirm = document.getElementById('cpConfirm').value.trim();
  if (!current || !newPw || !confirm) { showAlert('cpAlert','err','<i class="fa fa-times-circle"></i> All fields are required.'); return; }
  if (newPw.length < 6) { showAlert('cpAlert','err','<i class="fa fa-times-circle"></i> New password must be at least 6 characters.'); return; }
  if (newPw !== confirm) { showAlert('cpAlert','err','<i class="fa fa-times-circle"></i> Passwords do not match.'); return; }
  var fd = new FormData();
  fd.append('current_password', current);
  fd.append('new_password', newPw);
  fd.append('confirm_password', confirm);
  fetch('change-password.php', {method:'POST', body:fd})
    .then(function(r){ return r.json(); })
    .then(function(d){
      if (d.success) {
        showAlert('cpAlert','ok','<i class="fa fa-check-circle"></i> ' + d.message);
        showToast('ok', d.message);
        document.getElementById('cpCurrent').value = '';
        document.getElementById('cpNew').value = '';
        document.getElementById('cpConfirm').value = '';
      } else {
        showAlert('cpAlert','err','<i class="fa fa-times-circle"></i> ' + d.message);
      }
    })
    .catch(function(){ showAlert('cpAlert','err','<i class="fa fa-times-circle"></i> Network error.'); });
}

function testDB() {
  var btn = event.currentTarget;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Testing...';
  btn.disabled = true;
  fetch('db-test.php')
    .then(function(r){ return r.json(); })
    .then(function(d){
      btn.innerHTML = '<i class="fa fa-plug"></i> Test Connection';
      btn.disabled = false;
      if (d.success) {
        document.getElementById('dbStatus').className = 'db-badge db-ok';
        document.getElementById('dbStatus').innerHTML = '<i class="fa fa-check"></i> Connected';
        showAlert('dbAlert','ok','<i class="fa fa-check-circle"></i> Connection OK! ' + (d.version||''));
        showToast('ok','Database is connected!');
        loadDBStats();
      } else {
        document.getElementById('dbStatus').className = 'db-badge db-warn';
        document.getElementById('dbStatus').innerHTML = '<i class="fa fa-times"></i> Error';
        showAlert('dbAlert','err','<i class="fa fa-times-circle"></i> ' + (d.message||'Connection failed'));
        showToast('err','DB connection failed!');
      }
    })
    .catch(function(){
      btn.innerHTML = '<i class="fa fa-plug"></i> Test Connection';
      btn.disabled = false;
      showAlert('dbAlert','err','<i class="fa fa-times-circle"></i> Network error.');
      showToast('err','Network error!');
    });
}

function loadDBStats() {
  fetch('db-test.php')
    .then(function(r){ return r.json(); })
    .then(function(d){
      if (!d.success) return;
      if (d.tables    !== undefined) document.getElementById('dbTables').textContent      = d.tables;
      if (d.students  !== undefined) document.getElementById('dbStudents').textContent    = d.students;
      if (d.teachers  !== undefined) document.getElementById('dbTeachers').textContent    = d.teachers;
      if (d.materials !== undefined) document.getElementById('dbMaterials').textContent   = d.materials;
      if (d.assignments !== undefined) document.getElementById('dbAssignments').textContent = d.assignments;
    }).catch(function(){});
}
loadDBStats();

function showAlert(id, type, html) {
  var el = document.getElementById(id);
  el.className = 'alert show alert-' + (type==='ok' ? 'ok' : 'err');
  el.innerHTML = html;
  setTimeout(function(){ el.classList.remove('show'); }, 4000);
}

function showToast(type, msg) {
  var id  = type==='ok' ? 'toastOk'  : 'toastErr';
  var mid = type==='ok' ? 'toastOkMsg' : 'toastErrMsg';
  document.getElementById(mid).textContent = msg;
  var t = document.getElementById(id);
  t.classList.add('show');
  setTimeout(function(){ t.classList.remove('show'); }, 3000);
}
</script>
</body>
</html>
