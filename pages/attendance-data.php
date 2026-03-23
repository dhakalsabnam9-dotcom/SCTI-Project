<?php
ob_start();
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'teacher') {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
require_once '../includes/config.php';

$action = $_GET['action'] ?? 'student_chart';

try {
    $db = getDBConnection();

    // ── 1. Student list (optionally filtered by course+semester) ─────────────
    if ($action === 'students') {
        $class = trim($_GET['class'] ?? '');
        // Try to match "COURSE Semester N" pattern e.g. "BIT Semester 1"
        if ($class && preg_match('/^(.+?)\s+Semester\s+(\d+)$/i', $class, $m)) {
            $course   = trim($m[1]);
            $semester = 'Semester ' . $m[2];
            $stmt = $db->prepare("SELECT id, full_name, student_id, course, semester FROM students WHERE status='active' AND course=? AND semester=? ORDER BY full_name ASC");
            $stmt->execute([$course, $semester]);
        } elseif ($class) {
            // fallback: match course name only
            $stmt = $db->prepare("SELECT id, full_name, student_id, course, semester FROM students WHERE status='active' AND course=? ORDER BY full_name ASC");
            $stmt->execute([$class]);
        } else {
            $stmt = $db->query("SELECT id, full_name, student_id, course, semester FROM students WHERE status='active' ORDER BY full_name ASC");
        }
        $rows = $stmt->fetchAll();
        ob_end_clean();
        echo json_encode(['success'=>true,'students'=>$rows]); exit();
    }

    // ── 1b. Get distinct classes from students ────────────────────────────────
    if ($action === 'classes') {
        $rows = $db->query("SELECT DISTINCT course, semester FROM students WHERE status='active' AND course IS NOT NULL AND course != '' ORDER BY course, semester ASC")->fetchAll();
        $classes = [];
        foreach ($rows as $r) {
            if ($r['semester']) {
                $classes[] = $r['course'] . ' ' . $r['semester'];
            } else {
                $classes[] = $r['course'];
            }
        }
        ob_end_clean();
        echo json_encode(['success'=>true,'classes'=>array_values(array_unique($classes))]); exit();
    }

    // ── 2. Existing attendance for a date (for mark-attendance page) ─────────
    if ($action === 'existing') {
        $date  = $_GET['date']  ?? date('Y-m-d');
        $class = $_GET['class'] ?? '';
        $stmt  = $db->prepare("SELECT student_id, status, remarks, late_reason FROM attendance WHERE attendance_date=? AND class_name=?");
        $stmt->execute([$date, $class]);
        $rows  = $stmt->fetchAll();
        $map   = [];
        foreach ($rows as $r) $map[$r['student_id']] = $r;
        ob_end_clean();
        echo json_encode(['success'=>true,'records'=>$map]); exit();
    }

    // ── 3. Individual student chart data ─────────────────────────────────────
    if ($action === 'student_chart') {
        $sid    = intval($_GET['student_id'] ?? 0);
        $range  = $_GET['range'] ?? '1month';   // 1week|1month|3months|6months|1year|custom
        $from   = $_GET['from'] ?? '';
        $to     = $_GET['to']   ?? date('Y-m-d');

        if (!$sid) { ob_end_clean(); echo json_encode(['success'=>false,'message'=>'student_id required']); exit(); }

        [$fromDate, $toDate] = resolveDateRange($range, $from, $to);

        $stmt = $db->prepare("SELECT attendance_date, status FROM attendance
                               WHERE student_id=? AND attendance_date BETWEEN ? AND ?
                               ORDER BY attendance_date ASC");
        $stmt->execute([$sid, $fromDate, $toDate]);
        $rows = $stmt->fetchAll();

        $counts = ['present'=>0,'absent'=>0,'late'=>0];
        $daily  = [];
        foreach ($rows as $r) {
            $counts[$r['status']] = ($counts[$r['status']] ?? 0) + 1;
            $daily[$r['attendance_date']] = $r['status'];
        }
        $total = array_sum($counts);
        $rate  = $total > 0 ? round(($counts['present'] + $counts['late']) / $total * 100, 1) : 0;

        // Build label/data arrays for line chart
        $labels = []; $pData = []; $aData = []; $lData = [];
        $grouped = groupByPeriod($rows, $range);
        foreach ($grouped as $label => $g) {
            $labels[] = $label;
            $pData[]  = $g['present'];
            $aData[]  = $g['absent'];
            $lData[]  = $g['late'];
        }

        ob_end_clean();
        echo json_encode([
            'success' => true,
            'counts'  => $counts,
            'total'   => $total,
            'rate'    => $rate,
            'labels'  => $labels,
            'present' => $pData,
            'absent'  => $aData,
            'late'    => $lData,
            'from'    => $fromDate,
            'to'      => $toDate,
        ]);
        exit();
    }

    // ── 4. All-students summary chart ────────────────────────────────────────
    if ($action === 'all_chart') {
        $range = $_GET['range'] ?? '1month';
        $from  = $_GET['from'] ?? '';
        $to    = $_GET['to']   ?? date('Y-m-d');

        [$fromDate, $toDate] = resolveDateRange($range, $from, $to);

        // Per-student totals
        $stmt = $db->prepare("
            SELECT s.id, s.full_name, s.student_id as sid,
                   SUM(a.status='present') as present,
                   SUM(a.status='absent')  as absent,
                   SUM(a.status='late')    as late,
                   COUNT(a.id)             as total
            FROM students s
            LEFT JOIN attendance a ON a.student_id=s.id
                AND a.attendance_date BETWEEN ? AND ?
            WHERE s.status='active'
            GROUP BY s.id
            ORDER BY s.full_name ASC
        ");
        $stmt->execute([$fromDate, $toDate]);
        $rows = $stmt->fetchAll();

        $labels = []; $rates = []; $presents = []; $absents = []; $lates = [];
        foreach ($rows as $r) {
            $labels[]   = $r['full_name'];
            $total      = intval($r['total']);
            $rate       = $total > 0 ? round((intval($r['present']) + intval($r['late'])) / $total * 100, 1) : 0;
            $rates[]    = $rate;
            $presents[] = intval($r['present']);
            $absents[]  = intval($r['absent']);
            $lates[]    = intval($r['late']);
        }

        // Daily trend for all students combined
        $stmt2 = $db->prepare("
            SELECT attendance_date,
                   SUM(status='present') as present,
                   SUM(status='absent')  as absent,
                   SUM(status='late')    as late
            FROM attendance
            WHERE attendance_date BETWEEN ? AND ?
            GROUP BY attendance_date
            ORDER BY attendance_date ASC
        ");
        $stmt2->execute([$fromDate, $toDate]);
        $trend = $stmt2->fetchAll();

        $tLabels = []; $tPresent = []; $tAbsent = []; $tLate = [];
        foreach ($trend as $t) {
            $tLabels[]  = date('M d', strtotime($t['attendance_date']));
            $tPresent[] = intval($t['present']);
            $tAbsent[]  = intval($t['absent']);
            $tLate[]    = intval($t['late']);
        }

        ob_end_clean();
        echo json_encode([
            'success'  => true,
            'labels'   => $labels,
            'rates'    => $rates,
            'presents' => $presents,
            'absents'  => $absents,
            'lates'    => $lates,
            'trend'    => ['labels'=>$tLabels,'present'=>$tPresent,'absent'=>$tAbsent,'late'=>$tLate],
            'from'     => $fromDate,
            'to'       => $toDate,
        ]);
        exit();
    }

    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>'Unknown action']);

} catch(Exception $e) {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}

// ── Helpers ──────────────────────────────────────────────────────────────────
function resolveDateRange($range, $from, $to) {
    $toDate = $to ?: date('Y-m-d');
    switch ($range) {
        case '1week':   $fromDate = date('Y-m-d', strtotime('-7 days',  strtotime($toDate))); break;
        case '3months': $fromDate = date('Y-m-d', strtotime('-3 months',strtotime($toDate))); break;
        case '6months': $fromDate = date('Y-m-d', strtotime('-6 months',strtotime($toDate))); break;
        case '1year':   $fromDate = date('Y-m-d', strtotime('-1 year',  strtotime($toDate))); break;
        case 'custom':  $fromDate = $from ?: date('Y-m-d', strtotime('-30 days')); break;
        default:        $fromDate = date('Y-m-d', strtotime('-30 days', strtotime($toDate))); // 1month
    }
    return [$fromDate, $toDate];
}

function groupByPeriod($rows, $range) {
    $groups = [];
    foreach ($rows as $r) {
        $d = $r['attendance_date'];
        if (in_array($range, ['1week','1month'])) {
            $key = date('M d', strtotime($d));
        } elseif ($range === '3months') {
            $key = date('M d', strtotime($d));
        } else {
            // 6months / 1year / custom → group by week
            $key = 'W' . date('W', strtotime($d)) . ' ' . date('M Y', strtotime($d));
        }
        if (!isset($groups[$key])) $groups[$key] = ['present'=>0,'absent'=>0,'late'=>0];
        $groups[$key][$r['status']]++;
    }
    return $groups;
}
