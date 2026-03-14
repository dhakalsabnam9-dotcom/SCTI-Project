// =============================================
//  GALLERY PAGE
// =============================================
const galleryPage = `
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

// Distinct, vivid gradients per category
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

      // Update hero counters
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

  if (activeCategory !== '') {
    const p    = getPalette(activeCategory);
    const icon = CAT_ICONS[activeCategory.toLowerCase()] || CAT_ICONS.default;
    content.innerHTML = buildCategorySection(activeCategory, icon, p, images);
    fadeInCards();
    return;
  }

  // Group by category
  const groups = {}, nocat = [];
  images.forEach((img, i) => {
    img._idx = i;
    if (img.category) {
      (groups[img.category] = groups[img.category] || []).push(img);
    } else {
      nocat.push(img);
    }
  });

  let html = '';
  let ci   = 0;
  Object.keys(groups).forEach(cat => {
    const icon = CAT_ICONS[cat.toLowerCase()] || CAT_ICONS.default;
    const p    = getPalette(cat);
    html += buildCategorySection(cat, icon, p, groups[cat]);
    ci++;
  });
  if (nocat.length) {
    html += buildCategorySection('General', 'fa-images', getPalette('__gen__'), nocat);
  }

  content.innerHTML = html;
  fadeInCards();
}

function buildCategorySection(cat, icon, p, images) {
  const count = images.length;
  const tiles  = images.map(img => buildTile(img, img._idx ?? 0, p)).join('');

  return `
    <div class="glp-section">
      <div class="glp-section-head" style="--fa:${p.from};--fb:${p.to}">
        <div class="glp-sh-left">
          <div class="glp-sh-icon"><i class="fa ${icon}"></i></div>
          <div class="glp-sh-text">
            <h2>${cat}</h2>
            <span>${count} photo${count !== 1 ? 's' : ''} in this collection</span>
          </div>
        </div>
        <div class="glp-sh-badge">${count}<small>PHOTOS</small></div>
      </div>
      <div class="glp-grid">${tiles}</div>
    </div>`;
}

function buildTile(img, index, p) {
  const src = img.thumbnail_path || img.file_path;
  return `
    <div class="glp-tile" onclick="openLightbox(${index})">
      <div class="glp-tile-img-wrap">
        <img src="${src}" alt="${img.title}" loading="lazy"
             onerror="this.src='assets/images/img1.jpg'">
        <div class="glp-tile-over">
          <div class="glp-tile-zoom"><i class="fa fa-magnifying-glass-plus"></i></div>
        </div>
      </div>
      <p class="glp-tile-title">${img.title}</p>
    </div>`;
}

function fadeInCards() {
  document.querySelectorAll('.glp-section').forEach((el, i) => {
    el.style.opacity   = '0';
    el.style.transform = 'translateY(28px)';
    setTimeout(() => {
      el.style.transition = 'opacity .5s ease, transform .5s ease';
      el.style.opacity    = '1';
      el.style.transform  = 'translateY(0)';
    }, i * 90);
  });
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
  document.body.style.overflow = 'hidden';
}

function closeLightbox() {
  document.getElementById('glLightbox').classList.remove('active');
  document.body.style.overflow = '';
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
  document.querySelectorAll('.glp-filter').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.glp-filter').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      loadGalleryImages(btn.dataset.category);
    });
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
