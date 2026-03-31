<?php
require_once 'includes/config.php';
try {
    $db = getDBConnection();
    $pass = password_hash('Student@123', PASSWORD_DEFAULT);

    $first = ['Ram','Sita','Hari','Gita','Bikash','Sunita','Nabin','Puja','Anil','Maya','Rajan','Kamala','Saroj','Amrit','Binod','Sabina','Dipak','Anita','Suresh','Rekha','Prakash','Nirmala','Santosh','Laxmi','Bishnu','Sarita','Kamal','Rita','Mohan','Sushila','Arjun','Mina','Rajesh','Kopila','Dinesh','Sangita','Umesh','Kabita','Ganesh','Sundar','Manisha','Kiran','Sandeep','Roshan','Priya','Deepak','Anjali','Sanjay','Pooja','Niraj','Bibek','Shristi','Prabin','Samjhana','Nirajan','Barsha','Sujan','Kritika','Pawan','Smriti','Ashok','Babita','Chandra','Devi','Eshan','Gopal','Hira','Indra','Jyoti','Keshav','Lalita','Mahesh','Namrata','Prabha','Rabindra','Sabita','Tilak','Uma','Vijay','Yam','Abhi','Sabnam','Tejash','Sandesh','Alisha','Aashika','Aayusha','Aarav','Aditi','Aditya','Ajita','Akasha','Roshan','Suman','Nisha','Rupa','Bimal','Sanjiv','Pratima','Naresh','Manju','Tulsi'];

    $last = ['Thapa','Sharma','Poudel','Rai','Karki','Basnet','Adhikari','Tamang','Gurung','Shrestha','Bhandari','Pandey','Magar','Limbu','Lama','Khadka','Bhattarai','Chaudhary','Yadav','Mahato','Koirala','Regmi','Dahal','Acharya','Subedi','Ghimire','Oli','Parajuli','Joshi','KC','Malla','Rana','Sah','Tiwari','Upreti','Dhakal','Giri','Hamal','Jha','Kafle','Luitel','Niroula','Ojha','Pradhan','Rijal','Sapkota','Upadhyay','Wagle','Yonjan','Bajracharya'];

    $programs = [
        ['course'=>'B.Tech Ed in IT',      'sems'=>['Semester 1','Semester 2','Semester 3','Semester 4']],
        ['course'=>'B.Tech Ed in Civil',   'sems'=>['Semester 1','Semester 2','Semester 3','Semester 4']],
        ['course'=>'Diploma in Civil',     'sems'=>['Semester 1','Semester 2','Semester 3']],
        ['course'=>'Animal Husbandry',     'sems'=>['Semester 1','Semester 2','Semester 3','Semester 4','Semester 5']],
        ['course'=>'Diploma Electrical',   'sems'=>['Semester 1','Semester 2','Semester 3']],
    ];

    $addrs = ['Sindhuli','Kathmandu','Pokhara','Chitwan','Butwal','Dharan','Biratnagar','Hetauda','Janakpur','Nepalgunj','Birgunj','Itahari','Dhangadhi','Tulsipur','Ghorahi'];
    $quals = ['SEE','+2 Science','+2 Management','+2 Humanities','+2 Education'];
    $pfx   = ['984','985','986','980','981','982'];

    // Fix existing "Sem Semester" duplicates
    $db->exec("UPDATE students SET semester=REPLACE(semester,'Sem Semester','Semester') WHERE semester LIKE 'Sem Semester%'");

    $maxRoll = $db->query("SELECT MAX(CAST(SUBSTRING_INDEX(student_id,'-',-1) AS UNSIGNED)) FROM students")->fetchColumn();
    $roll = max(intval($maxRoll)+1, 1001);

    $ins = 0; $skip = 0;

    for ($i = 0; $i < 500; $i++) {
        $fn   = $first[array_rand($first)];
        $ln   = $last[array_rand($last)];
        $full = $fn.' '.$ln;
        $prog = $programs[$i % count($programs)];
        $sem  = $prog['sems'][$i % count($prog['sems'])];
        $sid  = 'STU-2026-'.($roll+$i);
        $slug = strtolower(preg_replace('/[^a-z]/i','',iconv('UTF-8','ASCII//TRANSLIT',$fn))).'.'.strtolower(preg_replace('/[^a-z]/i','',iconv('UTF-8','ASCII//TRANSLIT',$ln))).($roll+$i);
        $email= $slug.'@student.scti.edu.np';
        $phone= $pfx[array_rand($pfx)].rand(1000000,9999999);
        $addr = $addrs[array_rand($addrs)];
        $qual = $quals[array_rand($quals)];

        $chk = $db->prepare("SELECT id FROM students WHERE username=? OR student_id=? OR email=?");
        $chk->execute([$slug,$sid,$email]);
        if ($chk->fetch()) { $skip++; continue; }

        $db->prepare("INSERT INTO students (username,email,password,full_name,student_id,course,semester,phone,address,status) VALUES (?,?,?,?,?,?,?,?,?,'active')")
           ->execute([$slug,$email,$pass,$full,$sid,$prog['course'],$sem,$phone,$addr]);
        $ins++;
    }

    echo "<div style='font-family:sans-serif;max-width:500px;margin:40px auto;padding:30px;background:#d4edda;border-radius:10px;border:1px solid #c3e6cb'>";
    echo "<h2 style='color:#155724'>✓ Done!</h2>";
    echo "<p>Inserted: <b>$ins</b> | Skipped: <b>$skip</b></p>";
    echo "<p>Password: <code>Student@123</code></p>";
    echo "<a href='http://localhost/scti-school/dashboards/teacher-dashboard.php' style='background:#004080;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;display:inline-block;margin-top:10px'>Go to Dashboard</a>";
    echo "</div>";
} catch(Exception $e) {
    echo "<div style='color:red;font-family:sans-serif;padding:20px'>Error: ".$e->getMessage()."</div>";
}
?>
