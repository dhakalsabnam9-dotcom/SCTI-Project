// =============================================
//  GALLERY PAGE — Beautiful Category Group UI
// =============================================
const galleryPage = `
  <div class="gh-hero">
    <div class="gh-hero-shapes">
      <div class="gh-shape gh-s1"></div>
      <div class="gh-shape gh-s2"></div>
      <div class="gh-shape gh-s3"></div>
    </div>
    <div class="gh-hero-body">
      <div class="gh-hero-badge"><i class="fa fa-camera"></i> SCTI Gallery</div>
      <h1>Our Photo Gallery</h1>
      <p>Explore moments of learning, achievement, and campus life at SCTI</p>
      <div class="gh-hero-stats" id="ghHeroStats" style="display:none;">
        <div class="gh-hs-item"><span id="ghHsPhotos">0</span><small>Photos</small></div>
        <div class="gh-hs-div"></div>
        <div class="gh-hs-item"><span id="ghHsCats">0</span><small>Categories</small></div>
      </div>
    </div>
    <div class="gh-wave">
      <svg viewBox="0 0 1440 70" preserveAspectRatio="none">
        <path d="M0,35 C480,70 960,0 1440,35 L1440,70 L0,70 Z" fill="#f0f4f8"/>
      </svg>
    </div>
  </div>

  <div class="gh-tabs-wrap">
    <div class="gh-tabs" id="galleryFilters">
      <button class="gh-tab active" data-category="">
        <i class="fa fa-border-all"></i> All Categories
      </button>
    </div>
  </div>

  <div class="gh-main">
    <div class="container">
      <div id="galleryLoading" class="gh-loading">
        <div class="gh-pulse"><div></div><div></div><div></div></div>
        <p>Loading gallery...</p>
      </div>
      <div id="galleryContent"></div>
      <div id="galleryEmpty" class="gh-empty" style="display:none;">
        <i class="fa fa-images"></i>
        <h3>No Photos Yet</h3>
        <p>No images found in this category.</p>
      </div>
    </div>
  </div>

  <div id="glLightbox" class="ghl" onclick="glbBgClick(event)">
    <button class="ghl-close" onclick="closeLightbox()"><i class="fa fa-xmark"></i></button>
    <button class="ghl-nav ghl-prev" onclick="navigateLightbox(-1)"><i class="fa fa-chevron-left"></i></button>
    <button class="ghl-nav ghl-next" onclick="navigateLightbox(1)"><i class="fa fa-chevron-right"></i></button>
    <div class="ghl-stage">
      <div class="ghl-img-box">
        <img id="lightboxImage" src="" alt="">
      </div>
      <div class="ghl-caption">
        <div class="ghl-caption-left">
          <h3 id="lightboxTitle"></h3>
          <p id="lightboxDescription"></p>
        </div>
        <div class="ghl-caption-right">
          <span id="lightboxCategory" class="ghl-cat-badge"></span>
          <span id="glbCounter" class="ghl-counter"></span>
        </div>
      </div>
    </div>
  </div>
`;

// =============================================
//  CONFIG
// =============================================
let galleryImages = [];
let currentImageIndex = 0;

const catIcons = {
  campus:       'fa-university',
  events:       'fa-calendar-star',
  students:     'fa-user-graduate',
  facilities:   'fa-building',
  activities:   'fa-person-running',
  achievements: 'fa-trophy',
  sports:       'fa-futbol',
  labs:         'fa-flask',
  library:      'fa-book-open',
  classroom:    'fa-chalkboard-teacher',
  ceremony:     'fa-award',
  default:      'fa-folder-open'
};

const palettes = [
  { c1: '#004080', c2: '#0d6efd', light: '#e8f0fe', text: '#004080' },
  { c1: '#0d6efd', c2: '#17a2b8', light: '#e0f7fa', text: '#0d6efd' },
  { c1: '#198754', c2: '#20c997', light: '#e6f9f0', text: '#198754' },
  { c1: '#fd7e14', c2: '#ffc107', light: '#fff3cd', text: '#b45309' },
  { c1: '#6f42c1', c2: '#d63384', light: '#f3e8ff', text: '#6f42c1' },
  { c1: '#dc3545', c2: '#fd7e14', light: '#fde8e8', text: '#dc3545' },
  { c1: '#0dcaf0', c2: '#0d6efd', light: '#e0f4ff', text: '#0369a1' },
  { c1: '#20c997', c2: '#198754', light: '#d1fae5', text: '#065f46' },
];
let paletteIdx = 0;
const catPaletteMap = {};

function getPalette(cat) {
  if (!catPaletteMap[cat]) {
    catPaletteMap[cat] = palettes[paletteIdx % palettes.length];
    paletteIdx++;
  }
  return catPaletteMap[cat];
}

// =============================================
//  LOAD CATEGORIES
// =============================================
async function loadGalleryCategories() {
  try {
    const res  = await fetch('pages/gallery-categories.php');
    const data = await res.json();
    if (!data.success) { setupGalleryFilters(); return; }

    const container = document.getElementById('galleryFilters');
    if (!container) return;

    data.categories.forEach(cat => {
      const icon = catIcons[cat.name.toLowerCase()] || catIcons.default;
      const p    = getPalette(cat.name);
      const btn  = document.createElement('button');
      btn.className = 'gh-tab';
      btn.setAttribute('data-category', cat.name);
      btn.style.setProperty('--tc1', p.c1);
      btn.innerHTML = `<i class="fa ${icon}"></i> ${cat.name}`;
      container.appendChild(btn);
    });

    setupGalleryFilters();
  } catch (e) {
    setupGalleryFilters();
  }
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
      const photoEl = document.getElementById('ghHsPhotos');
      const catEl   = document.getElementById('ghHsCats');
      const statsEl = document.getElementById('ghHeroStats');
      if (photoEl) photoEl.textContent = result.images.length;
      if (catEl)   catEl.textContent   = cats.size;
      if (statsEl) statsEl.style.display = 'flex';
    } else {
      empty.style.display = 'flex';
    }
  } catch (err) {
    console.error('Gallery load error:', err);
    empty.style.display = 'flex';
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
    const icon = catIcons[activeCategory.toLowerCase()] || catIcons.default;
    content.innerHTML = categoryCardHTML(activeCategory, icon, p, images, 0);
    animateCards();
    return;
  }

  const groups = {};
  const nocat  = [];
  images.forEach((img, i) => {
    img._idx = i;
    if (img.category) {
      if (!groups[img.category]) groups[img.category] = [];
      groups[img.category].push(img);
    } else {
      nocat.push(img);
    }
  });

  let html = '';
  let cardIdx = 0;
  Object.keys(groups).forEach(cat => {
    const icon = catIcons[cat.toLowerCase()] || catIcons.default;
    const p    = getPalette(cat);
    html += categoryCardHTML(cat, icon, p, groups[cat], cardIdx);
    cardIdx++;
  });

  if (nocat.length) {
    const p = getPalette('__general__');
    html += categoryCardHTML('General', 'fa-images', p, nocat, cardIdx);
  }

  content.innerHTML = html;
  animateCards();
}

// =============================================
//  CATEGORY CARD HTML
// =============================================
function categoryCardHTML(cat, icon, p, images, cardIdx) {
  const count = images.length;

  // Up to 3 blurred preview thumbs for header background
  const previews = images.slice(0, 3).map(img => {
    const src = img.thumbnail_path || img.file_path;
    return `<div class="gc-preview-thumb" style="background-image:url('${src}')"></div>`;
  }).join('');

  const items = images.map(img => itemHTML(img, img._idx !== undefined ? img._idx : 0, p)).join('');

  return `
    <div class="gc-card" style="--c1:${p.c1};--c2:${p.c2};--cl:${p.light};--ct:${p.text};animation-delay:${cardIdx * 0.1}s">
      <div class="gc-header">
        <div class="gc-header-bg"></div>
        <div class="gc-preview-strip">${previews}</div>
        <div class="gc-preview-overlay"></div>
        <div class="gc-header-content">
          <div class="gc-icon-wrap"><i class="fa ${icon}"></i></div>
          <div class="gc-header-text">
            <h2>${cat}</h2>
            <p>${count} photo${count !== 1 ? 's' : ''} in this collection</p>
          </div>
          <div class="gc-count-badge"><span>${count}</span><small>photos</small></div>
        </div>
        <div class="gc-header-wave">
          <svg viewBox="0 0 400 30" preserveAspectRatio="none">
            <path d="M0,15 C100,30 300,0 400,15 L400,30 L0,30 Z" fill="white"/>
          </svg>
        </div>
      </div>
      <div class="gc-body">
        <div class="gc-grid">${items}</div>
        ${count > 8 ? `<div class="gc-show-more" onclick="toggleShowMore(this)">
          <i class="fa fa-chevron-down"></i> Show all ${count} photos
        </div>` : ''}
      </div>
    </div>`;
}

function itemHTML(img, index, p) {
  const thumb = img.thumbnail_path || img.file_path;
  return `
    <div class="gc-item" onclick="openLightbox(${index})">
      <div class="gc-img-wrap">
        <img src="${thumb}" alt="${img.title}" loading="lazy"
             onerror="this.src='assets/images/img1.jpg'">
      </div>
      <div class="gc-overlay">
        <div class="gc-overlay-icon"><i class="fa fa-magnifying-glass-plus"></i></div>
        <div class="gc-overlay-title">${img.title}</div>
      </div>
    </div>`;
}

function toggleShowMore(btn) {
  const grid = btn.previousElementSibling;
  const isExpanded = grid.classList.toggle('gc-grid-expanded');
  btn.innerHTML = isExpanded
    ? '<i class="fa fa-chevron-up"></i> Show less'
    : `<i class="fa fa-chevron-down"></i> Show all photos`;
}

function animateCards() {
  document.querySelectorAll('.gc-card').forEach((card, i) => {
    card.style.opacity   = '0';
    card.style.transform = 'translateY(40px)';
    setTimeout(() => {
      card.style.transition = 'opacity 0.55s ease, transform 0.55s ease';
      card.style.opacity    = '1';
      card.style.transform  = 'translateY(0)';
    }, i * 120);
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
    imgEl.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
    imgEl.style.opacity    = '1';
    imgEl.style.transform  = 'translateX(0)';
  }, 160);
}

function glbBgClick(e) {
  if (e.target.id === 'glLightbox') closeLightbox();
}

// =============================================
//  FILTERS
// =============================================
function setupGalleryFilters() {
  document.querySelectorAll('.gh-tab').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.gh-tab').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      loadGalleryImages(btn.getAttribute('data-category'));
    });
  });
}

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
