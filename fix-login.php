<?php
require_once 'includes/config.php';

$db = getDBConnection();

// Show all teachers
$all = $db->query("SELECT id, full_name, username, LEFT(password,30) as pass_preview, status FROM teachers ORDER BY id")->fetchAll();

echo "<style>body{font-family:sans-serif;max-width:700px;margin:40px auto;padding:20px} table{width:100%;border-collapse:collapse} td,th{padding:8px 12px;border:1px solid #ddd;font-size:13px} th{background:#004080;color:#fff} .ok{background:#d4edda} .btn{display:inline-block;padding:10px 20px;background:#004080;color:#fff;border-radius:6px;text-decoration:none;margin-top:20px;font-size:14px}</style>";

echo "<h2>Teachers in DB</h2>";
if (empty($all)) {
    echo "<p style='color:red'>❌ No teachers found in DB!</p>";
} else {
    echo "<table><tr><th>ID</th><th>Full Name</th><th>Username</th><th>Password (preview)</th><th>Status</th></tr>";
    foreach ($all as $t) {
        echo "<tr><td>{$t['id']}</td><td>{$t['full_name']}</td><td><strong>{$t['username']}</strong></td><td><code>{$t['pass_preview']}...</code></td><td>{$t['status']}</td></tr>";
    }
    echo "</table>";
}

// Fix button
if (isset($_GET['fix'])) {
    $hashed = password_hash('123456', PASSWORD_BCRYPT);
    if (empty($all)) {
        $stmt = $db->prepare("INSERT INTO teachers (full_name, username, password, email, department, status) VALUES (?,?,?,?,?,'active')");
        $stmt->execute(['Demo Teacher', 'teacher123', $hashed, 'teacher@scti.edu.np', 'General']);
        $id = $db->lastInsertId();
        echo "<p style='color:green'>✅ Inserted new teacher with ID: $id</p>";
    } else {
        $firstId = $all[0]['id'];
        $stmt = $db->prepare("UPDATE teachers SET username='teacher123', password=? WHERE id=?");
        $stmt->execute([$hashed, $firstId]);
        echo "<p style='color:green'>✅ Updated teacher ID: $firstId — username=teacher123, password=123456</p>";
    }

    // Verify
    $row = $db->query("SELECT password FROM teachers WHERE username='teacher123' LIMIT 1")->fetch();
    if ($row && password_verify('123456', $row['password'])) {
        echo "<p style='color:green;font-weight:bold'>✅ Hash verified — login WILL work now!</p>";
        echo "<p><a href='pages/login-simple.php'>→ Go to Login Page</a></p>";
    } else {
        echo "<p style='color:red'>❌ Hash verification failed — something is wrong</p>";
    }
    echo "<p style='color:red;font-size:13px'>⚠️ Delete fix-login.php now!</p>";
} else {
    echo "<br><a class='btn' href='fix-login.php?fix=1'>🔧 Fix Now: Set teacher123 / 123456</a>";
}
?>
