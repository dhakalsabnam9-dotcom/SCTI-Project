<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

require_once '../includes/config.php';

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Gallery | SCTI Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
    
    .container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 20px;
    }
    
    .page-header {
      background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
      color: white;
      padding: 30px;
      border-radius: 10px;
      margin-bottom: 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 15px rgba(23,162,184,0.2);
    }
    
    .page-header h1 {
      margin: 0;
      font-size: 28px;
    }
    
    .back-btn {
      background: rgba(255,255,255,0.2);
      color: white;
      padding: 10px 20px;
      border-radius: 5px;
      text-decoration: none;
      transition: all 0.3s;
    }
    
    .back-btn:hover {
      background: rgba(255,255,255,0.3);
    }
    
    .upload-section {
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }
    
    .upload-section h2 {
      margin-top: 0;
      color: #17a2b8;
      margin-bottom: 20px;
    }
    
    .upload-area {
      border: 3px dashed #17a2b8;
      border-radius: 10px;
      padding: 40px;
      text-align: center;
      background: #f8f9fa;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .upload-area:hover {
      background: #e9ecef;
      border-color: #138496;
    }
    
    .upload-area.dragover {
      background: #d1ecf1;
      border-color: #0c5460;
    }
    
    .upload-area i {
      font-size: 48px;
      color: #17a2b8;
      margin-bottom: 15px;
    }
    
    .upload-area p {
      color: #666;
      margin: 10px 0;
    }
    
    #fileInput {
      display: none;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 8px;
      color: #333;
      font-weight: 500;
    }
    
    .form-group input,
    .form-group textarea,
    .form-group select {
      width: 100%;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 14px;
    }
    
    .form-group textarea {
      resize: vertical;
      min-height: 80px;
    }
    
    .btn {
      padding: 12px 30px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
      transition: all 0.3s;
    }
    
    .btn-primary {
      background: linear-gradient(135deg, #17a2b8, #138496);
      color: white;
    }
    
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(23,162,184,0.3);
    }
    
    .btn-danger {
      background: linear-gradient(135deg, #dc3545, #c82333);
      color: white;
    }
    
    .btn-danger:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(220,53,69,0.3);
    }
    
    .gallery-section {
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .gallery-section h2 {
      margin-top: 0;
      color: #17a2b8;
      margin-bottom: 20px;
    }
    
    .filter-bar {
      display: flex;
      gap: 15px;
      margin-bottom: 25px;
      flex-wrap: wrap;
    }
    
    .filter-bar select,
    .filter-bar input {
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 14px;
    }
    
    .gallery-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
    }
    
    .gallery-item {
      background: white;
      border-radius: 15px;
      overflow: hidden;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      position: relative;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      cursor: pointer;
    }
    
    .gallery-item::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #17a2b8, #138496, #0c5460);
      transform: scaleX(0);
      transform-origin: left;
      transition: transform 0.4s;
      z-index: 10;
    }
    
    .gallery-item:hover::before {
      transform: scaleX(1);
    }
    
    .gallery-item:hover {
      transform: translateY(-10px) scale(1.02);
      box-shadow: 0 15px 40px rgba(23,162,184,0.3);
    }
    
    .gallery-item-image-wrapper {
      position: relative;
      width: 100%;
      height: 250px;
      overflow: hidden;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .gallery-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: all 0.5s ease;
    }
    
    .gallery-item:hover img {
      transform: scale(1.15) rotate(2deg);
      filter: brightness(0.85) contrast(1.1);
    }
    
    .image-overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(to bottom, rgba(23,162,184,0.2) 0%, rgba(0,0,0,0.8) 100%);
      opacity: 0;
      transition: all 0.4s;
      display: flex;
      align-items: flex-end;
      padding: 20px;
    }
    
    .gallery-item:hover .image-overlay {
      opacity: 1;
    }
    
    .overlay-info {
      color: white;
      width: 100%;
      transform: translateY(20px);
      transition: transform 0.4s;
    }
    
    .gallery-item:hover .overlay-info {
      transform: translateY(0);
    }
    
    .overlay-info h4 {
      margin: 0 0 5px 0;
      font-size: 16px;
      font-weight: 600;
      text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    
    .overlay-info p {
      margin: 0;
      font-size: 12px;
      opacity: 0.9;
    }
    
    .gallery-item-info {
      padding: 20px;
      background: linear-gradient(to bottom, white 0%, #f8f9fa 100%);
    }
    
    .gallery-item-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 12px;
    }
    
    .gallery-item-info h3 {
      margin: 0;
      font-size: 17px;
      color: #333;
      font-weight: 600;
      flex: 1;
      line-height: 1.4;
      transition: color 0.3s;
    }
    
    .gallery-item:hover .gallery-item-info h3 {
      color: #17a2b8;
    }
    
    .gallery-item-info p {
      margin: 0 0 15px 0;
      font-size: 13px;
      color: #666;
      line-height: 1.6;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    
    .gallery-item-meta {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 15px;
      font-size: 12px;
      color: #999;
    }
    
    .gallery-item-meta i {
      color: #17a2b8;
      transition: transform 0.3s;
    }
    
    .gallery-item:hover .gallery-item-meta i {
      transform: scale(1.2) rotate(360deg);
    }
    
    .gallery-item-actions {
      display: flex;
      gap: 10px;
    }
    
    .gallery-item-actions button {
      flex: 1;
      padding: 10px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 13px;
      font-weight: 600;
      transition: all 0.3s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      position: relative;
      overflow: hidden;
    }
    
    .gallery-item-actions button::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255,255,255,0.3);
      transform: translate(-50%, -50%);
      transition: width 0.4s, height 0.4s;
    }
    
    .gallery-item-actions button:hover::before {
      width: 200px;
      height: 200px;
    }
    
    .gallery-item-actions button i {
      position: relative;
      z-index: 1;
      transition: transform 0.3s;
    }
    
    .gallery-item-actions button:hover i {
      transform: scale(1.2) rotate(15deg);
    }
    
    .btn-edit {
      background: linear-gradient(135deg, #ffc107, #ff9800);
      color: white;
    }
    
    .btn-edit:hover {
      background: linear-gradient(135deg, #ff9800, #f57c00);
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(255,193,7,0.4);
    }
    
    .btn-delete {
      background: linear-gradient(135deg, #dc3545, #c82333);
      color: white;
    }
    
    .btn-delete:hover {
      background: linear-gradient(135deg, #c82333, #bd2130);
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(220,53,69,0.4);
    }
    
    .category-badge {
      display: inline-block;
      padding: 6px 14px;
      background: linear-gradient(135deg, #17a2b8, #138496);
      color: white;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      box-shadow: 0 2px 8px rgba(23,162,184,0.3);
    }
    
    .image-badge {
      position: absolute;
      top: 15px;
      right: 15px;
      background: rgba(255,255,255,0.95);
      padding: 8px 14px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 600;
      color: #17a2b8;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      z-index: 10;
      backdrop-filter: blur(10px);
    }
    
    .quick-view-btn {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) scale(0);
      background: white;
      color: #17a2b8;
      border: none;
      padding: 15px 25px;
      border-radius: 50px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      box-shadow: 0 8px 25px rgba(0,0,0,0.3);
      z-index: 10;
    }
    
    .gallery-item:hover .quick-view-btn {
      transform: translate(-50%, -50%) scale(1);
    }
    
    .quick-view-btn:hover {
      background: #17a2b8;
      color: white;
      transform: translate(-50%, -50%) scale(1.1);
    }
    
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: #999;
    }
    
    .empty-state i {
      font-size: 64px;
      margin-bottom: 20px;
      opacity: 0.3;
    }
    
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 1000;
      align-items: center;
      justify-content: center;
    }
    
    .modal.active {
      display: flex;
    }
    
    .modal-content {
      background: white;
      padding: 30px;
      border-radius: 10px;
      max-width: 500px;
      width: 90%;
      max-height: 90vh;
      overflow-y: auto;
    }
    
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    
    .modal-header h3 {
      margin: 0;
      color: #17a2b8;
    }
    
    .close-modal {
      background: none;
      border: none;
      font-size: 24px;
      cursor: pointer;
      color: #999;
    }
    
    .close-modal:hover {
      color: #333;
    }
    
    .alert {
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
    }
    
    .alert-success {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    
    .alert-error {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Manage Gallery - Upload and manage images for the school website</marquee>
</div>

<div class="container">
  
  <!-- Page Header -->
  <div class="page-header">
    <div>
      <h1><i class="fa fa-images"></i> Manage Gallery</h1>
      <div style="background: transparent; padding: 0; font-size: 14px; margin-top: 6px;">
        <a href="../dashboards/admin-dashboard.php" style="color: white; text-decoration: none;"><i class="fa fa-home"></i> Dashboard</a> / Manage Gallery
      </div>
    </div>
    <a href="../dashboards/admin-dashboard.php" class="back-btn">
      <i class="fa fa-arrow-left"></i> Back to Dashboard
    </a>
  </div>

  <!-- Upload Section -->
  <div class="upload-section">
    <h2><i class="fa fa-cloud-upload-alt"></i> Upload New Image</h2>
    
    <div id="alertContainer"></div>
    
    <form id="uploadForm" enctype="multipart/form-data">
      <div class="upload-area" id="uploadArea">
        <i class="fa fa-cloud-upload-alt"></i>
        <h3>Drag & Drop Image Here</h3>
        <p>or click to browse</p>
        <p style="font-size: 12px; color: #999;">Supported formats: JPG, PNG, GIF, WEBP (Max 10MB)</p>
        <input type="file" id="fileInput" name="image" accept="image/*" required>
      </div>
      
      <div class="form-group" style="margin-top: 20px;">
        <label for="imageTitle">Image Title *</label>
        <input type="text" id="imageTitle" name="title" required placeholder="Enter image title">
      </div>
      
      <div class="form-group">
        <label for="imageDescription">Description</label>
        <textarea id="imageDescription" name="description" placeholder="Enter image description (optional)"></textarea>
      </div>
      
      <div class="form-group">
        <label for="imageCategory">Category</label>
        <select id="imageCategory" name="category">
          <option value="">Select category</option>
          <option value="campus">Campus</option>
          <option value="events">Events</option>
          <option value="students">Students</option>
          <option value="facilities">Facilities</option>
          <option value="activities">Activities</option>
          <option value="achievements">Achievements</option>
          <option value="other">Other</option>
        </select>
      </div>
      
      <button type="submit" class="btn btn-primary">
        <i class="fa fa-upload"></i> Upload Image
      </button>
    </form>
  </div>

  <!-- Gallery Section -->
  <div class="gallery-section">
    <h2><i class="fa fa-th"></i> Gallery Images</h2>
    
    <div class="filter-bar">
      <select id="filterCategory">
        <option value="">All Categories</option>
        <option value="campus">Campus</option>
        <option value="events">Events</option>
        <option value="students">Students</option>
        <option value="facilities">Facilities</option>
        <option value="activities">Activities</option>
        <option value="achievements">Achievements</option>
        <option value="other">Other</option>
      </select>
      
      <input type="text" id="searchInput" placeholder="Search images...">
      
      <button class="btn btn-primary" onclick="loadGallery()">
        <i class="fa fa-sync"></i> Refresh
      </button>
    </div>
    
    <div class="gallery-grid" id="galleryGrid">
      <div class="empty-state">
        <i class="fa fa-images"></i>
        <p>No images uploaded yet. Upload your first image above!</p>
      </div>
    </div>
  </div>

</div>

<!-- Edit Modal -->
<div class="modal" id="editModal">
  <div class="modal-content">
    <div class="modal-header">
      <h3><i class="fa fa-edit"></i> Edit Image</h3>
      <button class="close-modal" onclick="closeEditModal()">×</button>
    </div>
    
    <form id="editForm">
      <input type="hidden" id="editImageId" name="id">
      
      <div class="form-group">
        <label for="editImageTitle">Image Title *</label>
        <input type="text" id="editImageTitle" name="title" required>
      </div>
      
      <div class="form-group">
        <label for="editImageDescription">Description</label>
        <textarea id="editImageDescription" name="description"></textarea>
      </div>
      
      <div class="form-group">
        <label for="editImageCategory">Category</label>
        <select id="editImageCategory" name="category">
          <option value="">Select category</option>
          <option value="campus">Campus</option>
          <option value="events">Events</option>
          <option value="students">Students</option>
          <option value="facilities">Facilities</option>
          <option value="activities">Activities</option>
          <option value="achievements">Achievements</option>
          <option value="other">Other</option>
        </select>
      </div>
      
      <div style="display: flex; gap: 10px;">
        <button type="submit" class="btn btn-primary" style="flex: 1;">
          <i class="fa fa-save"></i> Save Changes
        </button>
        <button type="button" class="btn" style="background: #6c757d; color: white; flex: 1;" onclick="closeEditModal()">
          Cancel
        </button>
      </div>
    </form>
  </div>
</div>

<script>
// Upload area drag and drop
const uploadArea = document.getElementById('uploadArea');
const fileInput = document.getElementById('fileInput');

uploadArea.addEventListener('click', () => fileInput.click());

uploadArea.addEventListener('dragover', (e) => {
  e.preventDefault();
  uploadArea.classList.add('dragover');
});

uploadArea.addEventListener('dragleave', () => {
  uploadArea.classList.remove('dragover');
});

uploadArea.addEventListener('drop', (e) => {
  e.preventDefault();
  uploadArea.classList.remove('dragover');
  
  const files = e.dataTransfer.files;
  if (files.length > 0) {
    fileInput.files = files;
    showAlert('File selected: ' + files[0].name, 'success');
  }
});

fileInput.addEventListener('change', (e) => {
  if (e.target.files.length > 0) {
    showAlert('File selected: ' + e.target.files[0].name, 'success');
  }
});

// Upload form submission
document.getElementById('uploadForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const formData = new FormData(e.target);
  
  try {
    const response = await fetch('gallery-upload.php', {
      method: 'POST',
      body: formData
    });
    
    const result = await response.json();
    
    if (result.success) {
      showAlert('Image uploaded successfully!', 'success');
      e.target.reset();
      loadGallery();
    } else {
      showAlert(result.message || 'Upload failed', 'error');
    }
  } catch (error) {
    showAlert('Error uploading image: ' + error.message, 'error');
  }
});

// Load gallery images
async function loadGallery() {
  const category = document.getElementById('filterCategory').value;
  const search = document.getElementById('searchInput').value;
  
  try {
    const response = await fetch(`gallery-list.php?category=${category}&search=${search}`);
    const result = await response.json();
    
    const galleryGrid = document.getElementById('galleryGrid');
    
    if (result.success && result.images.length > 0) {
      galleryGrid.innerHTML = result.images.map(image => {
        const date = new Date(image.created_at);
        const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        
        return `
        <div class="gallery-item">
          <div class="gallery-item-image-wrapper">
            <img src="../${image.thumbnail_path || image.file_path}" alt="${image.title}" loading="lazy">
            ${image.category ? `<span class="image-badge">${image.category}</span>` : ''}
            <button class="quick-view-btn" onclick="quickView('../${image.file_path}', '${image.title}')">
              <i class="fa fa-eye"></i> Quick View
            </button>
            <div class="image-overlay">
              <div class="overlay-info">
                <h4>${image.title}</h4>
                <p><i class="fa fa-calendar"></i> ${formattedDate}</p>
              </div>
            </div>
          </div>
          <div class="gallery-item-info">
            <div class="gallery-item-header">
              <h3>${image.title}</h3>
            </div>
            ${image.description ? `<p>${image.description}</p>` : '<p style="color: #ccc; font-style: italic;">No description</p>'}
            <div class="gallery-item-meta">
              <span><i class="fa fa-calendar"></i> ${formattedDate}</span>
              ${image.category ? `<span><i class="fa fa-tag"></i> ${image.category}</span>` : ''}
            </div>
            <div class="gallery-item-actions">
              <button class="btn-edit" onclick="editImage(${image.id})">
                <i class="fa fa-edit"></i> Edit
              </button>
              <button class="btn-delete" onclick="deleteImage(${image.id})">
                <i class="fa fa-trash"></i> Delete
              </button>
            </div>
          </div>
        </div>
      `;
      }).join('');
    } else {
      galleryGrid.innerHTML = `
        <div class="empty-state">
          <i class="fa fa-images"></i>
          <p>No images found</p>
        </div>
      `;
    }
  } catch (error) {
    showAlert('Error loading gallery: ' + error.message, 'error');
  }
}

// Quick view function
function quickView(imagePath, title) {
  const modal = document.createElement('div');
  modal.className = 'modal active';
  modal.style.zIndex = '2000';
  modal.innerHTML = `
    <div class="modal-content" style="max-width: 90%; max-height: 90vh; padding: 0; overflow: hidden;">
      <div style="position: relative;">
        <button onclick="this.closest('.modal').remove()" style="position: absolute; top: 15px; right: 15px; background: rgba(0,0,0,0.7); color: white; border: none; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; font-size: 20px; z-index: 10; transition: all 0.3s;">
          ×
        </button>
        <img src="${imagePath}" alt="${title}" style="width: 100%; height: auto; display: block; max-height: 85vh; object-fit: contain;">
        <div style="padding: 20px; background: white;">
          <h3 style="margin: 0; color: #17a2b8;">${title}</h3>
        </div>
      </div>
    </div>
  `;
  document.body.appendChild(modal);
  
  modal.addEventListener('click', (e) => {
    if (e.target === modal) {
      modal.remove();
    }
  });
}

// Edit image
async function editImage(id) {
  try {
    const response = await fetch(`gallery-get.php?id=${id}`);
    const result = await response.json();
    
    if (result.success) {
      document.getElementById('editImageId').value = result.image.id;
      document.getElementById('editImageTitle').value = result.image.title;
      document.getElementById('editImageDescription').value = result.image.description || '';
      document.getElementById('editImageCategory').value = result.image.category || '';
      
      document.getElementById('editModal').classList.add('active');
    }
  } catch (error) {
    showAlert('Error loading image: ' + error.message, 'error');
  }
}

// Edit form submission
document.getElementById('editForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const formData = new FormData(e.target);
  
  try {
    const response = await fetch('gallery-update.php', {
      method: 'POST',
      body: formData
    });
    
    const result = await response.json();
    
    if (result.success) {
      showAlert('Image updated successfully!', 'success');
      closeEditModal();
      loadGallery();
    } else {
      showAlert(result.message || 'Update failed', 'error');
    }
  } catch (error) {
    showAlert('Error updating image: ' + error.message, 'error');
  }
});

// Delete image
async function deleteImage(id) {
  if (!confirm('Are you sure you want to delete this image?')) {
    return;
  }
  
  try {
    const response = await fetch('gallery-delete.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ id })
    });
    
    const result = await response.json();
    
    if (result.success) {
      showAlert('Image deleted successfully!', 'success');
      loadGallery();
    } else {
      showAlert(result.message || 'Delete failed', 'error');
    }
  } catch (error) {
    showAlert('Error deleting image: ' + error.message, 'error');
  }
}

// Close edit modal
function closeEditModal() {
  document.getElementById('editModal').classList.remove('active');
}

// Show alert
function showAlert(message, type) {
  const alertContainer = document.getElementById('alertContainer');
  const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
  
  alertContainer.innerHTML = `
    <div class="alert ${alertClass}">
      ${message}
    </div>
  `;
  
  setTimeout(() => {
    alertContainer.innerHTML = '';
  }, 5000);
}

// Filter and search
document.getElementById('filterCategory').addEventListener('change', loadGallery);
document.getElementById('searchInput').addEventListener('input', debounce(loadGallery, 500));

function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Load gallery on page load
loadGallery();
</script>

</body>
</html>
