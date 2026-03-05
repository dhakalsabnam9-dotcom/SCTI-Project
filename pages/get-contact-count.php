<?php
header('Content-Type: application/json');

// Database configuration
require_once('../includes/config.php');

// Get database connection
$conn = getDBConnection();

// Initialize response
$response = [
    'success' => false,
    'count' => 0
];

try {
    // Count total contact messages
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM contact_messages");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $response['success'] = true;
    $response['count'] = $result['total'];
    
} catch(PDOException $e) {
    $response['error'] = $e->getMessage();
}

$conn = null;
echo json_encode($response);
?>
