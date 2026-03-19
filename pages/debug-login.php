<?php
// TEMP DEBUG - DELETE AFTER USE
require_once('../includes/config.php');
$conn = getDBConnection();

$tables = ['students', 'teachers', 'admins'];
foreach ($tables as $t) {
    echo "<h3>$t</h3><pre>";
    $cols = $conn->query("DESCRIBE `$t`")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        echo $c['Field'] . " | " . $c['Type'] . " | Key:" . $c['Key'] . " | Default:" . $c['Default'] . "\n";
    }
    echo "</pre>";

    // Try direct update on first row
    $first = $conn->query("SELECT id FROM `$t` LIMIT 1")->fetch();
    if ($first) {
        $id = (int)$first['id'];
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $ok = $mysqli->query("UPDATE `$t` SET last_login=NOW(), updated_at=NOW() WHERE id=$id");
        echo "Update id=$id result=" . ($ok ? 'OK' : 'FAIL') . " affected=" . $mysqli->affected_rows . " error=" . $mysqli->error . "<br>";
        $mysqli->close();
    } else {
        echo "No rows in $t<br>";
    }
}
