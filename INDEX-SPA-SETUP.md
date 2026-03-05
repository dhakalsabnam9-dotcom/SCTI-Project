# Index.html SPA Setup - Complete

## Overview
Successfully converted `index.html` to be the main Single Page Application (SPA) for the SCTI website. The file now serves as the landing page with all functionality integrated.

## Structure

### 1. Top Header (Sliding Text)
- Marquee animation with announcements
- Text: "Examination Notification | Genius 2025 | Enrollment Open 2025–26"
- Always visible at the top

### 2. Sticky Navigation Bar
- Fixed position at top (below sliding text)
- Navigation items:
  - Home
  - Programs
  - Gallery
  - Notice Board
  - Contact Us
  - Login
  - Sign Up
- All links use `onclick="loadPage('pageName')"` - no page navigation
- Mobile responsive with hamburger menu

### 3. Dynamic Content Area
- `<div id="content-area">` - where all content loads
- Only this section changes when navigating
- Smooth fade-in animations

### 4. Footer
- Fixed at bottom
- Copyright information

## Pages Available

All pages load dynamically within the content area:

1. **Home** - Landing page with:
   - Banner image
   - Notice board & events
   - Campus information
   - Popular courses
   - Teachers section

2. **Programs** - Course offerings with:
   - Diploma in Animal Husbandry
   - B.Tech. Ed. in IT
   - B.Tech. Ed. in Civil Engineering
   - Diploma in Civil Engineering
   - Admission & benefits information

3. **Gallery** - Photo gallery with:
   - Workshop images
   - Lab practice
   - Graduation
   - Sports week
   - Campus life
   - Hover overlay effects

4. **Notice Board** - Announcements with:
   - Admission notices
   - Orientation programs
   - Class schedules
   - Sports week
   - Workshops
   - Urgent badges

5. **Contact Us** - Contact information with:
   - Address, phone, email
   - Office hours
   - Contact form with validation

6. **Login** - Login form with:
   - User type selection (Student/Teacher/Admin)
   - Username & password fields
   - Test credentials display
   - AJAX submission
   - Error handling

7. **Signup** - Registration form with:
   - User type selection
   - Full name, username, email
   - Password & confirm password
   - Client-side validation
   - AJAX submission
   - Error handling

## Key Features

### SPA Functionality
- No page reloads when navigating
- Navbar stays fixed at top
- Only content below navbar changes
- Smooth transitions with fade-in animations
- Mobile menu auto-closes after selection

### Login/Signup Integration
- Forms load within the SPA
- AJAX submissions to PHP backend
- Inline error messages
- Success messages with auto-redirect
- Test credentials:
  - Admin: admin/admin123
  - Teacher: teacher/teacher123
  - Student: student/student123

### Responsive Design
- Mobile-friendly navigation
- Hamburger menu for small screens
- Responsive grid layouts
- Touch-friendly buttons

## How It Works

1. **Page Load**: `window.onload` automatically loads the home page
2. **Navigation**: Clicking any nav link calls `loadPage(pageName)`
3. **Content Update**: `loadPage()` function:
   - Gets content from `pages` object
   - Updates `content-area` innerHTML
   - Scrolls to top smoothly
   - Closes mobile menu
   - Updates active menu item
4. **Form Submission**: AJAX handlers prevent page reload and communicate with PHP backend

## Files

- **Main File**: `index.html` (the SPA)
- **Styles**: `style.css` (includes all SPA styles)
- **Backend**: 
  - `login-simple.php` (handles login)
  - `signup.php` (handles registration)

## Testing

Open in browser: `http://localhost/scti-school/index.html`

Test checklist:
- ✅ Sliding text animation at top
- ✅ Sticky navbar (doesn't move when scrolling)
- ✅ All nav links work without page reload
- ✅ Home page loads by default
- ✅ Programs, Gallery, Notices, Contact load correctly
- ✅ Login form loads and submits via AJAX
- ✅ Signup form loads and submits via AJAX
- ✅ Mobile menu works (hamburger icon)
- ✅ Footer stays at bottom

## Benefits

1. **User Experience**: Smooth, app-like navigation
2. **Performance**: No full page reloads
3. **Consistency**: Navbar and header always visible
4. **Mobile-Friendly**: Responsive design throughout
5. **Maintainability**: Single file for all pages
6. **SEO-Friendly**: Can still be indexed (with proper setup)

## Next Steps

If you need to:
- Add new pages: Add to `pages` object in JavaScript
- Modify existing pages: Edit content in `pages` object
- Change styles: Update `style.css`
- Add backend functionality: Create new PHP files and link via AJAX
