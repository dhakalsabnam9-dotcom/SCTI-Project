<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success'=>false,'message'=>'Unauthorized']);
    exit();
}
header('Content-Type: application/json');
require_once '../includes/config.php';

try {
    $db = getDBConnection();

    // DB version
    $version = $db->query("SELECT VERSION()")->fetchColumn();

    // Table count
    $tables = $db->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()")->fetchColumn();

    // Row counts
    $students    = $db->query("SELECT COUNT(*) FROM students")->fetchColumn();
    $teachers    = $db->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
    $materials   = $db->query("SELECT COUNT(*) FROM materials")->fetchColumn();
    $assignments = $db->query("SELECT COUNT(*) FROM assignments")->fetchColumn();

    echo json_encode([
        'success'     => true,
        'version'     => 'MySQL ' . $version,
        'tables'      => intval($tables),
        'students'    => intval($students),
        'teachers'    => intval($teachers),
        'materials'   => intval($materials),
        'assignments' => intval($assignments),
    ]);
} catch (Exception $e) {
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
