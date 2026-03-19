-- Update teacher username to teacher123
-- Run this in phpMyAdmin or MySQL CLI
UPDATE teachers SET username = 'teacher123' WHERE id = 1;

-- If you want to update by current username (replace 'old_username' with actual):
-- UPDATE teachers SET username = 'teacher123' WHERE username = 'old_username';

-- Verify:
SELECT id, full_name, username, department FROM teachers;
