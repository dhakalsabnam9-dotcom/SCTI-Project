# SCTI Portal

A web-based academic management system for **Sindhuli Community Technical Institute (SCTI)** — a nonprofit technical institute supported by DCC Sindhuli, Kamalamai Municipality, and CTEVT.

The portal handles student enrollment, attendance, assignments, grading, course materials, notices, and a public-facing website — all in one place.

---

## Features

### Public
- Browse academic programs and subjects
- View photo gallery by category
- Read public notice board
- Submit contact/inquiry form

### Admin
- Manage students, teachers, and programs
- Publish notices with audience targeting (All / Students / Teachers / Emergency)
- Manage gallery images
- View contact form submissions
- Real-time dashboard with activity feed

### Teacher
- Mark daily attendance (present / absent / late) with optional late reason
- Create, edit, and delete assignments with due dates
- View and grade student submissions
- Enter internal and external marks
- Upload course materials
- View attendance and grade analytics charts

### Student
- View attendance history and percentage (with low-attendance warning)
- View grades and GPA by subject
- Submit assignments with file upload
- Download course materials
- View class timetable
- Manage profile and change password

---

## Tech Stack

| Layer      | Technology                        |
|------------|-----------------------------------|
| Backend    | PHP 7.4+                          |
| Database   | MySQL (PDO)                       |
| Frontend   | HTML, CSS, Vanilla JavaScript     |
| Icons      | Font Awesome 6.5.1                |
| Server     | Apache (XAMPP / WAMP)             |

---

## Getting Started

### Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server (XAMPP or WAMP recommended)

### Installation

1. Clone or copy the project into your web server's root directory:
   ```
   C:/xampp/htdocs/scti
   ```

2. Import the database:
   - Open **phpMyAdmin** at `http://localhost/phpmyadmin`
   - Create a database named `scti_school`
   - Import `database/setup/database-setup.sql`
   - Run any migration files in `database/migrations/` as needed

3. Configure the database connection in `includes/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'scti_school');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

4. Open the portal in your browser:
   ```
   http://localhost/scti
   ```

---

## Default Credentials

These are seeded by `database/setup/database-setup.sql`:

| Role    | Username         | Password     |
|---------|------------------|--------------|
| Admin   | `admin`          | `admin123`   |
| Teacher | `bibek.bhandari` | `teacher123` |
| Student | `john.doe`       | `student123` |

> First-time teacher and student logins are redirected to a forced password change screen.  
> Additional students and teachers are created through the admin panel with auto-generated credentials.

---

## Project Structure

```
scti/
├── index.php                  # Public homepage
├── includes/
│   ├── config.php             # DB config and helpers
│   └── logout.php             # Session destroy + redirect
├── dashboards/
│   ├── admin-dashboard.php
│   ├── teacher-dashboard.php
│   └── student-dashboard.php
├── pages/                     # All feature pages (88 PHP files)
├── assets/
│   ├── css/style.css
│   ├── js/
│   └── images/
├── database/
│   ├── setup/                 # Initial schema SQL files
│   ├── migrations/            # Incremental migration SQL files
│   └── fixes/                 # Hotfix SQL scripts
├── uploads/
│   ├── gallery/               # Uploaded gallery images
│   └── materials/             # Uploaded course materials
└── admin/
    └── Admin-Notice-Board.html
```

---

## User Roles

```
Admin
 └── Full system control (students, teachers, programs, notices, gallery)

Teacher
 └── Attendance → Assignments → Grading → Materials → Analytics

Student
 └── View attendance, grades, assignments → Submit work → Download materials
```

---

## Security

- Role-based access control on every protected page
- PDO prepared statements (SQL injection prevention)
- `password_verify()` for password checking
- HTTPOnly session cookies
- Remember Me via secure token stored in DB
- Account status check (active / inactive) on login

---

## License

This project was developed for **Sindhuli Community Technical Institute (SCTI)**.  
All rights reserved.
