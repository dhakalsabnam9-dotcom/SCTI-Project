// =============================================
//  GALLERY PAGE — Category Cards → Grid → Lightbox
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
    .glp-content{background:#f4f6fb;padding:40px 0 60px;min-height:400px}
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

    /* CATEGORY CARDS GRID */
    .glp-cat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:24px}
    .glp-cat-card{position:relative;border-radius:20px;overflow:hidden;cursor:pointer;aspect-ratio:4/3;box-shadow:0 6px 24px rgba(0,0,0,.14);transition:transform .35s ease,box-shadow .35s ease}
    .glp-cat-card:hover{transform:translateY(-8px) scale(1.02);box-shadow:0 20px 50px rgba(0,0,0,.22)}
    .glp-cat-cover{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:block;transition:transform .5s ease}
    .glp-cat-card:hover .glp-cat-cover{transform:scale(1.08)}
    .glp-cat-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.75) 0%,rgba(0,0,0,.1) 55%,transparent 100%);transition:background .3s ease}
    .glp-cat-card:hover .glp-cat-overlay{background:linear-gradient(to top,rgba(0,0,0,.88) 0%,rgba(0,0,0,.35) 55%,rgba(0,0,0,.1) 100%)}
    .glp-cat-info{position:absolute;bottom:0;left:0;right:0;padding:20px 22px;color:white;transform:translateY(6px);transition:transform .3s ease}
    .glp-cat-card:hover .glp-cat-info{transform:translateY(0)}
    .glp-cat-name{font-size:20px;font-weight:800;text-transform:capitalize;margin:0 0 4px;text-shadow:0 2px 8px rgba(0,0,0,.4)}
    .glp-cat-count{font-size:13px;opacity:.85;font-weight:500}
    .glp-cat-expand{position:absolute;top:14px;right:14px;width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,.18);border:1.5px solid rgba(255,255,255,.4);display:flex;align-items:center;justify-content:center;font-size:14px;color:white;opacity:0;transform:scale(.7);transition:opacity .25s,transform .25s}
    .glp-cat-card:hover .glp-cat-expand{opacity:1;transform:scale(1)}

    /* CATEGORY MODAL */
    .glp-modal{position:fixed;inset:0;background:rgba(4,8,18,.97);z-index:9000;display:none;flex-direction:column;backdrop-filter:blur(8px)}
    .glp-modal.open{display:flex;animation:glpMIn .3s ease}
    @keyframes glpMIn{from{opacity:0;transform:scale(.97)}to{opacity:1;transform:scale(1)}}
    .glp-modal-head{display:flex;align-items:center;justify-content:space-between;padding:18px 28px;border-bottom:1px solid rgba(255,255,255,.1);flex-shrink:0}
    .glp-modal-title{display:flex;align-items:center;gap:14px}
    .glp-modal-title h2{margin:0;color:white;font-size:22px;font-weight:800;text-transform:capitalize}
    .glp-modal-title span{background:rgba(255,255,255,.15);color:rgba(255,255,255,.8);padding:4px 12px;border-radius:20px;font-size:13px;font-weight:600}
    .glp-modal-close{width:42px;height:42px;border-radius:50%;background:rgba(255,255,255,.1);border:1.5px solid rgba(255,255,255,.2);color:white;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .22s}
    .glp-modal-close:hover{background:#dc3545;border-color:transparent;transform:rotate(90deg) scale(1.1)}
    .glp-modal-body{flex:1;overflow-y:auto;padding:28px}
    .glp-modal-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
    .glp-mg-tile{border-radius:14px;overflow:hidden;cursor:pointer;background:#111;box-shadow:0 4px 16px rgba(0,0,0,.3);transition:transform .3s,box-shadow .3s;display:flex;flex-direction:column}
    .glp-mg-tile:hover{transform:translateY(-5px);box-shadow:0 16px 36px rgba(0,0,0,.5)}
    .glp-mg-img-wrap{position:relative;width:100%;padding-top:72%;overflow:hidden}
    .glp-mg-img-wrap img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:block;transition:transform .4s ease}
    .glp-mg-tile:hover .glp-mg-img-wrap img{transform:scale(1.07)}
    .glp-mg-zoom{position:absolute;inset:0;background:rgba(0,0,0,.4);display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity .25s}
    .glp-mg-tile:hover .glp-mg-zoom{opacity:1}
    .glp-mg-zoom i{font-size:28px;color:white}
    .glp-mg-info{padding:10px 13px 12px;background:#1a1a2e}
    .glp-mg-title{font-size:13px;font-weight:600;color:#e2e8f0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin:0 0 3px}
    .glp-mg-desc{font-size:11px;color:#718096;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin:0}

    /* LIGHTBOX */
    .glp-lb{position:fixed;inset:0;background:rgba(0,0,0,.98);z-index:10000;display:none;align-items:center;justify-content:center;backdrop-filter:blur(10px)}
    .glp-lb.active{display:flex;animation:glpLbIn .25s ease}
    @keyframes glpLbIn{from{opacity:0}to{opacity:1}}
    .glp-lb-close{position:fixed;top:18px;right:18px;width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,.1);border:1.5px solid rgba(255,255,255,.2);color:white;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .22s;z-index:10001}
    .glp-lb-close:hover{background:#dc3545;border-color:transparent;transform:rotate(90deg) scale(1.1)}
    .glp-lb-nav{position:fixed;top:50%;transform:translateY(-50%);width:50px;height:50px;border-radius:50%;background:rgba(255,255,255,.1);border:1.5px solid rgba(255,255,255,.2);color:white;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .22s;z-index:10001}
    .glp-lb-prev{left:14px}.glp-lb-next{right:14px}
    .glp-lb-nav:hover{background:rgba(0,64,128,.8);border-color:transparent;transform:translateY(-50%) scale(1.1)}
    .glp-lb-stage{width:92%;max-width:960px;display:flex;flex-direction:column;align-items:center}
    .glp-lb-img{width:100%;border-radius:12px;overflow:hidden;background:#0d0d0d;max-height:68vh;display:flex;align-items:center;justify-content:center;box-shadow:0 20px 60px rgba(0,0,0,.7)}
    .glp-lb-img img{max-width:100%;max-height:68vh;object-fit:contain;display:block;transition:opacity .2s}
    .glp-lb-info{width:100%;margin-top:14px;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);border-radius:12px;padding:14px 20px;display:flex;align-items:center;justify-content:space-between;gap:14px}
    .glp-lb-info h3{margin:0 0 3px;color:white;font-size:16px;font-weight:700}
    .glp-lb-info p{margin:0;color:rgba(255,255,255,.5);font-size:13px}
    .glp-lb-meta{display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0}
    .glp-lb-cat{display:inline-block;padding:4px 12px;background:linear-gradient(135deg,#004080,#0059b3);color:white;border-radius:16px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px}
    .glp-lb-num{color:rgba(255,255,255,.4);font-size:12px;font-weight:600}

    @media(max-width:768px){
      .glp-hero-inner h1{font-size:26px}
      .glp-cat-grid{grid-template-columns:repeat(2,1fr);gap:14px}
      .glp-modal-grid{grid-template-columns:repeat(2,1fr);gap:10px}
      .glp-modal-body{padding:16px}
    }
    @media(max-width:480px){
      .glp-cat-grid{grid-template-columns:1fr;gap:12px}
      .glp-modal-grid{grid-template-columns:1fr;gap:8px}
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
          <div class="glp-counter-pill"><i class="fa fa-image"></i><span id="glpTotalPhotos">0</span> Photos</div>
          <div class="glp-counter-pill"><i class="fa fa-layer-group"></i><span id="glpTotalCats">0</span> Albums</div>
        </div>
      </div>
      <div class="glp-hero-wave">
        <svg viewBox="0 0 1440 60" preserveAspectRatio="none">
          <path d="M0,30 C360,60 1080,0 1440,30 L1440,60 L0,60 Z" fill="#f4f6fb"/>
        </svg>
      </div>
    </div>

    <!-- CONTENT -->
    <div class="glp-content">
      <div class="container">
        <div id="galleryLoading" class="glp-loading">
          <div class="glp-dots"><span></span><span></span><span></span></div>
          <p>Loading gallery...</p>
        </div>
        <div id="galleryCatGrid" class="glp-cat-grid" style="display:none"></div>
        <div id="galleryEmpty" class="glp-empty" style="display:none">
          <i class="fa fa-images"></i>
          <h3>No Photos Yet</h3>
          <p>No images have been uploaded yet.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- CATEGORY MODAL (album expand) -->
  <div id="glpCatModal" class="glp-modal">
    <div class="glp-modal-head">
      <div class="glp-modal-title">
        <h2 id="glpModalCatName">Album</h2>
        <span id="glpModalCatCount">0 photos</span>
      </div>
      <button class="glp-modal-close" onclick="closeAlbum()"><i class="fa fa-xmark"></i></button>
    </div>
    <div class="glp-modal-body">
      <div id="glpModalGrid" class="glp-modal-grid"></div>
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
//  STATE
// =============================================
let glpAllImages    = [];   // all images from API
let glpAlbumImages  = [];   // images in currently open album
let glpLbImages     = [];   // images in current lightbox context
let glpLbIndex      = 0;

const CAT_ICONS = {
  campus:'fa-university', events:'fa-calendar-star', students:'fa-user-graduate',
  facilities:'fa-building', activities:'fa-person-running', achievements:'fa-trophy',
  sports:'fa-futbol', labs:'fa-flask', library:'fa-book-open',
  classroom:'fa-chalkboard', ceremony:'fa-award', default:'fa-folder-open'
};

const PALETTES = [
  { from:'#1e3a8a', to:'#3b82f6' },
  { from:'#065f46', to:'#10b981' },
  { from:'#7c2d12', to:'#f97316' },
  { from:'#4c1d95', to:'#8b5cf6' },
  { from:'#881337', to:'#f43f5e' },
  { from:'#164e63', to:'#06b6d4' },
  { from:'#713f12', to:'#eab308' },
  { from:'#134e4a', to:'#14b8a6' },
];
let _pi = 0;
const _pm = {};
function getPalette(cat) {
  if (!_pm[cat]) { _pm[cat] = PALETTES[_pi % PALETTES.length]; _pi++; }
  return _pm[cat];
}

// =============================================
//  INIT
// =============================================
function initGallery() {
  if (!document.getElementById('galleryCatGrid')) return;
  glpAllImages   = [];
  glpAlbumImages = [];
  _pi = 0;
  Object.keys(_pm).forEach(k => delete _pm[k]);
  loadAllImages();
}

// =============================================
//  LOAD ALL IMAGES
// =============================================
async function loadAllImages() {
  const loading = document.getElementById('galleryLoading');
  const catGrid = document.getElementById('galleryCatGrid');
  const empty   = document.getElementById('galleryEmpty');

  loading.style.display = 'flex';
  catGrid.style.display = 'none';
  empty.style.display   = 'none';

  try {
    const res    = await fetch('pages/gallery-public.php');
    const result = await res.json();

    if (result.success && result.images && result.images.length > 0) {
      glpAllImages = result.images;
      renderCategoryCards(result.images);
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
//  RENDER CATEGORY CARDS
// =============================================
function renderCategoryCards(images) {
  // Group by category
  const groups = {};
  images.forEach(img => {
    const cat = img.category || 'General';
    if (!groups[cat]) groups[cat] = [];
    groups[cat].push(img);
  });

  const catGrid = document.getElementById('galleryCatGrid');
  const cats    = Object.keys(groups);

  // Update counters
  const pc = document.getElementById('glpTotalPhotos');
  const cc = document.getElementById('glpTotalCats');
  const cw = document.getElementById('glpCounters');
  if (pc) pc.textContent = images.length;
  if (cc) cc.textContent = cats.length;
  if (cw) cw.style.display = 'flex';

  catGrid.innerHTML = cats.map(cat => buildCatCard(cat, groups[cat])).join('');
  catGrid.style.display = 'grid';

  // Fade in
  catGrid.style.opacity   = '0';
  catGrid.style.transform = 'translateY(20px)';
  setTimeout(() => {
    catGrid.style.transition = 'opacity .4s ease, transform .4s ease';
    catGrid.style.opacity    = '1';
    catGrid.style.transform  = 'translateY(0)';
  }, 30);
}

function buildCatCard(cat, images) {
  const cover = images[0].thumbnail_path || images[0].file_path;
  const icon  = CAT_ICONS[cat.toLowerCase()] || CAT_ICONS.default;
  const p     = getPalette(cat);
  const count = images.length;
  const catJson = cat.replace(/'/g, "\\'");

  return `
    <div class="glp-cat-card" onclick="openAlbum('${catJson}')">
      <img class="glp-cat-cover" src="${cover}" alt="${cat}"
           onerror="this.src='assets/images/img1.jpg'">
      <div class="glp-cat-overlay"></div>
      <div class="glp-cat-info">
        <div class="glp-cat-name"><i class="fa ${icon}"></i> ${cat}</div>
        <div class="glp-cat-count">${count} photo${count !== 1 ? 's' : ''}</div>
      </div>
      <div class="glp-cat-expand"><i class="fa fa-expand"></i></div>
    </div>`;
}

// =============================================
//  OPEN ALBUM MODAL
// =============================================
function openAlbum(cat) {
  const groups = {};
  glpAllImages.forEach(img => {
    const c = img.category || 'General';
    if (!groups[c]) groups[c] = [];
    groups[c].push(img);
  });

  glpAlbumImages = groups[cat] || [];

  document.getElementById('glpModalCatName').textContent  = cat;
  document.getElementById('glpModalCatCount').textContent = glpAlbumImages.length + ' photo' + (glpAlbumImages.length !== 1 ? 's' : '');

  const grid = document.getElementById('glpModalGrid');
  grid.innerHTML = glpAlbumImages.map((img, i) => buildModalTile(img, i)).join('');

  document.getElementById('glpCatModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeAlbum() {
  document.getElementById('glpCatModal').classList.remove('open');
  // only restore scroll if lightbox is also closed
  if (!document.getElementById('glLightbox').classList.contains('active')) {
    document.body.style.overflow = '';
  }
}

function buildModalTile(img, index) {
  const src = img.thumbnail_path || img.file_path;
  return `
    <div class="glp-mg-tile" onclick="openLightboxFromAlbum(${index})">
      <div class="glp-mg-img-wrap">
        <img src="${src}" alt="${img.title}" loading="lazy"
             onerror="this.src='assets/images/img1.jpg'">
        <div class="glp-mg-zoom"><i class="fa fa-magnifying-glass-plus"></i></div>
      </div>
      <div class="glp-mg-info">
        <p class="glp-mg-title">${img.title}</p>
        <p class="glp-mg-desc">${img.description || ''}</p>
      </div>
    </div>`;
}

// =============================================
//  LIGHTBOX (from album)
// =============================================
function openLightboxFromAlbum(index) {
  glpLbImages = glpAlbumImages;
  glpLbIndex  = index;
  showLightboxImage(index);
  document.getElementById('glLightbox').classList.add('active');
  document.body.style.overflow = 'hidden';
}

function showLightboxImage(index) {
  const img = glpLbImages[index];
  if (!img) return;
  const imgEl = document.getElementById('lightboxImage');
  imgEl.style.opacity = '0';
  setTimeout(() => {
    imgEl.src = img.file_path;
    imgEl.alt = img.title;
    imgEl.style.opacity = '1';
  }, 120);
  document.getElementById('lightboxTitle').textContent       = img.title;
  document.getElementById('lightboxDescription').textContent = img.description || '';
  document.getElementById('lightboxCategory').textContent    = img.category || '';
  document.getElementById('glbCounter').textContent          = `${index + 1} / ${glpLbImages.length}`;
}

function closeLightbox() {
  document.getElementById('glLightbox').classList.remove('active');
  // keep body locked if album modal is still open
  if (!document.getElementById('glpCatModal').classList.contains('open')) {
    document.body.style.overflow = '';
  }
}

function navigateLightbox(dir) {
  glpLbIndex = (glpLbIndex + dir + glpLbImages.length) % glpLbImages.length;
  showLightboxImage(glpLbIndex);
}

function glbBgClick(e) {
  if (e.target.id === 'glLightbox') closeLightbox();
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
    return;
  }
  const modal = document.getElementById('glpCatModal');
  if (modal && modal.classList.contains('open') && e.key === 'Escape') {
    closeAlbum();
  }
});
