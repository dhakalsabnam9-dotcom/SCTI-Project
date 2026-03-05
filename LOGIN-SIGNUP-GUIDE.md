# SCTI Login & Signup System Guide

## 🎯 Features Implemented

### ✅ Login System
- Username and password authentication
- User type selection (Admin/Teacher/Student)
- Error messages for invalid credentials
- Session management
- Auto-redirect to appropriate dashboard
- Remember me functionality

### ✅ Signup System
- New user registration
- Username uniqueness validation
- Email uniqueness validation
- Password confirmation
- Minimum password length (6 characters)
- Email format validation
- Auto-login after successful signup
- Auto-redirect to dashboard

## 🔐 Login Flow

### Step 1: Access Login Page
```
http://localhost/scti-school/login-simple.php
```

### Step 2: Enter Credentials
- Select User Type
- Enter Username
- Enter Password
- Click "Login"

### Step 3: Validation
✅ **Success:** Redirects to dashboard
❌ **Error:** Shows error message

### Error Messages:
- "All fields are required!" - Missing fields
- "Invalid username, password, or user type!" - Wrong credentials

## 📝 Signup Flow

### Step 1: Access Signup Page
```
http://localhost/scti-school/signup.php
```

### Step 2: Fill Registration Form
- Select User Type (Student/Teacher/Admin)
- Enter Full Name
- Choose Username
- Enter Email
- Create Password (min 6 characters)
- Confirm Password
- Click "Sign Up"

### Step 3: Validation

✅ **Success Cases:**
- All fields filled correctly
- Username doesn't exist
- Email not registered
- Passwords match
- Email format valid
- Password length >= 6 characters
→ **Result:** Account created, auto-login, redirect to dashboard

❌ **Error Cases:**
1. "All fields are required!" - Missing fields
2. "Username already exists! Please choose a different username."
3. "Email already registered! Please use a different email."
4. "Passwords do not match!"
5. "Password must be at least 6 characters long!"
6. "Invalid email format!"
7. "Invalid user type!"

## 🗄️ Database Integration

### Tables Used:
- `students` - Student accounts
- `teachers` - Teacher accounts
- `admins` - Admin accounts

### Signup Process:
1. Check if username exists
2. Check if email exists
3. Hash password using bcrypt
4. Generate unique ID (STU2025XXXX for students, TCHXXX for teachers)
5. Insert new user record
6. Create session
7. Redirect to dashboard

## 🎨 Navigation

### Homepage Navbar:
- Home
- Programs
- Gallery
- Notice Board
- Contact Us
- **Login** ← Click to login
- **Sign Up** ← Click to register

### Login Page:
- Link to Sign Up at bottom
- "Don't have an account? Register Here"

### Signup Page:
- Link to Login at bottom
- "Already have an account? Login Here"

## 🚀 Testing Instructions

### Test Login:

1. **Open:** `http://localhost/scti-school/login-simple.php`

2. **Test Valid Login:**
   - User Type: Administrator
   - Username: admin
   - Password: admin123
   - **Expected:** Redirects to Admin Dashboard

3. **Test Invalid Login:**
   - User Type: Student
   - Username: wronguser
   - Password: wrongpass
   - **Expected:** Error message "Invalid username, password, or user type!"

### Test Signup:

1. **Open:** `http://localhost/scti-school/signup.php`

2. **Test Successful Signup:**
   - User Type: Student
   - Full Name: John Doe
   - Username: john.doe2025
   - Email: john.doe@student.scti.edu.np
   - Password: password123
   - Confirm Password: password123
   - **Expected:** Account created, redirects to Student Dashboard

3. **Test Duplicate Username:**
   - Try to signup with username: admin
   - **Expected:** Error "Username already exists!"

4. **Test Password Mismatch:**
   - Password: password123
   - Confirm Password: password456
   - **Expected:** Error "Passwords do not match!"

5. **Test Short Password:**
   - Password: 12345
   - **Expected:** Error "Password must be at least 6 characters long!"

6. **Test Invalid Email:**
   - Email: notanemail
   - **Expected:** Error "Invalid email format!"

## 📊 User Dashboards

### After Login/Signup, users are redirected to:

**Admin:**
- URL: `Admin-Notice-Board.html`
- Features: Notice management, statistics

**Teacher:**
- URL: `teacher-dashboard.php`
- Features: Class management, student lists

**Student:**
- URL: `student-dashboard.php`
- Features: Course view, grades, attendance

## 🔒 Security Features

### Password Security:
- ✅ Hashed using `password_hash()` (bcrypt)
- ✅ Never stored in plain text
- ✅ Minimum 6 characters required

### SQL Injection Prevention:
- ✅ PDO prepared statements
- ✅ Parameter binding

### XSS Protection:
- ✅ `htmlspecialchars()` on all outputs
- ✅ Input sanitization

### Session Security:
- ✅ Session-based authentication
- ✅ User type verification
- ✅ Auto-logout on session end

## 📁 Files Created/Modified

### New Files:
- `signup.php` - Registration page with validation

### Modified Files:
- `login-simple.php` - Added signup link in navbar
- `index.html` - Added signup link in navbar

## 🎯 URLs Reference

| Page | URL |
|------|-----|
| Homepage | `http://localhost/scti-school/` |
| Login | `http://localhost/scti-school/login-simple.php` |
| Signup | `http://localhost/scti-school/signup.php` |
| Admin Dashboard | `http://localhost/scti-school/Admin-Notice-Board.html` |
| Teacher Dashboard | `http://localhost/scti-school/teacher-dashboard.php` |
| Student Dashboard | `http://localhost/scti-school/student-dashboard.php` |

## 🐛 Troubleshooting

### Issue: "Connection failed"
**Solution:** Check MySQL is running in XAMPP

### Issue: "Username already exists"
**Solution:** Choose a different username or login with existing account

### Issue: "Email already registered"
**Solution:** Use a different email or login with existing account

### Issue: Page not found
**Solution:** Ensure files are in `C:\xampp\htdocs\scti-school\`

### Issue: Can't login after signup
**Solution:** Check database to verify user was created:
```sql
SELECT * FROM students WHERE username = 'your_username';
```

## ✅ Validation Rules Summary

| Field | Rules |
|-------|-------|
| User Type | Required, must be student/teacher/admin |
| Full Name | Required |
| Username | Required, must be unique |
| Email | Required, must be unique, valid format |
| Password | Required, minimum 6 characters |
| Confirm Password | Required, must match password |

## 🎉 Success Indicators

### Login Success:
- ✅ No error message
- ✅ Redirected to dashboard
- ✅ User name displayed in dashboard

### Signup Success:
- ✅ No error message
- ✅ Account created in database
- ✅ Auto-logged in
- ✅ Redirected to dashboard

---

**© 2025 SCTI - School Management System**  
**Version:** 2.0 with Login & Signup  
**Last Updated:** March 2025
