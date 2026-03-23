# SCTI School Management System

A web-based school management system for **Sindhuli Community Technical Institute (SCTI)**, built with PHP, MySQL, and vanilla JavaScript. Runs on XAMPP (Apache + MySQL).

---

## Tech Stack

- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Backend:** PHP 8.x
- **Database:** MySQL
- **Server:** Apache via XAMPP
- **Icons:** Font Awesome 6.5.1
- **Charts:** Chart.js 4.4.0

---

## Setup

1. Clone or copy the project to `C:\xampp\htdocs\scti-school\`
2. Start Apache and MySQL in XAMPP Control Panel
3. Open `http://localhost/phpmyadmin/` and create database `scti_school`
4. Import `database-setup.sql` to create all tables
5. Run any additional SQL migration files if needed (see below)
6. Visit `http://localhost/scti-school/`

### Database Config

Edit `includes/config.php` if your credentials differ:

```php
DB_HOST: localhost
DB_NAME: scti_school
DB_USER: root
DB_PASS: (empty by default)
```

### SQL Migration Files

Run these in phpMyAdmin if you encounter column errors:

| File | Purpose |
|---|---|
| `database-setup.sql` | Full schema — run first |
| `fix-attendance-table.sql` | Renames `date` → `attendance_date`, adds `marked_by` |
| `add-attendance-late-reason.sql` | Adds `late_reason` column to attendance |
| `add-contact-table.sql` | Contacts table |
| `add-notice-audience.sql` | Audience column for notices |
| `add-student-qualification.sql` | Qualification column for students |
| `add-teacher-columns.sql` | Extra teacher profile columns |
| `create-programs-table.sql` | Programs/courses table |
| `create-gallery-table.sql` | Gallery table |
| `create-assignments-table.sql` | Assignments table |
| `create-materials-table.sql` | Course materials table |
| `create-submissions-table.sql` | Assignment submissions table |

---

## Default Login Credentials

| Role | Username | Password | Dashboard |
|---|---|---|---|
| Admin | `admin` | `admin123` | `/dashboards/admin-dashboard.php` |
| Teacher | `teacher` | `teacher123` | `/dashboards/teacher-dashboard.php` |
| Student | `student` | `student123` | `/dashboards/student-dashboard.php` |

> Passwords are hashed with PHP `password_hash()`. Change defaults after first login.

---

## Project Structure

```
scti-school/
├── index.html                  # Public SPA (Home, Programs, Gallery, Notices, Contact)
├── index.php                   # Redirects to index.html
├── .htaccess                   # Apache config
│
├── assets/
│   ├── css/style.css           # Global stylesheet
│   ├── js/
│   │   ├── app.js              # SPA router
│   │   ├── menu.js             # Navigation
│   │   ├── pages.js            # Page loader
│   │   └── pages/              # Per-page JS modules
│   └── images/                 # Logos, banners, photos
│
├── includes/
│   ├── config.php              # DB connection
│   └── logout.php              # Session logout
│
├── dashboards/
│   ├── admin-dashboard.php
│   ├── teacher-dashboard.php
│   └── student-dashboard.php
│
├── pages/                      # PHP page handlers (88 files)
│   ├── login-simple.php
│   ├── attendance-save.php
│   ├── attendance-data.php
│   ├── student-attendance-data.php
│   ├── teacher-attendance.php
│   ├── student-attendance.php
│   ├── gallery-*.php
│   ├── assignment-*.php
│   ├── notice-*.php
│   └── ...
│
├── admin/
│   └── Admin-Notice-Board.html
│
└── uploads/
    ├── gallery/                # Uploaded gallery images
    └── materials/              # Uploaded course materials
```

---

## Features

### Public Website (SPA)
- Home page with banner, events, campus info
- Programs page — course listings with details
- Gallery — dynamic image/video gallery
- Notice Board — public announcements
- Contact form — stored in DB, viewable by admin

### Admin Dashboard
- Manage students, teachers, programs
- Notice board management with audience targeting
- View and reply to contact messages
- Gallery management (upload images/videos)
- Assignment and grade oversight

### Teacher Dashboard
- Mark attendance by class/date/period
- View attendance charts and analytics
- Create and manage assignments
- Upload course materials
- Enter student grades
- Add new students

### Student Dashboard
- View attendance rate with charts
- View grades and GPA
- Submit assignments
- Access course materials
- Edit profile (name, email, phone, address)
- View notices

---

## Key Pages Reference

| URL | Description |
|---|---|
| `http://localhost/scti-school/` | Public homepage |
| `/dashboards/admin-dashboard.php` | Admin panel |
| `/dashboards/teacher-dashboard.php` | Teacher panel |
| `/dashboards/student-dashboard.php` | Student panel |
| `/pages/teacher-attendance.php` | Mark attendance |
| `/pages/student-attendance.php` | Student attendance view |
| `/pages/manage-gallery.php` | Gallery management |
| `/pages/manage-programs.php` | Program/course management |

---

## Database Tables

| Table | Description |
|---|---|
| `students` | Student accounts (`course`, `semester`, `student_id`, etc.) |
| `teachers` | Teacher accounts |
| `admins` | Admin accounts |
| `programs` | Courses (`title`, `content` pipe-separated subjects, `status`) |
| `attendance` | Records (`student_id`, `attendance_date`, `status`, `marked_by`) |
| `grades` | Marks (`student_id`, `subject`, `internal_marks`, `external_marks`) |
| `assignments` | Tasks (`created_by`, `due_date`, `subject`) |
| `assignment_submissions` | Student submissions |
| `materials` | Course files (`uploaded_by`, `file_path`) |
| `gallery` | Media items (`file_type`, `category`) |
| `notices` | Announcements (`audience`, `status`) |
| `contacts` | Contact form submissions |

---

## Notes

- `students.course` stores the program title (e.g. `"BIT"`)
- `students.semester` stores full string (e.g. `"Semester 1"`)
- `programs.content` stores subjects as pipe-separated string (e.g. `"Math|Physics|English"`)
- `materials` table uses `uploaded_by` (not `teacher_id`)
- `attendance` table uses `attendance_date` (not `date`) and `marked_by` (not `teacher_id`)
- No public signup — admin/teacher creates all user accounts
- Git branch: `Sabnam_learning`

---

## Theme Colors

| Name | Hex |
|---|---|
| Primary Blue | `#004080` |
| Secondary Blue | `#0059b3` |
| Dark Navy | `#00264d` |
| Success Green | `#28a745` |
| Warning Orange | `#fd7e14` |
| Danger Red | `#dc3545` |
