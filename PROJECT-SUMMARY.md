# SCTI School Management System - Project Summary

## 🎨 Theme Colors
- **Primary Blue:** `#004080`
- **Secondary Blue:** `#0059b3`
- **Gold Accent:** `#ffd700`
- **Dark Blue:** `#00264d`
- **Success Green:** `#28a745`
- **Warning Orange:** `#ffc107`
- **Danger Red:** `#dc3545`

## 📁 Project Structure
```
scti-school/
├── index.html (SPA - Single Page Application)
├── assets/
│   ├── css/style.css
│   ├── js/
│   │   ├── app.js
│   │   ├── menu.js
│   │   ├── pages.js
│   │   └── pages/
│   │       ├── home.js
│   │       ├── programs.js
│   │       ├── gallery.js
│   │       ├── notices.js
│   │       ├── contact.js
│   │       ├── login.js
│   │       └── signup.js
│   └── images/
├── pages/
│   ├── login-simple.php
│   ├── signup.php
│   ├── contact-submit.php
│   ├── view-contacts.php
│   └── get-contact-count.php
├── dashboards/
│   ├── admin-dashboard.php
│   ├── teacher-dashboard.php
│   └── student-dashboard.php
├── includes/
│   └── config.php
└── database-setup.sql
```

## 🗄️ Database Structure
**Database Name:** `scti_school`

### Tables:
1. **students** - Student accounts and information
2. **teachers** - Teacher accounts and information
3. **admins** - Administrator accounts
4. **contacts** - Contact form submissions
5. **notices** - Notice board announcements

## ✅ Completed Features

### 1. Single Page Application (SPA)
- ✅ Sticky navigation bar
- ✅ Dynamic content loading without page refresh
- ✅ Smooth transitions and animations
- ✅ Mobile responsive menu
- ✅ Active menu highlighting

### 2. Authentication System
- ✅ Login form with password visibility toggle
- ✅ Signup form with comprehensive validation:
  - Password: 7-12 characters
  - At least one uppercase letter
  - At least one lowercase letter
  - At least one number
  - At least one special character (!@#$%^&*)
  - Real-time password strength indicator
  - Password match validation
  - Email format validation
- ✅ Session management
- ✅ Role-based access (Admin/Teacher/Student)
- ✅ Auto-redirect to appropriate dashboard

### 3. Contact Form System
- ✅ Contact form with validation
- ✅ Database storage (contacts table)
- ✅ AJAX submission
- ✅ Success/error messages
- ✅ Admin view for contact messages
- ✅ Contact count on admin dashboard

### 4. Admin Dashboard
- ✅ Statistics cards (Students, Teachers, Programs, Notices, Contacts)
- ✅ Quick actions menu
- ✅ View contact messages button
- ✅ Recent activity feed
- ✅ Logout functionality
- ✅ Live contact count via AJAX

### 5. UI Enhancements
- ✅ Gradient backgrounds
- ✅ Smooth animations (fade-in, slide-down, hover effects)
- ✅ Password strength indicator with glow effects
- ✅ Form validation with real-time feedback
- ✅ Responsive design for all screen sizes
- ✅ Modern card-based layouts
- ✅ Icon integration (Font Awesome)

### 6. Pages Content
- ✅ Home page with banner, notices, events, campus info
- ✅ Programs page with 4 courses (detailed descriptions)
- ✅ Gallery page with image grid and overlays
- ✅ Notice Board page with card layout
- ✅ Contact Us page with form and contact info

## 🔐 Default Login Credentials

### Admin
- Username: `admin`
- Password: `admin123`
- Dashboard: `/dashboards/admin-dashboard.php`

### Teacher
- Username: `teacher`
- Password: `teacher123`
- Dashboard: `/dashboards/teacher-dashboard.php`

### Student
- Username: `student`
- Password: `student123`
- Dashboard: `/dashboards/student-dashboard.php`

## 🌐 URLs

### Main Application
- Homepage (SPA): `http://localhost/scti-school/`
- Login: Click "Login" in navbar (SPA)
- Signup: Click "Sign Up" in navbar (SPA)

### Admin Panel
- Admin Dashboard: `http://localhost/scti-school/dashboards/admin-dashboard.php`
- View Contacts: `http://localhost/scti-school/pages/view-contacts.php`

### Database
- phpMyAdmin: `http://localhost/phpmyadmin/`
- Database: `scti_school`

## 🎯 Key Features

### Password Security
- Bcrypt hashing for password storage
- Real-time validation with visual feedback
- Password strength indicator (Weak/Medium/Strong)
- Eye icon toggle for show/hide password

### Contact Form
- Fields: Name, Email, Phone, Subject, Message
- Server-side and client-side validation
- Stores in database with timestamp
- Admin can view all submissions
- Status tracking (new/read/replied/archived)

### Responsive Design
- Mobile-first approach
- Breakpoints for tablet and desktop
- Touch-friendly navigation
- Optimized for all screen sizes

## 🔧 Technologies Used
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Backend:** PHP 8.x
- **Database:** MySQL (via phpMyAdmin)
- **Server:** Apache (XAMPP)
- **Icons:** Font Awesome 7.0.1
- **Version Control:** Git + GitHub

## 📊 Database Configuration
```php
DB_HOST: localhost
DB_NAME: scti_school
DB_USER: root
DB_PASS: (empty)
```

## 🚀 Deployment
- Local: XAMPP (`C:\xampp\htdocs\scti-school\`)
- Repository: GitHub (dev branch)
- Repository URL: `https://github.com/dhakalsabnam9-dotcom/SCTI-Project.git`

## 📝 Notes
- All passwords are hashed using PHP's `password_hash()` function
- Session-based authentication
- AJAX used for form submissions (no page reload)
- Single Page Application for better user experience
- All forms have comprehensive validation
- Mobile responsive throughout

## 🎨 UI/UX Highlights
- Consistent color scheme (Blue theme)
- Smooth animations and transitions
- Hover effects on interactive elements
- Loading states for async operations
- Success/error message feedback
- Modern gradient backgrounds
- Card-based layouts
- Icon-enhanced navigation

---

**Project Status:** ✅ Fully Functional
**Last Updated:** 2025
**Developer:** Kiro AI Assistant
