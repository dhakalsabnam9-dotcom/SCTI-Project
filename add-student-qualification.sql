-- Run this in phpMyAdmin to add qualification column to students table
ALTER TABLE students ADD COLUMN IF NOT EXISTS qualification VARCHAR(100) DEFAULT NULL AFTER phone;
