<?php
$db = new PDO('mysql:host=localhost;dbname=scti_school', 'root', '');
$hashed = password_hash('teacher123', PASSWORD_DEFAULT);
$rows = $db->query("SELECT id, full_name, username FROM teachers ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    $db->prepare("UPDATE teachers SET password=? WHERE id=?")->execute([$hashed, $r['id']]);
    echo "Reset: " . $r['full_name'] . " | Username: " . $r['username'] . " | Password: teacher123\n";
}
echo "\nDone.\n";
