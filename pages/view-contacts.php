<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login-simple.php");
    exit();
}

// Database configuration
require_once('../includes/config.php');

// Get database connection
$conn = getDBConnection();

// Fetch all contact messages
try {
    $stmt = $conn->prepare("
        SELECT * FROM contacts 
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching contacts: " . $e->getMessage();
}

$conn = null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Messages - SCTI Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

  <!-- External CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
  
  <style>
    .admin-header {
      background: linear-gradient(135deg, #004080, #0059b3);
      color: white;
      padding: 20px 0;
      margin-bottom: 30px;
    }
    
    .admin-header .container {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .admin-header h1 {
      margin: 0;
      font-size: 24px;
    }
    
    .admin-header a {
      color: white;
      text-decoration: none;
      padding: 8px 16px;
      background: rgba(255,255,255,0.2);
      border-radius: 5px;
      transition: all 0.3s;
    }
    
    .admin-header a:hover {
      background: rgba(255,255,255,0.3);
    }
    
    .stats-card {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 30px;
      display: flex;
      gap: 30px;
    }
    
    .stat-item {
      flex: 1;
      text-align: center;
      padding: 15px;
      border-radius: 8px;
    }
    
    .stat-item.new {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white;
    }
    
    .stat-item.read {
      background: linear-gradient(135deg, #17a2b8, #138496);
      color: white;
    }
    
    .stat-item.total {
      background: linear-gradient(135deg, #004080, #0059b3);
      color: white;
    }
    
    .stat-item h3 {
      font-size: 36px;
      margin: 0 0 10px 0;
    }
    
    .stat-item p {
      margin: 0;
      font-size: 14px;
      opacity: 0.9;
    }
    
    .contact-card {
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 20px;
      border-left: 5px solid #004080;
      transition: all 0.3s;
    }
    
    .contact-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    
    .contact-card.new {
      border-left-color: #28a745;
      background: linear-gradient(to right, #f0fff4, white);
    }
    
    .contact-header {
      display: flex;
      justify-content: space-between;
      align-items: start;
      margin-bottom: 15px;
    }
    
    .contact-info h3 {
      margin: 0 0 5px 0;
      color: #004080;
      font-size: 20px;
    }
    
    .contact-meta {
      font-size: 13px;
      color: #666;
    }
    
    .contact-meta i {
      margin-right: 5px;
      color: #004080;
    }
    
    .contact-meta span {
      margin-right: 15px;
    }
    
    .status-badge {
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
    }
    
    .status-badge.new {
      background: #28a745;
      color: white;
    }
    
    .status-badge.read {
      background: #17a2b8;
      color: white;
    }
    
    .status-badge.replied {
      background: #ffc107;
      color: #333;
    }
    
    .contact-subject {
      font-weight: 600;
      color: #333;
      margin-bottom: 10px;
      font-size: 16px;
    }
    
    .contact-message {
      color: #555;
      line-height: 1.6;
      padding: 15px;
      background: #f8f9fa;
      border-radius: 8px;
      margin-bottom: 15px;
    }
    
    .contact-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 15px;
      border-top: 1px solid #e0e0e0;
      font-size: 13px;
      color: #666;
    }
    
    .contact-date {
      display: flex;
      align-items: center;
      gap: 5px;
    }
    
    .no-contacts {
      text-align: center;
      padding: 60px 20px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .no-contacts i {
      font-size: 60px;
      color: #ccc;
      margin-bottom: 20px;
    }
    
    .no-contacts h3 {
      color: #666;
      margin: 0;
    }
  </style>
</head>

<body>

<!-- Admin Header -->
<div class="admin-header">
  <div class="container">
    <h1><i class="fa fa-envelope"></i> Contact Messages</h1>
    <div>
      <a href="../dashboards/admin-dashboard.php"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
      <a href="logout.php" style="margin-left: 10px; background: #dc3545;"><i class="fa fa-sign-out"></i> Logout</a>
    </div>
  </div>
</div>

<!-- Main Content -->
<section class="section">
  <div class="container">
    
    <?php if (isset($error)): ?>
      <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>
    
    <?php
    // Calculate statistics
    $totalContacts = count($contacts);
    $newContacts = count(array_filter($contacts, function($c) { return $c['status'] === 'new'; }));
    $readContacts = count(array_filter($contacts, function($c) { return $c['status'] === 'read'; }));
    ?>
    
    <!-- Statistics -->
    <div class="stats-card">
      <div class="stat-item new">
        <h3><?php echo $newContacts; ?></h3>
        <p><i class="fa fa-envelope"></i> New Messages</p>
      </div>
      <div class="stat-item read">
        <h3><?php echo $readContacts; ?></h3>
        <p><i class="fa fa-envelope-open"></i> Read Messages</p>
      </div>
      <div class="stat-item total">
        <h3><?php echo $totalContacts; ?></h3>
        <p><i class="fa fa-inbox"></i> Total Messages</p>
      </div>
    </div>
    
    <!-- Contact Messages -->
    <?php if (empty($contacts)): ?>
      <div class="no-contacts">
        <i class="fa fa-inbox"></i>
        <h3>No contact messages yet</h3>
        <p>Contact messages will appear here when users submit the contact form.</p>
      </div>
    <?php else: ?>
      <?php foreach ($contacts as $contact): ?>
        <div class="contact-card <?php echo $contact['status']; ?>">
          <div class="contact-header">
            <div class="contact-info">
              <h3><?php echo htmlspecialchars($contact['name']); ?></h3>
              <div class="contact-meta">
                <span><i class="fa fa-envelope"></i><?php echo htmlspecialchars($contact['email']); ?></span>
                <span><i class="fa fa-phone"></i><?php echo htmlspecialchars($contact['phone']); ?></span>
              </div>
            </div>
            <span class="status-badge <?php echo $contact['status']; ?>">
              <?php echo $contact['status']; ?>
            </span>
          </div>
          
          <div class="contact-subject">
            <i class="fa fa-tag"></i> <?php echo htmlspecialchars($contact['subject']); ?>
          </div>
          
          <div class="contact-message">
            <?php echo nl2br(htmlspecialchars($contact['message'])); ?>
          </div>
          
          <div class="contact-footer">
            <div class="contact-date">
              <i class="fa fa-clock"></i>
              <?php echo date('F j, Y - g:i A', strtotime($contact['created_at'])); ?>
            </div>
            <div>
              <i class="fa fa-map-marker-alt"></i> IP: <?php echo htmlspecialchars($contact['ip_address']); ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
    
  </div>
</section>

<!-- Footer -->
<footer class="footer">
  <p>© 2025 Sindhuli Community Technical Institute (SCTI) - Admin Panel</p>
</footer>

</body>
</html>
