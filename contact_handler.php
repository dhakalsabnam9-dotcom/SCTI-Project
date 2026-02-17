<?php
// contact_handler.php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get form data
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['number'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validation
if (empty($name) || empty($email) || empty($phone) || empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

// Create contacts table if it doesn't exist
$createTable = "CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'new'
)";

$conn->query($createTable);

// Insert contact submission
$stmt = $conn->prepare("INSERT INTO contacts (name, email, phone, message) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ssss', $name, $email, $phone, $message);

if ($stmt->execute()) {
    // Array of random success messages
    $successMessages = [
        'Thank you for contacting us! We will get back to you soon.',
        'Your message has been sent successfully! We appreciate your interest.',
        'Message received! Our team will respond to you shortly.',
        'Thank you for reaching out! We\'ll get back to you as soon as possible.',
        'Your inquiry has been submitted successfully! We\'ll contact you soon.',
        'Message sent! Thank you for contacting SCTI. We\'ll respond promptly.',
        'Thank you for your message! Our team will review and respond shortly.',
        'Your contact form has been submitted successfully! We appreciate your patience.',
        'Message delivered! We\'ll get back to you at the earliest opportunity.',
        'Thank you for contacting SCTI! Your message has been received and we\'ll respond soon.'
    ];
    
    // Select a random message
    $randomMessage = $successMessages[array_rand($successMessages)];
    
    echo json_encode(['success' => true, 'message' => $randomMessage]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to submit your message. Please try again.']);
}

$stmt->close();
$conn->close();
?>
