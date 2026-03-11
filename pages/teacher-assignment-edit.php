<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../index.php');
    exit();
}
$fullName = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Teacher';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Assignment | SCTI Teacher Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
    
    .top-header {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white; padding: 10px 0; text-align: center;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .top-header marquee {
      font-size: 14px; font-weight: 500;
    }
    
    .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
    
    .page-header {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;
      box-shadow: 0 4px 15px rgba(40,167,69,0.2);
    }
    .page-header h1 { margin: 0 0 10px 0; font-size: 28px; }
    .breadcrumb { opacity: 1; font-size: 14px; background: transparent; padding: 0; margin-top: 10px; }
    .breadcrumb a { color: white; text-decoration: none; }
    
    .form-container {
      background: white; border-radius: 10px; padding: 40px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .form-section {
      margin-bottom: 30px;
    }
    .form-section h2 {
      color: #28a745; font-size: 20px; margin-bottom: 20px;
      padding-bottom: 10px; border-bottom: 2px solid #e9ecef;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    .form-label {
      display: block; font-weight: 600; color: #333;
      margin-bottom: 8px; font-size: 14px;
    }
    .form-label .required {
      color: #dc3545; margin-left: 3px;
    }
    .form-input, .form-select, .form-textarea {
      width: 100%; padding: 12px 15px; border: 2px solid #dee2e6;
      border-radius: 6px; font-size: 14px; font-family: inherit;
      transition: all 0.3s;
    }
    .form-input:focus, .form-select:focus, .form-textarea:focus {
      outline: none; border-color: #28a745;
      box-shadow: 0 0 0 3px rgba(40,167,69,0.1);
    }
    .form-textarea {
      min-height: 120px; resize: vertical;
    }
    
    .form-row {
      display: grid; grid-template-columns: 1fr 1fr;
      gap: 20px;
    }
    
    .form-help {
      font-size: 12px; color: #6c757d; margin-top: 5px;
    }
    
    .file-upload-area {
      border: 2px dashed #dee2e6; border-radius: 6px;
      padding: 30px; text-align: center; background: #f8f9fa;
      cursor: pointer; transition: all 0.3s;
    }
    .file-upload-area:hover {
      border-color: #28a745; background: #e8f5e9;
    }
    .file-upload-area i {
      font-size: 48px; color: #28a745; margin-bottom: 15px;
    }
    .file-upload-area p {
      color: #666; margin: 5px 0;
    }
    
    .checkbox-group {
      display: flex; align-items: center; gap: 10px;
    }
    .checkbox-input {
      width: 20px; height: 20px; cursor: pointer;
    }
    
    .form-actions {
      display: flex; gap: 15px; justify-content: flex-end;
      margin-top: 30px; padding-top: 30px;
      border-top: 2px solid #e9ecef;
    }
    .btn {
      padding: 12px 30px; border: none; border-radius: 6px;
      cursor: pointer; font-size: 14px; font-weight: 600;
      transition: all 0.3s; display: inline-flex;
      align-items: center; gap: 8px;
    }
    .btn-primary {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white;
    }
    .btn-primary:hover {
      background: linear-gradient(135deg, #20c997, #28a745);
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(40,167,69,0.3);
    }
    .btn-secondary {
      background: #6c757d; color: white;
    }
    .btn-secondary:hover {
      background: #5a6268;
    }
    .btn-danger {
      background: #dc3545; color: white;
    }
    .btn-danger:hover {
      background: #c82333;
    }
    
    #instructionsFileList, #resourcesFileList {
      margin-top: 10px;
    }
    
    .file-item {
      padding: 10px;
      background: #e8f5e9;
      border-radius: 6px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 8px;
    }
    
    .file-item-remove {
      background: #dc3545;
      color: white;
      border: none;
      padding: 5px 10px;
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .file-item-remove:hover {
      background: #c82333;
    }
    
    .footer {
      background: #2c3e50; color: white;
      text-align: center; padding: 20px;
      border-radius: 10px;
    }
    .footer p {
      margin: 0; font-size: 14px;
    }
  </style>
</head>
<body>

<div class="top-header">
  <marquee>Edit Assignment - Update assignment details and settings</marquee>
</div>

<div class="container">
  
  <div class="page-header">
    <h1><i class="fa fa-edit"></i> Edit Assignment</h1>
    <div class="breadcrumb">
      <a href="../dashboards/teacher-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / 
      <a href="teacher-assignments.php">Assignments</a> / Edit
    </div>
  </div>

  <div class="form-container">
    <form>
      
      <div class="form-section">
        <h2><i class="fa fa-info-circle"></i> Basic Information</h2>
        
        <div class="form-group">
          <label class="form-label">Assignment Title<span class="required">*</span></label>
          <input type="text" class="form-input" value="Database Design Project" placeholder="Enter assignment title">
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Class<span class="required">*</span></label>
            <select class="form-select">
              <option>Select a class</option>
              <option selected>Database Management - B.Tech IT Semester 3</option>
              <option>Programming Fundamentals - B.Tech IT Semester 1</option>
              <option>Web Development - Diploma Civil Semester 2</option>
              <option>Data Structures - B.Tech IT Semester 2</option>
            </select>
          </div>
          
          <div class="form-group">
            <label class="form-label">Assignment Type<span class="required">*</span></label>
            <select class="form-select">
              <option>Select type</option>
              <option selected>Project</option>
              <option>Quiz</option>
              <option>Lab Assignment</option>
              <option>Homework</option>
              <option>Exam</option>
            </select>
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label">Description<span class="required">*</span></label>
          <textarea class="form-textarea" placeholder="Enter assignment description">Design and implement a complete database system for a library management application. Include ER diagrams, normalization, and SQL queries.</textarea>
        </div>
      </div>

      <div class="form-section">
        <h2><i class="fa fa-calendar-alt"></i> Schedule & Grading</h2>
        
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Due Date<span class="required">*</span></label>
            <input type="datetime-local" class="form-input" value="2026-03-15T23:59">
          </div>
          
          <div class="form-group">
            <label class="form-label">Total Points<span class="required">*</span></label>
            <input type="number" class="form-input" value="100" placeholder="Enter total points">
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Available From</label>
            <input type="datetime-local" class="form-input" value="2026-03-01T00:00">
            <div class="form-help">When students can start viewing this assignment</div>
          </div>
          
          <div class="form-group">
            <label class="form-label">Late Submission Penalty (%)</label>
            <input type="number" class="form-input" value="10" placeholder="Enter penalty percentage">
            <div class="form-help">Percentage deducted per day late</div>
          </div>
        </div>
      </div>

      <div class="form-section">
        <h2><i class="fa fa-file-upload"></i> Attachments & Resources</h2>
        
        <div class="form-group">
          <label class="form-label">Assignment Instructions (PDF/DOC)</label>
          <input type="file" id="instructionsFile" accept=".pdf,.doc,.docx" style="display: none;" onchange="handleInstructionsUpload(this)">
          <div class="file-upload-area" onclick="document.getElementById('instructionsFile').click();">
            <i class="fa fa-cloud-upload-alt"></i>
            <p><strong>Click to upload</strong> or drag and drop</p>
            <p class="form-help">PDF, DOC, DOCX up to 10MB</p>
          </div>
          <div id="instructionsFileList" style="margin-top: 10px;"></div>
        </div>
        
        <div class="form-group">
          <label class="form-label">Additional Resources</label>
          <input type="file" id="resourcesFile" multiple accept=".pdf,.doc,.docx,.zip,.ppt,.pptx" style="display: none;" onchange="handleResourcesUpload(this)">
          <div class="file-upload-area" onclick="document.getElementById('resourcesFile').click();">
            <i class="fa fa-paperclip"></i>
            <p><strong>Upload reference materials</strong></p>
            <p class="form-help">Multiple files allowed</p>
          </div>
          <div id="resourcesFileList" style="margin-top: 10px;"></div>
        </div>
      </div>

      <div class="form-section">
        <h2><i class="fa fa-cog"></i> Settings</h2>
        
        <div class="form-group">
          <div class="checkbox-group">
            <input type="checkbox" class="checkbox-input" id="allow-late" checked>
            <label for="allow-late" class="form-label" style="margin: 0;">Allow late submissions</label>
          </div>
        </div>
        
        <div class="form-group">
          <div class="checkbox-group">
            <input type="checkbox" class="checkbox-input" id="notify-students" checked>
            <label for="notify-students" class="form-label" style="margin: 0;">Notify students via email</label>
          </div>
        </div>
        
        <div class="form-group">
          <div class="checkbox-group">
            <input type="checkbox" class="checkbox-input" id="show-grades">
            <label for="show-grades" class="form-label" style="margin: 0;">Show grades immediately after grading</label>
          </div>
        </div>
        
        <div class="form-group">
          <div class="checkbox-group">
            <input type="checkbox" class="checkbox-input" id="plagiarism-check">
            <label for="plagiarism-check" class="form-label" style="margin: 0;">Enable plagiarism detection</label>
          </div>
        </div>
      </div>

      <div class="form-actions">
        <button type="button" class="btn btn-danger" onclick="deleteAssignment();">
          <i class="fa fa-trash"></i> Delete Assignment
        </button>
        <button type="button" class="btn btn-secondary" onclick="cancelEdit();">
          <i class="fa fa-times"></i> Cancel
        </button>
        <button type="button" class="btn btn-primary" onclick="saveAssignment();">
          <i class="fa fa-save"></i> Save Changes
        </button>
      </div>
      
    </form>
  </div>

</div>

<footer class="footer" style="margin-top: 40px;">
  <p>© 2025 SCTI - Teacher Portal</p>
</footer>

<script>
// File upload handlers with actual server upload
function handleInstructionsUpload(input) {
  const fileList = document.getElementById('instructionsFileList');
  fileList.innerHTML = '';
  
  if (input.files.length > 0) {
    const file = input.files[0];
    const fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
    
    if (fileSize > 10) {
      alert('File size exceeds 10MB limit!');
      input.value = '';
      return;
    }
    
    // Show uploading state
    fileList.innerHTML = '<div style="padding: 10px; background: #fff3cd; border-radius: 6px;"><i class="fa fa-spinner fa-spin"></i> Uploading...</div>';
    
    // Upload file to server
    uploadFileToServer(file, 'instructions', (success, data) => {
      if (success) {
        const fileItem = document.createElement('div');
        fileItem.style.cssText = 'padding: 10px; background: #e8f5e9; border-radius: 6px; display: flex; justify-content: space-between; align-items: center;';
        fileItem.innerHTML = `
          <div>
            <i class="fa fa-file-pdf" style="color: #28a745; margin-right: 8px;"></i>
            <span style="font-size: 14px; color: #333;">${file.name}</span>
            <span style="font-size: 12px; color: #666; margin-left: 10px;">(${fileSize} MB)</span>
            <span style="font-size: 11px; color: #28a745; margin-left: 10px;"><i class="fa fa-check-circle"></i> Uploaded</span>
          </div>
          <button type="button" onclick="removeInstructionsFile()" style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">
            <i class="fa fa-times"></i> Remove
          </button>
        `;
        fileList.innerHTML = '';
        fileList.appendChild(fileItem);
      } else {
        fileList.innerHTML = '<div style="padding: 10px; background: #f8d7da; border-radius: 6px; color: #721c24;"><i class="fa fa-exclamation-circle"></i> Upload failed</div>';
        input.value = '';
      }
    });
  }
}

function handleResourcesUpload(input) {
  const fileList = document.getElementById('resourcesFileList');
  fileList.innerHTML = '';
  
  if (input.files.length > 0) {
    // Show uploading state
    fileList.innerHTML = '<div style="padding: 10px; background: #fff3cd; border-radius: 6px;"><i class="fa fa-spinner fa-spin"></i> Uploading ' + input.files.length + ' file(s)...</div>';
    
    // Upload files to server
    uploadMultipleFilesToServer(Array.from(input.files), (success, uploadedFiles) => {
      fileList.innerHTML = '';
      
      if (success) {
        uploadedFiles.forEach((fileData, index) => {
          const fileSize = (fileData.size / 1024 / 1024).toFixed(2);
          
          const fileItem = document.createElement('div');
          fileItem.style.cssText = 'padding: 10px; background: #e8f5e9; border-radius: 6px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;';
          fileItem.innerHTML = `
            <div>
              <i class="fa fa-file" style="color: #28a745; margin-right: 8px;"></i>
              <span style="font-size: 14px; color: #333;">${fileData.name}</span>
              <span style="font-size: 12px; color: #666; margin-left: 10px;">(${fileSize} MB)</span>
              <span style="font-size: 11px; color: #28a745; margin-left: 10px;"><i class="fa fa-check-circle"></i> Uploaded</span>
            </div>
            <button type="button" onclick="removeResourceFile(${index})" style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">
              <i class="fa fa-times"></i> Remove
            </button>
          `;
          fileList.appendChild(fileItem);
        });
      } else {
        fileList.innerHTML = '<div style="padding: 10px; background: #f8d7da; border-radius: 6px; color: #721c24;"><i class="fa fa-exclamation-circle"></i> Upload failed</div>';
        input.value = '';
      }
    });
  }
}

// Upload single file to server
function uploadFileToServer(file, type, callback) {
  const formData = new FormData();
  formData.append('files[]', file);
  formData.append('type', type);
  
  fetch('upload-assignment-files.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      callback(true, data.files[0]);
    } else {
      alert('Upload failed: ' + data.message);
      callback(false, null);
    }
  })
  .catch(error => {
    console.error('Upload error:', error);
    alert('Upload failed: Network error');
    callback(false, null);
  });
}

// Upload multiple files to server
function uploadMultipleFilesToServer(files, callback) {
  const formData = new FormData();
  files.forEach(file => {
    formData.append('files[]', file);
  });
  formData.append('type', 'resources');
  
  fetch('upload-assignment-files.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const uploadedFiles = data.files.map(f => ({
        name: f.original_name,
        size: f.size,
        path: f.path
      }));
      callback(true, uploadedFiles);
    } else {
      alert('Upload failed: ' + data.message);
      callback(false, []);
    }
  })
  .catch(error => {
    console.error('Upload error:', error);
    alert('Upload failed: Network error');
    callback(false, []);
  });
}

function removeInstructionsFile() {
  document.getElementById('instructionsFile').value = '';
  document.getElementById('instructionsFileList').innerHTML = '';
}

function removeResourceFile(index) {
  const input = document.getElementById('resourcesFile');
  const dt = new DataTransfer();
  const files = Array.from(input.files);
  
  files.forEach((file, i) => {
    if (i !== index) {
      dt.items.add(file);
    }
  });
  
  input.files = dt.files;
  handleResourcesUpload(input);
}

// Form action handlers
function deleteAssignment() {
  if (confirm('Are you sure you want to delete this assignment? This action cannot be undone.')) {
    // Show loading state
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Deleting...';
    btn.disabled = true;
    
    // Simulate deletion (replace with actual backend call)
    setTimeout(() => {
      alert('Assignment deleted successfully!');
      window.location.href = 'teacher-assignments.php';
    }, 1000);
  }
}

function cancelEdit() {
  if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
    window.location.href = 'teacher-assignments.php';
  }
}

function saveAssignment() {
  // Validate required fields
  const title = document.querySelector('input[placeholder="Enter assignment title"]').value;
  const classSelect = document.querySelector('select.form-select').value;
  const description = document.querySelector('textarea.form-textarea').value;
  const dueDate = document.querySelector('input[type="datetime-local"]').value;
  const points = document.querySelector('input[placeholder="Enter total points"]').value;
  
  if (!title.trim()) {
    alert('Please enter an assignment title');
    return;
  }
  
  if (classSelect === 'Select a class') {
    alert('Please select a class');
    return;
  }
  
  if (!description.trim()) {
    alert('Please enter an assignment description');
    return;
  }
  
  if (!dueDate) {
    alert('Please select a due date');
    return;
  }
  
  if (!points || points <= 0) {
    alert('Please enter valid total points');
    return;
  }
  
  // Show loading state
  const btn = event.target.closest('button');
  const originalText = btn.innerHTML;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
  btn.disabled = true;
  
  // Simulate save (replace with actual backend call)
  setTimeout(() => {
    alert('Assignment updated successfully!');
    window.location.href = 'teacher-assignments.php';
  }, 1500);
}

// Drag and drop support
document.addEventListener('DOMContentLoaded', function() {
  const uploadAreas = document.querySelectorAll('.file-upload-area');
  
  uploadAreas.forEach(area => {
    area.addEventListener('dragover', (e) => {
      e.preventDefault();
      area.style.borderColor = '#28a745';
      area.style.background = '#e8f5e9';
    });
    
    area.addEventListener('dragleave', (e) => {
      e.preventDefault();
      area.style.borderColor = '#dee2e6';
      area.style.background = '#f8f9fa';
    });
    
    area.addEventListener('drop', (e) => {
      e.preventDefault();
      area.style.borderColor = '#dee2e6';
      area.style.background = '#f8f9fa';
      
      const fileInput = area.previousElementSibling;
      if (fileInput && fileInput.tagName === 'INPUT') {
        fileInput.files = e.dataTransfer.files;
        fileInput.dispatchEvent(new Event('change'));
      }
    });
  });
});
</script>

</body>
</html>
