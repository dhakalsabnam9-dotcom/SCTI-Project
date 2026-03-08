<?php
session_start();

// Check if user is logged in and is teacher
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    header('Location: ../index.php');
    exit();
}

// Redirect to coming soon page
header('Location: coming-soon-template.php?page=assignments-teacher&icon=file-alt');
exit();
?>
