<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../index.php'); exit();
}
require_once '../includes/config.php';
try {
    $db = getDBConnection();
    $tables = $db->query("SHOW TABLES FROM scti_school")->fetchAll(PDO::FETCH_COLUMN);
} catch(Exception $e) { $tables = []; }
$selected = isset($_GET['table']) ? preg_replace('/[^a-zA-Z0-9_]/','',$_GET['table']) : '';
$rows = []; $cols = []; $total = 0;
if ($selected && in_array($selected, $tables)) {
    try {
        $cols = $db->query("SHOW COLUMNS FROM `$selected`")->fetchAll(PDO::FETCH_COLUMN);
        $total = $db->query("SELECT COUNT(*) FROM `$selected`")->fetchColumn();
        $page = max(1, intval($_GET['p'] ?? 1));
        $limit = 20; $offset = ($page-1)*$limit;
        $rows = $db->query("SELECT * FROM `$selected` LIMIT $limit OFFSET $offset")->fetchAll();
    } catch(Exception $e) { $rows = []; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>DB Viewer | SCTI Admin</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',sans-serif;background:#f0f4f8;min-height:100vh;display:flex;flex-direction:column}
.top-bar{background:#00264d;color:white;padding:7px 20px;font-size:13px}
.pg-header{background:linear-gradient(135deg,#004080,#0059b3);color:#fff;padding:18px 28px;display:flex;justify-content:space-between;align-items:center}
.pg-header h1{font-size:20px;font-weight:700;display:flex;align-items:center;gap:10px;margin:0}
.pg-header .bc{font-size:12px;color:rgba(255,255,255,.75)}
.pg-header .bc a{color:#fff;text-decoration:none}
.btn-hdr{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;border:none;transition:.2s;text-decoration:none;background:rgba(255,255,255,.18);color:#fff}
.btn-hdr:hover{background:rgba(255,255,255,.32)}
.layout{display:flex;flex:1;min-height:0}
.sidebar{width:220px;background:white;border-right:1px solid #e0e6ef;padding:16px;overflow-y:auto;flex-shrink:0}
.sidebar h3{font-size:12px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px}
.tbl-link{display:flex;align-items:center;justify-content:space-between;padding:9px 12px;border-radius:8px;font-size:13px;color:#333;text-decoration:none;transition:.2s;margin-bottom:4px;border:2px solid transparent}
.tbl-link:hover{background:#f0f4ff;color:#004080;border-color:#004080}
.tbl-link.active{background:linear-gradient(135deg,#004080,#0059b3);color:white;border-color:transparent}
.tbl-link .cnt{font-size:11px;opacity:.7;font-weight:600}
.main{flex:1;padding:20px;overflow:auto}
.table-info{background:white;border-radius:10px;padding:14px 18px;margin-bottom:16px;display:flex;align-items:center;gap:16px;box-shadow:0 2px 8px rgba(0,0,0,.07)}
.table-info h2{font-size:16px;color:#004080;margin:0}
.table-info .meta{font-size:12px;color:#888}
.data-table{width:100%;border-collapse:collapse;background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.07);font-size:13px}
.data-table th{background:linear-gradient(135deg,#004080,#0059b3);color:white;padding:10px 14px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap}
.data-table td{padding:9px 14px;border-bottom:1px solid #f0f0f0;color:#333;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.data-table tr:last-child td{border-bottom:none}
.data-table tr:hover td{background:#f0f4ff}
.null-val{color:#ccc;font-style:italic}
.pagination{display:flex;gap:8px;margin-top:16px;align-items:center;flex-wrap:wrap}
.pg-btn{padding:7px 14px;border:2px solid #e0e6ef;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;color:#555;background:white;transition:.2s}
.pg-btn:hover{border-color:#004080;color:#004080}
.pg-btn.active{background:#004080;color:white;border-color:#004080}
.empty{text-align:center;padding:60px 20px;color:#aaa}
.empty i{font-size:48px;display:block;margin-bottom:12px;color:#c5cfe0}
footer{background:#00264d;color:white;text-align:center;padding:10px;font-size:13px}
</style>
</head>
<body>
<div class="top-bar"><marquee>Database Viewer — SCTI Admin Panel</marquee></div>
<div class="pg-header">
  <div>
    <h1><i class="fa fa-database"></i> Database Viewer</h1>
    <div class="bc"><a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / DB Viewer</div>
  </div>
  <a href="../dashboards/admin-dashboard.php" class="btn-hdr"><i class="fa fa-arrow-left"></i> Back</a>
</div>

<div class="layout">
  <div class="sidebar">
    <h3><i class="fa fa-table"></i> Tables</h3>
    <?php foreach($tables as $t):
      $cnt = 0;
      try { $cnt = $db->query("SELECT COUNT(*) FROM `$t`")->fetchColumn(); } catch(Exception $e){}
    ?>
    <a href="?table=<?=urlencode($t)?>" class="tbl-link <?=$selected===$t?'active':''?>">
      <span><i class="fa fa-table" style="margin-right:6px;font-size:11px"></i><?=htmlspecialchars($t)?></span>
      <span class="cnt"><?=$cnt?></span>
    </a>
    <?php endforeach; ?>
  </div>

  <div class="main">
    <?php if (!$selected): ?>
    <div class="empty">
      <i class="fa fa-database"></i>
      <p>Select a table from the left to view its data</p>
    </div>
    <?php elseif (empty($rows) && $total == 0): ?>
    <div class="table-info">
      <h2><i class="fa fa-table"></i> <?=htmlspecialchars($selected)?></h2>
      <span class="meta">0 rows</span>
    </div>
    <div class="empty"><i class="fa fa-inbox"></i><p>No data in this table</p></div>
    <?php else: ?>
    <?php
      $page = max(1, intval($_GET['p'] ?? 1));
      $limit = 20;
      $totalPages = ceil($total / $limit);
    ?>
    <div class="table-info">
      <h2><i class="fa fa-table"></i> <?=htmlspecialchars($selected)?></h2>
      <div>
        <span class="meta"><i class="fa fa-rows" style="color:#004080"></i> <?=$total?> total rows</span>
        &nbsp;|&nbsp;
        <span class="meta"><i class="fa fa-columns" style="color:#004080"></i> <?=count($cols)?> columns</span>
        &nbsp;|&nbsp;
        <span class="meta">Page <?=$page?> of <?=$totalPages?></span>
      </div>
    </div>
    <div style="overflow-x:auto">
      <table class="data-table">
        <thead>
          <tr>
            <?php foreach($cols as $c): ?>
            <th><?=htmlspecialchars($c)?></th>
            <?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach($rows as $row): ?>
          <tr>
            <?php foreach($cols as $c): ?>
            <td title="<?=htmlspecialchars($row[$c] ?? '')?>">
              <?php if ($row[$c] === null): ?>
                <span class="null-val">NULL</span>
              <?php elseif (strlen($row[$c]) > 60): ?>
                <?=htmlspecialchars(substr($row[$c],0,60))?>...
              <?php else: ?>
                <?=htmlspecialchars($row[$c])?>
              <?php endif; ?>
            </td>
            <?php endforeach; ?>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
      <?php if ($page > 1): ?>
        <a href="?table=<?=urlencode($selected)?>&p=<?=$page-1?>" class="pg-btn"><i class="fa fa-chevron-left"></i> Prev</a>
      <?php endif; ?>
      <?php for($i=max(1,$page-2); $i<=min($totalPages,$page+2); $i++): ?>
        <a href="?table=<?=urlencode($selected)?>&p=<?=$i?>" class="pg-btn <?=$i==$page?'active':''?>"><?=$i?></a>
      <?php endfor; ?>
      <?php if ($page < $totalPages): ?>
        <a href="?table=<?=urlencode($selected)?>&p=<?=$page+1?>" class="pg-btn">Next <i class="fa fa-chevron-right"></i></a>
      <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</div>
<footer>© 2025 SCTI — Admin Panel</footer>
</body>
</html>
