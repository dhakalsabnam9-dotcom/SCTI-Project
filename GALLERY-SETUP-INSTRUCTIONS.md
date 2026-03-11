# Gallery Management System - Setup Instructions

## What Was Created

### 1. Admin Dashboard Updates
- ✅ Added Gallery statistics card (shows image count)
- ✅ Added "Manage Gallery" quick action button
- ✅ Both link to the gallery management page

### 2. Gallery Manager Page (`pages/manage-gallery.php`)
- ✅ Drag & drop image upload interface
- ✅ Image metadata form (title, description, category)
- ✅ Gallery grid display with filtering
- ✅ Search functionality
- ✅ Edit and delete capabilities
- ✅ Responsive design

### 3. Backend API Files
- ✅ `pages/gallery-upload.php` - Handles image uploads with thumbnail generation
- ✅ `pages/gallery-list.php` - Lists gallery images with filtering
- ✅ `pages/gallery-get.php` - Gets single image details
- ✅ `pages/gallery-update.php` - Updates image metadata
- ✅ `pages/gallery-delete.php` - Soft deletes images
- ✅ `pages/get-gallery-count.php` - Gets image count for dashboard

### 4. Database
- ✅ `create-gallery-table.sql` - SQL script to create the gallery_images table

### 5. Upload Directories
- ✅ `uploads/gallery/` - Main image storage
- ✅ `uploads/gallery/thumbnails/` - Thumbnail storage

## Installation Steps

### Step 1: Create Database Table

Run the SQL script to create the gallery_images table:

```bash
# Option 1: Using MySQL command line
mysql -u your_username -p your_database_name < create-gallery-table.sql

# Option 2: Using phpMyAdmin
# - Open phpMyAdmin
# - Select your database
# - Go to SQL tab
# - Copy and paste the contents of create-gallery-table.sql
# - Click "Go"
```

### Step 2: Verify Directory Permissions

Make sure the upload directories have write permissions:

```bash
# On Linux/Mac
chmod 755 uploads/gallery
chmod 755 uploads/gallery/thumbnails

# On Windows (already done, but verify in File Explorer)
# Right-click folder → Properties → Security → Make sure your web server user has write access
```

### Step 3: Test the System

1. **Login as Admin**
   - Go to your admin dashboard
   - You should see the new "Gallery Images" card
   - You should see the "Manage Gallery" button

2. **Upload Test Image**
   - Click "Manage Gallery"
   - Drag and drop an image or click to browse
   - Fill in title (required)
   - Add description and category (optional)
   - Click "Upload Image"

3. **Verify Upload**
   - Check if image appears in the gallery grid
   - Check if files exist in `uploads/gallery/` and `uploads/gallery/thumbnails/`
   - Check if database record was created

## Features

### Image Upload
- Supports: JPG, PNG, GIF, WEBP
- Max file size: 10MB
- Automatic thumbnail generation (300x300px)
- Drag & drop or click to browse

### Image Management
- Edit title, description, and category
- Delete images (soft delete - keeps files)
- Filter by category
- Search by title or description
- Real-time gallery count on dashboard

### Categories
- Campus
- Events
- Students
- Facilities
- Activities
- Achievements
- Other

## Security Features

- ✅ Admin authentication required
- ✅ File type validation (only images)
- ✅ File size validation (10MB max)
- ✅ MIME type checking
- ✅ Unique filename generation
- ✅ SQL injection prevention (prepared statements)
- ✅ XSS prevention (input sanitization)

## Troubleshooting

### Images not uploading?
1. Check directory permissions
2. Check PHP upload_max_filesize in php.ini
3. Check post_max_size in php.ini
4. Check error logs

### Thumbnails not generating?
1. Make sure GD library is installed: `php -m | grep gd`
2. Check directory write permissions
3. Check PHP error logs

### Gallery count showing 0?
1. Make sure database table is created
2. Check database connection in config.php
3. Upload at least one image

## Next Steps

To make the public gallery page dynamic:

1. Update `assets/js/pages/gallery.js` to fetch from `pages/gallery-list.php`
2. Remove hardcoded gallery images
3. Display images dynamically from the database

## File Structure

```
scti-project/
├── dashboards/
│   └── admin-dashboard.php (updated)
├── pages/
│   ├── manage-gallery.php (new)
│   ├── gallery-upload.php (new)
│   ├── gallery-list.php (new)
│   ├── gallery-get.php (new)
│   ├── gallery-update.php (new)
│   ├── gallery-delete.php (new)
│   └── get-gallery-count.php (new)
├── uploads/
│   └── gallery/
│       ├── (uploaded images)
│       └── thumbnails/
│           └── (generated thumbnails)
└── create-gallery-table.sql (new)
```

## Support

If you encounter any issues:
1. Check PHP error logs
2. Check browser console for JavaScript errors
3. Verify database connection
4. Ensure all files are uploaded correctly
