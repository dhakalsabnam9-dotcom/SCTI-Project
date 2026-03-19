<?php
/**
 * One-time reset: Sets teacher username='teacher123' and password='123456'
 * DELETE THIS FILE after running it!
 * Visit: http://localhost/scti/reset-teacher-login.php
 */
require_once 'includes/config.php';

$newUsername = 'teacher123';
$newPassword = '123456';
$hashed      = password_hash($newPassword, PASSWORD_BCRYPT);

try {
    $db = getDBConnection();

    // First, show all teachers so we know what exists
    $all = $db->query("SELECT id, full_name, username, department, status FROM teachers ORDER BY id")->fetchAll();

    if (empty($all)) {
        // No teachers exist — insert one
        $stmt = $db->prepare("INSERT INTO teachers (full_name, username, password, email, department, status) VALUES (?, ?, ?, ?, ?, 'active')");
        $stmt->execute(['Demo Teacher', $newUsername, $hashed, 'teacher@scti.edu.np', 'General']);
        $insertedId = $db->lastInsertId();
        echo "<div style='font-family:sans-serif;max-width:560px;margin:40px auto;padding:24px;border-radius:10px;background:#d4edda;border:1px solid #c3e6cb'>";
        echo "<h2 style='color:#155724;margin:0 0 14px'>✅ Teacher inserted (ID: $insertedId)</h2>";
        echo "<p>No teachers existed, so a new one was created.</p>";
    } else {
        // Update the first teacher found
        $firstId = $all[0]['id'];
        $stmt = $db->prepare("UPDATE teachers SET username=?, password=? WHERE id=?");
        $stmt->execute([$newUsername, $hashed, $firstId]);
        $rows = $stmt->rowCount();

        echo "<div style='font-family:sans-serif;max-width:560px;margin:40px auto;padding:24px;border-radius:10px;background:#d4edda;border:1px solid #c3e6cb'>";
        echo "<h2 style='color:#155724;margin:0 0 14px'>✅ Done! Updated teacher ID: $firstId ($rows row updated)</h2>";
    }

    echo "<table style='width:100%;border-collapse:collapse;font-size:14px;margin-top:10px'>";
    echo "<tr><td style='padding:6px 0;color:#555'>Username:</td><td><strong>teacher123</strong></td></tr>";
    echo "<tr><td style='padding:6px 0;color:#555'>Password:</td><td><strong>123456</strong></td></tr>";
    echo "<tr><td style='padding:6px 0;color:#555'>User Type:</td><td><strong>Teacher</strong></td></tr>";
    echo "</table>";
    echo "<p style='margin:16px 0 0;color:#721c24;background:#f8d7da;padding:10px 14px;border-radius:6px;font-size:13px'><strong>⚠️ Delete this file immediately after use!</strong></p>";
    echo "</div>";

    // Verify the hash works
    $verify = $db->query("SELECT password FROM teachers WHERE username='teacher123' LIMIT 1")->fetch();
    $hashOk = $verify && password_verify($newPassword, $verify['password']);

    echo "<div style='font-family:sans-serif;max-width:560px;margin:16px auto;padding:16px;border-radius:8px;background:" . ($hashOk ? '#d4edda' : '#f8d7da') . ";font-size:13px'>";
    echo "<strong>Hash verification: " . ($hashOk ? '✅ Password hash is valid — login will work!' : '❌ Hash mismatch — something went wrong') . "</strong>";
    echo "</div>";

    // Show all teachers
    $all = $db->query("SELECT id, full_name, username, department, status FROM teachers ORDER BY id")->fetchAll();
    echo "<div style='font-family:sans-serif;max-width:560px;margin:16px auto;padding:16px;border-radius:8px;background:#f8f9fa;font-size:13px'>";
    echo "<strong>All teachers in DB:</strong><br><br>";
    foreach ($all as $t) {
        $hl = $t['username'] === 'teacher123' ? 'background:#fff3cd;padding:4px 8px;border-radius:4px;display:inline-block' : '';
        echo "<div style='margin-bottom:6px'><span style='$hl'>ID:{$t['id']} | <strong>{$t['full_name']}</strong> | <code>{$t['username']}</code> | {$t['department']} | {$t['status']}</span></div>";
    }
    echo "</div>";

} catch(Exception $e) {
    echo "<div style='font-family:sans-serif;color:red;padding:20px;max-width:560px;margin:40px auto'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>
