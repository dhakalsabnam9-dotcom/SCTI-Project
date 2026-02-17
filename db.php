<?php
// db.php
// Shared database connection for frontend and admin panel.

$DB_HOST = 'localhost'; // adjust if MySQL runs elsewhere
$DB_USER = 'root';      // your MySQL username
$DB_PASS = '';          // your MySQL password
$DB_NAME = 'scti_db';   // database name

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Ensure UTF-8 encoding
$conn->set_charset('utf8mb4');
