# SCTI School Management System

A comprehensive web-based school management system for Sindhuli Community Technical Institute (SCTI).

## 🎓 Features

- **User Authentication System**
  - Three user types: Admin, Teacher, Student
  - Secure login with session management
  - Role-based dashboard access

- **Admin Dashboard**
  - Notice board management
  - Add, edit, delete notices
  - Statistics and analytics
  - Search and filter functionality

- **Teacher Dashboard**
  - View assigned classes
  - Manage students
  - Check schedules
  - Grade management

- **Student Dashboard**
  - View enrolled courses
  - Check grades and attendance
  - Access course materials
  - View GPA

- **Public Pages**
  - Homepage with banner
  - Programs listing
  - Photo gallery
  - Notice board
  - Contact information

## 🚀 Installation

### Prerequisites
- XAMPP (Apache + MySQL + PHP)
- Web browser
- Git (optional)

### Setup Steps

1. **Clone or Download**
   ```bash
   git clone https://github.com/dhakalsabnam9-dotcom/SCTI-Project.git
   cd SCTI-Project
   ```

2. **Move to XAMPP**
   - Copy project folder to `C:\xampp\htdocs\scti-school\`

3. **Start XAMPP**
   - Start Apache
   - Start MySQL

4. **Import Database**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Click "Import"
   - Choose `database-setup.sql`
   - Click "Go"

5. **Access Application**
   - Open browser: `http://localhost/scti-school/`

## 🔐 Login Credentials

### Administrator
- **Username:** admin
- **Password:** admin123
- **Access:** Full system control

### Teacher
- **Username:** teacher
- **Password:** teacher123
- **Access:** Class and student management

### Student
- **Username:** student
- **Password:** student123
- **Access:** Course and grade viewing

## 📁 Project Structure

```
scti-school/
├── index.html              # Homepage
├── index.php               # Landing page handler
├── login-simple.php        # Login page
├── logout.php              # Logout handler
├── config.php              # Database configuration
├── database-setup.sql      # Database schema
├── Admin-Notice-Board.html # Admin dashboard
├── teacher-dashboard.php   # Teacher dashboard
├── student-dashboard.php   # Student dashboard
├── About Us.html           # About page
├── Programs.html           # Programs page
├── Gallery.html            # Gallery page
├── Notice Board.html       # Public notice board
├── Contact Us.html         # Contact page
├── style.css               # Main stylesheet
├── .htaccess               # Apache configuration
└── images/                 # Image assets
```

## 🛠️ Technologies Used

- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP 8.1+
- **Database:** MySQL
- **Server:** Apache
- **Icons:** Font Awesome
- **Version Control:** Git

## 📱 Responsive Design

The system is fully responsive and works on:
- Desktop computers
- Tablets
- Mobile phones

## 🔒 Security Features

- Password hashing (bcrypt)
- SQL injection prevention (PDO prepared statements)
- XSS protection
- Session management
- CSRF token support
- Protected configuration files

## 📊 Database Schema

### Tables
- `admins` - Administrator accounts
- `teachers` - Teacher accounts
- `students` - Student accounts
- `notices` - Notice board entries

## 🌐 URLs

- **Homepage:** `http://localhost/scti-school/`
- **Login:** `http://localhost/scti-school/login-simple.php`
- **Admin Dashboard:** `http://localhost/scti-school/Admin-Notice-Board.html`
- **Teacher Dashboard:** `http://localhost/scti-school/teacher-dashboard.php`
- **Student Dashboard:** `http://localhost/scti-school/student-dashboard.php`

## 📝 Configuration

Edit `config.php` to change database settings:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'scti_school');
define('DB_USER', 'root');
define('DB_PASS', '');
```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is developed for Sindhuli Community Technical Institute (SCTI).

## 👥 Authors

- Development Team - SCTI Project

## 📞 Support

For support and queries:
- Email: admin@scti.edu.np
- Website: [SCTI Official Website]

## 🎯 Future Enhancements

- [ ] Online exam system
- [ ] Attendance tracking
- [ ] Fee management
- [ ] Library management
- [ ] Email notifications
- [ ] Mobile app
- [ ] Parent portal
- [ ] Report generation

## 📸 Screenshots

### Homepage
Clean and professional homepage with banner, courses, and teacher information.

### Login Page
Secure login with three user types and remember me functionality.

### Admin Dashboard
Comprehensive notice board management with statistics and search.

### Teacher Dashboard
Class management with student lists and schedules.

### Student Dashboard
Course overview with grades and attendance tracking.

---

**© 2025 Sindhuli Community Technical Institute (SCTI)**

**Version:** 1.0  
**Last Updated:** March 2025
