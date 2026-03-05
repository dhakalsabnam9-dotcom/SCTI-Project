-- Fix contacts table - Add only missing columns
USE scti_db;

-- Add subject column (skip if exists)
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'scti_db' 
AND TABLE_NAME = 'contacts' 
AND COLUMN_NAME = 'subject';

SET @query = IF(@col_exists = 0, 
    'ALTER TABLE contacts ADD COLUMN subject VARCHAR(255) NOT NULL DEFAULT "" AFTER phone', 
    'SELECT "subject column already exists" as message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add ip_address column (skip if exists)
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'scti_db' 
AND TABLE_NAME = 'contacts' 
AND COLUMN_NAME = 'ip_address';

SET @query = IF(@col_exists = 0, 
    'ALTER TABLE contacts ADD COLUMN ip_address VARCHAR(45) AFTER status', 
    'SELECT "ip_address column already exists" as message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add user_agent column (skip if exists)
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'scti_db' 
AND TABLE_NAME = 'contacts' 
AND COLUMN_NAME = 'user_agent';

SET @query = IF(@col_exists = 0, 
    'ALTER TABLE contacts ADD COLUMN user_agent TEXT AFTER ip_address', 
    'SELECT "user_agent column already exists" as message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add created_at column (skip if exists)
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'scti_db' 
AND TABLE_NAME = 'contacts' 
AND COLUMN_NAME = 'created_at';

SET @query = IF(@col_exists = 0, 
    'ALTER TABLE contacts ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER user_agent', 
    'SELECT "created_at column already exists" as message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add updated_at column (skip if exists)
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'scti_db' 
AND TABLE_NAME = 'contacts' 
AND COLUMN_NAME = 'updated_at';

SET @query = IF(@col_exists = 0, 
    'ALTER TABLE contacts ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at', 
    'SELECT "updated_at column already exists" as message');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Show final table structure
DESCRIBE contacts;

SELECT 'Contacts table updated successfully!' as Result;
