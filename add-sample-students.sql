USE scti_school;

-- Add sample students for all courses and semesters
INSERT INTO students (username, email, password, full_name, student_id, course, semester, phone, status) VALUES
('ram.thapa',      'ram@student.scti.edu.np',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ram Thapa',       'STU2025002', 'B.Tech Ed in IT',    'Semester 1', '9841000001', 'active'),
('sita.sharma',    'sita@student.scti.edu.np',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sita Sharma',     'STU2025003', 'B.Tech Ed in IT',    'Semester 1', '9841000002', 'active'),
('hari.poudel',    'hari@student.scti.edu.np',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hari Poudel',     'STU2025004', 'B.Tech Ed in IT',    'Semester 2', '9841000003', 'active'),
('gita.rai',       'gita@student.scti.edu.np',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Gita Rai',        'STU2025005', 'B.Tech Ed in IT',    'Semester 2', '9841000004', 'active'),
('bikash.karki',   'bikash@student.scti.edu.np', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bikash Karki',    'STU2025006', 'B.Tech Ed in Civil', 'Semester 1', '9841000005', 'active'),
('sunita.basnet',  'sunita@student.scti.edu.np', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sunita Basnet',   'STU2025007', 'B.Tech Ed in Civil', 'Semester 1', '9841000006', 'active'),
('nabin.adhikari', 'nabin@student.scti.edu.np',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nabin Adhikari',  'STU2025008', 'Diploma in Civil',   'Semester 1', '9841000007', 'active'),
('puja.tamang',    'puja@student.scti.edu.np',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Puja Tamang',     'STU2025009', 'Diploma in Civil',   'Semester 2', '9841000008', 'active');

-- Fix existing John Doe semester format
UPDATE students SET semester = 'Semester 1' WHERE student_id = 'STU2025001';
