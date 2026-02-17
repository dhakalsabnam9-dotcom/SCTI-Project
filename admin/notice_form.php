<?php
// admin/notice_form.php
require_once 'auth.php';
require_once '../db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$title = '';
$content = '';
$published_at = '';
$message = '';

if ($id > 0) {
    // Load existing notice for edit
    $stmt = $conn->prepare("SELECT title, content, published_at FROM notices WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($title, $content, $published_at);
    if ($stmt->fetch()) {
        // convert to datetime-local format
        $published_at = date('Y-m-d\TH:i', strtotime($published_at));
    } else {
        $message = 'Notice not found.';
    }
    $stmt->close();
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $published_at = $_POST['published_at'] ?? '';

    if ($title === '' || $content === '' || $published_at === '') {
        $message = 'All fields are required.';
    } else {
        $published_at_sql = date('Y-m-d H:i:s', strtotime($published_at));

        if ($id > 0) {
            // Update
            $stmt = $conn->prepare("UPDATE notices SET title = ?, content = ?, published_at = ? WHERE id = ?");
            $stmt->bind_param('sssi', $title, $content, $published_at_sql, $id);
            if ($stmt->execute()) {
                header('Location: admin_index.php');
                exit;
            } else {
                $message = 'Error updating notice.';
            }
            $stmt->close();
        } else {
            // Insert
            $stmt = $conn->prepare("INSERT INTO notices (title, content, published_at) VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $title, $content, $published_at_sql);
            if ($stmt->execute()) {
                header('Location: admin_index.php');
                exit;
            } else {
                $message = 'Error creating notice.';
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
  <title><?php echo $id > 0 ? 'Edit Notice' : 'Create Notice'; ?></title>
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
      <h1><?php echo $id > 0 ? 'Edit Notice' : 'Create Notice'; ?></h1>
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
          <label for="published_at">Published At *</label>
          <input type="datetime-local" name="published_at" id="published_at"
                 value="<?php echo htmlspecialchars($published_at); ?>" required>
        </div>

        <div class="form-group">
          <label for="content">Content *</label>
          <textarea name="content" id="content" required><?php echo htmlspecialchars($content); ?></textarea>
        </div>

        <div class="form-actions">
          <button type="submit" class="button">
            <?php echo $id > 0 ? 'Update Notice' : 'Create Notice'; ?>
          </button>
        </div>
      </form>
    </div>
  </main>
</div>
</body>
</html>
