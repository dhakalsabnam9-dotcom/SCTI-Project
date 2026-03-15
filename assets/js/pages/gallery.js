// =============================================
//  GALLERY PAGE
// =============================================
const galleryPage = `
  <style>
    .glp-wrap{background:#f4f6fb;min-height:60vh}
    .glp-hero{position:relative;background:linear-gradient(135deg,#004080 0%,#0059b3 60%,#17a2b8 100%);padding:52px 20px 0;text-align:center;overflow:hidden}
    .glp-hero-inner{position:relative;z-index:2;color:white;padding-bottom:28px}
    .glp-hero-icon{font-size:48px;margin-bottom:12px;opacity:.9}
    .glp-hero-inner h1{font-size:38px;font-weight:900;margin:0 0 10px;letter-spacing:-1px}
    .glp-hero-inner p{font-size:15px;opacity:.82;max-width:480px;margin:0 auto 22px;line-height:1.6}
    .glp-hero-counters{display:flex;justify-content:center;gap:12px;flex-wrap:wrap;margin-bottom:4px}
    .glp-counter-pill{display:inline-flex;align-items:center;gap:7px;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:30px;padding:8px 20px;font-size:14px;font-weight:700;color:white}
    .glp-hero-wave{position:relative;z-index:2;line-height:0}
    .glp-hero-wave svg{width:100%;height:50px;display:block}
    .glp-filter-bar{background:white;border-bottom:2px solid #e8ecf2;position:sticky;top:100px;z-index:100;box-shadow:0 3px 12px rgba(0,0,0,.07)}
    .glp-filters{display:flex;gap:8px;padding:12px 20px;overflow-x:auto;scrollbar-width:none}
    .glp-filters::-webkit-scrollbar{display:none}
    .glp-filter{display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border:2px solid #dde3ed;background:#f8fafc;color:#5a6a80;border-radius:20px;cursor:pointer;font-size:13px;font-weight:600;white-space:nowrap;transition:all .2s ease}
    .glp-filter:hover{border-color:#004080;color:#004080;background:white;transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,64,128,.12)}
    .glp-filter.active{background:linear-gradient(135deg,var(--fa,#004080),var(--fb,#0059b3));border-color:transparent;color:white;box-shadow:0 4px 14px rgba(0,64,128,.3)}
    .glp-content{background:#f4f6fb;padding:36px 0 60px;min-height:400px}
    .glp-loading{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:80px 20px;gap:16px}
    .glp-dots{display:flex;gap:10px}
    .glp-dots span{width:14px;height:14px;border-radius:50%;background:#004080;animation:glpBounce 1.3s ease-in-out infinite}
    .glp-dots span:nth-child(2){animation-delay:.18s;background:#0059b3}
    .glp-dots span:nth-child(3){animation-delay:.36s;background:#17a2b8}
    @keyframes glpBounce{0%,80%,100%{transform:scale(.5);opacity:.4}40%{transform:scale(1.2);opacity:1}}
    .glp-loading p{color:#8a9ab5;font-size:14px;font-weight:500}
    .glp-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:80px 20px;text-align:center}
    .glp-empty i{font-size:64px;color:#c5cfe0;margin-bottom:18px}
    .glp-empty h3{margin:0 0 8px;color:#4a5568;font-size:20px;font-weight:700}
    .glp-empty p{margin:0;color:#8a9ab5;font-size:14px}
    .glp-section{background:white;border-radius:20px;overflow:hidden;margin-bottom:32px;box-shadow:0 3px 18px rgba(0,0,0,.07)}
    .glp-section-head{background:linear-gradient(135deg,var(--fa,#004080) 0%,var(--fb,#0059b3) 100%);padding:22px 28px;display:flex;align-items:center;justify-content:space-between;gap:16px}
    .glp-sh-left{display:flex;align-items:center;gap:16px;min-width:0}
    .glp-sh-icon{width:52px;height:52px;border-radius:14px;background:rgba(255,255,255,.2);border:2px solid rgba(255,255,255,.35);display:flex;align-items:center;justify-content:center;font-size:22px;color:white;flex-shrink:0}
    .glp-sh-text h2{margin:0 0 3px;font-size:20px;font-weight:800;color:white;text-transform:capitalize;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .glp-sh-text span{font-size:12px;color:rgba(255,255,255,.7);font-weight:500}
    .glp-sh-badge{display:flex;flex-direction:column;align-items:center;background:rgba(255,255,255,.18);border:2px solid rgba(255,255,255,.3);border-radius:14px;padding:10px 16px;color:white;flex-shrink:0;min-width:60px;text-align:center;font-size:26px;font-weight:900;line-height:1}
    .glp-sh-badge small{font-size:9px;text-transform:uppercase;letter-spacing:1px;opacity:.75;margin-top:3px;font-weight:700}
    .glp-flat-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;padding:24px 26px 30px}
    .glp-tile{border-radius:16px;overflow:hidden;cursor:pointer;background:white;box-shadow:0 4px 16px rgba(0,0,0,.10);transition:transform .3s ease,box-shadow .3s ease;display:flex;flex-direction:column}
    .glp-tile:hover{transform:translateY(-7px);box-shadow:0 20px 44px rgba(0,0,0,.17)}
    .glp-tile-img-wrap{position:relative;width:100%;padding-top:75%;overflow:hidden;background:#dde3ed;flex-shrink:0}
    .glp-tile-img-wrap img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:block;transition:transform .45s ease}
    .glp-tile:hover .glp-tile-img-wrap img{transform:scale(1.08)}
    .glp-tile-over{position:absolute;inset:0;background:rgba(0,0,0,.35);display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity .25s ease}
    .glp-tile:hover .glp-tile-over{opacity:1}
    .glp-tile-zoom{width:48px;height:48px;border-radius:50%;background:rgba(255,255,255,.2);border:2px solid rgba(255,255,255,.7);display:flex;align-items:center;justify-content:center;font-size:20px;color:white;transform:scale(.5);transition:transform .3s cubic-bezier(.34,1.56,.64,1)}
    .glp-tile:hover .glp-tile-zoom{transform:scale(1)}
    .glp-tile-cat{position:absolute;top:10px;left:10px;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;color:white;text-transform:capitalize;letter-spacing:.3px}
    .glp-tile-title{padding:11px 14px 13px;font-size:13px;font-weight:600;color:#2d3748;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;background:white;border-top:1px solid #eef1f6;margin:0}
    .glp-lb{position:fixed;inset:0;background:rgba(4,8,18,.96);z-index:10000;display:none;align-items:center;justify-content:center;backdrop-filter:blur(6px)}
    .glp-lb.active{display:flex;animation:glpLbIn .28s ease}
    @keyframes glpLbIn{from{opacity:0}to{opacity:1}}
    .glp-lb-close{position:fixed;top:20px;right:20px;width:42px;height:42px;border-radius:50%;background:rgba(255,255,255,.1);border:1.5px solid rgba(255,255,255,.2);color:white;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .22s;z-index:10001}
    .glp-lb-close:hover{background:#dc3545;border-color:transparent;transform:rotate(90deg) scale(1.1)}
    .glp-lb-nav{position:fixed;top:50%;transform:translateY(-50%);width:48px;height:48px;border-radius:50%;background:rgba(255,255,255,.1);border:1.5px solid rgba(255,255,255,.2);color:white;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .22s;z-index:10001}
    .glp-lb-prev{left:16px}.glp-lb-next{right:16px}
    .glp-lb-nav:hover{background:rgba(0,64,128,.8);border-color:transparent;transform:translateY(-50%) scale(1.1)}
    .glp-lb-stage{width:92%;max-width:900px;display:flex;flex-direction:column;align-items:center;animation:glpLbZoom .35s cubic-bezier(.34,1.56,.64,1)}
    @keyframes glpLbZoom{from{transform:scale(.85);opacity:0}to{transform:scale(1);opacity:1}}
    .glp-lb-img{width:100%;border-radius:12px;overflow:hidden;background:#0d0d0d;max-height:65vh;display:flex;align-items:center;justify-content:center;box-shadow:0 20px 60px rgba(0,0,0,.6)}
    .glp-lb-img img{max-width:100%;max-height:65vh;object-fit:contain;display:block;transition:opacity .25s ease}
    .glp-lb-info{width:100%;margin-top:12px;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);border-radius:12px;padding:14px 20px;display:flex;align-items:center;justify-content:space-between;gap:14px}
    .glp-lb-info h3{margin:0 0 3px;color:white;font-size:16px;font-weight:700}
    .glp-lb-info p{margin:0;color:rgba(255,255,255,.5);font-size:13px}
    .glp-lb-meta{display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0}
    .glp-lb-cat{display:inline-block;padding:4px 12px;background:linear-gradient(135deg,#004080,#0059b3);color:white;border-radius:16px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px}
    .glp-lb-num{color:rgba(255,255,255,.4);font-size:12px;font-weight:600}
    @media(max-width:768px){
      .glp-hero{padding:40px 16px 0}
      .glp-hero-inner h1{font-size:26px}
      .glp-flat-grid{grid-template-columns:repeat(2,1fr);gap:12px;padding:16px}
      .glp-section-head{padding:16px 18px}
      .glp-sh-text h2{font-size:16px}
    }
    @media(max-width:480px){
      .glp-flat-grid{grid-template-columns:repeat(1,1fr);gap:10px;padding:12px}
      .glp-sh-badge{display:none}
    }
  </style>
  <div class="glp-wrap">

    <!-- HERO -->
    <div class="glp-hero">
      <div class="glp-hero-inner">
        <div class="glp-hero-icon"><i class="fa fa-images"></i></div>
        <h1>Photo Gallery</h1>
        <p>Explore moments of learning, achievement &amp; campus life at SCTI</p>
        <div class="glp-hero-counters" id="glpCounters" style="display:none">
          <div class="glp-counter-pill">
            <i class="fa fa-image"></i>
            <span id="glpTotalPhotos">0</span> Photos
          </div>
          <div class="glp-counter-pill">
            <i class="fa fa-layer-group"></i>
            <span id="glpTotalCats">0</span> Categories
          </div>
        </div>
      </div>
      <div class="glp-hero-wave">
        <svg viewBox="0 0 1440 60" preserveAspectRatio="none">
          <path d="M0,30 C360,60 1080,0 1440,30 L1440,60 L0,60 Z" fill="#f4f6fb"/>
        </svg>
      </div>
    </div>

    <!-- FILTER BAR -->
    <div class="glp-filter-bar">
      <div class="glp-filters" id="galleryFilters">
        <button class="glp-filter active" data-category="">
          <i class="fa fa-border-all"></i> All
        </button>
      </div>
    </div>

    <!-- CONTENT -->
    <div class="glp-content">
      <div class="container">

        <div id="galleryLoading" class="glp-loading">
          <div class="glp-dots"><span></span><span></span><span></span></div>
          <p>Loading gallery...</p>
        </div>

        <div id="galleryContent"></div>

        <div id="galleryEmpty" class="glp-empty" style="display:none">
          <i class="fa fa-images"></i>
          <h3>No Photos Yet</h3>
          <p>No images found in this category.</p>
        </div>

      </div>
    </div>

  </div>

  <!-- LIGHTBOX -->
  <div id="glLightbox" class="glp-lb" onclick="glbBgClick(event)">
    <button class="glp-lb-close" onclick="closeLightbox()"><i class="fa fa-xmark"></i></button>
    <button class="glp-lb-nav glp-lb-prev" onclick="navigateLightbox(-1)"><i class="fa fa-chevron-left"></i></button>
    <button class="glp-lb-nav glp-lb-next" onclick="navigateLightbox(1)"><i class="fa fa-chevron-right"></i></button>
    <div class="glp-lb-stage">
      <div class="glp-lb-img"><img id="lightboxImage" src="" alt=""></div>
      <div class="glp-lb-info">
        <div>
          <h3 id="lightboxTitle"></h3>
          <p id="lightboxDescription"></p>
        </div>
        <div class="glp-lb-meta">
          <span id="lightboxCategory" class="glp-lb-cat"></span>
          <span id="glbCounter" class="glp-lb-num"></span>
        </div>
      </div>
    </div>
  </div>
`;

// =============================================
//  STATE & CONFIG
// =============================================
let galleryImages   = [];
let currentImageIndex = 0;

const CAT_ICONS = {
  campus:'fa-university', events:'fa-calendar-star', students:'fa-user-graduate',
  facilities:'fa-building', activities:'fa-person-running', achievements:'fa-trophy',
  sports:'fa-futbol', labs:'fa-flask', library:'fa-book-open',
  classroom:'fa-chalkboard', ceremony:'fa-award', default:'fa-folder-open'
};

const PALETTES = [
  { from:'#1e3a8a', to:'#3b82f6', accent:'#60a5fa' },
  { from:'#065f46', to:'#10b981', accent:'#34d399' },
  { from:'#7c2d12', to:'#f97316', accent:'#fb923c' },
  { from:'#4c1d95', to:'#8b5cf6', accent:'#a78bfa' },
  { from:'#881337', to:'#f43f5e', accent:'#fb7185' },
  { from:'#164e63', to:'#06b6d4', accent:'#22d3ee' },
  { from:'#713f12', to:'#eab308', accent:'#fde047' },
  { from:'#134e4a', to:'#14b8a6', accent:'#2dd4bf' },
];
let _pi = 0;
const _pm = {};
function getPalette(cat) {
  if (!_pm[cat]) { _pm[cat] = PALETTES[_pi % PALETTES.length]; _pi++; }
  return _pm[cat];
}

// =============================================
//  LOAD CATEGORIES
// =============================================
async function loadGalleryCategories() {
  try {
    const res  = await fetch('pages/gallery-categories.php');
    const data = await res.json();
    if (!data.success) { setupGalleryFilters(); return; }

    const bar = document.getElementById('galleryFilters');
    if (!bar) return;

    data.categories.forEach(cat => {
      const icon = CAT_ICONS[cat.name.toLowerCase()] || CAT_ICONS.default;
      const p    = getPalette(cat.name);
      const btn  = document.createElement('button');
      btn.className = 'glp-filter';
      btn.dataset.category = cat.name;
      btn.style.cssText = `--fa:${p.from};--fb:${p.to}`;
      btn.innerHTML = `<i class="fa ${icon}"></i> ${cat.name}`;
      bar.appendChild(btn);
    });
    setupGalleryFilters();
  } catch(e) { setupGalleryFilters(); }
}

// =============================================
//  LOAD IMAGES
// =============================================
async function loadGalleryImages(category = '') {
  const content = document.getElementById('galleryContent');
  const loading = document.getElementById('galleryLoading');
  const empty   = document.getElementById('galleryEmpty');

  loading.style.display = 'flex';
  content.innerHTML     = '';
  empty.style.display   = 'none';

  try {
    const res    = await fetch(`pages/gallery-public.php?category=${encodeURIComponent(category)}`);
    const result = await res.json();

    if (result.success && result.images.length > 0) {
      galleryImages = result.images;
      renderGallery(result.images, category);

      const cats = new Set(result.images.map(i => i.category).filter(Boolean));
      const pc = document.getElementById('glpTotalPhotos');
      const cc = document.getElementById('glpTotalCats');
      const cw = document.getElementById('glpCounters');
      if (pc) pc.textContent = result.images.length;
      if (cc) cc.textContent = cats.size;
      if (cw) cw.style.display = 'flex';
    } else {
      empty.style.display = 'flex';
    }
  } catch(err) {
    console.error('Gallery error:', err);
    document.getElementById('galleryEmpty').style.display = 'flex';
  } finally {
    loading.style.display = 'none';
  }
}

// =============================================
//  RENDER
// =============================================

function renderGallery(images, activeCategory) {
  const content = document.getElementById('galleryContent');
  images.forEach((img, i) => { img._idx = i; });
  const tiles = images.map(img => buildTile(img, img._idx)).join('');
  content.innerHTML = `<div class="glp-flat-grid">${tiles}</div>`;
  fadeInCards();
}


function buildTile(img, index) {
  const src = img.thumbnail_path || img.file_path;
  const p   = getPalette(img.category || '__gen__');
  const cat = img.category || '';
  return `
    <div class="glp-tile" onclick="openLightbox(${index})">
      <div class="glp-tile-img-wrap">
        <img src="${src}" alt="${img.title}" loading="lazy"
             onerror="this.src='assets/images/img1.jpg'">
        <div class="glp-tile-over">
          <div class="glp-tile-zoom"><i class="fa fa-magnifying-glass-plus"></i></div>
        </div>
        ${cat ? `<span class="glp-tile-cat" style="background:linear-gradient(135deg,${p.from},${p.to})">${cat}</span>` : ''}
      </div>
      <p class="glp-tile-title">${img.title}</p>
    </div>`;
}

function filterToCategory(cat, colorFrom, colorTo) {
  document.querySelectorAll('.glp-filter').forEach(b => {
    b.classList.remove('active');
    if (b.dataset.category === cat) {
      b.classList.add('active');
      b.style.setProperty('--fa', colorFrom);
      b.style.setProperty('--fb', colorTo);
    }
  });
  loadGalleryImages(cat);
}

function fadeInCards() {
  const grid = document.querySelector('.glp-flat-grid');
  if (!grid) return;
  grid.style.opacity   = '0';
  grid.style.transform = 'translateY(20px)';
  setTimeout(() => {
    grid.style.transition = 'opacity .4s ease, transform .4s ease';
    grid.style.opacity    = '1';
    grid.style.transform  = 'translateY(0)';
  }, 30);
}

// =============================================
//  LIGHTBOX
// =============================================
function openLightbox(index) {
  currentImageIndex = index;
  const img = galleryImages[index];
  document.getElementById('lightboxImage').src               = img.file_path;
  document.getElementById('lightboxTitle').textContent       = img.title;
  document.getElementById('lightboxDescription').textContent = img.description || '';
  document.getElementById('lightboxCategory').textContent    = img.category || '';
  document.getElementById('glbCounter').textContent          = `${index + 1} / ${galleryImages.length}`;
  document.getElementById('glLightbox').classList.add('active');
  const lb = document.getElementById('glLightbox'); if (lb.requestFullscreen) lb.requestFullscreen().catch(()=>{});
  document.body.style.overflow = 'hidden';
}

function closeLightbox() {
  document.getElementById('glLightbox').classList.remove('active');
  document.body.style.overflow = '';
  if (document.fullscreenElement) document.exitFullscreen().catch(()=>{});
}

function navigateLightbox(dir) {
  currentImageIndex = (currentImageIndex + dir + galleryImages.length) % galleryImages.length;
  const imgEl = document.getElementById('lightboxImage');
  imgEl.style.opacity   = '0';
  imgEl.style.transform = dir > 0 ? 'translateX(40px)' : 'translateX(-40px)';
  setTimeout(() => {
    openLightbox(currentImageIndex);
    imgEl.style.transition = 'opacity .3s, transform .3s';
    imgEl.style.opacity    = '1';
    imgEl.style.transform  = 'translateX(0)';
  }, 150);
}

function glbBgClick(e) {
  if (e.target.id === 'glLightbox') closeLightbox();
}

// =============================================
//  FILTERS
// =============================================
function setupGalleryFilters() {
  // Re-attach onclick to all filter buttons after dynamic ones are added
  document.querySelectorAll('.glp-filter').forEach(function(btn) {
    btn.onclick = function() {
      document.querySelectorAll('.glp-filter').forEach(function(b){ b.classList.remove('active'); });
      btn.classList.add('active');
      loadGalleryImages(btn.dataset.category);
    };
  });
}

// =============================================
//  KEYBOARD
// =============================================
document.addEventListener('keydown', e => {
  const lb = document.getElementById('glLightbox');
  if (lb && lb.classList.contains('active')) {
    if (e.key === 'Escape')     closeLightbox();
    if (e.key === 'ArrowLeft')  navigateLightbox(-1);
    if (e.key === 'ArrowRight') navigateLightbox(1);
  }
});

// =============================================
//  INIT
// =============================================
function initGallery() {
  if (document.getElementById('galleryContent')) {
    loadGalleryCategories();
    loadGalleryImages();
  }
}
