// =============================================
//  NOTICE BOARD PAGE
// =============================================
const noticesPage = `
  <style>
    .nbp-wrap{background:#f4f6fb;min-height:60vh}
    .nbp-hero{position:relative;background:linear-gradient(135deg,#4c1d95 0%,#7c3aed 60%,#a78bfa 100%);padding:52px 20px 0;text-align:center;overflow:hidden}
    .nbp-hero-inner{position:relative;z-index:2;color:white;padding-bottom:28px}
    .nbp-hero-icon{font-size:48px;margin-bottom:12px;opacity:.9}
    .nbp-hero-inner h1{font-size:38px;font-weight:900;margin:0 0 10px;letter-spacing:-1px}
    .nbp-hero-inner p{font-size:15px;opacity:.82;max-width:480px;margin:0 auto 22px;line-height:1.6}
    .nbp-hero-counters{display:flex;justify-content:center;gap:12px;flex-wrap:wrap;margin-bottom:4px}
    .nbp-counter-pill{display:inline-flex;align-items:center;gap:7px;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:30px;padding:8px 20px;font-size:14px;font-weight:700;color:white}
    .nbp-hero-wave{position:relative;z-index:2;line-height:0}
    .nbp-hero-wave svg{width:100%;height:50px;display:block}
    .nbp-filter-bar{background:white;border-bottom:2px solid #e8ecf2;position:sticky;top:100px;z-index:100;box-shadow:0 3px 12px rgba(0,0,0,.07)}
    .nbp-filters{display:flex;gap:8px;padding:12px 20px;overflow-x:auto;scrollbar-width:none}
    .nbp-filters::-webkit-scrollbar{display:none}
    .nbp-filter{display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border:2px solid #dde3ed;background:#f8fafc;color:#5a6a80;border-radius:20px;cursor:pointer;font-size:13px;font-weight:600;white-space:nowrap;transition:all .2s ease}
    .nbp-filter:hover{border-color:#7c3aed;color:#7c3aed;background:white;transform:translateY(-1px);box-shadow:0 4px 12px rgba(124,58,237,.12)}
    .nbp-filter.active{background:linear-gradient(135deg,var(--fa,#4c1d95),var(--fb,#7c3aed));border-color:transparent;color:white;box-shadow:0 4px 14px rgba(124,58,237,.3)}
    .nbp-content{background:#f4f6fb;padding:36px 0 60px;min-height:400px}
    .nbp-loading{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:80px 20px;gap:16px}
    .nbp-dots{display:flex;gap:10px}
    .nbp-dots span{width:14px;height:14px;border-radius:50%;background:#7c3aed;animation:nbpBounce 1.3s ease-in-out infinite}
    .nbp-dots span:nth-child(2){animation-delay:.18s;background:#a78bfa}
    .nbp-dots span:nth-child(3){animation-delay:.36s;background:#c4b5fd}
    @keyframes nbpBounce{0%,80%,100%{transform:scale(.5);opacity:.4}40%{transform:scale(1.2);opacity:1}}
    .nbp-loading p{color:#8a9ab5;font-size:14px;font-weight:500}
    .nbp-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:80px 20px;text-align:center}
    .nbp-empty i{font-size:64px;color:#c5cfe0;margin-bottom:18px}
    .nbp-empty h3{margin:0 0 8px;color:#4a5568;font-size:20px;font-weight:700}
    .nbp-empty p{margin:0;color:#8a9ab5;font-size:14px}
    .nbp-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}
    .nbp-card{border-radius:16px;overflow:hidden;background:white;box-shadow:0 4px 16px rgba(0,0,0,.09);transition:transform .3s ease,box-shadow .3s ease;display:flex;flex-direction:column;cursor:default}
    .nbp-card:hover{transform:translateY(-6px);box-shadow:0 18px 40px rgba(0,0,0,.15)}
    .nbp-card-top{padding:20px 20px 14px;flex:1}
    .nbp-card-badges{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px}
    .nbp-badge{padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:capitalize}
    .nbp-badge-urgent{background:#f8d7da;color:#721c24}
    .nbp-badge-high{background:#fff3cd;color:#856404}
    .nbp-badge-normal{background:#e8ecf2;color:#4a5568}
    .nbp-badge-cat{color:white;font-size:10px}
    .nbp-card-title{font-size:16px;font-weight:800;color:#1a202c;margin:0 0 10px;line-height:1.4}
    .nbp-card-body{font-size:13px;color:#555;line-height:1.65;margin:0}
    .nbp-card-foot{padding:12px 20px 16px;border-top:1px solid #eef1f6;display:flex;align-items:center;justify-content:space-between;gap:8px}
    .nbp-card-date{display:flex;align-items:center;gap:6px;font-size:12px;color:#8a9ab5;font-weight:500}
    .nbp-card-date i{color:#7c3aed}
    .nbp-read-more{font-size:12px;font-weight:700;color:#7c3aed;text-decoration:none;display:flex;align-items:center;gap:4px;transition:gap .2s}
    .nbp-read-more:hover{gap:8px}
    @media(max-width:768px){
      .nbp-hero{padding:40px 16px 0}
      .nbp-hero-inner h1{font-size:26px}
      .nbp-grid{grid-template-columns:repeat(2,1fr);gap:14px}
    }
    @media(max-width:480px){
      .nbp-grid{grid-template-columns:1fr;gap:12px}
    }
  </style>
  <div class="nbp-wrap">

    <div class="nbp-hero">
      <div class="nbp-hero-inner">
        <div class="nbp-hero-icon"><i class="fa fa-bullhorn"></i></div>
        <h1>Notice Board</h1>
        <p>Stay updated with the latest announcements and important dates at SCTI</p>
        <div class="nbp-hero-counters" id="nbpCounters" style="display:none">
          <div class="nbp-counter-pill"><i class="fa fa-bell"></i><span id="nbpTotal">0</span> Notices</div>
          <div class="nbp-counter-pill"><i class="fa fa-exclamation-circle"></i><span id="nbpUrgent">0</span> Urgent</div>
        </div>
      </div>
      <div class="nbp-hero-wave">
        <svg viewBox="0 0 1440 60" preserveAspectRatio="none">
          <path d="M0,30 C360,60 1080,0 1440,30 L1440,60 L0,60 Z" fill="#f4f6fb"/>
        </svg>
      </div>
    </div>

    <div class="nbp-filter-bar">
      <div class="nbp-filters" id="nbpFilters">
        <button class="nbp-filter active" data-cat="" style="--fa:#4c1d95;--fb:#7c3aed">
          <i class="fa fa-border-all"></i> All
        </button>
        <button class="nbp-filter" data-cat="urgent" style="--fa:#991b1b;--fb:#dc2626">
          <i class="fa fa-exclamation-circle"></i> Urgent
        </button>
        <button class="nbp-filter" data-cat="admission" style="--fa:#065f46;--fb:#10b981">
          <i class="fa fa-door-open"></i> Admission
        </button>
        <button class="nbp-filter" data-cat="exam" style="--fa:#1e3a8a;--fb:#3b82f6">
          <i class="fa fa-pen-to-square"></i> Exam
        </button>
        <button class="nbp-filter" data-cat="event" style="--fa:#7c2d12;--fb:#f97316">
          <i class="fa fa-calendar-star"></i> Events
        </button>
        <button class="nbp-filter" data-cat="holiday" style="--fa:#713f12;--fb:#eab308">
          <i class="fa fa-umbrella-beach"></i> Holiday
        </button>
        <button class="nbp-filter" data-cat="general" style="--fa:#164e63;--fb:#06b6d4">
          <i class="fa fa-info-circle"></i> General
        </button>
      </div>
    </div>

    <div class="nbp-content">
      <div class="container">
        <div id="nbpLoading" class="nbp-loading">
          <div class="nbp-dots"><span></span><span></span><span></span></div>
          <p>Loading notices...</p>
        </div>
        <div id="nbpGrid" class="nbp-grid" style="display:none"></div>
        <div id="nbpEmpty" class="nbp-empty" style="display:none">
          <i class="fa fa-bullhorn"></i>
          <h3>No Notices</h3>
          <p>No notices found in this category.</p>
        </div>
      </div>
    </div>
  </div>
`;

// =============================================
//  STATE
// =============================================
let nbpAllNotices = [];

const NBP_CAT_COLORS = {
  urgent:    { from:'#991b1b', to:'#dc2626' },
  admission: { from:'#065f46', to:'#10b981' },
  exam:      { from:'#1e3a8a', to:'#3b82f6' },
  event:     { from:'#7c2d12', to:'#f97316' },
  holiday:   { from:'#713f12', to:'#eab308' },
  general:   { from:'#164e63', to:'#06b6d4' },
  default:   { from:'#4c1d95', to:'#7c3aed' },
};

// =============================================
//  LOAD
// =============================================
async function loadNotices(category = '') {
  const grid    = document.getElementById('nbpGrid');
  const loading = document.getElementById('nbpLoading');
  const empty   = document.getElementById('nbpEmpty');
  if (!grid) return;

  loading.style.display = 'flex';
  grid.style.display    = 'none';
  empty.style.display   = 'none';

  try {
    const res    = await fetch('pages/notice-list.php?status=active');
    const result = await res.json();

    if (result.success && result.notices.length > 0) {
      nbpAllNotices = result.notices;

      // Update counters
      const urgent = result.notices.filter(n => n.priority === 'urgent').length;
      const tc = document.getElementById('nbpTotal');
      const uc = document.getElementById('nbpUrgent');
      const cw = document.getElementById('nbpCounters');
      if (tc) tc.textContent = result.notices.length;
      if (uc) uc.textContent = urgent;
      if (cw) cw.style.display = 'flex';

      renderNotices(category);
    } else {
      empty.style.display = 'flex';
    }
  } catch(err) {
    console.error('Notices error:', err);
    if (empty) empty.style.display = 'flex';
  } finally {
    loading.style.display = 'none';
  }
}

function renderNotices(category) {
  const grid  = document.getElementById('nbpGrid');
  const empty = document.getElementById('nbpEmpty');
  if (!grid) return;

  let list = nbpAllNotices;
  if (category) list = list.filter(n => n.category === category || (category === 'urgent' && n.priority === 'urgent'));

  if (!list.length) {
    grid.style.display  = 'none';
    empty.style.display = 'flex';
    return;
  }

  grid.innerHTML    = list.map(n => buildNoticeCard(n)).join('');
  grid.style.display = 'grid';
  empty.style.display = 'none';

  // Fade in
  grid.style.opacity   = '0';
  grid.style.transform = 'translateY(18px)';
  setTimeout(() => {
    grid.style.transition = 'opacity .4s ease, transform .4s ease';
    grid.style.opacity    = '1';
    grid.style.transform  = 'translateY(0)';
  }, 30);
}

function buildNoticeCard(n) {
  const c    = NBP_CAT_COLORS[n.category] || NBP_CAT_COLORS.default;
  const date = new Date(n.notice_date).toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric'});
  const priLabel = n.priority.charAt(0).toUpperCase() + n.priority.slice(1);
  const catLabel = n.category.charAt(0).toUpperCase() + n.category.slice(1);
  const priClass = n.priority === 'urgent' ? 'nbp-badge-urgent' : n.priority === 'high' ? 'nbp-badge-high' : 'nbp-badge-normal';

  return `
    <div class="nbp-card">
      <div class="nbp-card-top">
        <div class="nbp-card-badges">
          <span class="nbp-badge ${priClass}">${priLabel}</span>
          <span class="nbp-badge nbp-badge-cat" style="background:linear-gradient(135deg,${c.from},${c.to})">${catLabel}</span>
        </div>
        <h3 class="nbp-card-title">${escNbp(n.title)}</h3>
        <p class="nbp-card-body">${escNbp(n.description)}</p>
      </div>
      <div class="nbp-card-foot">
        <div class="nbp-card-date"><i class="fa fa-calendar"></i> ${date}</div>
        <a href="#" class="nbp-read-more" onclick="return false">Read More <i class="fa fa-arrow-right"></i></a>
      </div>
    </div>`;
}

function escNbp(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// =============================================
//  FILTERS
// =============================================
function setupNoticeFilters() {
  document.querySelectorAll('.nbp-filter').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.nbp-filter').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      renderNotices(btn.dataset.cat);
    });
  });
}

// =============================================
//  INIT
// =============================================
function initNotices() {
  if (document.getElementById('nbpGrid')) {
    loadNotices();
    setupNoticeFilters();
  }
}
