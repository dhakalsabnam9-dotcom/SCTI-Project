<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php'); exit();
}
require_once '../includes/config.php';

$messages = [];
$error = '';
try {
    $db = getDBConnection();
    $stmt = $db->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 50");
    $messages = $stmt->fetchAll();
} catch (Exception $e) {
    $error = 'Could not load messages.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Messages | SCTI Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; }
    .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
    .page-header {
      background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
      color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px;
      box-shadow: 0 4px 15px rgba(220,53,69,0.2);
    }
    .page-header h1 { margin: 0 0 8px 0; font-size: 28px; }
    .breadcrumb { background: transparent !important; padding: 0; font-size: 14px; }
    .breadcrumb a { color: white; text-decoration: none; }
    .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 25px; }
    .stat-box { background: white; border-radius: 10px; padding: 18px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-top: 4px solid #dc3545; }
    .stat-box .num { font-size: 28px; font-weight: 700; color: #dc3545; }
    .stat-box .lbl { color: #666; font-size: 13px; margin-top: 4px; }
    .messages-list { display: flex; flex-direction: column; gap: 15px; }
    .message-card {
      background: white; border-radius: 10px; padding: 25px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-left: 5px solid #dc3545;
      transition: all 0.3s;
    }
    .message-card:hover { transform: translateX(5px); box-shadow: 0 5px 20px rgba(220,53,69,0.2); }
    .message-card.read { border-left-color: #adb5bd; opacity: 0.85; }
    .msg-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
    .sender-info { display: flex; align-items: center; gap: 12px; }
    .sender-avatar {
      width: 45px; height: 45px; border-radius: 50%;
      background: linear-gradient(135deg, #dc3545, #c82333);
      display: flex; align-items: center; justify-content: center;
      color: white; font-size: 18px; font-weight: 700;
    }
    .sender-name { font-weight: 700; color: #333; font-size: 16px; }
    .sender-email { color: #666; font-size: 13px; }
    .msg-time { color: #999; font-size: 12px; }
    .msg-subject { font-weight: 600; color: #333; margin-bottom: 8px; font-size: 15px; }
    .msg-body { color: #555; font-size: 14px; line-height: 1.6; margin-bottom: 15px; }
    .msg-actions { display: flex; gap: 8px; }
    .btn-sm {
      padding: 8px 16px; border: none; border-radius: 5px;
      cursor: pointer; font-size: 13px; transition: all 0.3s;
      display: inline-flex; align-items: center; gap: 5px;
    }
    .btn-reply { background: #cce5ff; color: #004085; }
    .btn-reply:hover { background: #004080; color: white; }
    .btn-mark { background: #d4edda; color: #155724; }
    .btn-mark:hover { background: #28a745; color: white; }
    .btn-delete { background: #f8d7da; color: #dc3545; }
    .btn-delete:hover { background: #dc3545; color: white; }
    .badge-new { background: #f8d7da; color: #721c24; padding: 3px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
    .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; background: #f8d7da; color: #721c24; }
    .empty-state { text-align: center; padding: 60px 20px; color: #666; }
    .empty-state i { font-size: 60px; color: #dee2e6; margin-bottom: 15px; }
  </style>
</head>
<body>
<div class="top-header"><marquee>Contact Messages - View and respond to messages from visitors and students</marquee></div>
<div class="container">
  <div class="page-header">
    <h1><i class="fa fa-envelope-open-text"></i> Contact Messages</h1>
    <div class="breadcrumb"><a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Contact Messages</div>
  </div>

  <div class="stats-row">
    <div class="stat-box"><div class="num"><?php echo count($messages); ?></div><div class="lbl">Total Messages</div></div>
    <div class="stat-box"><div class="num"><?php echo count($messages); ?></div><div class="lbl">Unread</div></div>
    <div class="stat-box"><div class="num">0</div><div class="lbl">Replied</div></div>
  </div>

  <?php if ($error): ?>
    <div class="alert"><i class="fa fa-exclamation-circle"></i> <?php echo $error; ?></div>
  <?php endif; ?>

  <div class="messages-list">
    <?php if (empty($messages)): ?>
      <div class="empty-state">
        <i class="fa fa-inbox"></i>
        <p style="font-size:18px;margin-bottom:8px;">No messages yet</p>
        <p>Contact form submissions will appear here.</p>
      </div>
    <?php else: ?>
      <?php foreach ($messages as $msg): ?>
      <div class="message-card">
        <div class="msg-header">
          <div class="sender-info">
            <div class="sender-avatar"><?php echo strtoupper(substr($msg['name'] ?? 'U', 0, 1)); ?></div>
            <div>
              <div class="sender-name"><?php echo htmlspecialchars($msg['name'] ?? 'Unknown'); ?> <span class="badge-new">New</span></div>
              <div class="sender-email"><?php echo htmlspecialchars($msg['email'] ?? ''); ?></div>
            </div>
          </div>
          <div class="msg-time"><?php echo isset($msg['created_at']) ? date('M d, Y H:i', strtotime($msg['created_at'])) : ''; ?></div>
        </div>
        <?php if (!empty($msg['subject'])): ?>
          <div class="msg-subject"><?php echo htmlspecialchars($msg['subject']); ?></div>
        <?php endif; ?>
        <div class="msg-body"><?php echo nl2br(htmlspecialchars($msg['message'] ?? '')); ?></div>
        <div class="msg-actions">
          <button class="btn-sm btn-reply"><i class="fa fa-reply"></i> Reply</button>
          <button class="btn-sm btn-mark"><i class="fa fa-check"></i> Mark Read</button>
          <button class="btn-sm btn-delete"><i class="fa fa-trash"></i> Delete</button>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
<footer class="footer" style="margin-top:30px;"><p>© 2025 SCTI - Admin Panel</p></footer>
</body>
</html>
