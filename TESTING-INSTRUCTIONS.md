# Testing Instructions - Clear Browser Cache

## Issue
You're seeing the old version of the website because your browser has cached the previous files.

## Solution: Clear Browser Cache

### Method 1: Hard Refresh (Quickest)
1. Open `http://localhost/scti-school/index.html` in your browser
2. Press one of these key combinations:
   - **Windows Chrome/Edge**: `Ctrl + Shift + R` or `Ctrl + F5`
   - **Windows Firefox**: `Ctrl + Shift + R` or `Ctrl + F5`
   - **Mac Chrome/Safari**: `Cmd + Shift + R`

### Method 2: Clear Cache Manually
1. Open your browser
2. Press `Ctrl + Shift + Delete` (Windows) or `Cmd + Shift + Delete` (Mac)
3. Select "Cached images and files"
4. Click "Clear data" or "Clear now"
5. Reload the page: `http://localhost/scti-school/index.html`

### Method 3: Open in Incognito/Private Mode
1. Open a new Incognito/Private window:
   - **Chrome**: `Ctrl + Shift + N`
   - **Firefox**: `Ctrl + Shift + P`
   - **Edge**: `Ctrl + Shift + N`
2. Navigate to: `http://localhost/scti-school/index.html`

## What You Should See After Clearing Cache

### 1. Sliding Text at Top
- Blue background bar at the very top
- White text scrolling: "Examination Notification | Genius 2025 | Enrollment Open 2025–26"

### 2. Navigation Bar (Below Sliding Text)
- SCTI logo on the left
- Navigation menu with these items:
  - Home
  - Programs
  - Gallery
  - Notice Board
  - Contact Us
  - **Login** ← Should be visible
  - **Sign Up** ← Should be visible

### 3. Content Area
- Home page loads automatically with:
  - Banner image
  - Notice board and events
  - Campus information
  - Popular courses
  - Teachers section

### 4. Test Navigation
Click each menu item and verify:
- ✅ Page doesn't reload
- ✅ Navbar stays fixed at top
- ✅ Only content below navbar changes
- ✅ Smooth fade-in animation

### 5. Test Login/Signup
- Click "Login" → Login form should appear below navbar
- Click "Sign Up" → Signup form should appear below navbar
- Forms should have all fields and test credentials

## Still Not Working?

If you still don't see Login/Signup after clearing cache:

1. **Check XAMPP is running**:
   - Apache should be started
   - MySQL should be started

2. **Verify file location**:
   ```
   C:\Users\HP\Desktop\scti project final\index.html
   ```

3. **Check browser console for errors**:
   - Press `F12` to open Developer Tools
   - Click "Console" tab
   - Look for any red error messages
   - Share the errors if you see any

4. **Try a different browser**:
   - If using Chrome, try Firefox or Edge
   - This helps identify if it's a browser-specific issue

## Expected URL
```
http://localhost/scti-school/index.html
```

or

```
http://localhost/scti%20project%20final/index.html
```

(depending on your XAMPP folder name)

## Verification Checklist

After clearing cache, you should see:
- [ ] Sliding text animation at the very top
- [ ] SCTI logo in navbar
- [ ] "Login" link in navbar
- [ ] "Sign Up" link in navbar
- [ ] Home page content loads automatically
- [ ] Clicking nav links changes content without page reload
- [ ] Navbar stays fixed when scrolling

If all checkboxes are checked, the setup is working correctly!
