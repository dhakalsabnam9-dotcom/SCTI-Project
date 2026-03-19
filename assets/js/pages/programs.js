// =============================================
//  PROGRAMS PAGE — Dynamic from DB
// =============================================
const programsPage = `
  <style>
    .prp-wrap{background:#f4f6fb;min-height:60vh}
    .prp-hero{position:relative;background:linear-gradient(135deg,#004080 0%,#0059b3 60%,#28a745 100%);padding:52px 20px 0;text-align:center;overflow:hidden}
    .prp-hero-inner{position:relative;z-index:2;color:white;padding-bottom:28px}
    .prp-hero-icon{font-size:48px;margin-bottom:12px;opacity:.9}
    .prp-hero-inner h1{font-size:38px;font-weight:900;margin:0 0 10px;letter-spacing:-1px}
    .prp-hero-inner p{font-size:15px;opacity:.82;max-width:480px;margin:0 auto 22px;line-height:1.6}
    .prp-hero-counters{display:flex;justify-content:center;gap:12px;flex-wrap:wrap;margin-bottom:4px}
    .prp-counter-pill{display:inline-flex;align-items:center;gap:7px;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:30px;padding:8px 20px;font-size:14px;font-weight:700;color:white}
    .prp-hero-wave{position:relative;z-index:2;line-height:0}
    .prp-hero-wave svg{width:100%;height:50px;display:block}
    .prp-filter-bar{background:white;border-bottom:2px solid #e8ecf2;box-shadow:0 3px 12px rgba(0,0,0,.07)}
    .prp-filters{display:flex;gap:8px;padding:14px 20px;overflow-x:auto;scrollbar-width:none;flex-wrap:wrap}
    .prp-filters::-webkit-scrollbar{display:none}
    .prp-filter{padding:8px 18px;border-radius:25px;border:2px solid #e0e6ef;background:#f8fafc;color:#666;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:.2s}
    .prp-filter:hover{border-color:#004080;color:#004080;background:white}
    .prp-filter.prp-active{background:linear-gradient(135deg,#004080,#0059b3);border-color:transparent;color:white;box-shadow:0 4px 14px rgba(0,64,128,.3)}
    .prp-content{background:#f4f6fb;padding:36px 0 60px;min-height:400px}
    .prp-loading{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:80px 20px;gap:16px}
    .prp-dots{display:flex;gap:10px}
    .prp-dots span{width:14px;height:14px;border-radius:50%;background:#004080;animation:prpBounce 1.3s ease-in-out infinite}
    .prp-dots span:nth-child(2){animation-delay:.18s;background:#0059b3}
    .prp-dots span:nth-child(3){animation-delay:.36s;background:#28a745}
    @keyframes prpBounce{0%,80%,100%{transform:scale(.5);opacity:.4}40%{transform:scale(1.2);opacity:1}}
    .prp-loading p{color:#8a9ab5;font-size:14px;font-weight:500}
    .prp-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:80px 20px;text-align:center}
    .prp-empty i{font-size:64px;color:#c5cfe0;margin-bottom:18px}
    .prp-empty h3{font-size:20px;color:#8a9ab5;margin-bottom:8px;font-weight:700}
    .prp-empty p{margin:0;color:#8a9ab5;font-size:14px}
    .prp-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px}
    .prp-card{background:white;border-radius:16px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,.09);transition:transform .3s ease,box-shadow .3s ease;display:flex;flex-direction:column}
    .prp-card:hover{transform:translateY(-7px);box-shadow:0 20px 44px rgba(0,0,0,.15)}
    .prp-card-banner{height:6px}
    .prp-banner-blue{background:linear-gradient(90deg,#004080,#0059b3)}
    .prp-banner-green{background:linear-gradient(90deg,#28a745,#20c997)}
    .prp-banner-orange{background:linear-gradient(90deg,#fd7e14,#ffc107)}
    .prp-banner-purple{background:linear-gradient(90deg,#6f42c1,#e83e8c)}
    .prp-banner-red{background:linear-gradient(90deg,#dc3545,#c82333)}
    .prp-banner-teal{background:linear-gradient(90deg,#17a2b8,#138496)}
    .prp-card-head{padding:20px 20px 14px;display:flex;align-items:flex-start;gap:14px}
    .prp-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;color:white;flex-shrink:0}
    .prp-icon-blue{background:linear-gradient(135deg,#004080,#0059b3)}
    .prp-icon-green{background:linear-gradient(135deg,#28a745,#20c997)}
    .prp-icon-orange{background:linear-gradient(135deg,#fd7e14,#ffc107)}
    .prp-icon-purple{background:linear-gradient(135deg,#6f42c1,#e83e8c)}
    .prp-icon-red{background:linear-gradient(135deg,#dc3545,#c82333)}
    .prp-icon-teal{background:linear-gradient(135deg,#17a2b8,#138496)}
    .prp-card-title{font-size:15px;font-weight:800;color:#1a202c;margin:0 0 4px;line-height:1.3}
    .prp-card-code{font-size:11px;color:#aaa;font-weight:600}
    .prp-card-body{padding:0 20px 14px;flex:1}
    .prp-card-desc{font-size:13px;color:#666;line-height:1.6;margin-bottom:12px}
    .prp-card-content{list-style:none;padding:0;margin:0 0 12px}
    .prp-card-content li{font-size:12px;color:#555;padding:4px 0;padding-left:18px;position:relative;border-bottom:1px solid #f5f5f5}
    .prp-card-content li:last-child{border-bottom:none}
    .prp-card-content li::before{content:"checkmark";content:"✓";position:absolute;left:0;color:#28a745;font-weight:700}
    .prp-card-foot{padding:12px 20px 16px;border-top:1px solid #eef1f6;display:flex;flex-direction:column;gap:6px}
    .prp-info-row{display:flex;align-items:center;gap:6px;font-size:12px;color:#888}
    .prp-info-row i{color:#004080;width:14px}
    .prp-cta{margin-top:8px;padding:10px 16px;border-radius:8px;background:linear-gradient(135deg,#004080,#0059b3);color:white;border:none;cursor:pointer;font-size:12px;font-weight:700;width:100%;display:flex;align-items:center;justify-content:center;gap:6px;transition:.2s}
    .prp-cta:hover{opacity:.88}
    .prp-admission{background:white;border-radius:20px;padding:36px;margin-top:40px;box-shadow:0 4px 18px rgba(0,0,0,.07)}
    .prp-admission h2{font-size:24px;font-weight:800;color:#004080;margin-bottom:8px;text-align:center}
    .prp-admission p{text-align:center;color:#888;font-size:14px;margin-bottom:28px}
    .prp-adm-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px}
    .prp-adm-card{background:#f8fafc;border-radius:12px;padding:22px}
    .prp-adm-icon{width:52px;height:52px;background:linear-gradient(135deg,#004080,#0059b3);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:22px;color:white;margin-bottom:14px}
    .prp-adm-card h3{font-size:16px;font-weight:700;color:#222;margin-bottom:12px}
    .prp-adm-card ul{list-style:none;padding:0}
    .prp-adm-card ul li{padding:6px 0 6px 20px;position:relative;color:#555;font-size:13px;border-bottom:1px dashed #e8ecf2}
    .prp-adm-card ul li:last-child{border-bottom:none}
    .prp-adm-card ul li::before{content:"→";position:absolute;left:0;color:#004080;font-weight:700}
    .prp-cta-row{display:flex;justify-content:center;gap:14px;margin-top:32px;flex-wrap:wrap}
    .prp-btn{padding:13px 28px;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;border:none;display:inline-flex;align-items:center;gap:8px;transition:.2s;text-decoration:none}
    .prp-btn-primary{background:linear-gradient(135deg,#004080,#0059b3);color:white}
    .prp-btn-primary:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,64,128,.3)}
    .prp-btn-outline{background:white;color:#004080;border:2px solid #004080}
    .prp-btn-outline:hover{background:#004080;color:white}
    @media(max-width:900px){.prp-grid{grid-template-columns:repeat(2,1fr)}.prp-adm-grid{grid-template-columns:1fr}}
    @media(max-width:540px){.prp-grid{grid-template-columns:1fr}}
  </style>
  <div class="prp-wrap">
    <div class="prp-hero">
      <div class="prp-hero-inner">
        <div class="prp-hero-icon"><i class="fa fa-graduation-cap"></i></div>
        <h1>Programs &amp; Courses</h1>
        <p>Explore our comprehensive technical education programs designed for your success</p>
        <div class="prp-hero-counters" id="prpCounters" style="display:none">
          <div class="prp-counter-pill"><i class="fa fa-graduation-cap"></i><span id="prpTotal">0</span> Programs</div>
          <div class="prp-counter-pill"><i class="fa fa-check-circle"></i><span id="prpActive">0</span> Active</div>
        </div>
      </div>
      <div class="prp-hero-wave">
        <svg viewBox="0 0 1440 60" preserveAspectRatio="none">
          <path d="M0,30 C360,60 1080,0 1440,30 L1440,60 L0,60 Z" fill="#f4f6fb"/>
        </svg>
      </div>
    </div>
    <div class="prp-filter-bar">
      <div class="prp-filters" id="prpFilters">
        <button class="prp-filter prp-active" onclick="prpFilter(this,'all')" type="button"><i class="fa fa-border-all"></i> All</button>
        <button class="prp-filter" onclick="prpFilter(this,'active')" type="button"><i class="fa fa-check-circle"></i> Active</button>
        <button class="prp-filter" onclick="prpFilter(this,'upcoming')" type="button"><i class="fa fa-clock"></i> Upcoming</button>
      </div>
    </div>
    <div class="prp-content">
      <div class="container">
        <div id="prpLoading" class="prp-loading">
          <div class="prp-dots"><span></span><span></span><span></span></div>
          <p>Loading programs...</p>
        </div>
        <div id="prpGrid" class="prp-grid" style="display:none"></div>
        <div id="prpEmpty" class="prp-empty" style="display:none">
          <i class="fa fa-graduation-cap"></i>
          <h3>No Programs Found</h3>
          <p>No programs match the selected filter.</p>
        </div>
        <div id="prpAdmission" style="display:none">
          <div class="prp-admission">
            <h2>Admission &amp; Benefits</h2>
            <p>Join SCTI and build a strong technical foundation for your career</p>
            <div class="prp-adm-grid">
              <div class="prp-adm-card">
                <div class="prp-adm-icon"><i class="fa fa-user-check"></i></div>
                <h3>Eligibility &amp; Admission</h3>
                <ul>
                  <li>Minimum SEE GPA of 2.0 for Diploma programs</li>
                  <li>Minimum +2 or equivalent for B.Tech. Ed. programs</li>
                  <li>Strong foundation in Mathematics and Science</li>
                  <li>Interest in technical fields</li>
                </ul>
              </div>
              <div class="prp-adm-card">
                <div class="prp-adm-icon"><i class="fa fa-star"></i></div>
                <h3>Why Choose SCTI?</h3>
                <ul>
                  <li>Industry-aligned curriculum</li>
                  <li>Hands-on training with modern equipment</li>
                  <li>Experienced faculty and industry experts</li>
                  <li>Career guidance and job placement support</li>
                  <li>Focus on practical and technical skills</li>
                </ul>
              </div>
            </div>
            <div class="prp-cta-row">
              <a href="#" onclick="loadPage('contact');return false;" class="prp-btn prp-btn-primary"><i class="fa fa-envelope"></i> Contact Us</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
`;

var prpAllPrograms = [];
var prpCurrentFilter = 'all';

function prpFilter(btn, filter) {
  prpCurrentFilter = filter;
  document.querySelectorAll('.prp-filter').forEach(function(b) { b.classList.remove('prp-active'); });
  btn.classList.add('prp-active');
  prpRender(filter);
}

function initPrograms() {
  if (!document.getElementById('prpGrid')) return;
  prpAllPrograms = [];
  var loading = document.getElementById('prpLoading');
  var grid    = document.getElementById('prpGrid');
  var empty   = document.getElementById('prpEmpty');
  loading.style.display = 'flex';
  grid.style.display    = 'none';
  empty.style.display   = 'none';

  fetch('pages/program-list.php?status=all')
    .then(function(r) { return r.json(); })
    .then(function(result) {
      loading.style.display = 'none';
      if (result.success && result.programs && result.programs.length > 0) {
        prpAllPrograms = result.programs;
        var tc = document.getElementById('prpTotal');
        var ac = document.getElementById('prpActive');
        var cw = document.getElementById('prpCounters');
        if (tc) tc.textContent = result.programs.length;
        if (ac) ac.textContent = result.programs.filter(function(p) { return p.status === 'active'; }).length;
        if (cw) cw.style.display = 'flex';
        prpRender(prpCurrentFilter);
        var adm = document.getElementById('prpAdmission');
        if (adm) adm.style.display = 'block';
      } else {
        empty.style.display = 'flex';
      }
    })
    .catch(function(err) {
      console.error('Programs error:', err);
      loading.style.display = 'none';
      empty.style.display   = 'flex';
    });
}

function prpRender(filter) {
  var grid  = document.getElementById('prpGrid');
  var empty = document.getElementById('prpEmpty');
  if (!grid) return;
  var list = prpAllPrograms.slice();
  if (filter === 'active')   list = list.filter(function(p) { return p.status === 'active'; });
  if (filter === 'upcoming') list = list.filter(function(p) { return p.status === 'upcoming'; });
  if (!list.length) {
    grid.style.display  = 'none';
    empty.style.display = 'flex';
    return;
  }
  grid.innerHTML      = list.map(prpBuildCard).join('');
  grid.style.display  = 'grid';
  empty.style.display = 'none';
}

function prpBuildCard(p) {
  var color  = p.color || 'blue';
  var icon   = p.icon  || 'fa-graduation-cap';
  var topics = (p.content || '').split('|').filter(function(t) { return t.trim(); });
  var topicsHtml = topics.length
    ? '<ul class="prp-card-content">' + topics.map(function(t) { return '<li>' + prpEsc(t.trim()) + '</li>'; }).join('') + '</ul>'
    : '';

  return '<div class="prp-card">'
    + '<div class="prp-card-banner prp-banner-' + color + '"></div>'
    + '<div class="prp-card-head">'
    +   '<div class="prp-icon prp-icon-' + color + '"><i class="fa ' + icon + '"></i></div>'
    +   '<div>'
    +     '<div class="prp-card-title">' + prpEsc(p.title) + '</div>'
    +     '<div class="prp-card-code">' + prpEsc(p.code) + ' | ' + prpEsc(p.affiliation || '') + '</div>'
    +   '</div>'
    + '</div>'
    + '<div class="prp-card-body">'
    +   '<div class="prp-card-desc">' + prpEsc(p.description || '') + '</div>'
    +   topicsHtml
    + '</div>'
    + '<div class="prp-card-foot">'
    +   '<div class="prp-info-row"><i class="fa fa-clock"></i> Duration: ' + prpEsc(p.duration || '') + '</div>'
    +   '<div class="prp-info-row"><i class="fa fa-chart-bar"></i> ' + prpEsc(p.assessment || '') + '</div>'
    +   '<button class="prp-cta" onclick="loadPage(\'contact\')"><i class="fa fa-envelope"></i> Contact Us</button>'
    + '</div>'
    + '</div>';
}

function prpEsc(s) {
  return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}
