<?php
// admin/admin_index.php
require_once 'auth.php';
require_once '../db.php';

// Fetch notices
$noticeSql = "SELECT id, title, content, published_at 
              FROM notices 
              ORDER BY published_at DESC, created_at DESC";
$noticeResult = $conn->query($noticeSql);

// Fetch gallery
$gallerySql = "SELECT id, title, filename, created_at 
               FROM gallery 
               ORDER BY created_at DESC";
$galleryResult = $conn->query($gallerySql);

// Fetch contact submissions
$contactSql = "SELECT id, name, email, phone, message, created_at, status 
               FROM contacts 
               ORDER BY created_at DESC 
               LIMIT 50";
$contactResult = $conn->query($contactSql);

// Fetch events
$eventSql = "SELECT id, title, event_date, description, created_at 
             FROM events 
             ORDER BY event_date DESC, created_at DESC";
$eventResult = $conn->query($eventSql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SCTI Admin Panel</title>
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
      <h1>Dashboard</h1>
      <div>
        <a href="notice_form.php" class="button">+ New Notice</a>
        <a href="event_form.php" class="button secondary">+ New Event</a>
        <a href="gallery_form.php" class="button secondary">+ New Gallery Image</a>
        <a href="logout.php" class="button danger">Logout</a>
      </div>
    </div>

    <!-- Notices Table -->
    <div class="table-wrapper">
      <h2>Notices</h2>
      <table>
        <thead>
          <tr>
            <th>S.N</th>
            <th>Title</th>
            <th>Published At</th>
            <th>Content</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($noticeResult && $noticeResult->num_rows > 0): ?>
          <?php $i = 1; ?>
          <?php while ($row = $noticeResult->fetch_assoc()): ?>
            <tr>
              <td><?php echo $i++; ?></td>
              <td><?php echo htmlspecialchars($row['title']); ?></td>
              <td>
                <?php 
                  $date = date('d M Y, h:i A', strtotime($row['published_at']));
                  echo htmlspecialchars($date);
                ?>
              </td>
              <td><?php echo nl2br(htmlspecialchars(mb_strimwidth($row['content'], 0, 120, '...'))); ?></td>
              <td class="actions">
                <a href="notice_form.php?id=<?php echo $row['id']; ?>" class="button small secondary">Edit</a>
                <a href="delete_notice.php?id=<?php echo $row['id']; ?>"
                   class="button small danger"
                   onclick="return confirm('Delete this notice?');">
                  Delete
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="5">No notices found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Gallery Table -->
    <div class="table-wrapper">
      <h2>Gallery Images</h2>
      <table>
        <thead>
          <tr>
            <th>S.N</th>
            <th>Preview</th>
            <th>Title</th>
            <th>File</th>
            <th>Uploaded At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($galleryResult && $galleryResult->num_rows > 0): ?>
          <?php $j = 1; ?>
          <?php while ($row = $galleryResult->fetch_assoc()): ?>
            <tr>
              <td><?php echo $j++; ?></td>
              <td>
                <img src="../uploads/<?php echo htmlspecialchars($row['filename']); ?>"
                     alt="<?php echo htmlspecialchars($row['title']); ?>"
                     style="width:80px; height:60px; object-fit:cover;">
              </td>
              <td><?php echo htmlspecialchars($row['title']); ?></td>
              <td><?php echo htmlspecialchars($row['filename']); ?></td>
              <td><?php echo htmlspecialchars($row['created_at']); ?></td>
              <td class="actions">
                <a href="gallery_form.php?id=<?php echo $row['id']; ?>" class="button small secondary">Edit</a>
                <a href="delete_gallery.php?id=<?php echo $row['id']; ?>"
                   class="button small danger"
                   onclick="return confirm('Delete this gallery image?');">
                  Delete
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="6">No gallery images found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Contact Submissions Table -->
    <div class="table-wrapper">
      <h2>Contact Submissions</h2>
      <table>
        <thead>
          <tr>
            <th>S.N</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Message</th>
            <th>Submitted At</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($contactResult && $contactResult->num_rows > 0): ?>
          <?php $k = 1; ?>
          <?php while ($row = $contactResult->fetch_assoc()): ?>
            <tr>
              <td><?php echo $k++; ?></td>
              <td><?php echo htmlspecialchars($row['name']); ?></td>
              <td><?php echo htmlspecialchars($row['email']); ?></td>
              <td><?php echo htmlspecialchars($row['phone']); ?></td>
              <td><?php echo nl2br(htmlspecialchars(mb_strimwidth($row['message'], 0, 100, '...'))); ?></td>
              <td>
                <?php 
                  $date = date('d M Y, h:i A', strtotime($row['created_at']));
                  echo htmlspecialchars($date);
                ?>
              </td>
              <td>
                <span style="padding: 4px 8px; border-radius: 3px; font-size: 12px; 
                  background: <?php echo $row['status'] === 'read' ? '#d4edda' : '#fff3cd'; ?>; 
                  color: <?php echo $row['status'] === 'read' ? '#155724' : '#856404'; ?>;">
                  <?php echo ucfirst($row['status']); ?>
                </span>
              </td>
              <td class="actions">
                <a href="view_contact.php?id=<?php echo $row['id']; ?>" class="button small secondary">View</a>
                <a href="delete_contact.php?id=<?php echo $row['id']; ?>"
                   class="button small danger"
                   onclick="return confirm('Delete this contact submission?');">
                  Delete
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="8">No contact submissions found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Events Table -->
    <div class="table-wrapper">
      <h2>Events</h2>
      <table>
        <thead>
          <tr>
            <th>S.N</th>
            <th>Title</th>
            <th>Event Date</th>
            <th>Description</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($eventResult && $eventResult->num_rows > 0): ?>
          <?php $l = 1; ?>
          <?php while ($row = $eventResult->fetch_assoc()): ?>
            <tr>
              <td><?php echo $l++; ?></td>
              <td><?php echo htmlspecialchars($row['title']); ?></td>
              <td>
                <?php 
                  $date = date('d M Y', strtotime($row['event_date']));
                  echo htmlspecialchars($date);
                ?>
              </td>
              <td><?php echo nl2br(htmlspecialchars(mb_strimwidth($row['description'], 0, 120, '...'))); ?></td>
              <td class="actions">
                <a href="event_form.php?id=<?php echo $row['id']; ?>" class="button small secondary">Edit</a>
                <a href="delete_event.php?id=<?php echo $row['id']; ?>"
                   class="button small danger"
                   onclick="return confirm('Delete this event?');">
                  Delete
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="5">No events found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

  </main>
</div>
</body>
</html>
