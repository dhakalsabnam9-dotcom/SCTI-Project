# Quick Start Guide - SCTI Login System

## ✅ Files Created

1. **login-simple.php** - Working login (NO database needed!)
2. **student-dashboard.php** - Student dashboard
3. **teacher-dashboard.php** - Teacher dashboard
4. **Admin-Notice-Board.html** - Admin dashboard (already exists)
5. **logout.php** - Logout handler

## 🚀 How to Run (3 Easy Steps)

### Step 1: Start Your Server

Make sure you have a PHP server running:

**Option A: Using XAMPP**
- Start Apache from XAMPP Control Panel
- Place files in `C:\xampp\htdocs\your-folder\`

**Option B: Using PHP Built-in Server**
```bash
cd your-project-folder
php -S localhost:8000
```

**Option C: Using WAMP/MAMP**
- Start WAMP/MAMP
- Place files in www folder

### Step 2: Access Login Page

Open your browser and go to:
```
http://localhost/your-folder/login-simple.php
```

Or if using PHP built-in server:
```
http://localhost:8000/login-simple.php
```

### Step 3: Login with Test Credentials

**Admin Login:**
- User Type: Administrator
- Username: `admin`
- Password: `admin123`
- Redirects to: Admin Notice Board

**Teacher Login:**
- User Type: Teacher
- Username: `teacher`
- Password: `teacher123`
- Redirects to: Teacher Dashboard

**Student Login:**
- User Type: Student
- Username: `student`
- Password: `student123`
- Redirects to: Student Dashboard

## 📋 What Each Dashboard Shows

### Student Dashboard
- Enrolled courses count
- Attendance percentage
- Pending assignments
- GPA
- Course list with grades

### Teacher Dashboard
- Courses teaching
- Total students
- Pending grades
- Classes today
- Class schedule

### Admin Dashboard
- Notice board management
- Add/Edit/Delete notices
- Statistics
- Search and filter

## 🔧 Troubleshooting

### "Page not found" Error
✅ Make sure PHP server is running
✅ Check the URL path is correct
✅ Verify files are in the correct folder

### "Login not working"
✅ Make sure you selected the correct User Type
✅ Username and password are case-sensitive
✅ Try: admin / admin123 with Administrator type

### "Blank page"
✅ Check PHP is installed: `php -v`
✅ Look for PHP errors in browser console
✅ Enable error display in PHP

### "Logout not working"
✅ Make sure logout.php exists
✅ Check the logout button links to logout.php

## 🎯 Next Steps

1. ✅ Test all three login types
2. ✅ Explore each dashboard
3. ✅ Try the logout button
4. ⏭️ Set up database for production (optional)
5. ⏭️ Customize dashboards with real data

## 📝 Important Notes

- **login-simple.php** works WITHOUT database (perfect for testing!)
- **login.php** requires database setup (for production)
- All passwords are hardcoded in login-simple.php
- Session expires when browser closes
- Logout clears all session data

## 🔐 Security Note

This is a DEMO version with hardcoded credentials. For production:
- Use the database version (login.php)
- Import database-setup.sql
- Change all default passwords
- Enable HTTPS
- Add more security features

## 💡 Tips

- Keep login-simple.php for testing
- Use login.php for production with database
- Both versions work independently
- You can switch between them anytime

---

**Need Help?**
- Check if PHP is running
- Verify file paths
- Look at browser console for errors
- Make sure all files are in the same folder

**Ready to Go?**
Just open: `http://localhost/your-folder/login-simple.php`
