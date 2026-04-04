<?php
require_once 'includes/config.php';
try {
    $db = getDBConnection();

    // Ensure grades table exists with correct columns
    $db->exec("CREATE TABLE IF NOT EXISTS grades (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        subject VARCHAR(200) NOT NULL,
        exam_type VARCHAR(50) DEFAULT 'Mid-Term',
        internal_marks DECIMAL(5,2) DEFAULT 0,
        external_marks DECIMAL(5,2) DEFAULT 0,
        remarks TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // Add exam_type column if missing
    try { $db->exec("ALTER TABLE grades ADD COLUMN exam_type VARCHAR(50) DEFAULT 'Mid-Term'"); } catch(Exception $e) {}

    // Clear existing seeded grades to re-seed fresh
    $db->exec("DELETE FROM grades");

    $students = $db->query("SELECT id FROM students WHERE status='active'")->fetchAll(PDO::FETCH_COLUMN);
    $subjects = ['Mathematics','English','Computer Science','Physics','Chemistry','Programming','Database','Networking'];
    $examTypes = ['Mid-Term','Final'];

    $inserted = 0;
    foreach ($students as $sid) {
        foreach ($subjects as $subj) {
            foreach ($examTypes as $exam) {
                $internal = rand(25, 40);
                $external = rand(40, 60);
                $db->prepare("INSERT INTO grades (student_id, subject, exam_type, internal_marks, external_marks) VALUES (?,?,?,?,?)")
                   ->execute([$sid, $subj, $exam, $internal, $external]);
                $inserted++;
            }
        }
    }

    echo "<div style='font-family:sans-serif;max-width:500px;margin:40px auto;padding:30px;background:#d4edda;border-radius:10px'>";
    echo "<h2 style='color:#155724'>✓ Grades seeded!</h2>";
    echo "<p>Inserted: <b>$inserted</b> grade records</p>";
    echo "<p>Students: <b>".count($students)."</b> × 8 subjects × 2 exam types</p>";
    echo "<a href='http://localhost/scti-school/pages/teacher-grades.php' style='background:#004080;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;display:inline-block;margin-top:10px'>View Grades</a>";
    echo "</div>";
} catch(Exception $e) {
    echo "<div style='color:red;font-family:sans-serif;padding:20px'>Error: ".$e->getMessage()."</div>";
}
?>
