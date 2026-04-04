USE scti_school;

CREATE TABLE IF NOT EXISTS subjects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  code VARCHAR(50),
  program VARCHAR(200),
  semester VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO subjects (name, code, program, semester) VALUES
-- B.Tech Ed in IT
('Mathematics I','MATH101','B.Tech Ed in IT','Semester 1'),
('English Communication','ENG101','B.Tech Ed in IT','Semester 1'),
('Computer Fundamentals','CS101','B.Tech Ed in IT','Semester 1'),
('Programming in C','CS102','B.Tech Ed in IT','Semester 1'),
('Mathematics II','MATH102','B.Tech Ed in IT','Semester 2'),
('Data Structures','CS201','B.Tech Ed in IT','Semester 2'),
('Object Oriented Programming','CS202','B.Tech Ed in IT','Semester 2'),
('Database Management','CS203','B.Tech Ed in IT','Semester 2'),
('Web Development','CS301','B.Tech Ed in IT','Semester 3'),
('Computer Networks','CS302','B.Tech Ed in IT','Semester 3'),
('Operating Systems','CS303','B.Tech Ed in IT','Semester 3'),
('Software Engineering','CS401','B.Tech Ed in IT','Semester 4'),
('Artificial Intelligence','CS402','B.Tech Ed in IT','Semester 4'),
('Project Work','CS403','B.Tech Ed in IT','Semester 4'),
-- B.Tech Ed in Civil
('Engineering Mathematics','MATH201','B.Tech Ed in Civil','Semester 1'),
('Engineering Drawing','CE101','B.Tech Ed in Civil','Semester 1'),
('Building Materials','CE102','B.Tech Ed in Civil','Semester 1'),
('Surveying I','CE201','B.Tech Ed in Civil','Semester 2'),
('Structural Mechanics','CE202','B.Tech Ed in Civil','Semester 2'),
('Concrete Technology','CE203','B.Tech Ed in Civil','Semester 2'),
('Surveying II','CE301','B.Tech Ed in Civil','Semester 3'),
('Transportation Engineering','CE302','B.Tech Ed in Civil','Semester 3'),
('Hydraulics','CE303','B.Tech Ed in Civil','Semester 3'),
('Structural Design','CE401','B.Tech Ed in Civil','Semester 4'),
('Construction Management','CE402','B.Tech Ed in Civil','Semester 4'),
-- Diploma in Civil
('Basic Mathematics','MATH301','Diploma in Civil','Semester 1'),
('Engineering Drawing','DCE101','Diploma in Civil','Semester 1'),
('Construction Materials','DCE102','Diploma in Civil','Semester 1'),
('Surveying','DCE201','Diploma in Civil','Semester 2'),
('Structural Analysis','DCE202','Diploma in Civil','Semester 2'),
('AutoCAD','DCE301','Diploma in Civil','Semester 3'),
('Project Management','DCE302','Diploma in Civil','Semester 3'),
-- Animal Husbandry
('Animal Nutrition','AH101','Animal Husbandry','Semester 1'),
('Livestock Management','AH102','Animal Husbandry','Semester 1'),
('Veterinary Science Basics','AH201','Animal Husbandry','Semester 2'),
('Animal Breeding','AH202','Animal Husbandry','Semester 2'),
('Farm Management','AH301','Animal Husbandry','Semester 3'),
('Poultry Science','AH302','Animal Husbandry','Semester 3'),
('Animal Health','AH401','Animal Husbandry','Semester 4'),
('Fisheries','AH402','Animal Husbandry','Semester 4'),
('Agricultural Economics','AH501','Animal Husbandry','Semester 5'),
-- Diploma Electrical
('Basic Electricity','EL101','Diploma Electrical','Semester 1'),
('Circuit Theory','EL102','Diploma Electrical','Semester 1'),
('Electrical Machines','EL201','Diploma Electrical','Semester 2'),
('Power Systems','EL202','Diploma Electrical','Semester 2'),
('Electronics','EL301','Diploma Electrical','Semester 3'),
('Electrical Installation','EL302','Diploma Electrical','Semester 3');
