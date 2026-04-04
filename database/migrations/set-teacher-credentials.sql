-- Run this in phpMyAdmin > SQL tab
-- Sets the FIRST teacher's username=teacher123 and password=123456

-- Step 1: Check what teachers exist
SELECT id, full_name, username, status FROM teachers ORDER BY id;

-- Step 2: Update the first teacher (change the id if needed based on Step 1 results)
UPDATE teachers 
SET 
    username = 'teacher123',
    password = '$2y$10$TKh8H1.PfunDStrTRBi0tuu4zSWc4BTxaHHe0GJnkfxYMbGMnlnGy'
WHERE id = (SELECT min_id FROM (SELECT MIN(id) AS min_id FROM teachers) t);

-- Step 3: Verify
SELECT id, full_name, username, status FROM teachers;

-- NOTE: If the above UPDATE affects 0 rows, there are no teachers in the DB.
-- In that case, INSERT one:
-- INSERT INTO teachers (full_name, username, password, email, department, status)
-- VALUES ('Demo Teacher', 'teacher123', '$2y$10$TKh8H1.PfunDStrTRBi0tuu4zSWc4BTxaHHe0GJnkfxYMbGMnlnGy', 'teacher@scti.edu.np', 'General', 'active');
