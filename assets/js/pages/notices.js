// =============================================
//  NOTICE BOARD PAGE — Full-Width Enhanced UI
// =============================================
const noticesPage = `
  <style>
    .nbp-wrap{background:#f0f2f8}
    .nbp-hero{position:relative;background:linear-gradient(135deg,#1e0a4e 0%,#4c1d95 45%,#7c3aed 80%,#a78bfa 100%);padding:70px 20px 0;text-align:center;overflow:hidden}
    .nbp-hero::before{content:'';position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")}
    .nbp-hero-inner{position:relative;z-index:2;color:white;padding-bottom:36px}
    .nbp-hero-icon{font-size:56px;margin-bottom:16px;filter:drop-shadow(0 4px 16px rgba(0,0,0,.35))}
    .nbp-hero-inner h1{font-size:46px;font-weight:900;margin:0 0 12px;letter-spacing:-2px;text-shadow:0 2px 16px rgba(0,0,0,.3)}
    .nbp-hero-inner p{font-size:16px;opacity:.85;max-width:560px;margin:0 auto 28px;line-height:1.7}
    .nbp-hero-stats{display:flex;justify-content:center;gap:14px;flex-wrap:wrap}
    .nbp-stat-pill{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.28);border-radius:30px;padding:10px 24px;font-size:14px;font-weight:700;color:white;backdrop-filter:blur(6px)}
    .nbp-hero-wave{position:relative;z-index:2;line-height:0;margin-top:10px}
    .nbp-hero-wave svg{width:100%;height:60px;display:block}
    .nbp-filter-bar{background:white;border-bottom:2px solid #e4e8f0;box-shadow:0 4px 18px rgba(0,0,0,.08);position:sticky;top:80px;z-index:100}
    .nbp-filters{display:flex;gap:8px;padding:14px 32px;overflow-x:auto;scrollbar-width:none;align-items:center}
    .nbp-filters::-webkit-scrollbar{display:none}
    .nbp-filter-label{font-size:11px;font-weight:700;color:#aaa;text-transform:uppercase;letter-spacing:1px;margin-right:6px;white-space:nowrap;flex-shrink:0}
    .nbp-filter{display:inline-flex;align-items:center;gap:6px;padding:9px 20px;border:2px solid #e0e6ef;background:#f8fafc;color:#5a6a80;border-radius:24px;cursor:pointer;font-size:13px;font-weight:600;white-space:nowrap;transition:all .22s;outline:none;flex-shrink:0}
    .nbp-filter:hover{border-color:#7c3aed;color:#7c3aed;background:white;transform:translateY(-1px);box-shadow:0 4px 12px rgba(124,58,237,.12)}
    .nbp-filter.nbp-active{background:linear-gradient(135deg,#4c1d95,#7c3aed);border-color:transparent;color:white;box-shadow:0 4px 18px rgba(124,58,237,.38);transform:translateY(-1px)}
    .nbp-main{padding:36px 32px 80px}
    .nbp-loading{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:100px 20px;gap:18px}
    .nbp-dots{display:flex;gap:10px}
    .nbp-dots span{width:14px;height:14px;border-radius:50%;background:#7c3aed;animation:nbpBounce 1.3s ease-in-out infinite}
    .nbp-dots span:nth-child(2){animation-delay:.18s;background:#a78bfa}
    .nbp-dots span:nth-child(3){animation-delay:.36s;background:#c4b5fd}
    @keyframes nbpBounce{0%,80%,100%{transform:scale(.5);opacity:.4}40%{transform:scale(1.2);opacity:1}}
    .nbp-loading p{color:#8a9ab5;font-size:14px;font-weight:500}
    .nbp-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:100px 20px;text-align:center}
    .nbp-empty i{font-size:72px;color:#c5cfe0;margin-bottom:20px}
    .nbp-empty h3{margin:0 0 10px;color:#4a5568;font-size:22px;font-weight:700}
    .nbp-empty p{margin:0;color:#8a9ab5;font-size:14px}
    .nbp-list{display:flex;flex-direction:column;gap:20px;width:100%}
    .nbp-card{background:white;border-radius:20px;overflow:hidden;box-shadow:0 3px 16px rgba(0,0,0,.08);transition:transform .28s,box-shadow .28s;border-left:6px solid #7c3aed;width:100%}
    .nbp-card:hover{transform:translateY(-4px);box-shadow:0 16px 40px rgba(0,0,0,.14)}
    .nbp-card.nbp-urgent-card{border-left-color:#dc2626}
    .nbp-card.nbp-exam-card{border-left-color:#3b82f6}
    .nbp-card.nbp-admission-card{border-left-color:#10b981}
    .nbp-card.nbp-event-card{border-left-color:#f97316}
    .nbp-card.nbp-holiday-card{border-left-color:#eab308}
    .nbp-card.nbp-general-card{border-left-color:#06b6d4}
    .nbp-card-accent{height:4px;width:100%}
    .nbp-card-inner{display:flex;align-items:stretch}
    .nbp-card-icon-col{width:88px;flex-shrink:0;display:flex;align-items:flex-start;justify-content:center;padding:26px 0}
    .nbp-cat-icon{width:54px;height:54px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;color:white;box-shadow:0 4px 14px rgba(0,0,0,.18)}
    .nbp-card-content{flex:1;padding:22px 32px 22px 0;min-width:0}
    .nbp-card-meta{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:10px}
    .nbp-badge{padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;text-transform:capitalize;display:inline-flex;align-items:center;gap:4px}
    .nbp-badge-urgent{background:#fee2e2;color:#991b1b}
    .nbp-badge-high{background:#fef3c7;color:#92400e}
    .nbp-badge-normal{background:#ede9fe;color:#5b21b6}
    .nbp-badge-cat{color:white}
    .nbp-card-date{font-size:12px;color:#aaa;font-weight:500;margin-left:auto;white-space:nowrap;display:flex;align-items:center;gap:5px}
    .nbp-card-date i{color:#a78bfa}
    .nbp-card-title{font-size:20px;font-weight:800;color:#1a202c;margin:0 0 10px;line-height:1.35}
    .nbp-card-desc{font-size:14px;color:#4a5568;line-height:1.8;margin:0 0 18px;white-space:pre-line}
    .nbp-card-divider{border:none;border-top:1px dashed #e8ecf2;margin:0 0 14px}
    .nbp-card-footer{display:flex;align-items:center;gap:10px;flex-wrap:wrap;background:#f8f7ff;margin:0 -32px -22px -0;padding:12px 32px 14px 0;border-top:1px solid #ede9fe;border-radius:0 0 20px 0}
    .nbp-card-tag{display:inline-flex;align-items:center;gap:5px;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:600;background:#ede9fe;color:#6d28d9}
    .nbp-read-btn{margin-left:auto;display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:10px;font-size:12px;font-weight:700;color:white;border:none;cursor:pointer;transition:.2s}
    .nbp-read-btn:hover{opacity:.88;transform:translateY(-1px)}
    .nbp-section-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px}
    .nbp-section-title{font-size:22px;font-weight:800;color:#1a202c;display:flex;align-items:center;gap:10px}
    .nbp-section-title i{color:#7c3aed}
    .nbp-count-badge{background:linear-gradient(135deg,#4c1d95,#7c3aed);color:white;border-radius:20px;padding:5px 16px;font-size:13px;font-weight:700}
    .nbp-urgent-banner{background:linear-gradient(135deg,#7f1d1d,#dc2626);border-radius:16px;padding:18px 28px;margin-bottom:28px;display:flex;align-items:center;gap:16px;color:white;box-shadow:0 6px 20px rgba(220,38,38,.3)}
    .nbp-urgent-banner i.nbp-pulse{font-size:28px;flex-shrink:0;animation:nbpPulse 1.5s ease-in-out infinite}
    @keyframes nbpPulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.7;transform:scale(1.1)}}
    .nbp-urgent-banner-text h3{font-size:16px;font-weight:800;margin:0 0 4px}
    .nbp-urgent-banner-text p{font-size:13px;opacity:.88;margin:0}
    @media(max-width:768px){.nbp-main{padding:24px 16px 60px}.nbp-hero-inner h1{font-size:30px}.nbp-card-icon-col{width:60px}.nbp-cat-icon{width:40px;height:40px;font-size:18px}.nbp-card-content{padding:16px 16px 16px 0}.nbp-card-title{font-size:16px}}
    @media(max-width:480px){.nbp-card-inner{flex-direction:column}.nbp-card-icon-col{width:100%;padding:18px 20px 0;flex-direction:row;justify-content:flex-start;gap:12px;align-items:center}.nbp-card-content{padding:12px 18px 18px}.nbp-card-date{margin-left:0}}
    .nbp-admin-bar{display:flex;gap:8px;margin-top:12px;padding-top:10px;border-top:1px dashed #e0e6ef;flex-wrap:wrap}
    .nbp-abtn{padding:7px 16px;border:none;border-radius:8px;cursor:pointer;font-size:12px;font-weight:700;display:inline-flex;align-items:center;gap:5px;transition:.2s}
    .nbp-abtn-edit{background:#fef3c7;color:#92400e}.nbp-abtn-edit:hover{background:#f59e0b;color:#fff}
    .nbp-abtn-tog{background:#d1fae5;color:#065f46}.nbp-abtn-tog:hover{background:#10b981;color:#fff}
    .nbp-abtn-del{background:#fee2e2;color:#991b1b}.nbp-abtn-del:hover{background:#dc2626;color:#fff}
  </style>
  <div class="nbp-wrap">
    <div class="nbp-hero">
      <div class="nbp-hero-inner">
        <div class="nbp-hero-icon"><i class="fa fa-bullhorn"></i></div>
        <h1>Notice Board</h1>
        <p>Stay updated with the latest announcements, exam schedules, events and important dates at SCTI</p>
        <div class="nbp-hero-stats" id="nbpCounters" style="display:none">
          <div class="nbp-stat-pill"><i class="fa fa-bell"></i><span id="nbpTotal">0</span>&nbsp;Total Notices</div>
          <div class="nbp-stat-pill"><i class="fa fa-circle-exclamation"></i><span id="nbpUrgent">0</span>&nbsp;Urgent</div>
          <div class="nbp-stat-pill"><i class="fa fa-calendar-check"></i><span id="nbpRecent">0</span>&nbsp;This Month</div>
        </div>
      </div>
      <div class="nbp-hero-wave">
        <svg viewBox="0 0 1440 60" preserveAspectRatio="none">
          <path d="M0,30 C480,60 960,0 1440,30 L1440,60 L0,60 Z" fill="#f0f2f8"/>
        </svg>
      </div>
    </div>
    <div class="nbp-filter-bar">
      <div class="nbp-filters">
        <span class="nbp-filter-label">Filter:</span>
        <button class="nbp-filter nbp-active" onclick="nbpFilter(this,'')" type="button"><i class="fa fa-border-all"></i> All</button>
        <button class="nbp-filter" onclick="nbpFilter(this,'urgent')" type="button"><i class="fa fa-circle-exclamation"></i> Urgent</button>
        <button class="nbp-filter" onclick="nbpFilter(this,'admission')" type="button"><i class="fa fa-door-open"></i> Admission</button>
        <button class="nbp-filter" onclick="nbpFilter(this,'exam')" type="button"><i class="fa fa-pen-to-square"></i> Exam</button>
        <button class="nbp-filter" onclick="nbpFilter(this,'event')" type="button"><i class="fa fa-calendar-days"></i> Events</button>
        <button class="nbp-filter" onclick="nbpFilter(this,'holiday')" type="button"><i class="fa fa-umbrella-beach"></i> Holiday</button>
        <button class="nbp-filter" onclick="nbpFilter(this,'general')" type="button"><i class="fa fa-info-circle"></i> General</button>
      </div>
    </div>
    <div class="nbp-main">
      <div id="nbpLoading" class="nbp-loading">
        <div class="nbp-dots"><span></span><span></span><span></span></div>
        <p>Loading notices...</p>
      </div>
      <div id="nbpBoard" style="display:none">
        <div id="nbpUrgentBanner"></div>
        <div class="nbp-section-header">
          <div class="nbp-section-title"><i class="fa fa-list-ul"></i> All Notices</div>
          <div class="nbp-count-badge" id="nbpCountLabel">0 notices</div>
        </div>
        <div id="nbpList" class="nbp-list"></div>
      </div>
      <div id="nbpEmpty" class="nbp-empty" style="display:none">
        <i class="fa fa-bullhorn"></i>
        <h3>No Notices Found</h3>
        <p>No notices in this category right now.</p>
      </div>
    </div>
    <footer style="background:#1e0a4e;color:rgba(255,255,255,.7);text-align:center;padding:20px;font-size:13px;margin-top:40px;border-top:1px solid rgba(255,255,255,.1)">
      <p>© 2025 Sindhuli Community Technical Institute (SCTI) &nbsp;|&nbsp; <a href="#" onclick="loadPage('home');return false;" style="color:#a78bfa;text-decoration:none;">Home</a> &nbsp;|&nbsp; <a href="#" onclick="loadPage('contact');return false;" style="color:#a78bfa;text-decoration:none;">Contact Us</a></p>
    </footer>
  </div>
`;

var nbpAllNotices = [];
var nbpActiveFilter = '';
var nbpIsAdmin = false;

var NBP_CAT = {
  urgent:    { from:'#991b1b', to:'#dc2626', icon:'fa-circle-exclamation', cls:'nbp-urgent-card',    label:'Urgent' },
  admission: { from:'#065f46', to:'#10b981', icon:'fa-door-open',          cls:'nbp-admission-card', label:'Admission' },
  exam:      { from:'#1e3a8a', to:'#3b82f6', icon:'fa-pen-to-square',      cls:'nbp-exam-card',      label:'Exam' },
  event:     { from:'#7c2d12', to:'#f97316', icon:'fa-calendar-days',      cls:'nbp-event-card',     label:'Event' },
  holiday:   { from:'#713f12', to:'#eab308', icon:'fa-umbrella-beach',     cls:'nbp-holiday-card',   label:'Holiday' },
  general:   { from:'#164e63', to:'#06b6d4', icon:'fa-info-circle',        cls:'nbp-general-card',   label:'General' },
  def:       { from:'#4c1d95', to:'#7c3aed', icon:'fa-bell',               cls:'',                   label:'Notice' }
};

function nbpFilter(btn, cat) {
  nbpActiveFilter = cat;
  document.querySelectorAll('.nbp-filter').forEach(function(b){ b.classList.remove('nbp-active'); });
  btn.classList.add('nbp-active');
  nbpRender(cat);
}

function initNotices() {
  if (!document.getElementById('nbpList')) return;
  nbpAllNotices = [];
  var loading = document.getElementById('nbpLoading');
  var board   = document.getElementById('nbpBoard');
  var empty   = document.getElementById('nbpEmpty');
  loading.style.display = 'flex';
  board.style.display   = 'none';
  empty.style.display   = 'none';

  // Check if admin is logged in, then load notices
  fetch('pages/session-info.php')
    .then(function(r){ return r.json(); })
    .then(function(s){
      nbpIsAdmin = s.logged_in && s.user_type === 'admin';
      return fetch('pages/notice-list.php?status=active');
    })
    .then(function(r){ return r.json(); })
    .then(function(result){
      loading.style.display = 'none';
      if (result.success && result.notices && result.notices.length > 0) {
        nbpAllNotices = result.notices;
        nbpUpdateCounters(result.notices);
        nbpRender('');
      } else {
        empty.style.display = 'flex';
      }
    })
    .catch(function(err){
      console.error('Notices error:', err);
      document.getElementById('nbpLoading').style.display = 'none';
      document.getElementById('nbpEmpty').style.display   = 'flex';
    });
}

function nbpUpdateCounters(notices) {
  var now = new Date(), month = now.getMonth(), year = now.getFullYear();
  var recent = notices.filter(function(n){ var d=new Date(n.notice_date); return d.getMonth()===month && d.getFullYear()===year; }).length;
  var tc=document.getElementById('nbpTotal'), uc=document.getElementById('nbpUrgent'), rc=document.getElementById('nbpRecent'), cw=document.getElementById('nbpCounters');
  if(tc) tc.textContent = notices.length;
  if(uc) uc.textContent = notices.filter(function(n){ return n.priority==='urgent'; }).length;
  if(rc) rc.textContent = recent;
  if(cw) cw.style.display = 'flex';
}

function nbpRender(category) {
  var board=document.getElementById('nbpBoard'), list=document.getElementById('nbpList');
  var empty=document.getElementById('nbpEmpty'), label=document.getElementById('nbpCountLabel');
  var urgBanner=document.getElementById('nbpUrgentBanner');
  if (!list) return;
  var items = nbpAllNotices.slice();
  if (category === 'urgent') { items = items.filter(function(n){ return n.priority==='urgent'; }); }
  else if (category) { items = items.filter(function(n){ return n.category===category; }); }
  if (!items.length) { board.style.display='none'; empty.style.display='flex'; return; }
  board.style.display = 'block';
  empty.style.display = 'none';
  if (label) label.textContent = items.length + ' notice' + (items.length!==1?'s':'');
  var urgents = nbpAllNotices.filter(function(n){ return n.priority==='urgent'; });
  if (urgBanner) {
    if (!category && urgents.length > 0) {
      urgBanner.innerHTML = '<div class="nbp-urgent-banner"><i class="fa fa-triangle-exclamation nbp-pulse"></i><div class="nbp-urgent-banner-text"><h3>' + urgents.length + ' Urgent Notice' + (urgents.length>1?'s':'') + '</h3><p>' + nbpEsc(urgents[0].title) + (urgents.length>1?' and '+(urgents.length-1)+' more...':'') + '</p></div></div>';
    } else { urgBanner.innerHTML = ''; }
  }
  list.innerHTML = items.map(nbpBuildCard).join('');
}

function nbpBuildCard(n) {
  var cfg = NBP_CAT[n.category] || NBP_CAT.def;
  var dt  = nbpFmtDate(n.notice_date);
  var pl  = n.priority.charAt(0).toUpperCase() + n.priority.slice(1);
  var pc  = n.priority==='urgent' ? 'nbp-badge-urgent' : n.priority==='high' ? 'nbp-badge-high' : 'nbp-badge-normal';
  var adminBar = nbpIsAdmin ? (
    '<div class="nbp-admin-bar">'
    + '<button class="nbp-abtn nbp-abtn-edit" onclick="nbpEditNotice(' + n.id + ')"><i class="fa fa-edit"></i> Edit</button>'
    + '<button class="nbp-abtn nbp-abtn-tog" onclick="nbpToggleNotice(' + n.id + ',\'' + n.status + '\')">'
    +   '<i class="fa ' + (n.status==='active'?'fa-eye-slash':'fa-eye') + '"></i> '
    +   (n.status==='active' ? 'Deactivate' : 'Activate')
    + '</button>'
    + '<button class="nbp-abtn nbp-abtn-del" onclick="nbpDeleteNotice(' + n.id + ')"><i class="fa fa-trash"></i> Delete</button>'
    + '</div>'
  ) : '';
  return '<div class="nbp-card ' + cfg.cls + '" id="nbp-card-' + n.id + '">'
    + '<div class="nbp-card-accent" style="background:linear-gradient(90deg,' + cfg.from + ',' + cfg.to + ')"></div>'
    + '<div class="nbp-card-inner">'
    +   '<div class="nbp-card-icon-col"><div class="nbp-cat-icon" style="background:linear-gradient(135deg,' + cfg.from + ',' + cfg.to + ')"><i class="fa ' + cfg.icon + '"></i></div></div>'
    +   '<div class="nbp-card-content">'
    +     '<div class="nbp-card-meta">'
    +       '<span class="nbp-badge ' + pc + '"><i class="fa fa-circle-dot"></i> ' + pl + '</span>'
    +       '<span class="nbp-badge nbp-badge-cat" style="background:linear-gradient(135deg,' + cfg.from + ',' + cfg.to + ')"><i class="fa ' + cfg.icon + '"></i> ' + cfg.label + '</span>'
    +       '<span class="nbp-card-date"><i class="fa fa-calendar"></i> ' + dt + '</span>'
    +     '</div>'
    +     '<h3 class="nbp-card-title">' + nbpEsc(n.title) + '</h3>'
    +     '<p class="nbp-card-desc">' + nbpEsc(n.description) + '</p>'
    +     '<div class="nbp-card-footer">'
    +       '<span class="nbp-card-tag"><i class="fa fa-tag"></i> ' + cfg.label + '</span>'
    +       '<span class="nbp-card-tag"><i class="fa fa-calendar-days"></i> ' + dt + '</span>'
    +       '<button class="nbp-read-btn" style="background:linear-gradient(135deg,' + cfg.from + ',' + cfg.to + ')" onclick="nbpScrollTop()"><i class="fa fa-arrow-up"></i> Back to Top</button>'
    +     '</div>'
    +     adminBar
    +   '</div>'
    + '</div>'
    + '</div>';
}

/* ── ADMIN CRUD ── */
function nbpEditNotice(id) {
  var n = nbpAllNotices.find(function(x){ return x.id == id; });
  if (!n) return;
  var title = prompt('Edit Title:', n.title);
  if (title === null) return;
  var desc = prompt('Edit Description:', n.description);
  if (desc === null) return;
  fetch('pages/notice-save.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({
      id: n.id, title: title.trim(), description: desc.trim(),
      category: n.category, priority: n.priority,
      notice_date: (n.notice_date||'').split('T')[0].split(' ')[0],
      status: n.status
    })
  })
  .then(function(r){ return r.json(); })
  .then(function(d){
    if (d.success) { nbpShowToast('Notice updated!', 'ok'); initNotices(); }
    else nbpShowToast(d.message || 'Update failed', 'err');
  });
}

function nbpToggleNotice(id, curStatus) {
  var newStatus = curStatus === 'active' ? 'inactive' : 'active';
  var n = nbpAllNotices.find(function(x){ return x.id == id; });
  if (!n) return;
  fetch('pages/notice-save.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({
      id: n.id, title: n.title, description: n.description,
      category: n.category, priority: n.priority,
      notice_date: (n.notice_date||'').split('T')[0].split(' ')[0],
      status: newStatus
    })
  })
  .then(function(r){ return r.json(); })
  .then(function(d){
    if (d.success) { nbpShowToast('Status changed to ' + newStatus, 'ok'); initNotices(); }
    else nbpShowToast(d.message || 'Failed', 'err');
  });
}

function nbpDeleteNotice(id) {
  if (!confirm('Delete this notice? This cannot be undone.')) return;
  fetch('pages/notice-delete.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({id: parseInt(id)})
  })
  .then(function(r){ return r.json(); })
  .then(function(d){
    if (d.success) {
      nbpShowToast('Notice deleted', 'ok');
      var card = document.getElementById('nbp-card-' + id);
      if (card) card.remove();
      nbpAllNotices = nbpAllNotices.filter(function(x){ return x.id != id; });
      var label = document.getElementById('nbpCountLabel');
      if (label) label.textContent = nbpAllNotices.length + ' notice' + (nbpAllNotices.length!==1?'s':'');
    } else nbpShowToast(d.message || 'Delete failed', 'err');
  });
}

function nbpShowToast(msg, type) {
  var t = document.getElementById('nbpToast');
  if (!t) {
    t = document.createElement('div');
    t.id = 'nbpToast';
    t.style.cssText = 'position:fixed;bottom:22px;right:22px;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:700;z-index:99999;color:#fff;display:none;box-shadow:0 4px 18px rgba(0,0,0,.2)';
    document.body.appendChild(t);
  }
  t.style.background = type === 'ok' ? '#10b981' : '#dc2626';
  t.textContent = msg;
  t.style.display = 'block';
  setTimeout(function(){ t.style.display = 'none'; }, 3000);
}

function nbpScrollTop() { window.scrollTo({ top: 0, behavior: 'smooth' }); }

function nbpFmtDate(d) {
  try { return new Date(d).toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric'}); }
  catch(e){ return d; }
}

function nbpEsc(s) {
  return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
