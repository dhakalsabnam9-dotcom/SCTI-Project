// =============================================
//  PROGRAMS PAGE
// =============================================
const programsPage = `
  <style>
    .prg-wrap{background:#f4f6fb;min-height:60vh}
    .prg-hero{position:relative;background:linear-gradient(135deg,#004080 0%,#0059b3 60%,#1a6fc4 100%);padding:52px 20px 0;text-align:center;overflow:hidden}
    .prg-hero-inner{position:relative;z-index:2;color:white;padding-bottom:28px}
    .prg-hero-icon{font-size:48px;margin-bottom:12px;opacity:.9}
    .prg-hero-inner h1{font-size:38px;font-weight:900;margin:0 0 10px;letter-spacing:-1px}
    .prg-hero-inner p{font-size:15px;opacity:.82;max-width:480px;margin:0 auto 22px;line-height:1.6}
    .prg-hero-counters{display:flex;justify-content:center;gap:12px;flex-wrap:wrap;margin-bottom:4px}
    .prg-counter-pill{display:inline-flex;align-items:center;gap:7px;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:30px;padding:8px 20px;font-size:14px;font-weight:700;color:white}
    .prg-hero-wave{position:relative;z-index:2;line-height:0}
    .prg-hero-wave svg{width:100%;height:50px;display:block}
    .prg-filter-bar{background:white;border-bottom:2px solid #e8ecf2;position:sticky;top:100px;z-index:100;box-shadow:0 3px 12px rgba(0,0,0,.07)}
    .prg-filters{display:flex;gap:8px;padding:12px 20px;overflow-x:auto;scrollbar-width:none}
    .prg-filters::-webkit-scrollbar{display:none}
    .prg-filter{display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border:2px solid #dde3ed;background:#f8fafc;color:#5a6a80;border-radius:20px;cursor:pointer;font-size:13px;font-weight:600;white-space:nowrap;transition:all .2s ease}
    .prg-filter:hover{border-color:#004080;color:#004080;background:white;transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,64,128,.12)}
    .prg-filter.active{background:linear-gradient(135deg,#004080,#0059b3);border-color:transparent;color:white;box-shadow:0 4px 14px rgba(0,64,128,.3)}
    .prg-content{background:#f4f6fb;padding:36px 0 60px;min-height:400px}
    .prg-loading{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:80px 20px;gap:16px}
    .prg-dots{display:flex;gap:10px}
    .prg-dots span{width:14px;height:14px;border-radius:50%;background:#004080;animation:prgBounce 1.3s ease-in-out infinite}
    .prg-dots span:nth-child(2){animation-delay:.18s;background:#0059b3}
    .prg-dots span:nth-child(3){animation-delay:.36s;background:#1a6fc4}
    @keyframes prgBounce{0%,80%,100%{transform:scale(.5);opacity:.4}40%{transform:scale(1.2);opacity:1}}
    .prg-loading p{color:#8a9ab5;font-size:14px;font-weight:500}
    .prg-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:80px 20px;text-align:center}
    .prg-empty i{font-size:64px;color:#c5cfe0;margin-bottom:18px}
    .prg-empty h3{margin:0 0 8px;color:#4a5568;font-size:20px;font-weight:700}
    .prg-empty p{margin:0;color:#8a9ab5;font-size:14px}
    .prg-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:24px}
    .prg-card{background:white;border-radius:20px;overflow:hidden;box-shadow:0 4px 18px rgba(0,0,0,.08);transition:transform .3s ease,box-shadow .3s ease;display:flex;flex-direction:column}
    .prg-card:hover{transform:translateY(-7px);box-shadow:0 20px 44px rgba(0,0,0,.15)}
    .prg-card-head{padding:28px 24px 20px;display:flex;align-items:flex-start;gap:16px}
    .prg-card-icon{width:56px;height:56px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:24px;color:white;flex-shrink:0}
    .prg-card-title{flex:1;min-width:0}
    .prg-card-title h3{margin:0 0 6px;font-size:16px;font-weight:800;color:#1a2332;line-height:1.3}
    .prg-type-badge{display:inline-block;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px}
    .prg-card-body{padding:0 24px 20px;flex:1}
    .prg-desc{font-size:13px;color:#5a6a80;line-height:1.6;margin:0 0 16px}
    .prg-meta{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:16px}
    .prg-meta-item{display:inline-flex;align-items:center;gap:5px;background:#f0f4f8;border-radius:8px;padding:5px 10px;font-size:12px;color:#4a5568;font-weight:600}
    .prg-meta-item i{color:#004080;font-size:11px}
    .prg-card-footer{padding:16px 24px;border-top:1px solid #eef1f6;display:flex;gap:10px}
    .prg-btn{flex:1;padding:10px;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;border:none;transition:.2s;text-align:center;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:6px}
    .prg-btn-primary{background:linear-gradient(135deg,#004080,#0059b3);color:white}
    .prg-btn-primary:hover{transform:translateY(-1px);box-shadow:0 6px 16px rgba(0,64,128,.3)}
    .prg-btn-outline{background:transparent;color:#004080;border:2px solid #004080}
    .prg-btn-outline:hover{background:#004080;color:white}
    .prg-admission{background:white;border-radius:20px;padding:36px;margin-top:32px;box-shadow:0 4px 18px rgba(0,0,0,.07)}
    .prg-admission-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;margin-top:24px}
    .prg-adm-card{background:#f8fafc;border-radius:14px;padding:24px;border-left:4px solid #004080}
    .prg-adm-card h3{margin:0 0 12px;font-size:15px;font-weight:700;color:#1a2332;display:flex;align-items:center;gap:8px}
    .prg-adm-card h3 i{color:#004080}
    .prg-adm-card ul{margin:0;padding-left:18px}
    .prg-adm-card ul li{font-size:13px;color:#5a6a80;margin-bottom:6px;line-height:1.5}
    .prg-cta{text-align:center;margin-top:32px;padding:32px;background:linear-gradient(135deg,#004080,#0059b3);border-radius:20px;color:white}
    .prg-cta h2{margin:0 0 8px;font-size:24px;font-weight:800}
    .prg-cta p{margin:0 0 20px;opacity:.85;font-size:14px}
    .prg-cta-btns{display:flex;gap:12px;justify-content:center;flex-wrap:wrap}
    .prg-cta-btn{padding:12px 28px;border-radius:12px;font-size:14px;font-weight:700;cursor:pointer;border:none;transition:.2s;text-decoration:none;display:inline-flex;align-items:center;gap:8px}
    .prg-cta-btn.white{background:white;color:#004080}
    .prg-cta-btn.white:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(0,0,0,.2)}
    .prg-cta-btn.ghost{background:rgba(255,255,255,.18);color:white;border:2px solid rgba(255,255,255,.4)}
    .prg-cta-btn.ghost:hover{background:rgba(255,255,255,.3)}
    @media(max-width:768px){
      .prg-hero{padding:40px 16px 0}
      .prg-hero-inner h1{font-size:26px}
      .prg-grid{grid-template-columns:1fr;gap:16px}
    }
  </style>
  <div class="prg-wrap">

    <!-- HERO -->
    <div class="prg-hero">
      <div class="prg-hero-inner">
        <div class="prg-hero-icon"><i class="fa fa-graduation-cap"></i></div>
        <h1>Programs &amp; Courses</h1>
        <p>Explore our comprehensive technical education programs designed for your success</p>
        <div class="prg-hero-counters" id="prgCounters" style="display:none">
          <div class="prg-counter-pill">
            <i class="fa fa-book-open"></i>
            <span id="prgTotalPrograms">0</span> Programs
          </div>
          <div class="prg-counter-pill">
            <i class="fa fa-layer-group"></i>
            <span id="prgTotalTypes">0</span> Types
          </div>
        </div>
      </div>
      <div class="prg-hero-wave">
        <svg viewBox="0 0 1440 60" preserveAspectRatio="none">
          <path d="M0,30 C360,60 1080,0 1440,30 L1440,60 L0,60 Z" fill="#f4f6fb"/>
        </svg>
      </div>
    </div>

    <!-- FILTER BAR -->
    <div class="prg-filter-bar">
      <div class="prg-filters" id="programFilters">
        <button class="prg-filter active" data-type="">
          <i class="fa fa-border-all"></i> All
        </button>
      </div>
    </div>

    <!-- CONTENT -->
    <div class="prg-content">
      <div class="container">

        <div id="programLoading" class="prg-loading">
          <div class="prg-dots"><span></span><span></span><span></span></div>
          <p>Loading programs...</p>
        </div>

        <div id="programGrid" class="prg-grid" style="display:none"></div>

        <div id="programEmpty" class="prg-empty" style="display:none">
          <i class="fa fa-graduation-cap"></i>
          <h3>No Programs Found</h3>
          <p>No programs available in this category.</p>
        </div>

        <!-- ADMISSION INFO (always shown) -->
        <div id="prgAdmission" class="prg-admission" style="display:none">
          <div style="text-align:center;margin-bottom:8px">
            <h2 style="font-size:22px;font-weight:800;color:#1a2332;margin:0 0 6px">Admission &amp; Benefits</h2>
            <p style="color:#5a6a80;font-size:14px;margin:0">Everything you need to know about joining SCTI</p>
          </div>
          <div class="prg-admission-grid">
            <div class="prg-adm-card">
              <h3><i class="fa fa-user-check"></i> Eligibility &amp; Admission</h3>
              <ul>
                <li>Minimum SEE GPA of 2.0 for Diploma programs</li>
                <li>Minimum +2 or equivalent for B.Tech. Ed. programs</li>
                <li>Strong foundation in Mathematics and Science</li>
                <li>Interest in technical and vocational fields</li>
              </ul>
            </div>
            <div class="prg-adm-card">
              <h3><i class="fa fa-star"></i> Why Choose SCTI?</h3>
              <ul>
                <li>Industry-aligned curriculum</li>
                <li>Hands-on training with modern equipment</li>
                <li>Experienced faculty and industry experts</li>
                <li>Career guidance and job placement support</li>
                <li>Focus on practical and technical skills</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- CTA -->
        <div id="prgCta" class="prg-cta" style="display:none">
          <h2>Ready to Join SCTI?</h2>
          <p>Take the first step toward a rewarding technical career</p>
          <div class="prg-cta-btns">
            <a href="#" onclick="loadPage('signup'); return false;" class="prg-cta-btn white">
              <i class="fa fa-user-plus"></i> Apply Now
            </a>
            <a href="#" onclick="loadPage('contact'); return false;" class="prg-cta-btn ghost">
              <i class="fa fa-envelope"></i> Contact Us
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>
`;

// =============================================
//  STATE
// =============================================
let allPrograms = [];

const PRG_TYPE_ICONS = {
  'diploma':    'fa-certificate',
  'bachelor':   'fa-graduation-cap',
  'b.tech':     'fa-laptop-code',
  'b.tech ed.': 'fa-chalkboard-teacher',
  'certificate':'fa-award',
  'default':    'fa-book-open'
};

const PRG_PALETTES = [
  { from:'#004080', to:'#0059b3', light:'#e8f0fe' },
  { from:'#065f46', to:'#10b981', light:'#d1fae5' },
  { from:'#7c2d12', to:'#f97316', light:'#ffedd5' },
  { from:'#4c1d95', to:'#8b5cf6', light:'#ede9fe' },
  { from:'#881337', to:'#f43f5e', light:'#ffe4e6' },
  { from:'#164e63', to:'#06b6d4', light:'#cffafe' },
  { from:'#713f12', to:'#eab308', light:'#fef9c3' },
  { from:'#134e4a', to:'#14b8a6', light:'#ccfbf1' },
];
let _prgPi = 0;
const _prgPm = {};
function getPrgPalette(key) {
  if (!_prgPm[key]) { _prgPm[key] = PRG_PALETTES[_prgPi % PRG_PALETTES.length]; _prgPi++; }
  return _prgPm[key];
}

function getPrgIcon(type) {
  if (!type) return PRG_TYPE_ICONS.default;
  const t = type.toLowerCase();
  for (const k in PRG_TYPE_ICONS) {
    if (t.includes(k)) return PRG_TYPE_ICONS[k];
  }
  return PRG_TYPE_ICONS.default;
}

// =============================================
//  LOAD PROGRAMS
// =============================================
async function loadPrograms(type) {
  const grid    = document.getElementById('programGrid');
  const loading = document.getElementById('programLoading');
  const empty   = document.getElementById('programEmpty');
  const adm     = document.getElementById('prgAdmission');
  const cta     = document.getElementById('prgCta');

  loading.style.display = 'flex';
  grid.style.display    = 'none';
  empty.style.display   = 'none';

  try {
    if (allPrograms.length === 0) {
      const res  = await fetch('pages/program-list.php?status=active');
      const data = await res.json();
      if (data.success) {
        allPrograms = data.programs;
        buildProgramFilters(data.programs);
        updatePrgCounters(data.programs);
      }
    }

    const filtered = (type && type !== '')
      ? allPrograms.filter(p => (p.program_type || '').toLowerCase() === type.toLowerCase())
      : allPrograms;

    if (filtered.length > 0) {
      renderPrograms(filtered);
      grid.style.display = 'grid';
      if (adm) adm.style.display = 'block';
      if (cta) cta.style.display = 'block';
    } else {
      empty.style.display = 'flex';
    }
  } catch(err) {
    console.error('Programs error:', err);
    empty.style.display = 'flex';
  } finally {
    loading.style.display = 'none';
  }
}

// =============================================
//  BUILD FILTER BUTTONS
// =============================================
function buildProgramFilters(programs) {
  const bar = document.getElementById('programFilters');
  if (!bar) return;

  const types = [...new Set(programs.map(p => p.program_type).filter(Boolean))];
  types.forEach(type => {
    const p   = getPrgPalette(type);
    const btn = document.createElement('button');
    btn.className = 'prg-filter';
    btn.dataset.type = type;
    btn.innerHTML = `<i class="fa ${getPrgIcon(type)}"></i> ${type}`;
    bar.appendChild(btn);
  });
  setupProgramFilters();
}

function setupProgramFilters() {
  document.querySelectorAll('.prg-filter').forEach(function(btn) {
    btn.onclick = function() {
      document.querySelectorAll('.prg-filter').forEach(function(b){ b.classList.remove('active'); });
      btn.classList.add('active');
      loadPrograms(btn.dataset.type);
    };
  });
}

function updatePrgCounters(programs) {
  const types = new Set(programs.map(p => p.program_type).filter(Boolean));
  const tp = document.getElementById('prgTotalPrograms');
  const tt = document.getElementById('prgTotalTypes');
  const cw = document.getElementById('prgCounters');
  if (tp) tp.textContent = programs.length;
  if (tt) tt.textContent = types.size;
  if (cw) cw.style.display = 'flex';
}

// =============================================
//  RENDER
// =============================================
function renderPrograms(programs) {
  const grid = document.getElementById('programGrid');
  grid.innerHTML = programs.map(p => buildProgramCard(p)).join('');
  // fade in
  grid.style.opacity   = '0';
  grid.style.transform = 'translateY(20px)';
  setTimeout(() => {
    grid.style.transition = 'opacity .4s ease, transform .4s ease';
    grid.style.opacity    = '1';
    grid.style.transform  = 'translateY(0)';
  }, 30);
}

function buildProgramCard(p) {
  const pal   = getPrgPalette(p.program_type || p.title);
  const icon  = getPrgIcon(p.program_type);
  const type  = p.program_type || 'Program';
  const dur   = p.duration   ? `<div class="prg-meta-item"><i class="fa fa-clock"></i> ${p.duration}</div>` : '';
  const seats = p.seats      ? `<div class="prg-meta-item"><i class="fa fa-users"></i> ${p.seats} Seats</div>` : '';
  const fee   = p.fee        ? `<div class="prg-meta-item"><i class="fa fa-tag"></i> ${p.fee}</div>` : '';
  const desc  = p.description ? `<p class="prg-desc">${p.description}</p>` : '';

  return `
    <div class="prg-card">
      <div class="prg-card-head">
        <div class="prg-card-icon" style="background:linear-gradient(135deg,${pal.from},${pal.to})">
          <i class="fa ${icon}"></i>
        </div>
        <div class="prg-card-title">
          <h3>${p.title}</h3>
          <span class="prg-type-badge" style="background:${pal.light};color:${pal.from}">${type}</span>
        </div>
      </div>
      <div class="prg-card-body">
        ${desc}
        <div class="prg-meta">${dur}${seats}${fee}</div>
      </div>
      <div class="prg-card-footer">
        <a href="#" onclick="loadPage('signup'); return false;" class="prg-btn prg-btn-primary">
          <i class="fa fa-user-plus"></i> Apply Now
        </a>
        <a href="#" onclick="loadPage('contact'); return false;" class="prg-btn prg-btn-outline">
          <i class="fa fa-info-circle"></i> Enquire
        </a>
      </div>
    </div>`;
}

// =============================================
//  INIT
// =============================================
function initPrograms() {
  if (document.getElementById('programGrid')) {
    allPrograms = [];
    _prgPi = 0;
    Object.keys(_prgPm).forEach(k => delete _prgPm[k]);
    loadPrograms('');
  }
}
