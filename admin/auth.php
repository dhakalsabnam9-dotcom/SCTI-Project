<?php
// admin/auth.php
// Simple session-based authentication guard for admin pages.

session_start();

// If not logged in, redirect to login page
if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

