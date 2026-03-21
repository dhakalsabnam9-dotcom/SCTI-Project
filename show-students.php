<?php
$db = new PDO('mysql:host=localhost;dbname=scti_school', 'root', '');
$rows = $db->query('SELECT id, full_name, username, status FROM students ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
if (empty($rows)) {
    echo "No students found.\n";
} else {
    echo str_pad('ID',4) . ' | ' . str_pad('Full Name',25) . ' | ' . str_pad('Username',20) . ' | Status' . "\n";
    echo str_repeat('-', 70) . "\n";
    foreach ($rows as $r) {
        echo str_pad($r['id'],4) . ' | ' . str_pad($r['full_name'],25) . ' | ' . str_pad($r['username'],20) . ' | ' . $r['status'] . "\n";
    }
}
