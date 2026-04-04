<?php
ob_start();
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    ob_end_clean(); echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
require_once '../includes/config.php';

$action    = $_GET['action']    ?? '';
$semester  = trim($_GET['semester']  ?? '');
$subject   = trim($_GET['subject']   ?? '');
$exam_type = trim($_GET['exam_type'] ?? '');

try {
    $db = getDBConnection();

    // ── INDIVIDUAL student grades ────────────────────────────────────────────
    if ($action === 'individual') {
        $sid = intval($_GET['student_id'] ?? 0);
        if (!$sid) { ob_end_clean(); echo json_encode(['success'=>false,'message'=>'student_id required']); exit(); }

        $where = ['g.student_id = ?'];
        $params = [$sid];

        if ($semester) {
            $semNum = preg_replace('/[^0-9]/', '', $semester);
            $where[] = '(s.semester = ? OR s.semester = ?)';
            $params[] = $semester; $params[] = $semNum;
        }
        if ($subject)   { $where[] = 'g.subject = ?';   $params[] = $subject; }
        if ($exam_type) { $where[] = 'g.exam_type = ?'; $params[] = $exam_type; }

        $sql = "SELECT g.subject, g.exam_type, g.semester, g.internal_marks, g.external_marks,
                       (g.internal_marks + g.external_marks) AS total
                FROM grades g
                JOIN students s ON s.id = g.student_id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY g.subject ASC, g.exam_type ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totals = array_map(function($r){ return floatval($r['total']); }, $rows);
        $avg = count($totals) ? round(array_sum($totals)/count($totals), 1) : 0;
        $max = count($totals) ? max($totals) : 0;
        $min = count($totals) ? min($totals) : 0;

        ob_end_clean();
        echo json_encode(['success'=>true,'rows'=>$rows,'avg_total'=>$avg,'max_total'=>$max,'min_total'=>$min]);
        exit();
    }

    // ── ALL students summary ─────────────────────────────────────────────────
    if ($action === 'all') {
        $where = ['1=1'];
        $params = [];

        if ($semester) {
            $semNum = preg_replace('/[^0-9]/', '', $semester);
            $where[] = '(s.semester = ? OR s.semester = ?)';
            $params[] = $semester; $params[] = $semNum;
        }
        if ($subject)   { $where[] = 'g.subject = ?';   $params[] = $subject; }
        if ($exam_type) { $where[] = 'g.exam_type = ?'; $params[] = $exam_type; }

        $sql = "SELECT s.full_name AS name,
                       AVG(g.internal_marks) AS avg_internal,
                       AVG(g.external_marks) AS avg_external,
                       AVG(g.internal_marks + g.external_marks) AS avg_total,
                       COUNT(g.id) AS record_count
                FROM students s
                JOIN grades g ON g.student_id = s.id
                WHERE " . implode(' AND ', $where) . "
                GROUP BY s.id, s.full_name
                ORDER BY avg_total DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_end_clean();
        echo json_encode(['success'=>true,'students'=>$students]);
        exit();
    }

    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>'Unknown action']);
} catch(Exception $e) {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
