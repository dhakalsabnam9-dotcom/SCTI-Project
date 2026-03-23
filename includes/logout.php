<?php
session_start();
require_once '../includes/config.php';

// Clear remember me cookie and DB token
if (!empty($_COOKIE['scti_remember'])) {
    setcookie('scti_remember', '', time() - 3600, '/');
    if (isset($_SESSION['user_id'], $_SESSION['user_type'])) {
        try {
            $db = getDBConnection();
            $tbl = $_SESSION['user_type'] === 'teacher' ? 'teachers' : 'students';
            $db->prepare("UPDATE `$tbl` SET remember_token=NULL WHERE id=?")->execute([$_SESSION['user_id']]);
        } catch(Exception $e) { /* ignore */ }
    }
}

session_unset();
session_destroy();
header("Location: ../index.php");
exit();
