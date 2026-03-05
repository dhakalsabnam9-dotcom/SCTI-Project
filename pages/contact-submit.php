<?php
header('Content-Type: application/json');

// Database configuration
require_once('../includes/config.php');

// Get database connection
$conn = getDBConnection();

// Initialize response
$response = [
    'success' => false,
    'message' => ''
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($phone) || empty($subject) || empty($message)) {
        $response['message'] = "All fields are required!";
        echo json_encode($response);
        exit();
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = "Invalid email format!";
        echo json_encode($response);
        exit();
    }
    
    // Validate phone number (basic validation)
    if (!preg_match('/^[0-9+\-\s()]+$/', $phone)) {
        $response['message'] = "Invalid phone number format!";
        echo json_encode($response);
        exit();
    }
    
    try {
        // Get IP address and user agent
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        // Insert contact message into database
        $stmt = $conn->prepare("
            INSERT INTO contact_messages 
            (name, email, phone, subject, message, status, ip_address, user_agent) 
            VALUES 
            (:name, :email, :phone, :subject, :message, 'new', :ip_address, :user_agent)
        ");
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':ip_address', $ipAddress);
        $stmt->bindParam(':user_agent', $userAgent);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Thank you for contacting us! We will get back to you soon.";
        } else {
            $response['message'] = "Failed to submit your message. Please try again.";
        }
        
    } catch(PDOException $e) {
        $response['message'] = "Database error: " . $e->getMessage();
    }
    
} else {
    $response['message'] = "Invalid request method!";
}

$conn = null;
echo json_encode($response);
?>
