<?php
// admin/event_form.php
require_once 'auth.php';
require_once '../db.php';

// Create events table if it doesn't exist
$createTable = "CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    event_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($createTable);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$title = '';
$description = '';
$event_date = '';
$message = '';

if ($id > 0) {
    // Load existing event for edit
    $stmt = $conn->prepare("SELECT title, description, event_date FROM events WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($title, $description, $event_date);
    if ($stmt->fetch()) {
        // convert to date format
        $event_date = date('Y-m-d', strtotime($event_date));
    } else {
        $message = 'Event not found.';
    }
    $stmt->close();
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $event_date = $_POST['event_date'] ?? '';

    if ($title === '' || $description === '' || $event_date === '') {
        $message = 'All fields are required.';
    } else {
        if ($id > 0) {
            // Update
            $stmt = $conn->prepare("UPDATE events SET title = ?, description = ?, event_date = ? WHERE id = ?");
            $stmt->bind_param('sssi', $title, $description, $event_date, $id);
            if ($stmt->execute()) {
                header('Location: admin_index.php');
                exit;
            } else {
                $message = 'Error updating event.';
            }
            $stmt->close();
        } else {
            // Create
            $stmt = $conn->prepare("INSERT INTO events (title, description, event_date) VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $title, $description, $event_date);
            if ($stmt->execute()) {
                header('Location: admin_index.php');
                exit;
            } else {
                $message = 'Error creating event.';
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $id > 0 ? 'Edit Event' : 'Create Event'; ?></title>
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
      <h1><?php echo $id > 0 ? 'Edit Event' : 'Create Event'; ?></h1>
      <a href="admin_index.php" class="button secondary">Back to Dashboard</a>
    </div>

    <div class="form-wrapper">
      <?php if ($message !== ''): ?>
        <div class="message error"><?php echo htmlspecialchars($message); ?></div>
      <?php endif; ?>

      <form method="post">
        <div class="form-group">
          <label for="title">Title *</label>
          <input type="text" name="title" id="title"
                 value="<?php echo htmlspecialchars($title); ?>" required>
        </div>

        <div class="form-group">
          <label for="description">Description *</label>
          <textarea name="description" id="description" rows="5" required><?php echo htmlspecialchars($description); ?></textarea>
        </div>

        <div class="form-group">
          <label for="event_date">Event Date *</label>
          <input type="date" name="event_date" id="event_date"
                 value="<?php echo htmlspecialchars($event_date); ?>" required>
        </div>

        <div class="form-actions">
          <button type="submit" class="button">
            <?php echo $id > 0 ? 'Update Event' : 'Create Event'; ?>
          </button>
        </div>
      </form>
    </div>
  </main>
</div>
</body>
</html>
