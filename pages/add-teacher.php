<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php'); exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Teacher | SCTI Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Segoe UI',sans-serif;background:#f0f4f8;min-height:100vh}
    .top-bar{background:#00264d;color:white;padding:7px 20px;font-size:13px}
    .pg-header{background:linear-gradient(135deg,#004080,#0059b3);color:#fff;padding:20px 28px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 18px rgba(0,64,128,.25)}
    .pg-header h1{font-size:22px;font-weight:700;display:flex;align-items:center;gap:10px;margin:0 0 3px}
    .pg-header .bc{font-size:12px;color:rgba(255,255,255,.75)}
    .pg-header .bc a{color:#fff;text-decoration:none}
    .hdr-btns{display:flex;gap:8px}
    .btn-hdr{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;border:none;transition:.2s;text-decoration:none}
    .btn-hdr.ghost{background:rgba(255,255,255,.18);color:#fff}
    .btn-hdr.ghost:hover{background:rgba(255,255,255,.32)}
    .wrap{max-width:860px;margin:32px auto;padding:0 20px 40px}
    .card{background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,64,128,.10);overflow:hidden}
    .card-head{background:linear-gradient(135deg,#004080,#0059b3);padding:22px 28px;color:#fff;display:flex;align-items:center;gap:14px}
    .card-head .avatar{width:56px;height:56px;border-radius:50%;background:rgba(255,255,255,.2);border:3px solid rgba(255,255,255,.4);display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0}
    .card-head h2{font-size:20px;font-weight:700;margin:0 0 3px}
    .card-head p{font-size:13px;opacity:.8;margin:0}
    .card-body{padding:28px}
    .sec{margin-bottom:24px}
    .sec-label{font-size:11px;font-weight:800;color:#004080;text-transform:uppercase;letter-spacing:.8px;margin:0 0 14px;padding-bottom:8px;border-bottom:2px solid #e8f0fe;display:flex;align-items:center;gap:7px}
    .fgrid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .fgrid.three{grid-template-columns:1fr 1fr 1fr}
    .fgrid.full{grid-template-columns:1fr}
    .fg{display:flex;flex-direction:column;gap:5px}
    .fg label{font-size:12px;font-weight:600;color:#555}
    .fc{width:100%;padding:10px 13px;border:2px solid #dee2e6;border-radius:9px;font-size:13px;font-family:inherit;transition:.2s;background:#fff;color:#333}
    .fc:focus{outline:none;border-color:#004080;box-shadow:0 0 0 3px rgba(0,64,128,.1)}
    .fc[readonly]{background:#f8f9fa;color:#666;cursor:default}
    textarea.fc{resize:vertical;min-height:80px}
    .input-group{display:flex}
    .input-group .fc{border-radius:9px 0 0 9px;flex:1}
    .ig-btn{padding:0 13px;background:linear-gradient(135deg,#004080,#0059b3);color:#fff;border:none;border-radius:0 9px 9px 0;cursor:pointer;font-size:13px;display:flex;align-items:center;gap:5px;transition:.2s;white-space:nowrap}
    .ig-btn:hover{opacity:.85}
    .ig-btn.mid{border-radius:0;border-left:1px solid rgba(255,255,255,.3)}
    .auto-badge{display:inline-flex;align-items:center;gap:4px;background:#e8f0fe;color:#004080;font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px;margin-left:6px;vertical-align:middle}
    .cred-box{background:linear-gradient(135deg,#e8f0fe,#f0f4ff);border:2px solid #004080;border-radius:10px;padding:14px 18px;margin-top:14px;font-size:13px;display:none}
    .cred-box .ct{font-weight:700;color:#004080;margin-bottom:8px;font-size:14px}
    .cred-box .cr{display:flex;gap:24px;flex-wrap:wrap;margin-bottom:4px}
    .cred-box code{background:#fff;padding:3px 10px;border-radius:6px;font-weight:700}
    .cred-box code.u{color:#004080}
    .cred-box code.p{color:#dc3545}
    .cred-box .cn{font-size:11px;color:#888;margin-top:6px}
    .photo-sec{display:flex;align-items:center;gap:20px;padding:16px;background:#f8f9fa;border-radius:10px;border:2px dashed #dee2e6}
    .photo-preview{width:90px;height:90px;border-radius:50%;background:linear-gradient(135deg,#e8f0fe,#c8d8f0);border:3px solid #004080;display:flex;align-items:center;justify-content:center;font-size:34px;color:#004080;overflow:hidden;flex-shrink:0}
    .photo-preview img{width:100%;height:100%;object-fit:cover;display:none}
    .photo-info h4{font-size:14px;color:#333;margin:0 0 4px}
    .photo-info p{font-size:12px;color:#888;margin:0 0 10px}
    .btn-photo{padding:7px 16px;background:#e8f0fe;color:#004080;border:2px solid #004080;border-radius:8px;cursor:pointer;font-size:12px;font-weight:700;transition:.2s}
    .btn-photo:hover{background:#004080;color:#fff}
    .alert{padding:12px 16px;border-radius:9px;font-size:13px;margin-bottom:18px;display:none}
    .alert-ok{background:#d4edda;color:#155724;border:1px solid #c3e6cb}
    .alert-err{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb}
    .form-actions{display:flex;gap:12px;margin-top:8px}
    .btn-save{flex:1;padding:14px;background:linear-gradient(135deg,#004080,#0059b3);color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:9px;transition:.25s}
    .btn-save:hover{transform:translateY(-2px);box-shadow:0 8px 22px rgba(0,64,128,.35)}
    .btn-cancel{padding:14px 28px;background:#f0f0f0;color:#555;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;transition:.2s;text-decoration:none;display:flex;align-items:center;gap:7px}
    .btn-cancel:hover{background:#e0e0e0}
    .toast{position:fixed;bottom:22px;right:22px;color:white;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:700;z-index:99999;display:none;box-shadow:0 4px 15px rgba(0,0,0,.2);animation:slideUp .3s ease}
    @keyframes slideUp{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}
    .toast.ok{background:linear-gradient(135deg,#28a745,#20c997)}
    .toast.err{background:linear-gradient(135deg,#dc3545,#c82333)}
    footer{background:#00264d;color:white;text-align:center;padding:12px;font-size:13px}
    @media(max-width:640px){.fgrid,.fgrid.three{grid-template-columns:1fr}.form-actions{flex-direction:column}}
  </style>
</head>
<body>
<div class="top-bar"><marquee>Add New Teacher — Fill in all details to register a new teaching staff member at SCTI</marquee></div>

<div class="pg-header">
  <div>
    <h1><i class="fa fa-user-plus"></i> Add New Teacher</h1>
    <div class="bc">
      <a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> /
      <a href="manage-teachers.php">Teachers</a> / Add
    </div>
  </div>
  <div class="hdr-btns">
    <a href="manage-teachers.php" class="btn-hdr ghost"><i class="fa fa-arrow-left"></i> Back to Teachers</a>
  </div>
</div>

<div class="wrap">
  <div id="pageAlert" class="alert"></div>

  <div class="card">
    <div class="card-head">
      <div class="avatar"><i class="fa fa-user-tie"></i></div>
      <div>
        <h2>Teacher Registration Form</h2>
        <p>Fill in all the details below to add a new teacher to the system</p>
      </div>
    </div>

    <div class="card-body">

      <!-- PHOTO SECTION -->
      <div class="sec">
        <div class="sec-label"><i class="fa fa-camera"></i> Profile Photo</div>
        <div class="photo-sec">
          <div class="photo-preview" id="photoPreview">
            <i class="fa fa-user-tie" id="photoIcon"></i>
            <img id="photoImg" src="" alt="">
          </div>
          <div class="photo-info">
            <h4>Upload Photo <small style="color:#999;font-weight:400">(Optional)</small></h4>
            <p>JPG, PNG or GIF — max 2MB. Recommended: square image.</p>
            <input type="file" id="fPhoto" accept="image/*" style="display:none" onchange="previewPhoto(this)">
            <button class="btn-photo" onclick="document.getElementById('fPhoto').click()">
              <i class="fa fa-upload"></i> Choose Photo
            </button>
          </div>
        </div>
      </div>

      <!-- BASIC INFO -->
      <div class="sec">
        <div class="sec-label"><i class="fa fa-id-card"></i> Basic Information</div>
        <div class="fgrid" style="margin-bottom:14px">
          <div class="fg" style="grid-column:1/-1">
            <label>Full Name *</label>
            <input type="text" id="fName" class="fc" placeholder="e.g. Ram Bahadur Thapa" oninput="autoGenerate()">
          </div>
        </div>
        <div class="fgrid" style="margin-bottom:14px">
          <div class="fg">
            <label>Teacher ID <span class="auto-badge"><i class="fa fa-magic"></i> Auto</span></label>
            <div class="input-group">
              <input type="text" id="fTeacherId" class="fc" placeholder="TCH-2025-001" readonly>
              <button class="ig-btn" onclick="genTeacherId()" title="Regenerate ID"><i class="fa fa-sync"></i></button>
            </div>
          </div>
          <div class="fg">
            <label>Department *</label>
            <select id="fDept" class="fc" onchange="autoGenerate()">
              <option value="">Select Department...</option>
              <option>Computer Science</option>
              <option>Civil Engineering</option>
              <option>Electrical Engineering</option>
              <option>Animal Husbandry</option>
              <option>General</option>
            </select>
          </div>
        </div>
        <div class="fgrid">
          <div class="fg">
            <label>Date of Birth</label>
            <input type="date" id="fDob" class="fc">
          </div>
          <div class="fg">
            <label>Gender</label>
            <select id="fGender" class="fc">
              <option value="">Select...</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select>
          </div>
        </div>
      </div>

      <!-- CONTACT INFO -->
      <div class="sec">
        <div class="sec-label"><i class="fa fa-address-book"></i> Contact Information</div>
        <div class="fgrid" style="margin-bottom:14px">
          <div class="fg">
            <label>Email Address</label>
            <input type="email" id="fEmail" class="fc" placeholder="teacher@scti.edu.np">
          </div>
          <div class="fg">
            <label>Phone Number</label>
            <input type="text" id="fPhone" class="fc" placeholder="98XXXXXXXX">
          </div>
        </div>
        <div class="fgrid">
          <div class="fg">
            <label>Permanent Address</label>
            <input type="text" id="fAddress" class="fc" placeholder="e.g. Kathmandu, Nepal">
          </div>
          <div class="fg">
            <label>Emergency Contact</label>
            <input type="text" id="fEmergency" class="fc" placeholder="Name — 98XXXXXXXX">
          </div>
        </div>
      </div>

      <!-- ACADEMIC INFO -->
      <div class="sec">
        <div class="sec-label"><i class="fa fa-graduation-cap"></i> Academic & Professional</div>
        <div class="fgrid" style="margin-bottom:14px">
          <div class="fg">
            <label>Qualification</label>
            <input type="text" id="fQual" class="fc" placeholder="e.g. M.Sc. Computer Science">
          </div>
          <div class="fg">
            <label>Specialization</label>
            <input type="text" id="fSpec" class="fc" placeholder="e.g. Artificial Intelligence">
          </div>
        </div>
        <div class="fgrid" style="margin-bottom:14px">
          <div class="fg">
            <label>Experience</label>
            <input type="text" id="fExp" class="fc" placeholder="e.g. 5 Years">
          </div>
          <div class="fg">
            <label>Joining Date</label>
            <input type="date" id="fJoining" class="fc">
          </div>
        </div>
        <div class="fgrid full" style="margin-bottom:14px">
          <div class="fg">
            <label>Subjects Taught <small style="color:#999;font-weight:400">(comma separated)</small></label>
            <input type="text" id="fSubjects" class="fc" placeholder="e.g. Programming, Database Systems, Web Development">
          </div>
        </div>
        <div class="fgrid full">
          <div class="fg">
            <label>Bio / About</label>
            <textarea id="fBio" class="fc" placeholder="Brief description about the teacher's background, achievements, etc."></textarea>
          </div>
        </div>
      </div>

      <!-- LOGIN CREDENTIALS -->
      <div class="sec">
        <div class="sec-label"><i class="fa fa-key"></i> Login Credentials</div>
        <div class="fgrid">
          <div class="fg">
            <label>Username <span class="auto-badge"><i class="fa fa-magic"></i> Auto</span></label>
            <div class="input-group">
              <input type="text" id="fUsername" class="fc" placeholder="teacher.ram" oninput="updateCredBox()">
              <button class="ig-btn" onclick="genUsername()" title="Regenerate"><i class="fa fa-sync"></i></button>
            </div>
          </div>
          <div class="fg">
            <label>Password <span class="auto-badge"><i class="fa fa-magic"></i> Auto</span></label>
            <div class="input-group">
              <input type="text" id="fPassword" class="fc" placeholder="Auto-generated" oninput="updateCredBox()">
              <button class="ig-btn" onclick="genPassword()" title="Regenerate"><i class="fa fa-sync"></i></button>
              <button class="ig-btn mid" onclick="copyPwd()" title="Copy password"><i class="fa fa-copy" id="copyIcon"></i></button>
            </div>
          </div>
        </div>
        <!-- Credentials preview -->
        <div class="cred-box" id="credBox">
          <div class="ct"><i class="fa fa-shield-alt"></i> Generated Credentials — Share with teacher after saving</div>
          <div class="cr">
            <span><i class="fa fa-user" style="color:#004080"></i> Username: <code class="u" id="credUser"></code></span>
            <span><i class="fa fa-lock" style="color:#dc3545"></i> Password: <code class="p" id="credPass"></code></span>
          </div>
          <div class="cn"><i class="fa fa-info-circle"></i> Make sure to note these credentials before saving. Password cannot be recovered later.</div>
        </div>
      </div>

      <!-- STATUS -->
      <div class="sec">
        <div class="sec-label"><i class="fa fa-toggle-on"></i> Account Status</div>
        <div class="fgrid">
          <div class="fg">
            <label>Status</label>
            <select id="fStatus" class="fc">
              <option value="active">Active — Can login immediately</option>
              <option value="inactive">Inactive — Cannot login yet</option>
            </select>
          </div>
          <div class="fg">
            <label>Salary (NPR) <small style="color:#999;font-weight:400">(Optional)</small></label>
            <input type="number" id="fSalary" class="fc" placeholder="e.g. 35000">
          </div>
        </div>
      </div>

      <!-- ACTIONS -->
      <div id="formAlert" class="alert"></div>
      <div class="form-actions">
        <button class="btn-save" onclick="saveTeacher()">
          <i class="fa fa-save"></i> <span id="btnTxt">Save Teacher</span>
        </button>
        <button class="btn-cancel" onclick="resetForm()">
          <i class="fa fa-redo"></i> Reset
        </button>
        <a href="manage-teachers.php" class="btn-cancel">
          <i class="fa fa-times"></i> Cancel
        </a>
      </div>

    </div><!-- /card-body -->
  </div><!-- /card -->
</div><!-- /wrap -->

<div class="toast" id="toast"></div>
<footer>© 2025 SCTI — Admin Panel</footer>

<script>
// ── PHOTO PREVIEW ─────────────────────────────────────────────
function previewPhoto(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      var img = document.getElementById('photoImg');
      img.src = e.target.result;
      img.style.display = 'block';
      document.getElementById('photoIcon').style.display = 'none';
    };
    reader.readAsDataURL(input.files[0]);
  }
}

// ── AUTO-GENERATE ─────────────────────────────────────────────
function autoGenerate() {
  genTeacherId();
  genUsername();
  if (!document.getElementById('fPassword').value) genPassword();
  updateCredBox();
}

function genTeacherId() {
  var year = new Date().getFullYear();
  var num  = String(Math.floor(Math.random() * 900) + 100);
  document.getElementById('fTeacherId').value = 'TCH-' + year + '-' + num;
}

function genUsername() {
  var name  = document.getElementById('fName').value.trim().toLowerCase();
  var parts = name.split(/\s+/);
  var base  = parts.length >= 2
    ? parts[0] + '.' + parts[parts.length - 1]
    : (parts[0] || 'teacher') + Math.floor(Math.random() * 99 + 1);
  base = base.replace(/[^a-z0-9.]/g, '');
  document.getElementById('fUsername').value = base || 'teacher' + Math.floor(Math.random()*999);
  updateCredBox();
}

function genPassword() {
  var u='ABCDEFGHJKLMNPQRSTUVWXYZ', l='abcdefghjkmnpqrstuvwxyz', d='23456789', s='@#$!';
  var pwd = u[~~(Math.random()*u.length)] + l[~~(Math.random()*l.length)]
          + l[~~(Math.random()*l.length)] + d[~~(Math.random()*d.length)]
          + d[~~(Math.random()*d.length)] + s[~~(Math.random()*s.length)]
          + l[~~(Math.random()*l.length)] + u[~~(Math.random()*u.length)];
  pwd = pwd.split('').sort(function(){return Math.random()-.5;}).join('');
  document.getElementById('fPassword').value = pwd;
  updateCredBox();
}

function updateCredBox() {
  var u = document.getElementById('fUsername').value.trim();
  var p = document.getElementById('fPassword').value.trim();
  var box = document.getElementById('credBox');
  if (u || p) {
    document.getElementById('credUser').textContent = u || '—';
    document.getElementById('credPass').textContent = p || '—';
    box.style.display = 'block';
  } else {
    box.style.display = 'none';
  }
}

function copyPwd() {
  var pwd = document.getElementById('fPassword').value;
  if (!pwd) { toast('No password to copy', 'err'); return; }
  navigator.clipboard.writeText(pwd).then(function(){
    var icon = document.getElementById('copyIcon');
    icon.className = 'fa fa-check';
    toast('Password copied!', 'ok');
    setTimeout(function(){ icon.className = 'fa fa-copy'; }, 2000);
  }).catch(function(){ toast('Copy failed', 'err'); });
}

// ── SAVE ──────────────────────────────────────────────────────
function saveTeacher() {
  var name     = document.getElementById('fName').value.trim();
  var tid      = document.getElementById('fTeacherId').value.trim();
  var username = document.getElementById('fUsername').value.trim();
  var password = document.getElementById('fPassword').value.trim();
  var dept     = document.getElementById('fDept').value;

  if (!name)     { showAlert('Full Name is required.', 'err'); return; }
  if (!dept)     { showAlert('Department is required.', 'err'); return; }
  if (!tid)      { showAlert('Teacher ID is required. Click the sync button to generate.', 'err'); return; }
  if (!username) { showAlert('Username is required. Click the sync button to generate.', 'err'); return; }
  if (!password) { showAlert('Password is required. Click the sync button to generate.', 'err'); return; }

  var payload = {
    id: 0,
    name:          name,
    teacher_id:    tid,
    username:      username,
    password:      password,
    department:    dept,
    email:         document.getElementById('fEmail').value.trim(),
    phone:         document.getElementById('fPhone').value.trim(),
    qualification: document.getElementById('fQual').value.trim(),
    experience:    document.getElementById('fExp').value.trim(),
    subjects:      document.getElementById('fSubjects').value.trim(),
    status:        document.getElementById('fStatus').value
  };

  var btn = document.querySelector('.btn-save');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';

  fetch('teacher-save.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(payload)
  })
  .then(function(r){ return r.text(); })
  .then(function(txt){
    btn.disabled = false;
    btn.innerHTML = '<i class="fa fa-save"></i> <span id="btnTxt">Save Teacher</span>';
    var d;
    try { d = JSON.parse(txt); } catch(e) { showAlert('Server error: ' + txt, 'err'); return; }
    if (d.success) {
      showAlert('Teacher saved! Login: ' + username + ' / ' + password, 'ok');
      toast('Teacher added successfully!', 'ok');
      setTimeout(function(){ window.location.href = 'manage-teachers.php'; }, 2200);
    } else {
      showAlert(d.message || 'Failed to save teacher.', 'err');
    }
  })
  .catch(function(){ btn.disabled=false; showAlert('Network error. Please try again.', 'err'); });
}

// ── RESET ─────────────────────────────────────────────────────
function resetForm() {
  ['fName','fTeacherId','fUsername','fPassword','fEmail','fPhone','fAddress',
   'fEmergency','fQual','fSpec','fExp','fSubjects','fBio','fSalary','fDob','fJoining']
    .forEach(function(id){ var el=document.getElementById(id); if(el) el.value=''; });
  document.getElementById('fDept').value   = '';
  document.getElementById('fGender').value = '';
  document.getElementById('fStatus').value = 'active';
  document.getElementById('credBox').style.display = 'none';
  document.getElementById('formAlert').style.display = 'none';
  document.getElementById('pageAlert').style.display = 'none';
  // reset photo
  document.getElementById('photoImg').style.display = 'none';
  document.getElementById('photoIcon').style.display = '';
  document.getElementById('fPhoto').value = '';
}

// ── HELPERS ───────────────────────────────────────────────────
function showAlert(msg, type) {
  var el = document.getElementById('formAlert');
  el.className = 'alert alert-' + (type==='ok' ? 'ok' : 'err');
  el.textContent = msg;
  el.style.display = 'block';
  el.scrollIntoView({behavior:'smooth', block:'center'});
  if (type === 'ok') setTimeout(function(){ el.style.display='none'; }, 6000);
}
function toast(msg, type) {
  var t = document.getElementById('toast');
  t.textContent = msg; t.className = 'toast ' + (type||'ok');
  t.style.display = 'block';
  setTimeout(function(){ t.style.display='none'; }, 3500);
}
</script>
</body>
</html>
