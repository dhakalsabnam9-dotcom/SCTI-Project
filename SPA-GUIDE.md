# Single Page Application (SPA) Guide

## 🎯 What Changed?

Your website is now a **Single Page Application (SPA)** where:
- ✅ Navbar stays fixed at the top (never moves)
- ✅ Only content below navbar changes
- ✅ No page reload or navigation
- ✅ Smooth content transitions
- ✅ Faster and more user-friendly

---

## 🚀 How It Works

### Before (Multi-Page):
```
Click "Programs" → Navigate to Programs.html → New page loads → Navbar reloads
Click "Gallery" → Navigate to Gallery.html → New page loads → Navbar reloads
```
**Problem:** Navbar position shifts, colors change, page reloads

### After (Single Page):
```
Click "Programs" → Content changes → Navbar stays fixed
Click "Gallery" → Content changes → Navbar stays fixed
```
**Solution:** Navbar never moves, only content updates

---

## 📁 File Structure

### Main File:
- **index.html** (or index-spa.html) - Single page with all content

### How It's Organized:
```javascript
const pages = {
  home: `...home content...`,
  programs: `...programs content...`,
  gallery: `...gallery content...`,
  notices: `...notices content...`,
  contact: `...contact content...`
};
```

### Navigation:
```html
<a href="#" onclick="loadPage('home'); return false;">Home</a>
<a href="#" onclick="loadPage('programs'); return false;">Programs</a>
```

---

## ✨ Features

### 1. Sticky Navbar
- Always visible at top
- Never moves or shifts
- Consistent across all sections

### 2. Dynamic Content Loading
- Content changes instantly
- No page reload
- Smooth fade-in animation

### 3. Active Menu Highlighting
- Current section highlighted in gold
- Visual feedback for users
- Better navigation experience

### 4. Smooth Scrolling
- Auto-scroll to top on section change
- Smooth animations
- Professional feel

### 5. Mobile Responsive
- Hamburger menu works perfectly
- Auto-closes after selection
- Touch-friendly

---

## 🎨 Visual Effects

### Fade-In Animation:
```css
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
```

### Active Menu Item:
- Gold color (#ffd700)
- Bottom border
- Clear visual indicator

---

## 🔧 How to Add New Sections

### Step 1: Add Content to `pages` object
```javascript
const pages = {
  // ... existing pages ...
  
  newSection: `
    <section class="section">
      <div class="container">
        <h2>New Section Title</h2>
        <p>Your content here...</p>
      </div>
    </section>
  `
};
```

### Step 2: Add Menu Link
```html
<li><a href="#" onclick="loadPage('newSection'); return false;">New Section</a></li>
```

That's it! Your new section is ready.

---

## 🌐 URLs

### Main Page:
```
http://localhost/scti-school/
```

### All Sections Accessible From:
- Home (default)
- Programs
- Gallery
- Notice Board
- Contact Us

### External Pages (Still Separate):
- Login: `login-simple.php`
- Sign Up: `signup.php`

---

## 📊 Benefits

### User Experience:
- ✅ Faster navigation
- ✅ No page flicker
- ✅ Consistent navbar
- ✅ Smooth transitions
- ✅ Better performance

### Technical:
- ✅ Less server requests
- ✅ Faster load times
- ✅ Single HTML file
- ✅ Easy to maintain
- ✅ Modern approach

---

## 🎯 Key Functions

### loadPage(pageName)
```javascript
function loadPage(pageName) {
  // 1. Get content area
  const contentArea = document.getElementById('content-area');
  
  // 2. Load new content
  contentArea.innerHTML = pages[pageName] || pages.home;
  
  // 3. Scroll to top
  window.scrollTo({ top: 0, behavior: 'smooth' });
  
  // 4. Close mobile menu
  document.getElementById("menu").classList.remove("show");
  
  // 5. Update active menu item
  event.target.classList.add('active');
}
```

### On Page Load:
```javascript
window.onload = function() {
  loadPage('home'); // Load home by default
};
```

---

## 🔄 Content Flow

```
User clicks "Programs"
    ↓
loadPage('programs') called
    ↓
Content area updated with programs HTML
    ↓
Smooth fade-in animation
    ↓
Scroll to top
    ↓
Menu item highlighted
    ↓
Done! (Navbar never moved)
```

---

## 📱 Mobile Experience

### Before Click:
- Hamburger menu visible
- Menu hidden

### After Click:
- Content loads
- Menu auto-closes
- Smooth experience

---

## 🎨 Styling

### Navbar:
- Background: #004080 (blue)
- Position: sticky
- Top: 0
- Z-index: 1000

### Active Link:
- Color: #ffd700 (gold)
- Border-bottom: 3px solid gold

### Content Area:
- Fade-in animation: 0.3s
- Smooth transitions

---

## ✅ Testing Checklist

1. ✅ Open `http://localhost/scti-school/`
2. ✅ Click "Programs" - Content changes, navbar stays
3. ✅ Click "Gallery" - Content changes, navbar stays
4. ✅ Click "Notice Board" - Content changes, navbar stays
5. ✅ Click "Contact Us" - Content changes, navbar stays
6. ✅ Click "Home" - Back to homepage
7. ✅ Test on mobile - Hamburger menu works
8. ✅ Scroll down - Navbar stays at top

---

## 🚀 Performance

### Load Time:
- **Before:** 5 separate HTML files
- **After:** 1 HTML file with instant content switching

### User Experience:
- **Before:** Page reload on every click
- **After:** Instant content change

### Bandwidth:
- **Before:** Full page load each time
- **After:** Content already loaded

---

## 🎉 Result

A modern, fast, and user-friendly Single Page Application with:
- ✅ Fixed navbar that never moves
- ✅ Instant content switching
- ✅ Smooth animations
- ✅ Better user experience
- ✅ Professional feel

---

**© 2025 SCTI - School Management System**  
**Version:** 4.0 - Single Page Application  
**Last Updated:** March 2025
