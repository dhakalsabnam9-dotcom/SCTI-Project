<?php
// admin/delete_contact.php
require_once 'auth.php';
require_once '../db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM contacts WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}

header('Location: admin_index.php');
exit;
?>
