<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=scti_school', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "DB CONNECTION: OK\n\n";

    $tables = ['students','teachers','admins','notices','attendance','assignments','assignment_submissions','grades','programs'];
    foreach ($tables as $t) {
        $count = $db->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
        echo "  TABLE $t: OK ($count rows)\n";
    }

    echo "\n--- STUDENTS ---\n";
    $rows = $db->query("SELECT id, full_name, username, status FROM students")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) echo "  [{$r['id']}] {$r['full_name']} | user: {$r['username']} | {$r['status']}\n";

    echo "\n--- TEACHERS ---\n";
    $rows = $db->query("SELECT id, full_name, username, status FROM teachers")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) echo "  [{$r['id']}] {$r['full_name']} | user: {$r['username']} | {$r['status']}\n";

    echo "\n--- ADMINS ---\n";
    $rows = $db->query("SELECT id, full_name, username, status FROM admins")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) echo "  [{$r['id']}] {$r['full_name']} | user: {$r['username']} | {$r['status']}\n";

    echo "\n--- ASSIGNMENTS ---\n";
    $rows = $db->query("SELECT id, title, due_date FROM assignments")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) echo "  [{$r['id']}] {$r['title']} | due: {$r['due_date']}\n";

    echo "\n--- NOTICES ---\n";
    $rows = $db->query("SELECT id, title, status FROM notices")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) echo "  [{$r['id']}] {$r['title']} | {$r['status']}\n";

    echo "\nALL CHECKS DONE\n";
} catch(Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
