<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../index.php'); exit();
}
$fullName = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Teacher';
$days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday'];
$today = $days[date('w') == 0 ? 0 : (date('w') == 6 ? 5 : date('w'))];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Schedule | SCTI Teacher Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box;}
    body{font-family:'Segoe UI',sans-serif;background:#f0f4f8;min-height:100vh;}
    .top-bar{background:#1a5c2e;color:white;padding:7px 20px;font-size:13px;}
    .sch-header{background:linear-gradient(135deg,#28a745,#20c997);color:#fff;padding:22px 28px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 18px rgba(40,167,69,.3);}
    .sch-header h1{font-size:22px;font-weight:700;display:flex;align-items:center;gap:10px;margin:0 0 3px;}
    .sch-header .bc{font-size:12px;color:rgba(255,255,255,.8);}
    .sch-header .bc a{color:#fff;text-decoration:none;}
    .btn-hdr{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;border:none;transition:.2s;text-decoration:none;background:rgba(255,255,255,.18);color:#fff;}
    .btn-hdr:hover{background:rgba(255,255,255,.32);}

    /* STATS */
    .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;padding:18px 28px;background:#fff;border-bottom:1px solid #e9ecef;}
    .stat{display:flex;align-items:center;gap:12px;background:#f8f9fa;border-radius:10px;padding:12px 16px;}
    .stat-ico{width:42px;height:42px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:17px;color:#fff;flex-shrink:0;}
    .ico-green{background:linear-gradient(135deg,#28a745,#20c997);}
    .ico-teal{background:linear-gradient(135deg,#17a2b8,#138496);}
    .ico-orange{background:linear-gradient(135deg,#fd7e14,#ffc107);}
    .ico-purple{background:linear-gradient(135deg,#6f42c1,#e83e8c);}
    .stat-val{font-size:24px;font-weight:700;color:#222;line-height:1;}
    .stat-lbl{font-size:11px;color:#999;margin-top:2px;}

    .main{padding:24px 28px;}

    /* DAY FILTER */
    .day-filter{display:flex;gap:8px;margin-bottom:22px;flex-wrap:wrap;}
    .day-btn{padding:8px 18px;border-radius:25px;border:2px solid #e0e6ef;background:#f8fafc;color:#666;font-size:13px;font-weight:600;cursor:pointer;transition:.2s;}
    .day-btn:hover{border-color:#28a745;color:#28a745;}
    .day-btn.active{background:linear-gradient(135deg,#28a745,#20c997);border-color:transparent;color:white;box-shadow:0 4px 14px rgba(40,167,69,.3);}

    /* TODAY GRID */
    .section-title{font-size:16px;font-weight:700;color:#1a5c2e;margin-bottom:14px;display:flex;align-items:center;gap:8px;}
    .today-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px;margin-bottom:28px;}
    .cls-card{background:white;border-radius:14px;overflow:hidden;box-shadow:0 3px 12px rgba(0,0,0,.08);cursor:pointer;transition:transform .25s,box-shadow .25s;position:relative;}
    .cls-card:hover{transform:translateY(-6px);box-shadow:0 14px 32px rgba(40,167,69,.2);}
    .cls-card.active-now{border:2px solid #28a745;}
    .cls-card-top{height:6px;}
    .cls-card-top.green{background:linear-gradient(90deg,#28a745,#20c997);}
    .cls-card-top.teal{background:linear-gradient(90deg,#17a2b8,#138496);}
    .cls-card-top.orange{background:linear-gradient(90deg,#fd7e14,#ffc107);}
    .cls-card-top.purple{background:linear-gradient(90deg,#6f42c1,#e83e8c);}
    .cls-card-top.blue{background:linear-gradient(90deg,#004080,#0059b3);}
    .cls-card-body{padding:16px;}
    .cls-time{font-size:11px;font-weight:700;color:#28a745;margin-bottom:6px;display:flex;align-items:center;gap:5px;}
    .cls-subject{font-size:15px;font-weight:800;color:#1a202c;margin-bottom:4px;}
    .cls-meta{font-size:12px;color:#888;display:flex;flex-direction:column;gap:3px;}
    .cls-meta span{display:flex;align-items:center;gap:5px;}
    .cls-meta i{color:#28a745;width:13px;}
    .badge-now{background:#28a745;color:white;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;position:absolute;top:14px;right:14px;}
    .badge-free{background:#e9ecef;color:#aaa;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;position:absolute;top:14px;right:14px;}

    /* WEEKLY TABLE */
    .tbl-wrap{background:white;border-radius:14px;box-shadow:0 3px 12px rgba(0,0,0,.08);overflow:hidden;}
    .tbl-scroll{overflow-x:auto;}
    table.sch-tbl{width:100%;border-collapse:collapse;min-width:700px;}
    .sch-tbl th{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:13px 10px;text-align:center;font-size:13px;font-weight:700;}
    .sch-tbl th.today-hd{background:linear-gradient(135deg,#1a5c2e,#28a745);}
    .sch-tbl td{padding:8px;border:1px solid #f0f4f0;text-align:center;vertical-align:middle;}
    .sch-tbl tr:hover td{background:#f6fff8;}
    .time-cell{font-weight:700;color:#28a745;background:#f8fdf8 !important;font-size:12px;white-space:nowrap;padding:10px 14px;}
    .slot{background:linear-gradient(135deg,#28a745,#20c997);color:white;padding:9px 8px;border-radius:8px;font-size:11px;line-height:1.4;cursor:pointer;transition:.2s;}
    .slot:hover{transform:scale(1.04);box-shadow:0 4px 12px rgba(40,167,69,.35);}
    .slot .s-name{font-weight:700;font-size:12px;}
    .slot .s-room{opacity:.85;font-size:10px;margin-top:2px;}
    .slot-teal{background:linear-gradient(135deg,#17a2b8,#138496);}
    .slot-orange{background:linear-gradient(135deg,#fd7e14,#ffc107);color:#333;}
    .slot-purple{background:linear-gradient(135deg,#6f42c1,#e83e8c);}
    .slot-blue{background:linear-gradient(135deg,#004080,#0059b3);}
    .free-cell{color:#ccc;font-size:11px;font-style:italic;}
    .today-td{background:#f0fff4 !important;}

    /* MODAL */
    .modal-bg{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;align-items:center;justify-content:center;}
    .modal-bg.open{display:flex;}
    .modal{background:white;border-radius:16px;width:420px;max-width:95vw;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.25);animation:modalIn .25s ease;}
    @keyframes modalIn{from{transform:scale(.9);opacity:0}to{transform:scale(1);opacity:1}}
    .modal-head{padding:20px 22px;display:flex;align-items:center;gap:12px;}
    .modal-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;color:white;flex-shrink:0;}
    .modal-head h3{font-size:17px;font-weight:800;color:#1a202c;margin:0 0 2px;}
    .modal-head p{font-size:12px;color:#888;margin:0;}
    .modal-close{margin-left:auto;background:none;border:none;font-size:20px;color:#aaa;cursor:pointer;padding:4px;}
    .modal-close:hover{color:#333;}
    .modal-body{padding:0 22px 22px;}
    .modal-row{display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid #f0f0f0;font-size:13px;color:#555;}
    .modal-row:last-child{border-bottom:none;}
    .modal-row i{color:#28a745;width:16px;}
    .modal-row strong{color:#222;}

    footer{background:#1a5c2e;color:white;text-align:center;padding:12px;font-size:13px;margin-top:28px;}
    @media(max-width:860px){.stats{grid-template-columns:repeat(2,1fr);}.main{padding:16px;}}
  </style>
</head>
<body>
<div class="top-bar"><marquee>My Teaching Schedule — Weekly timetable and class overview | <?php echo htmlspecialchars($fullName); ?></marquee></div>

<div class="sch-header">
  <div>
    <h1><i class="fa fa-calendar-alt"></i> My Schedule</h1>
    <div class="bc"><a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / My Schedule</div>
  </div>
  <a href="../dashboards/teacher-dashboard.php" class="btn-hdr"><i class="fa fa-arrow-left"></i> Back</a>
</div>

<div class="stats">
  <div class="stat"><div class="stat-ico ico-green"><i class="fa fa-calendar-day"></i></div><div><div class="stat-val">4</div><div class="stat-lbl">Classes Today</div></div></div>
  <div class="stat"><div class="stat-ico ico-teal"><i class="fa fa-book-open"></i></div><div><div class="stat-val">5</div><div class="stat-lbl">Subjects Teaching</div></div></div>
  <div class="stat"><div class="stat-ico ico-orange"><i class="fa fa-clock"></i></div><div><div class="stat-val">18</div><div class="stat-lbl">Hours / Week</div></div></div>
  <div class="stat"><div class="stat-ico ico-purple"><i class="fa fa-users"></i></div><div><div class="stat-val">120</div><div class="stat-lbl">Total Students</div></div></div>
</div>

<div class="main">

  <!-- Day filter -->
  <div class="day-filter" id="dayFilter">
    <button class="day-btn" onclick="filterDay('Sunday',this)">Sun</button>
    <button class="day-btn" onclick="filterDay('Monday',this)">Mon</button>
    <button class="day-btn" onclick="filterDay('Tuesday',this)">Tue</button>
    <button class="day-btn" onclick="filterDay('Wednesday',this)">Wed</button>
    <button class="day-btn" onclick="filterDay('Thursday',this)">Thu</button>
    <button class="day-btn" onclick="filterDay('Friday',this)">Fri</button>
  </div>

  <!-- Today's classes grid -->
  <div class="section-title"><i class="fa fa-sun"></i> <span id="dayLabel">Today's Classes</span></div>
  <div class="today-grid" id="todayGrid"></div>

  <!-- Weekly timetable -->
  <div class="section-title" style="margin-top:8px;"><i class="fa fa-table"></i> Weekly Timetable</div>
  <div class="tbl-wrap">
    <div class="tbl-scroll">
      <table class="sch-tbl">
        <thead>
          <tr>
            <th>Time</th>
            <th id="th-Sun">Sunday</th>
            <th id="th-Mon">Monday</th>
            <th id="th-Tue">Tuesday</th>
            <th id="th-Wed">Wednesday</th>
            <th id="th-Thu">Thursday</th>
            <th id="th-Fri">Friday</th>
          </tr>
        </thead>
        <tbody id="tblBody"></tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal-bg" id="modalBg" onclick="closeModal(event)">
  <div class="modal">
    <div class="modal-head">
      <div class="modal-icon" id="mIcon"></div>
      <div><h3 id="mSubject"></h3><p id="mDay"></p></div>
      <button class="modal-close" onclick="closeModalDirect()"><i class="fa fa-times"></i></button>
    </div>
    <div class="modal-body">
      <div class="modal-row"><i class="fa fa-clock"></i><span><strong>Time:</strong> <span id="mTime"></span></span></div>
      <div class="modal-row"><i class="fa fa-door-open"></i><span><strong>Room:</strong> <span id="mRoom"></span></span></div>
      <div class="modal-row"><i class="fa fa-users"></i><span><strong>Students:</strong> <span id="mStudents"></span></span></div>
      <div class="modal-row"><i class="fa fa-tag"></i><span><strong>Type:</strong> <span id="mType"></span></span></div>
    </div>
  </div>
</div>

<footer>© 2025 SCTI — Teacher Portal</footer>

<script>
var schedule = {
  'Sunday':    [ {time:'9:00–10:30',  subject:'Prog. Fundamentals', room:'Room 101', students:35, type:'Lecture',  color:'green'},
                 {time:'1:30–3:00',   subject:'Web Development',    room:'Lab 3',    students:32, type:'Lab',      color:'teal'},
                 {time:'3:30–5:00',   subject:'Networking Basics',  room:'Room 104', students:28, type:'Lecture',  color:'orange'} ],
  'Monday':    [ {time:'11:00–12:30', subject:'Database Mgmt',      room:'Room 203', students:28, type:'Lecture',  color:'green'},
                 {time:'3:30–5:00',   subject:'Data Structures',    room:'Room 102', students:25, type:'Lecture',  color:'purple'} ],
  'Tuesday':   [ {time:'9:00–10:30',  subject:'Prog. Fundamentals', room:'Room 101', students:35, type:'Lecture',  color:'green'},
                 {time:'1:30–3:00',   subject:'Web Development',    room:'Lab 3',    students:32, type:'Lab',      color:'teal'} ],
  'Wednesday': [ {time:'11:00–12:30', subject:'Database Mgmt',      room:'Room 203', students:28, type:'Lecture',  color:'green'},
                 {time:'3:30–5:00',   subject:'Data Structures',    room:'Room 102', students:25, type:'Lecture',  color:'purple'} ],
  'Thursday':  [ {time:'9:00–10:30',  subject:'Prog. Fundamentals', room:'Room 101', students:35, type:'Lecture',  color:'green'},
                 {time:'1:30–3:00',   subject:'Web Development',    room:'Lab 3',    students:32, type:'Lab',      color:'teal'} ],
  'Friday':    []
};

var allTimes = ['9:00–10:30','11:00–12:30','1:30–3:00','3:30–5:00'];
var allDays  = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday'];
var today    = '<?php echo $today; ?>';
var activeDay = today;

function getSlot(day, time) {
  var list = schedule[day] || [];
  for (var i=0; i<list.length; i++) { if (list[i].time === time) return list[i]; }
  return null;
}

function renderTodayGrid(day) {
  var grid = document.getElementById('todayGrid');
  var list = schedule[day] || [];
  document.getElementById('dayLabel').textContent = (day === today ? "Today's Classes" : day + "'s Classes") + ' (' + day + ')';
  if (!list.length) {
    grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:40px;color:#aaa;"><i class="fa fa-coffee" style="font-size:40px;display:block;margin-bottom:12px;"></i>No classes scheduled</div>';
    return;
  }
  grid.innerHTML = list.map(function(c, idx) {
    var isNow = (day === today && idx === 0);
    return '<div class="cls-card' + (isNow ? ' active-now' : '') + '" onclick="openModal(' + JSON.stringify(c).replace(/"/g,'&quot;') + ',\'' + day + '\')">'
      + '<div class="cls-card-top ' + c.color + '"></div>'
      + (isNow ? '<span class="badge-now">NOW</span>' : '')
      + '<div class="cls-card-body">'
      +   '<div class="cls-time"><i class="fa fa-clock"></i>' + c.time + '</div>'
      +   '<div class="cls-subject">' + c.subject + '</div>'
      +   '<div class="cls-meta">'
      +     '<span><i class="fa fa-door-open"></i>' + c.room + '</span>'
      +     '<span><i class="fa fa-users"></i>' + c.students + ' Students</span>'
      +     '<span><i class="fa fa-tag"></i>' + c.type + '</span>'
      +   '</div>'
      + '</div></div>';
  }).join('');
}

function renderTable() {
  var body = document.getElementById('tblBody');
  var rows = '';
  allTimes.forEach(function(time) {
    rows += '<tr><td class="time-cell">' + time + '</td>';
    allDays.forEach(function(day) {
      var isToday = (day === today);
      var slot = getSlot(day, time);
      if (slot) {
        rows += '<td class="' + (isToday ? 'today-td' : '') + '">'
          + '<div class="slot slot-' + slot.color + '" onclick="openModal(' + JSON.stringify(slot).replace(/"/g,'&quot;') + ',\'' + day + '\')">'
          + '<div class="s-name">' + slot.subject + '</div>'
          + '<div class="s-room">' + slot.room + '</div>'
          + '</div></td>';
      } else {
        rows += '<td class="' + (isToday ? 'today-td' : '') + '"><span class="free-cell">Free</span></td>';
      }
    });
    rows += '</tr>';
  });
  body.innerHTML = rows;
  // highlight today header
  var map = {Sunday:'th-Sun',Monday:'th-Mon',Tuesday:'th-Tue',Wednesday:'th-Wed',Thursday:'th-Thu',Friday:'th-Fri'};
  var th = document.getElementById(map[today]);
  if (th) th.classList.add('today-hd');
}

function filterDay(day, btn) {
  activeDay = day;
  document.querySelectorAll('.day-btn').forEach(function(b){ b.classList.remove('active'); });
  btn.classList.add('active');
  renderTodayGrid(day);
}

function openModal(c, day) {
  var colors = {green:'linear-gradient(135deg,#28a745,#20c997)',teal:'linear-gradient(135deg,#17a2b8,#138496)',orange:'linear-gradient(135deg,#fd7e14,#ffc107)',purple:'linear-gradient(135deg,#6f42c1,#e83e8c)',blue:'linear-gradient(135deg,#004080,#0059b3)'};
  document.getElementById('mIcon').style.background = colors[c.color] || colors.green;
  document.getElementById('mIcon').innerHTML = '<i class="fa fa-book-open"></i>';
  document.getElementById('mSubject').textContent = c.subject;
  document.getElementById('mDay').textContent = day;
  document.getElementById('mTime').textContent = c.time;
  document.getElementById('mRoom').textContent = c.room;
  document.getElementById('mStudents').textContent = c.students + ' Students';
  document.getElementById('mType').textContent = c.type;
  document.getElementById('modalBg').classList.add('open');
}

function closeModal(e) { if (e.target === document.getElementById('modalBg')) closeModalDirect(); }
function closeModalDirect() { document.getElementById('modalBg').classList.remove('open'); }

// Init
(function(){
  // set active day button
  var dayMap = {Sunday:0,Monday:1,Tuesday:2,Wednesday:3,Thursday:4,Friday:5};
  var btns = document.querySelectorAll('.day-btn');
  var idx = dayMap[today];
  if (btns[idx]) btns[idx].classList.add('active');
  renderTodayGrid(today);
  renderTable();
})();
</script>
</body>
</html>
