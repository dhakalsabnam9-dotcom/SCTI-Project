<?php
ob_start();
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit();
}
require_once '../includes/config.php';

$sid    = intval($_SESSION['user_id']);
$action = $_GET['action'] ?? 'chart';

try {
    $db = getDBConnection();

    // ── Chart data ────────────────────────────────────────────────────────────
    if ($action === 'chart') {
        $range = $_GET['range'] ?? '1month';
        $from  = $_GET['from']  ?? '';
        $to    = $_GET['to']    ?? date('Y-m-d');

        [$fromDate, $toDate] = resolveDateRange($range, $from, $to);

        $stmt = $db->prepare("SELECT attendance_date, status FROM attendance
                               WHERE student_id=? AND attendance_date BETWEEN ? AND ?
                               ORDER BY attendance_date ASC");
        $stmt->execute([$sid, $fromDate, $toDate]);
        $rows = $stmt->fetchAll();

        $counts = ['present'=>0,'absent'=>0,'late'=>0];
        foreach ($rows as $r) $counts[$r['status']] = ($counts[$r['status']] ?? 0) + 1;
        $total = array_sum($counts);
        $rate  = $total > 0 ? round(($counts['present'] + $counts['late']) / $total * 100, 1) : 0;

        $grouped = groupByPeriod($rows, $range);
        $labels=[]; $pData=[]; $aData=[]; $lData=[];
        foreach ($grouped as $label => $g) {
            $labels[] = $label; $pData[] = $g['present']; $aData[] = $g['absent']; $lData[] = $g['late'];
        }

        ob_end_clean();
        echo json_encode(['success'=>true,'counts'=>$counts,'total'=>$total,'rate'=>$rate,
            'labels'=>$labels,'present'=>$pData,'absent'=>$aData,'late'=>$lData,
            'from'=>$fromDate,'to'=>$toDate]);
        exit();
    }

    // ── Detailed log ─────────────────────────────────────────────────────────
    if ($action === 'log') {
        $range = $_GET['range'] ?? '1month';
        $from  = $_GET['from']  ?? '';
        $to    = $_GET['to']    ?? date('Y-m-d');
        [$fromDate, $toDate] = resolveDateRange($range, $from, $to);

        $stmt = $db->prepare("SELECT a.attendance_date, a.status, a.class_name, a.period,
                                      a.late_reason, a.remarks,
                                      t.full_name as marked_by_name
                               FROM attendance a
                               LEFT JOIN teachers t ON t.id = a.marked_by
                               WHERE a.student_id=? AND a.attendance_date BETWEEN ? AND ?
                               ORDER BY a.attendance_date DESC");
        $stmt->execute([$sid, $fromDate, $toDate]);
        $rows = $stmt->fetchAll();
        ob_end_clean();
        echo json_encode(['success'=>true,'records'=>$rows,'from'=>$fromDate,'to'=>$toDate]);
        exit();
    }

    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>'Unknown action']);

} catch(Exception $e) {
    ob_end_clean();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}

function resolveDateRange($range, $from, $to) {
    $toDate = $to ?: date('Y-m-d');
    switch ($range) {
        case '1week':   $fromDate = date('Y-m-d', strtotime('-7 days',   strtotime($toDate))); break;
        case '3months': $fromDate = date('Y-m-d', strtotime('-3 months', strtotime($toDate))); break;
        case '6months': $fromDate = date('Y-m-d', strtotime('-6 months', strtotime($toDate))); break;
        case '1year':   $fromDate = date('Y-m-d', strtotime('-1 year',   strtotime($toDate))); break;
        case 'custom':  $fromDate = $from ?: date('Y-m-d', strtotime('-30 days')); break;
        default:        $fromDate = date('Y-m-d', strtotime('-30 days',  strtotime($toDate)));
    }
    return [$fromDate, $toDate];
}

function groupByPeriod($rows, $range) {
    $groups = [];
    foreach ($rows as $r) {
        $d = $r['attendance_date'];
        if (in_array($range, ['1week','1month','3months'])) {
            $key = date('M d', strtotime($d));
        } else {
            $key = 'W'.date('W', strtotime($d)).' '.date('M Y', strtotime($d));
        }
        if (!isset($groups[$key])) $groups[$key] = ['present'=>0,'absent'=>0,'late'=>0];
        $groups[$key][$r['status']]++;
    }
    return $groups;
}
