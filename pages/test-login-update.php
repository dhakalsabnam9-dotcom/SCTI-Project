<?php
// TEMP DEBUG FILE - DELETE AFTER TESTING
require_once('../includes/config.php');
$conn = getDBConnection();

$table = 'admins'; // change to students or teachers to test those
$id = 1;           // change to the actual user id you're testing

$upd = $conn->prepare("UPDATE $table SET last_login=NOW(), updated_at=NOW() WHERE id=?");
$result = $upd->execute([$id]);
$rows = $upd->rowCount();

echo json_encode([
    'result'       => $result,
    'rows_affected'=> $rows,
    'error'        => $upd->errorInfo(),
    'last_login_now' => date('Y-m-d H:i:s')
]);
