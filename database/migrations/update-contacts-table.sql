-- Update existing contacts table to add missing columns
USE scti_db;

-- Add subject column if it doesn't exist
ALTER TABLE contacts 
ADD COLUMN IF NOT EXISTS subject VARCHAR(255) NOT NULL AFTER phone;

-- Add status column if it doesn't exist
ALTER TABLE contacts 
ADD COLUMN IF NOT EXISTS status ENUM('new', 'read', 'replied', 'archived') DEFAULT 'new' AFTER message;

-- Add ip_address column if it doesn't exist
ALTER TABLE contacts 
ADD COLUMN IF NOT EXISTS ip_address VARCHAR(45) AFTER status;

-- Add user_agent column if it doesn't exist
ALTER TABLE contacts 
ADD COLUMN IF NOT EXISTS user_agent TEXT AFTER ip_address;

-- Add created_at column if it doesn't exist
ALTER TABLE contacts 
ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER user_agent;

-- Add updated_at column if it doesn't exist
ALTER TABLE contacts 
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

-- Create indexes if they don't exist
CREATE INDEX IF NOT EXISTS idx_contact_status ON contacts(status);
CREATE INDEX IF NOT EXISTS idx_contact_created ON contacts(created_at);

-- Show the updated table structure
DESCRIBE contacts;

SELECT 'Contacts table updated successfully!' as message;
