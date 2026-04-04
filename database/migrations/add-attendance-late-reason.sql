-- Add late_reason column to attendance table
ALTER TABLE attendance
  ADD COLUMN IF NOT EXISTS `late_reason` VARCHAR(255) DEFAULT NULL AFTER `remarks`;

-- Ensure attendance table exists with full structure
CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    class_name VARCHAR(150) DEFAULT NULL,
    attendance_date DATE NOT NULL,
    period VARCHAR(100) DEFAULT NULL,
    status ENUM('present','absent','late') DEFAULT 'present',
    remarks TEXT DEFAULT NULL,
    late_reason VARCHAR(255) DEFAULT NULL,
    marked_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_att_student (student_id),
    INDEX idx_att_date (attendance_date),
    INDEX idx_att_class (class_name)
);
