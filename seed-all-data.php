<?php
require_once 'includes/config.php';
try {
    $db = getDBConnection();

    // ── 1. GRADES ─────────────────────────────────────────────────────────
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
    try { $db->exec("ALTER TABLE grades ADD COLUMN exam_type VARCHAR(50) DEFAULT 'Mid-Term'"); } catch(Exception $e) {}

    $db->exec("DELETE FROM grades");
    $students = $db->query("SELECT id FROM students WHERE status='active'")->fetchAll(PDO::FETCH_COLUMN);
    $subjects = ['Mathematics','English','Computer Science','Physics','Chemistry','Programming','Database','Networking'];
    $examTypes = ['Mid-Term','Final'];
    $gCount = 0;
    foreach ($students as $sid) {
        foreach ($subjects as $subj) {
            foreach ($examTypes as $exam) {
                $db->prepare("INSERT INTO grades (student_id,subject,exam_type,internal_marks,external_marks) VALUES (?,?,?,?,?)")
                   ->execute([$sid,$subj,$exam,rand(25,40),rand(40,60)]);
                $gCount++;
            }
        }
    }

    // ── 2. ATTENDANCE ─────────────────────────────────────────────────────
    $db->exec("DELETE FROM attendance");
    $programs = [
        'B.Tech Ed in IT'    => ['Semester 1','Semester 2','Semester 3','Semester 4'],
        'B.Tech Ed in Civil' => ['Semester 1','Semester 2','Semester 3','Semester 4'],
        'Diploma in Civil'   => ['Semester 1','Semester 2','Semester 3'],
        'Animal Husbandry'   => ['Semester 1','Semester 2','Semester 3','Semester 4','Semester 5'],
        'Diploma Electrical' => ['Semester 1','Semester 2','Semester 3'],
    ];
    $periods = ['1st Period','2nd Period','3rd Period'];
    $statuses = ['present','present','present','present','absent','late']; // 67% present

    $aCount = 0;
    // Seed last 30 days of attendance
    for ($d = 29; $d >= 0; $d--) {
        $date = date('Y-m-d', strtotime("-$d days"));
        // Skip weekends
        $dow = date('N', strtotime($date));
        if ($dow >= 6) continue;

        foreach ($programs as $course => $sems) {
            foreach ($sems as $sem) {
                $semNum = intval(preg_replace('/[^0-9]/','',$sem));
                $stuList = $db->prepare("SELECT id FROM students WHERE status='active' AND course=? AND (semester=? OR semester=?) LIMIT 30");
                $stuList->execute([$course,$sem,$semNum]);
                $stuIds = $stuList->fetchAll(PDO::FETCH_COLUMN);
                if (!$stuIds) continue;

                $className = $course.' '.$sem;
                $period = $periods[array_rand($periods)];

                foreach ($stuIds as $sid) {
                    $status = $statuses[array_rand($statuses)];
                    $lateReason = $status==='late' ? 'Traffic jam' : '';
                    $db->prepare("INSERT INTO attendance (student_id,class_name,attendance_date,period,status,late_reason,marked_by) VALUES (?,?,?,?,?,?,1)")
                       ->execute([$sid,$className,$date,$period,$status,$lateReason]);
                    $aCount++;
                }
            }
        }
    }

    echo "<div style='font-family:sans-serif;max-width:600px;margin:40px auto;padding:30px;background:#d4edda;border-radius:10px;border:1px solid #c3e6cb'>";
    echo "<h2 style='color:#155724'>✓ All data seeded!</h2>";
    echo "<table style='width:100%;border-collapse:collapse;margin-top:16px'>";
    echo "<tr style='background:#004080;color:#fff'><th style='padding:10px;text-align:left'>Data</th><th style='padding:10px;text-align:left'>Records</th></tr>";
    echo "<tr style='background:#fff'><td style='padding:10px'>Grades</td><td style='padding:10px'><b>$gCount</b> records (8 subjects × 2 exam types)</td></tr>";
    echo "<tr style='background:#f8f9fa'><td style='padding:10px'>Attendance</td><td style='padding:10px'><b>$aCount</b> records (last 30 days)</td></tr>";
    echo "<tr style='background:#fff'><td style='padding:10px'>Students</td><td style='padding:10px'><b>".count($students)."</b> active students</td></tr>";
    echo "</table>";
    echo "<div style='margin-top:20px;display:flex;gap:10px;flex-wrap:wrap'>";
    echo "<a href='http://localhost/scti-school/pages/teacher-grades.php' style='background:#004080;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none'>View Grades</a>";
    echo "<a href='http://localhost/scti-school/pages/teacher-attendance.php' style='background:#28a745;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none'>View Attendance</a>";
    echo "</div></div>";
} catch(Exception $e) {
    echo "<div style='color:red;font-family:sans-serif;padding:20px'>Error: ".$e->getMessage()."</div>";
}
?>
