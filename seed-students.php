<?php
require_once 'includes/config.php';
try {
    $db = getDBConnection();
    $pass = password_hash('Student@123', PASSWORD_DEFAULT);

    $firstNames = ['Ram','Sita','Hari','Gita','Bikash','Sunita','Nabin','Puja','Anil','Maya',
        'Rajan','Kamala','Saroj','Amrit','Binod','Sabina','Dipak','Anita','Suresh','Rekha',
        'Prakash','Nirmala','Santosh','Laxmi','Bishnu','Sarita','Kamal','Rita','Mohan','Sushila',
        'Arjun','Mina','Rajesh','Kopila','Dinesh','Sangita','Umesh','Kabita','Ganesh','Sundar',
        'Aarav','Aarava','Aashika','Aayusha','Adit','Aditi','Aditya','Ajita','Akasha','Alisha',
        'Manisha','Kiran','Sandeep','Roshan','Priya','Deepak','Anjali','Sanjay','Pooja','Niraj',
        'Bibek','Shristi','Prabin','Samjhana','Nirajan','Barsha','Sujan','Kritika','Pawan','Smriti',
        'Ashok','Babita','Chandra','Devi','Eshan','Falguni','Gopal','Hira','Indra','Jyoti',
        'Keshav','Lalita','Mahesh','Namrata','Om','Prabha','Rabindra','Sabita','Tilak','Uma',
        'Vijay','Wangchuk','Yam','Zara','Abhi','Sabnam','Tejash','Bibek','Sandesh','Manish'];

    $lastNames = ['Thapa','Sharma','Poudel','Rai','Karki','Basnet','Adhikari','Tamang','Gurung',
        'Shrestha','Bhandari','Pandey','Magar','Limbu','Lama','Khadka','Bhattarai','Chaudhary',
        'Yadav','Mahato','Koirala','Regmi','Dahal','Acharya','Subedi','Ghimire','Oli','Parajuli',
        'Joshi','KC','Malla','Rana','Sah','Tiwari','Upreti','Vaidya','Wagle','Xetri','Yonjan',
        'Dhakal','Giri','Hamal','Iyengar','Jha','Kafle','Luitel','Niroula','Ojha','Pradhan'];

    $programs = [
        ['course'=>'B.Tech Ed in IT',       'semesters'=>['Semester 1','Semester 2','Semester 3','Semester 4']],
        ['course'=>'B.Tech Ed in Civil',     'semesters'=>['Semester 1','Semester 2','Semester 3','Semester 4']],
        ['course'=>'Diploma in Civil',       'semesters'=>['Semester 1','Semester 2','Semester 3']],
        ['course'=>'Animal Husbandry',       'semesters'=>['Semester 1','Semester 2','Semester 3','Semester 4','Semester 5']],
        ['course'=>'Diploma Electrical',     'semesters'=>['Semester 1','Semester 2','Semester 3']],
    ];

    $addresses = ['Sindhuli','Kathmandu','Pokhara','Chitwan','Butwal','Dharan','Biratnagar','Hetauda','Janakpur','Nepalgunj'];
    $quals = ['SEE','+2 Science','+2 Management','+2 Humanities','+2 Education'];
    $phonePfx = ['984','985','986','980','981','982'];

    $inserted = 0; $skipped = 0;

    // Fix existing "Sem Semester X" duplicates first
    $db->exec("UPDATE students SET semester = REPLACE(semester, 'Sem Semester', 'Semester') WHERE semester LIKE 'Sem Semester%'");

    // Get max existing roll number
    $maxRoll = $db->query("SELECT MAX(CAST(SUBSTRING_INDEX(student_id,'-',-1) AS UNSIGNED)) FROM students")->fetchColumn();
    $rollStart = max(intval($maxRoll) + 1, 101);

    $used = []; // track used usernames/emails/sids

    for ($i = 0; $i < 300; $i++) {
        $fn   = $firstNames[array_rand($firstNames)];
        $ln   = $lastNames[array_rand($lastNames)];
        $full = $fn . ' ' . $ln;
        $prog = $programs[$i % count($programs)]; // distribute evenly
        $sem  = $prog['semesters'][$i % count($prog['semesters'])];
        $roll = $rollStart + $i;
        $sid  = 'STU-2026-' . $roll;
        $slug = strtolower(preg_replace('/[^a-zA-Z]/','',$fn)).'.'.strtolower(preg_replace('/[^a-zA-Z]/','',$ln)).$roll;
        $email= $slug . '@student.scti.edu.np';
        $phone= $phonePfx[array_rand($phonePfx)] . rand(1000000,9999999);
        $addr = $addresses[array_rand($addresses)];
        $qual = $quals[array_rand($quals)];

        // skip if exists
        $chk = $db->prepare("SELECT id FROM students WHERE username=? OR student_id=? OR email=?");
        $chk->execute([$slug, $sid, $email]);
        if ($chk->fetch()) { $skipped++; continue; }

        $stmt = $db->prepare("INSERT INTO students (username,email,password,full_name,student_id,course,semester,phone,address,status) VALUES (?,?,?,?,?,?,?,?,?,'active')");
        $stmt->execute([$slug,$email,$pass,$full,$sid,$prog['course'],$sem,$phone,$addr]);
        $inserted++;
    }

    echo "<div style='font-family:sans-serif;max-width:500px;margin:40px auto;padding:30px;background:#d4edda;border-radius:10px;border:1px solid #c3e6cb'>";
    echo "<h2 style='color:#155724'>✓ Done!</h2>";
    echo "<p>Inserted: <b>$inserted</b> students</p>";
    echo "<p>Skipped (duplicates): <b>$skipped</b></p>";
    echo "<p>Password for all: <code>Student@123</code></p>";
    echo "<br><a href='http://localhost/scti-school/dashboards/teacher-dashboard.php' style='background:#004080;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none'>Go to Dashboard</a>";
    echo "</div>";
} catch(Exception $e) {
    echo "<div style='font-family:sans-serif;color:red;padding:20px'>Error: ".$e->getMessage()."</div>";
}
?>
