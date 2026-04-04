-- Fix attendance table columns to match the application
-- Run this in phpMyAdmin if you get column errors

-- Rename 'date' to 'attendance_date' if it exists as 'date'
ALTER TABLE attendance
  CHANGE COLUMN `date` `attendance_date` DATE NOT NULL;

-- Add missing columns if they don't exist
ALTER TABLE attendance
  ADD COLUMN IF NOT EXISTS `class_name`  VARCHAR(150) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `period`      VARCHAR(100) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `remarks`     TEXT         DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `marked_by`   INT          DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `updated_at`  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Verify structure
DESCRIBE attendance;
