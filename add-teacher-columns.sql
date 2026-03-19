-- Run this if teachers table is missing qualification/experience/subjects columns
ALTER TABLE teachers
  ADD COLUMN IF NOT EXISTS qualification VARCHAR(100) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS experience    VARCHAR(100) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS subjects      TEXT         DEFAULT NULL;
