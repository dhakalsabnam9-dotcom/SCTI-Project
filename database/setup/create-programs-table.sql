-- Programs table for SCTI
CREATE TABLE IF NOT EXISTS programs (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(200) NOT NULL,
  code        VARCHAR(50)  NOT NULL,
  icon        VARCHAR(50)  DEFAULT 'fa-graduation-cap',
  color       VARCHAR(20)  DEFAULT 'blue',
  duration    VARCHAR(50)  DEFAULT '3 years',
  affiliation VARCHAR(100) DEFAULT 'CTEVT',
  assessment  VARCHAR(100) DEFAULT '50% Internal + 50% External',
  description TEXT,
  content     TEXT,
  status      ENUM('active','inactive','upcoming') DEFAULT 'active',
  sort_order  INT DEFAULT 0,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default programs
INSERT INTO programs (title, code, icon, color, duration, affiliation, assessment, description, content, status) VALUES
('Diploma in Animal Husbandry', 'DAH', 'fa-paw', 'green', '3 years', 'CTEVT', '50% Internal + 50% External',
 'Comprehensive training in animal health, management, and breeding techniques.',
 'Animal nutrition and feeding|Veterinary science basics|Livestock management and breeding|Animal health and disease prevention|Farm management and economics',
 'active'),
('B.Tech. Ed. in Information Technology', 'BTIT', 'fa-laptop-code', 'blue', '4 years', 'TU', '30% Internal + 70% External',
 'Advanced IT education with focus on software development and educational technology.',
 'Programming and software development|Database management and cloud computing|Network security and cybersecurity|Educational technology integration|IT project management',
 'active'),
('B.Tech. Ed. in Civil Engineering', 'BTCE', 'fa-hard-hat', 'orange', '4 years', 'TU', '30% Internal + 70% External',
 'Professional civil engineering education with pedagogical training.',
 'Advanced structural analysis and design|Transportation and highway engineering|Environmental engineering and sustainability|Construction technology and management|Pedagogical approaches in engineering',
 'active'),
('Diploma in Civil Engineering', 'DCE', 'fa-building', 'purple', '3 years', 'CTEVT', '50% Internal + 50% External',
 'Practical civil engineering training for construction and infrastructure projects.',
 'Building construction technology|Surveying and leveling|Structural mechanics|Construction materials and testing|AutoCAD and design software',
 'active');
