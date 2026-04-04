-- Add audience column to notices table
ALTER TABLE notices ADD COLUMN IF NOT EXISTS audience ENUM('all','student','teacher','emergency') DEFAULT 'all' AFTER category;

-- Update existing notices to 'all'
UPDATE notices SET audience = 'all' WHERE audience IS NULL;
