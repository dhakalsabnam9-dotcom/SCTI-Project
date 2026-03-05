-- Create Database
CREATE DATABASE IF NOT EXISTS scti_school;
USE scti_school;

-- Students Table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    course VARCHAR(100),
    semester INT,
    phone VARCHAR(20),
    address TEXT,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    remember_token VARCHAR(100),
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Teachers Table
CREATE TABLE IF NOT EXISTS teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    teacher_id VARCHAR(20) UNIQUE NOT NULL,
    department VARCHAR(100),
    designation VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    remember_token VARCHAR(100),
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Admins Table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role VARCHAR(50) DEFAULT 'admin',
    phone VARCHAR(20),
    status ENUM('active', 'inactive') DEFAULT 'active',
    remember_token VARCHAR(100),
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Notices Table
CREATE TABLE IF NOT EXISTS notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category ENUM('admission', 'exam', 'event', 'holiday', 'general', 'urgent') NOT NULL,
    priority ENUM('normal', 'high', 'urgent') DEFAULT 'normal',
    status ENUM('active', 'inactive') DEFAULT 'active',
    notice_date DATE NOT NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL
);

-- Insert Sample Admin (password: admin123)
INSERT INTO admins (username, email, password, full_name, role, phone, status) 
VALUES (
    'admin',
    'admin@scti.edu.np',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'System Administrator',
    'super_admin',
    '9841234567',
    'active'
);

-- Insert Sample Teacher (password: teacher123)
INSERT INTO teachers (username, email, password, full_name, teacher_id, department, designation, phone, status) 
VALUES (
    'bibek.bhandari',
    'bibek@scti.edu.np',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Bibek Bhandari',
    'TCH001',
    'Computer Science',
    'Invigilator',
    '9841234568',
    'active'
);

-- Insert Sample Student (password: student123)
INSERT INTO students (username, email, password, full_name, student_id, course, semester, phone, status) 
VALUES (
    'john.doe',
    'john@student.scti.edu.np',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'John Doe',
    'STU2025001',
    'B.Tech Ed in IT',
    1,
    '9841234569',
    'active'
);

-- Insert Sample Notices
INSERT INTO notices (title, description, category, priority, status, notice_date, created_by) VALUES
('Admission Open', 'Applications for 2025/26 academic year are now open.', 'admission', 'high', 'active', '2025-11-01', 1),
('Orientation Program', 'Scheduled on Nov 10, 2025 for all new students.', 'event', 'normal', 'active', '2025-11-10', 1),
('Classes Begin', 'Nov 12, 2025. Please check your timetable online.', 'general', 'normal', 'active', '2025-11-12', 1),
('Sports Week', 'Dec 20–25, 2025. Registration for events is open.', 'event', 'normal', 'active', '2025-12-20', 1),
('AI Workshop', 'Sept 14, 2025 – Limited seats, register soon.', 'event', 'normal', 'active', '2025-09-14', 1);

-- Create indexes for better performance
CREATE INDEX idx_students_username ON students(username);
CREATE INDEX idx_students_email ON students(email);
CREATE INDEX idx_teachers_username ON teachers(username);
CREATE INDEX idx_teachers_email ON teachers(email);
CREATE INDEX idx_admins_username ON admins(username);
CREATE INDEX idx_admins_email ON admins(email);
CREATE INDEX idx_notices_status ON notices(status);
CREATE INDEX idx_notices_category ON notices(category);

-- Contact Messages Table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied', 'archived') DEFAULT 'new',
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create index for contact messages
CREATE INDEX idx_contact_status ON contact_messages(status);
CREATE INDEX idx_contact_created ON contact_messages(created_at);
