// Gallery page content
const galleryPage = `
  <section class="section">
    <div class="container">
      <div class="section-header">
        <h2><i class="fa fa-images"></i> Photo Gallery</h2>
        <p>Capturing moments of learning, growth, and achievement at SCTI</p>
      </div>

      <!-- Filter Section -->
      <div class="gallery-filters">
        <button class="filter-btn active" data-category="">
          <i class="fa fa-th"></i> All
        </button>
        <button class="filter-btn" data-category="campus">
          <i class="fa fa-university"></i> Campus
        </button>
        <button class="filter-btn" data-category="events">
          <i class="fa fa-calendar-alt"></i> Events
        </button>
        <button class="filter-btn" data-category="students">
          <i class="fa fa-user-graduate"></i> Students
        </button>
        <button class="filter-btn" data-category="facilities">
          <i class="fa fa-building"></i> Facilities
        </button>
        <button class="filter-btn" data-category="activities">
          <i class="fa fa-running"></i> Activities
        </button>
        <button class="filter-btn" data-category="achievements">
          <i class="fa fa-trophy"></i> Achievements
        </button>
      </div>

      <!-- Loading State -->
      <div id="galleryLoading" class="gallery-loading">
        <div class="spinner"></div>
        <p>Loading gallery...</p>
      </div>

      <!-- Gallery Grid -->
      <div class="gallery-grid" id="galleryGrid">
        <!-- Images will be loaded dynamically -->
      </div>

      <!-- Empty State -->
      <div id="galleryEmpty" class="gallery-empty" style="display: none;">
        <i class="fa fa-images"></i>
        <h3>No Images Found</h3>
        <p>There are no images in this category yet.</p>
      </div>
    </div>
  </section>

  <!-- Lightbox Modal -->
  <div id="lightbox" class="lightbox">
    <button class="lightbox-close" onclick="closeLightbox()">
      <i class="fa fa-times"></i>
    </button>
    <button class="lightbox-prev" onclick="navigateLightbox(-1)">
      <i class="fa fa-chevron-left"></i>
    </button>
    <button class="lightbox-next" onclick="navigateLightbox(1)">
      <i class="fa fa-chevron-right"></i>
    </button>
    <div class="lightbox-content">
      <img id="lightboxImage" src="" alt="">
      <div class="lightbox-info">
        <h3 id="lightboxTitle"></h3>
        <p id="lightboxDescription"></p>
        <span id="lightboxCategory" class="lightbox-category"></span>
      </div>
    </div>
  </div>
`;

// Gallery state
let galleryImages = [];
let currentImageIndex = 0;
let currentCategory = '';

// Load gallery images
async function loadGalleryImages(category = '') {
  const galleryGrid = document.getElementById('galleryGrid');
  const galleryLoading = document.getElementById('galleryLoading');
  const galleryEmpty = document.getElementById('galleryEmpty');
  
  // Show loading
  galleryLoading.style.display = 'flex';
  galleryGrid.style.display = 'none';
  galleryEmpty.style.display = 'none';
  
  try {
    const response = await fetch(\`pages/gallery-public.php?category=\${category}\`);
    const result = await response.json();
    
    if (result.success && result.images.length > 0) {
      galleryImages = result.images;
      currentCategory = category;
      renderGallery(result.images);
      galleryGrid.style.display = 'grid';
    } else {
      galleryEmpty.style.display = 'flex';
    }
  } catch (error) {
    console.error('Error loading gallery:', error);
    galleryEmpty.style.display = 'flex';
  } finally {
    galleryLoading.style.display = 'none';
  }
}

// Render gallery
function renderGallery(images) {
  const galleryGrid = document.getElementById('galleryGrid');
  
  galleryGrid.innerHTML = images.map((image, index) => \`
    <div class="gallery-item" onclick="openLightbox(\${index})" data-aos="fade-up" data-aos-delay="\${index * 50}">
      <img src="\${image.thumbnail_path || image.file_path}" alt="\${image.title}" loading="lazy">
      <div class="gallery-overlay">
        <div class="gallery-text">
          <i class="fa fa-search-plus"></i>
          <h4>\${image.title}</h4>
          \${image.category ? \`<span class="gallery-badge">\${image.category}</span>\` : ''}
        </div>
      </div>
    </div>
  \`).join('');
  
  // Initialize AOS animations if available
  if (typeof AOS !== 'undefined') {
    AOS.refresh();
  }
}

// Open lightbox
function openLightbox(index) {
  currentImageIndex = index;
  const image = galleryImages[index];
  
  document.getElementById('lightboxImage').src = image.file_path;
  document.getElementById('lightboxTitle').textContent = image.title;
  document.getElementById('lightboxDescription').textContent = image.description || '';
  document.getElementById('lightboxCategory').textContent = image.category || '';
  
  document.getElementById('lightbox').classList.add('active');
  document.body.style.overflow = 'hidden';
}

// Close lightbox
function closeLightbox() {
  document.getElementById('lightbox').classList.remove('active');
  document.body.style.overflow = '';
}

// Navigate lightbox
function navigateLightbox(direction) {
  currentImageIndex += direction;
  
  if (currentImageIndex < 0) {
    currentImageIndex = galleryImages.length - 1;
  } else if (currentImageIndex >= galleryImages.length) {
    currentImageIndex = 0;
  }
  
  openLightbox(currentImageIndex);
}

// Setup gallery filters
function setupGalleryFilters() {
  const filterButtons = document.querySelectorAll('.filter-btn');
  
  filterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      // Update active state
      filterButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      
      // Load filtered images
      const category = btn.getAttribute('data-category');
      loadGalleryImages(category);
    });
  });
}

// Keyboard navigation for lightbox
document.addEventListener('keydown', (e) => {
  const lightbox = document.getElementById('lightbox');
  if (lightbox && lightbox.classList.contains('active')) {
    if (e.key === 'Escape') {
      closeLightbox();
    } else if (e.key === 'ArrowLeft') {
      navigateLightbox(-1);
    } else if (e.key === 'ArrowRight') {
      navigateLightbox(1);
    }
  }
});

// Initialize gallery when page loads
setTimeout(() => {
  if (document.querySelector('.gallery-grid')) {
    loadGalleryImages();
    setupGalleryFilters();
  }
}, 100);
