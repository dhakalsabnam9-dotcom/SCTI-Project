<?php
// admin/gallery_form.php
require_once 'auth.php';
require_once '../db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$title = '';
$current_filename = '';
$message = '';

if ($id > 0) {
    // Load existing gallery item
    $stmt = $conn->prepare("SELECT title, filename FROM gallery WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($title, $current_filename);
    if (!$stmt->fetch()) {
        $message = 'Gallery item not found.';
    }
    $stmt->close();
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $uploadDir = __DIR__ . '/../uploads/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if ($title === '') {
        $message = 'Title is required.';
    } else {
        $newFilename = $current_filename;

        // If a new file is uploaded
        if (!empty($_FILES['image']['name'])) {
            $fileTmp  = $_FILES['image']['tmp_name'];
            $fileName = basename($_FILES['image']['name']);
            $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowed  = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($fileExt, $allowed)) {
                $message = 'Only image files (jpg, jpeg, png, gif, webp) are allowed.';
            } else {
                // Generate unique file name
                $newFilename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $fileName);
                $targetPath  = $uploadDir . $newFilename;

                if (!move_uploaded_file($fileTmp, $targetPath)) {
                    $message = 'Failed to upload image.';
                } else {
                    // Optionally delete old file when editing
                    if ($id > 0 && $current_filename && file_exists($uploadDir . $current_filename)) {
                        @unlink($uploadDir . $current_filename);
                    }
                }
            }
        }

        if ($message === '') {
            if ($id > 0) {
                // Update
                $stmt = $conn->prepare("UPDATE gallery SET title = ?, filename = ? WHERE id = ?");
                $stmt->bind_param('ssi', $title, $newFilename, $id);
                if ($stmt->execute()) {
                    header('Location: admin_index.php');
                    exit;
                } else {
                    $message = 'Error updating gallery item.';
                }
                $stmt->close();
            } else {
                // Create
                if ($newFilename === '') {
                    $message = 'Please upload an image.';
                } else {
                    $stmt = $conn->prepare("INSERT INTO gallery (title, filename) VALUES (?, ?)");
                    $stmt->bind_param('ss', $title, $newFilename);
                    if ($stmt->execute()) {
                        header('Location: admin_index.php');
                        exit;
                    } else {
                        $message = 'Error creating gallery item.';
                    }
                    $stmt->close();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $id > 0 ? 'Edit Gallery Image' : 'Add Gallery Image'; ?></title>
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
      <h1><?php echo $id > 0 ? 'Edit Gallery Image' : 'Add Gallery Image'; ?></h1>
      <a href="admin_index.php" class="button secondary">Back to Dashboard</a>
    </div>

    <div class="form-wrapper">
      <?php if ($message !== ''): ?>
        <div class="message error"><?php echo htmlspecialchars($message); ?></div>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data">
        <div class="form-group">
          <label for="title">Title *</label>
          <input type="text" name="title" id="title"
                 value="<?php echo htmlspecialchars($title); ?>" required>
        </div>

        <div class="form-group">
          <label for="image">
            <?php echo $id > 0 ? 'Replace Image (optional)' : 'Image *'; ?>
          </label>
          <input type="file" name="image" id="image" <?php echo $id > 0 ? '' : 'required'; ?>>
          <?php if ($id > 0 && $current_filename): ?>
            <p>Current image:</p>
            <img src="../uploads/<?php echo htmlspecialchars($current_filename); ?>"
                 alt="<?php echo htmlspecialchars($title); ?>"
                 style="width:120px; height:90px; object-fit:cover; margin-top:5px;">
          <?php endif; ?>
        </div>

        <div class="form-actions">
          <button type="submit" class="button">
            <?php echo $id > 0 ? 'Update Image' : 'Add Image'; ?>
          </button>
        </div>
      </form>
    </div>
  </main>
</div>
</body>
</html>
