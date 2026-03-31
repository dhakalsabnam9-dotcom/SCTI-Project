# SCTI Project - Clean Architecture

## Project Structure

```
scti-project/
Γöé
Γö£ΓöÇΓöÇ index.html                      # Main SPA landing page
Γö£ΓöÇΓöÇ index.php                       # Redirects to index.html
Γö£ΓöÇΓöÇ .htaccess                       # Apache configuration
Γö£ΓöÇΓöÇ database-setup.sql              # Database schema
Γö£ΓöÇΓöÇ README.md                       # Project documentation
Γöé
Γö£ΓöÇΓöÇ assets/                         # Static assets
Γöé   Γö£ΓöÇΓöÇ css/
Γöé   Γöé   ΓööΓöÇΓöÇ style.css              # Main stylesheet
Γöé   Γö£ΓöÇΓöÇ js/                        # JavaScript files (if needed)
Γöé   ΓööΓöÇΓöÇ images/                    # All images
Γöé       Γö£ΓöÇΓöÇ banner.png
Γöé       Γö£ΓöÇΓöÇ scti logo.jpeg
Γöé       Γö£ΓöÇΓöÇ bibek sir.jpg
Γöé       Γö£ΓöÇΓöÇ santosh.png
Γöé       Γö£ΓöÇΓöÇ tej.png
Γöé       Γö£ΓöÇΓöÇ work.jpg
Γöé       Γö£ΓöÇΓöÇ img1.jpg
Γöé       Γö£ΓöÇΓöÇ img 2.jpg
Γöé       Γö£ΓöÇΓöÇ img 3.jpg
Γöé       Γö£ΓöÇΓöÇ img 4.jpg
Γöé       ΓööΓöÇΓöÇ img5.jpg
Γöé
Γö£ΓöÇΓöÇ includes/                       # PHP includes and utilities
Γöé   Γö£ΓöÇΓöÇ config.php                 # Database configuration
Γöé   ΓööΓöÇΓöÇ logout.php                 # Logout handler
Γöé
Γö£ΓöÇΓöÇ pages/                          # Authentication pages
Γöé   Γö£ΓöÇΓöÇ login-simple.php           # Login page
Γöé   ΓööΓöÇΓöÇ signup.php                 # Registration page
Γöé
Γö£ΓöÇΓöÇ dashboards/                     # User dashboards
Γöé   Γö£ΓöÇΓöÇ student-dashboard.php      # Student dashboard
Γöé   ΓööΓöÇΓöÇ teacher-dashboard.php      # Teacher dashboard
Γöé
Γö£ΓöÇΓöÇ admin/                          # Admin panel
Γöé   ΓööΓöÇΓöÇ Admin-Notice-Board.html    # Admin notice board
Γöé
ΓööΓöÇΓöÇ .kiro/                          # Kiro IDE configuration
    ΓööΓöÇΓöÇ specs/                      # Project specifications
```

## File Organization

### Root Level
- **index.html**: Main SPA with all pages (Home, Programs, Gallery, Notices, Contact, Login, Signup)
- **index.php**: Simple redirect to index.html
- **.htaccess**: Apache server configuration
- **database-setup.sql**: MySQL database schema

### Assets Folder
- **css/**: All stylesheets
- **js/**: JavaScript files (currently embedded in index.html)
- **images/**: All images, logos, photos

### Includes Folder
- **config.php**: Database connection configuration
- **logout.php**: Session logout handler

### Pages Folder
- **login-simple.php**: Standalone login page (also accessible via SPA)
- **signup.php**: Standalone signup page (also accessible via SPA)

### Dashboards Folder
- **student-dashboard.php**: Student user dashboard
- **teacher-dashboard.php**: Teacher user dashboard

### Admin Folder
- **Admin-Notice-Board.html**: Admin panel for managing notices

## URL Structure

### Main Website
- Homepage: `http://localhost/scti-school/`
- Direct access: `http://localhost/scti-school/index.html`

### SPA Navigation (No page reload)
- Home: Click "Home" in navbar
- Programs: Click "Programs" in navbar
- Gallery: Click "Gallery" in navbar
- Notice Board: Click "Notice Board" in navbar
- Contact Us: Click "Contact Us" in navbar
- Login: Click "Login" in navbar
- Signup: Click "Sign Up" in navbar

### Direct Page Access
- Login: `http://localhost/scti-school/pages/login-simple.php`
- Signup: `http://localhost/scti-school/pages/signup.php`
- Student Dashboard: `http://localhost/scti-school/dashboards/student-dashboard.php`
- Teacher Dashboard: `http://localhost/scti-school/dashboards/teacher-dashboard.php`
- Admin Panel: `http://localhost/scti-school/admin/Admin-Notice-Board.html`

## Benefits of This Structure

1. **Organized**: Clear separation of concerns
2. **Scalable**: Easy to add new features
3. **Maintainable**: Files are logically grouped
4. **Professional**: Follows industry best practices
5. **Clean**: No clutter in root directory
6. **Secure**: Sensitive files in includes folder

## Path References

### In HTML/PHP Files
- CSS: `assets/css/style.css` or `../assets/css/style.css` (from subfolders)
- Images: `assets/images/filename.jpg` or `../assets/images/filename.jpg`
- Pages: `pages/login-simple.php` or `../pages/login-simple.php`

### In JavaScript (index.html)
- Images: `assets/images/filename.jpg`
- PHP endpoints: `pages/login-simple.php`, `pages/signup.php`
- Redirects: `admin/Admin-Notice-Board.html`, `dashboards/student-dashboard.php`

## Database Configuration

Database connection details are in `includes/config.php`:
- Host: localhost
- Database: scti_school
- Username: root
- Password: (empty)

## Test Credentials

- **Admin**: username: `admin`, password: `admin123`
- **Teacher**: username: `teacher`, password: `teacher123`
- **Student**: username: `student`, password: `student123`

## Deployment Notes

For production deployment:
1. Update database credentials in `includes/config.php`
2. Disable PHP error display in `.htaccess`
3. Use HTTPS for secure connections
4. Update file permissions for security
5. Consider moving `includes/config.php` outside web root
