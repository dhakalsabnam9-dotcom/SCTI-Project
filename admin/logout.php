<?php
// admin/logout.php
// Logs the admin user out and redirects to login.

session_start();
session_unset();
session_destroy();

header('Location: login.php');
exit;

