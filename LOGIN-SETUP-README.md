# SCTI School Management System - Login Setup Guide

## Files Created

1. **login.php** - Main login page with PHP authentication
2. **logout.php** - Logout handler
3. **config.php** - Database configuration and helper functions
4. **database-setup.sql** - Database schema and sample data

## Setup Instructions

### Step 1: Database Setup

1. Open phpMyAdmin or MySQL command line
2. Import the `database-setup.sql` file:
   ```sql
   mysql -u root -p < database-setup.sql
   ```
   OR use phpMyAdmin's Import feature

3. This will create:
   - Database: `scti_school`
   - Tables: `students`, `teachers`, `admins`, `notices`
   - Sample users with credentials (see below)

### Step 2: Configure Database Connection

Edit `config.php` if your database settings are different:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'scti_school');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Step 3: Update Login.html

Replace the form action in `Login.html`:

```html
<!-- Change this -->
<form class="login-form" id="loginForm">

<!-- To this -->
<form class="login-form" method="POST" action="login.php">
```

Or simply use `login.php` directly instead of `Login.html`

### Step 4: Test Login

Access: `http://localhost/your-project-folder/login.php`

## Default Login Credentials

### Administrator
- **Username:** admin
- **Email:** admin@scti.edu.np
- **Password:** admin123
- **Redirects to:** Admin-Notice-Board.html

### Teacher
- **Username:** bibek.bhandari
- **Email:** bibek@scti.edu.np
- **Password:** teacher123
- **Redirects to:** teacher-dashboard.php (needs to be created)

### Student
- **Username:** john.doe
- **Email:** john@student.scti.edu.np
- **Password:** student123
- **Redirects to:** student-dashboard.php (needs to be created)

## Features Implemented

### Security Features
✅ Password hashing using PHP's `password_hash()`
✅ SQL injection prevention using PDO prepared statements
✅ XSS protection with `htmlspecialchars()`
✅ Session management
✅ Remember Me functionality with secure tokens
✅ CSRF token generation (in config.php)

### Login Features
✅ User type selection (Student/Teacher/Admin)
✅ Username or Email login
✅ Remember Me checkbox (30-day cookie)
✅ Last login tracking
✅ Account status checking (active/inactive)
✅ Error messages display
✅ Form data persistence on error

### Database Features
✅ Separate tables for each user type
✅ Indexed fields for performance
✅ Remember token storage
✅ Last login timestamp
✅ User status management

## File Structure

```
project/
├── login.php                 # PHP login page
├── logout.php               # Logout handler
├── config.php               # Configuration file
├── database-setup.sql       # Database schema
├── Login.html               # HTML login page (optional)
├── Admin-Notice-Board.html  # Admin dashboard
├── style.css                # Styles
└── LOGIN-SETUP-README.md    # This file
```

## Next Steps

### 1. Create Dashboard Pages

You need to create these dashboard pages:

- `student-dashboard.php` - For students
- `teacher-dashboard.php` - For teachers
- Admin already redirects to `Admin-Notice-Board.html`

### 2. Protect Admin Pages

Add this at the top of `Admin-Notice-Board.html` (rename to .php):

```php
<?php
session_start();
require_once 'config.php';
checkUserType(['admin']);
?>
```

### 3. Add Logout Button

Update the logout link in your pages:

```html
<a href="logout.php" class="logout-btn">
  <i class="fa fa-sign-out"></i> Logout
</a>
```

### 4. Create Registration Page

Create `register.php` for new user registration

### 5. Create Password Reset

Create `forgot-password.php` for password recovery

## Password Hashing

To create new password hashes for users:

```php
<?php
$password = 'your_password';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo $hash;
?>
```

## Troubleshooting

### "Connection failed" Error
- Check database credentials in `config.php`
- Ensure MySQL server is running
- Verify database exists

### "Invalid username or password"
- Check if user exists in database
- Verify password is correct
- Check user status is 'active'

### Session Issues
- Ensure `session_start()` is called
- Check PHP session configuration
- Clear browser cookies

### Remember Me Not Working
- Check cookie settings in browser
- Verify token is stored in database
- Check cookie path and expiration

## Security Recommendations

### For Production:

1. **Change default passwords** immediately
2. **Use HTTPS** - Set `session.cookie_secure` to 1
3. **Disable error display** - Set `display_errors` to 0
4. **Use environment variables** for database credentials
5. **Implement rate limiting** for login attempts
6. **Add CAPTCHA** after failed login attempts
7. **Enable password complexity** requirements
8. **Implement account lockout** after multiple failed attempts
9. **Add email verification** for new accounts
10. **Regular security audits**

## Support

For issues or questions:
- Email: admin@scti.edu.np
- Check PHP error logs
- Verify database connection
- Test with sample credentials first

---

**Created for:** Sindhuli Community Technical Institute (SCTI)
**Version:** 1.0
**Date:** 2025
