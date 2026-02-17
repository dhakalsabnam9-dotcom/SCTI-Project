<?php
// admin/view_contact.php
require_once 'auth.php';
require_once '../db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
    // Mark as read
    $stmt = $conn->prepare("UPDATE contacts SET status = 'read' WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    // Fetch contact details
    $stmt = $conn->prepare("SELECT id, name, email, phone, message, created_at FROM contacts WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $contact = $result->fetch_assoc();
    $stmt->close();
} else {
    header('Location: admin_index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Contact - SCTI Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="admin-wrapper">
  <aside class="sidebar">
    <h2>SCTI Admin</h2>
    <ul>
      <li><a href="admin_index.php">Dashboard</a></li>
      <li><a href="notice_form.php">Create Notice</a></li>
      <li><a href="event_form.php">Create Event</a></li>
      <li><a href="gallery_form.php">Add Gallery Image</a></li>
    </ul>
  </aside>

  <main class="main-content">
    <div class="top-bar">
      <h1>Contact Submission Details</h1>
      <a href="admin_index.php" class="button secondary">Back to Dashboard</a>
    </div>

    <div class="form-wrapper">
      <?php if ($contact): ?>
        <div class="form-group">
          <label>Name:</label>
          <p style="padding: 10px; background: #f5f5f5; border-radius: 4px;"><?php echo htmlspecialchars($contact['name']); ?></p>
        </div>

        <div class="form-group">
          <label>Email:</label>
          <p style="padding: 10px; background: #f5f5f5; border-radius: 4px;"><?php echo htmlspecialchars($contact['email']); ?></p>
        </div>

        <div class="form-group">
          <label>Phone:</label>
          <p style="padding: 10px; background: #f5f5f5; border-radius: 4px;"><?php echo htmlspecialchars($contact['phone']); ?></p>
        </div>

        <div class="form-group">
          <label>Message:</label>
          <p style="padding: 10px; background: #f5f5f5; border-radius: 4px; white-space: pre-wrap;"><?php echo htmlspecialchars($contact['message']); ?></p>
        </div>

        <div class="form-group">
          <label>Submitted At:</label>
          <p style="padding: 10px; background: #f5f5f5; border-radius: 4px;">
            <?php 
              $date = date('d M Y, h:i A', strtotime($contact['created_at']));
              echo htmlspecialchars($date);
            ?>
          </p>
        </div>

        <div class="form-actions">
          <a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>" class="button">Reply via Email</a>
          <a href="delete_contact.php?id=<?php echo $contact['id']; ?>" 
             class="button danger"
             onclick="return confirm('Delete this contact submission?');">
            Delete
          </a>
        </div>
      <?php else: ?>
        <p>Contact submission not found.</p>
      <?php endif; ?>
    </div>
  </main>
</div>
</body>
</html>
