# 📸 Gallery System - Complete Setup Guide

## 🚀 Quick Start (3 Steps)

### Step 1: Create Database Table
Run this SQL command in your database:

```sql
CREATE TABLE IF NOT EXISTS gallery_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    file_path VARCHAR(255) NOT NULL,
    thumbnail_path VARCHAR(255),
    category VARCHAR(100),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_active (is_active),
    INDEX idx_display_order (display_order)
);
```

**How to run:**
- **Option A (phpMyAdmin):**
  1. Open phpMyAdmin
  2. Select your database (e.g., `scti_school`)
  3. Click "SQL" tab
  4. Paste the SQL above
  5. Click "Go"

- **Option B (Command Line):**
  ```bash
  mysql -u your_username -p your_database < create-gallery-table.sql
  ```

### Step 2: Verify Upload Folders Exist
The folders should already exist, but verify:
- `uploads/gallery/` ✓
- `uploads/gallery/thumbnails/` ✓

If not, create them manually or the system will create them automatically.

### Step 3: Test the System
1. Login as admin
2. Go to Admin Dashboard
3. Click the **purple "Gallery Images" card** or **"Manage Gallery" button**
4. Upload a test image!

---

## 📋 Detailed Setup Instructions

### Prerequisites
- ✅ PHP 7.4 or higher
- ✅ MySQL 5.7 or higher
- ✅ GD Library (for image processing)
- ✅ Admin account created

### File Structure
```
scti-project/
├── dashboards/
│   └── admin-dashboard.php (has gallery card)
├── pages/
│   ├── manage-gallery.php (admin panel)
│   ├── gallery-upload.php (upload handler)
│   ├── gallery-list.php (list images)
│   ├── gallery-get.php (get single image)
│   ├── gallery-update.php (update image)
│   ├── gallery-delete.php (delete image)
│   ├── gallery-public.php (public API)
│   └── get-gallery-count.php (dashboard count)
├── uploads/
│   └── gallery/
│       ├── (uploaded images)
│       └── thumbnails/
│           └── (auto-generated thumbnails)
├── assets/
│   ├── css/style.css (gallery styles)
│   └── js/pages/gallery.js (dynamic gallery)
└── create-gallery-table.sql (database schema)
```

---

## 🎯 Step-by-Step Setup

### 1️⃣ Database Setup

**Check if table exists:**
```sql
SHOW TABLES LIKE 'gallery_images';
```

**If table doesn't exist, create it:**
```sql
-- Copy from create-gallery-table.sql or use the SQL from Step 1 above
```

**Verify table was created:**
```sql
DESCRIBE gallery_images;
```

You should see columns: id, title, description, file_path, thumbnail_path, category, etc.

### 2️⃣ Folder Permissions

**On Windows:**
- Right-click `uploads/gallery` folder
- Properties → Security
- Make sure your web server user has "Write" permission

**On Linux/Mac:**
```bash
chmod 755 uploads/gallery
chmod 755 uploads/gallery/thumbnails
```

**Verify folders exist:**
```bash
ls -la uploads/gallery
ls -la uploads/gallery/thumbnails
```

### 3️⃣ PHP Configuration

**Check if GD library is installed:**
```php
<?php
if (extension_loaded('gd')) {
    echo "GD is installed";
} else {
    echo "GD is NOT installed - install it!";
}
?>
```

**Check upload limits in php.ini:**
```ini
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
```

**Restart web server after changes:**
```bash
# Apache
sudo service apache2 restart

# Nginx
sudo service nginx restart
```

### 4️⃣ Test Database Connection

**Create test file: `test-db.php`**
```php
<?php
require_once 'includes/config.php';

try {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM gallery_images");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    echo "✅ Database connected!<br>";
    echo "Gallery images count: " . $row['count'];
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
```

Run: `http://localhost/scti-project/test-db.php`

---

## 🎨 Using the Gallery System

### Admin Panel Features

#### 1. Upload Images
1. Go to **Manage Gallery**
2. **Drag & drop** image or **click to browse**
3. Fill in:
   - **Title** (required)
   - **Description** (optional)
   - **Category** (optional): Campus, Events, Students, Facilities, Activities, Achievements
4. Click **"Upload Image"**

#### 2. View Gallery
- All uploaded images appear in grid
- Filter by category
- Search by title/description
- Real-time updates

#### 3. Edit Images
1. Click **"Edit"** button on any image
2. Update title, description, or category
3. Click **"Save Changes"**

#### 4. Delete Images
1. Click **"Delete"** button
2. Confirm deletion
3. Image is soft-deleted (marked inactive)

### Public Gallery
- Automatically shows all active images
- Category filtering
- Lightbox view
- Responsive design

---

## 🔧 Troubleshooting

### Problem: Images not uploading

**Solution 1: Check file permissions**
```bash
ls -la uploads/gallery
# Should show write permissions
```

**Solution 2: Check PHP upload settings**
```bash
php -i | grep upload_max_filesize
php -i | grep post_max_size
```

**Solution 3: Check error logs**
```bash
# Apache
tail -f /var/log/apache2/error.log

# Nginx
tail -f /var/log/nginx/error.log
```

### Problem: Thumbnails not generating

**Solution: Install/enable GD library**
```bash
# Ubuntu/Debian
sudo apt-get install php-gd
sudo service apache2 restart

# CentOS/RHEL
sudo yum install php-gd
sudo service httpd restart

# Windows (XAMPP)
# Edit php.ini, uncomment: extension=gd
```

### Problem: Database connection error

**Solution: Check config.php**
```php
// includes/config.php
$servername = "localhost";
$username = "your_username";  // ← Check this
$password = "your_password";  // ← Check this
$dbname = "your_database";    // ← Check this
```

### Problem: Gallery count shows 0

**Solution: Check if table exists and has data**
```sql
SELECT COUNT(*) FROM gallery_images WHERE is_active = 1;
```

### Problem: Public gallery not showing images

**Solution: Check API endpoint**
Visit: `http://localhost/scti-project/pages/gallery-public.php`

Should return JSON:
```json
{
  "success": true,
  "images": [...],
  "count": 5
}
```

---

## ✅ Verification Checklist

- [ ] Database table `gallery_images` created
- [ ] Folders `uploads/gallery/` and `uploads/gallery/thumbnails/` exist
- [ ] Folder permissions set (755 or writable)
- [ ] GD library installed and enabled
- [ ] PHP upload limits configured (10MB+)
- [ ] Database connection working
- [ ] Admin can access Manage Gallery page
- [ ] Test image uploads successfully
- [ ] Thumbnails generate automatically
- [ ] Gallery count shows on dashboard
- [ ] Public gallery displays images
- [ ] Category filtering works
- [ ] Edit and delete functions work

---

## 🎓 Quick Test

### Test 1: Upload an Image
1. Login as admin
2. Dashboard → Manage Gallery
3. Upload any image (JPG, PNG, GIF, or WEBP)
4. Add title: "Test Image"
5. Select category: "Campus"
6. Click Upload

**Expected Result:** ✅ Image appears in gallery grid

### Test 2: View on Public Site
1. Go to Gallery page (public site)
2. Click "Campus" filter

**Expected Result:** ✅ Your test image appears

### Test 3: Edit Image
1. Click "Edit" on test image
2. Change title to "Updated Test"
3. Save

**Expected Result:** ✅ Title updates immediately

### Test 4: Delete Image
1. Click "Delete" on test image
2. Confirm

**Expected Result:** ✅ Image disappears from gallery

---

## 📞 Support

### Common Issues

**"Table doesn't exist"**
→ Run the CREATE TABLE SQL again

**"Permission denied"**
→ Check folder permissions (chmod 755)

**"GD library not found"**
→ Install php-gd extension

**"File too large"**
→ Increase upload_max_filesize in php.ini

**"Images not showing"**
→ Check if is_active = 1 in database

---

## 🎉 Success!

If you can:
1. ✅ Upload images
2. ✅ See them in admin panel
3. ✅ View them on public gallery
4. ✅ Edit and delete them

**Your gallery system is working perfectly!** 🎊

---

## 📚 Additional Resources

- **Admin Panel**: `http://localhost/scti-project/dashboards/admin-dashboard.php`
- **Manage Gallery**: `http://localhost/scti-project/pages/manage-gallery.php`
- **Public Gallery**: `http://localhost/scti-project/index.html` (click Gallery)
- **API Endpoint**: `http://localhost/scti-project/pages/gallery-public.php`

---

## 🔐 Security Notes

- ✅ Admin authentication required
- ✅ File type validation (images only)
- ✅ File size limit (10MB)
- ✅ SQL injection prevention
- ✅ XSS protection
- ✅ Unique filenames (prevents overwrite)

---

**Need help?** Check the error logs or verify each step above!
