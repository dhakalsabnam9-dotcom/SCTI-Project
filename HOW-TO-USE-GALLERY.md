# 📸 How to Use the Gallery System - Step by Step Guide

## 🎯 Quick Overview

Your gallery system has 4 main operations:
1. **CREATE** - Upload new images
2. **READ** - View all images
3. **UPDATE** - Edit image details
4. **DELETE** - Remove images

---

## 🚀 Getting Started

### Step 1: Access the Gallery Manager

**Option A: From Dashboard Card**
1. Login as admin
2. Go to Admin Dashboard
3. Look for the **purple "Gallery Images" card** (shows image count)
4. Click anywhere on the card

**Option B: From Quick Actions**
1. Login as admin
2. Go to Admin Dashboard
3. Scroll to "Quick Actions" section
4. Click **"Manage Gallery"** button (purple with sparkle ✨)

**Direct URL:**
```
http://localhost/scti-project/pages/manage-gallery.php
```

---

## 📤 CREATE - Upload New Images

### Method 1: Drag & Drop (Recommended)

1. **Find the Upload Section** (top of page)
   - Look for the dashed border box
   - Says "Drag & Drop Image Here"

2. **Drag Your Image**
   - Open your file explorer
   - Find an image (JPG, PNG, GIF, or WEBP)
   - Drag it over the upload box
   - Box turns blue when ready
   - Drop the file

3. **Fill in Details**
   - **Title** (Required): Enter image name
     - Example: "Campus Building"
   
   - **Description** (Optional): Add details
     - Example: "Main academic building with library"
   
   - **Category** (Optional): Select from dropdown
     - Campus
     - Events
     - Students
     - Facilities
     - Activities
     - Achievements
     - Other

4. **Click "Upload Image"**
   - Green button at bottom
   - Wait for success message
   - Image appears in gallery below

### Method 2: Click to Browse

1. **Click the Upload Box**
   - Click anywhere in the dashed border area
   - File browser opens

2. **Select Image**
   - Browse to your image
   - Click "Open"
   - File name appears

3. **Fill in Details** (same as Method 1)

4. **Click "Upload Image"**

### ✅ Upload Success Indicators

- ✅ Green success message appears
- ✅ Image appears in gallery grid below
- ✅ Gallery count updates on dashboard
- ✅ Form clears for next upload

### ❌ Upload Error Messages

**"Title is required"**
→ Fill in the title field

**"Invalid file type"**
→ Only JPG, PNG, GIF, WEBP allowed

**"File size exceeds 10MB"**
→ Compress your image or use smaller file

**"Failed to move uploaded file"**
→ Check folder permissions (see setup guide)

---

## 👀 READ - View Images

### Gallery Grid View

**What You See:**
- All uploaded images in a grid
- Each card shows:
  - Image thumbnail
  - Title
  - Description (first 2 lines)
  - Upload date
  - Category badge
  - Edit and Delete buttons

**Hover Effects:**
- Card lifts up
- Image zooms in
- Overlay appears with info
- Buttons highlight

### Filter by Category

1. **Find Filter Dropdown**
   - Below upload section
   - Says "All Categories"

2. **Select Category**
   - Click dropdown
   - Choose: Campus, Events, Students, etc.
   - Gallery updates instantly

3. **Clear Filter**
   - Select "All Categories"
   - Shows all images again

### Search Images

1. **Find Search Box**
   - Next to category filter
   - Has magnifying glass icon

2. **Type to Search**
   - Enter title or description text
   - Results filter as you type
   - Case-insensitive

3. **Clear Search**
   - Delete text
   - Press backspace
   - All images reappear

### Refresh Gallery

1. **Click Refresh Button**
   - Blue button with sync icon
   - Reloads all images from database
   - Updates counts

---

## ✏️ UPDATE - Edit Image Details

### Step-by-Step Edit Process

1. **Find Image to Edit**
   - Scroll through gallery
   - Or use search/filter

2. **Click "Edit" Button**
   - Yellow/orange button
   - Has pencil icon
   - On bottom of image card

3. **Edit Modal Opens**
   - Popup appears in center
   - Shows current details
   - Dark background overlay

4. **Update Fields**
   - **Title**: Change image name
   - **Description**: Update details
   - **Category**: Select new category

5. **Save Changes**
   - Click green "Save Changes" button
   - Or press Enter

6. **Cancel Edit**
   - Click "Cancel" button
   - Or click X in top-right
   - Or press ESC key
   - Or click outside modal

### ✅ Edit Success

- ✅ "Image updated successfully" message
- ✅ Modal closes automatically
- ✅ Gallery refreshes
- ✅ Changes visible immediately

### 💡 Edit Tips

- You can only edit text, not the image file
- To change image, delete and re-upload
- Changes are instant (no page reload)
- All fields are optional except title

---

## 🗑️ DELETE - Remove Images

### Step-by-Step Delete Process

1. **Find Image to Delete**
   - Locate in gallery grid
   - Use search if needed

2. **Click "Delete" Button**
   - Red button
   - Has trash icon
   - On bottom of image card

3. **Confirm Deletion**
   - Popup asks: "Are you sure?"
   - Click "OK" to delete
   - Click "Cancel" to keep

4. **Image Removed**
   - Success message appears
   - Image disappears from grid
   - Gallery count updates

### ⚠️ Important Delete Notes

- **Soft Delete**: Image file stays on server
- **Database**: Marked as inactive (is_active = 0)
- **Public Gallery**: Image won't show anymore
- **Admin Panel**: Image disappears from list
- **Permanent**: Cannot undo (unless you restore in database)

### 🔄 Restore Deleted Images

If you need to restore:
1. Go to database (phpMyAdmin)
2. Find `gallery_images` table
3. Find the image row
4. Change `is_active` from 0 to 1
5. Image reappears in gallery

---

## 🎨 UI Features Explained

### Upload Area

**Normal State:**
- Dashed cyan border
- Light gray background
- Upload icon and text

**Hover State:**
- Darker background
- Border color changes
- Cursor becomes pointer

**Drag Over State:**
- Blue background
- Darker border
- "Drop here" indication

### Image Cards

**Card Structure:**
```
┌─────────────────────┐
│   [Image Preview]   │ ← Hover to zoom
│                     │
│ Title               │ ← Changes color on hover
│ Description...      │
│ 📅 Date  🏷️ Category│ ← Icons animate
│                     │
│ [Edit]  [Delete]    │ ← Gradient buttons
└─────────────────────┘
```

**Hover Effects:**
- Top gradient line appears
- Card lifts up 10px
- Image zooms 115%
- Shadow grows
- Icons rotate 360°

### Buttons

**Edit Button (Yellow/Orange):**
- Gradient background
- Pencil icon
- Ripple effect on click
- Icon rotates on hover

**Delete Button (Red):**
- Gradient background
- Trash icon
- Ripple effect on click
- Icon rotates on hover

### Modals

**Edit Modal:**
- Slides in from top
- Dark overlay (80% black)
- White content box
- Rounded corners
- Close button (X)

**Close Methods:**
- Click X button
- Press ESC key
- Click outside modal
- Click Cancel button

---

## 📊 Dashboard Integration

### Gallery Count Card

**Location:** Admin Dashboard → Statistics Section

**Shows:**
- Total number of active images
- Purple gradient background
- Floating camera icon
- Pulsing animation

**Click Action:**
- Opens Manage Gallery page
- Direct access to upload

### Quick Action Button

**Location:** Admin Dashboard → Quick Actions

**Shows:**
- "Manage Gallery" text
- Images icon (rotating)
- Purple gradient
- Sparkle emoji on hover

**Click Action:**
- Opens Manage Gallery page

---

## 🔍 Troubleshooting Common Issues

### "No images uploaded yet"

**Cause:** Gallery is empty

**Solution:**
1. Upload your first image
2. Follow CREATE steps above

### Images not appearing after upload

**Cause:** Database or file issue

**Solution:**
1. Check browser console (F12)
2. Look for error messages
3. Verify database table exists
4. Check folder permissions

### Upload button not working

**Cause:** Missing title or file

**Solution:**
1. Make sure title is filled
2. Make sure file is selected
3. Check file type (JPG, PNG, GIF, WEBP only)
4. Check file size (under 10MB)

### Edit modal not opening

**Cause:** JavaScript error

**Solution:**
1. Refresh page (F5)
2. Clear browser cache
3. Check browser console for errors

### Delete not working

**Cause:** Database connection issue

**Solution:**
1. Check database connection
2. Verify admin permissions
3. Check browser console

---

## 💡 Pro Tips

### Organizing Images

1. **Use Categories**
   - Assign category when uploading
   - Makes filtering easier
   - Better organization

2. **Descriptive Titles**
   - Use clear, specific titles
   - Example: "2024 Sports Day Winners" not "IMG_001"

3. **Add Descriptions**
   - Helps with searching
   - Provides context
   - Better for SEO

### Bulk Operations

**To upload multiple images:**
1. Upload first image
2. Wait for success
3. Upload next image
4. Repeat

**To delete multiple images:**
1. Delete first image
2. Confirm
3. Delete next image
4. Repeat

### Best Practices

✅ **DO:**
- Compress images before upload
- Use descriptive titles
- Add categories
- Test on public gallery after upload

❌ **DON'T:**
- Upload huge files (>10MB)
- Use special characters in titles
- Delete images that are in use
- Upload copyrighted images

---

## 📱 Mobile Usage

### On Mobile Devices

**Upload:**
- Tap upload area
- Select from camera or gallery
- Fill form
- Tap "Upload Image"

**View:**
- Scroll through grid
- Tap to see details
- Swipe to navigate

**Edit:**
- Tap "Edit" button
- Modal opens
- Fill form
- Tap "Save"

**Delete:**
- Tap "Delete" button
- Confirm deletion

---

## 🎓 Quick Reference

### Keyboard Shortcuts

- **ESC** - Close modal
- **Enter** - Submit form (when focused)
- **Tab** - Navigate form fields

### File Requirements

- **Types:** JPG, PNG, GIF, WEBP
- **Max Size:** 10MB
- **Recommended:** Under 2MB for faster upload

### Categories

1. Campus
2. Events
3. Students
4. Facilities
5. Activities
6. Achievements
7. Other

---

## 🎉 Success Checklist

After following this guide, you should be able to:

- [ ] Access the gallery manager
- [ ] Upload images using drag & drop
- [ ] Upload images using click to browse
- [ ] View all images in grid
- [ ] Filter images by category
- [ ] Search images by title/description
- [ ] Edit image details
- [ ] Delete images
- [ ] See changes on public gallery

---

## 📞 Need Help?

**Check These First:**
1. GALLERY-SETUP-GUIDE.md - Setup instructions
2. Browser console (F12) - Error messages
3. Database - Verify table exists
4. Permissions - Check folder access

**Common Solutions:**
- Refresh page (F5)
- Clear browser cache
- Check database connection
- Verify admin login

---

## 🎬 Video Tutorial (Coming Soon)

Watch a video walkthrough of:
- Uploading images
- Editing details
- Deleting images
- Using filters and search

---

**Congratulations!** 🎊 You now know how to use the complete gallery system!

For setup instructions, see: `GALLERY-SETUP-GUIDE.md`
