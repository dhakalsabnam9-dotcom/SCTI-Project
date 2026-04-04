USE scti_school;
ALTER TABLE assignments ADD COLUMN IF NOT EXISTS assign_date DATETIME DEFAULT NULL AFTER class_name;
