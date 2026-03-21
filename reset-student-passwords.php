<?php
$db = new PDO('mysql:host=localhost;dbname=scti_school', 'root', '');
$newPassword = 'student123';
$hashed = password_hash($newPassword, PASSWORD_DEFAULT);

$rows = $db->query('SELECT id, full_name, username FROM students ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $r) {
    $stmt = $db->prepare("UPDATE students SET password=? WHERE id=?");
    $stmt->execute([$hashed, $r['id']]);
    echo "Reset: " . $r['full_name'] . " | Username: " . $r['username'] . " | Password: " . $newPassword . "\n";
}
echo "\nDone. All students can now login with password: " . $newPassword . "\n";
