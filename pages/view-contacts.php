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
    $stmt = $db->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 100");
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = 'Could not load messages: ' . $e->getMessage();
}

$total   = count($messages);
$today   = date('Y-m-d');
$todayCnt = 0;
foreach ($messages as $m) {
    if (isset($m['created_at']) && substr($m['created_at'], 0, 10) === $today) $todayCnt++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Messages | SCTI Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    *{margin:0;padding:0;box-sizing:border-box;}
    body{font-family:'Segoe UI',sans-serif;background:#f0f4f8;min-height:100vh;}
    .top-bar{background:#00264d;color:white;padding:7px 20px;font-size:13px;}
    /* HEADER */
    .pg-header{background:linear-gradient(135deg,#dc3545,#c82333);color:#fff;padding:20px 28px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 18px rgba(220,53,69,.3);}
    .pg-header h1{font-size:22px;font-weight:700;display:flex;align-items:center;gap:10px;margin:0 0 3px;}
    .pg-header .bc{font-size:12px;color:rgba(255,255,255,.75);}
    .pg-header .bc a{color:#fff;text-decoration:none;}
    .hdr-btns{display:flex;gap:8px;}
    .btn-hdr{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;border:none;transition:.2s;text-decoration:none;}
    .btn-hdr.ghost{background:rgba(255,255,255,.18);color:#fff;}
    .btn-hdr.ghost:hover{background:rgba(255,255,255,.32);}
    /* STATS */
    .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;padding:18px 28px;background:#fff;border-bottom:1px solid #e9ecef;}
    .stat{display:flex;align-items:center;gap:12px;background:#f8f9fa;border-radius:10px;padding:12px 16px;}
    .stat-ico{width:42px;height:42px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:17px;color:#fff;flex-shrink:0;}
    .ico-red{background:linear-gradient(135deg,#dc3545,#c82333);}
    .ico-blue{background:linear-gradient(135deg,#004080,#0059b3);}
    .ico-green{background:linear-gradient(135deg,#28a745,#20c997);}
    .ico-orange{background:linear-gradient(135deg,#fd7e14,#ffc107);}
    .stat-val{font-size:24px;font-weight:700;color:#222;line-height:1;}
    .stat-lbl{font-size:11px;color:#999;margin-top:2px;}
    /* LAYOUT */
    .layout{display:grid;grid-template-columns:380px 1fr;min-height:calc(100vh - 170px);}
    .panel{background:#fff;border-right:1px solid #e9ecef;padding:22px;overflow-y:auto;}
    .panel h3{font-size:15px;color:#dc3545;margin-bottom:16px;padding-bottom:10px;border-bottom:2px solid #fde8ea;display:flex;align-items:center;gap:8px;}
    .content{padding:22px;overflow-y:auto;}
    /* DETAIL PANEL */
    .detail-empty{text-align:center;padding:60px 20px;color:#ccc;}
    .detail-empty i{font-size:56px;display:block;margin-bottom:12px;}
    .detail-avatar{width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#dc3545,#c82333);display:flex;align-items:center;justify-content:center;color:white;font-size:26px;font-weight:700;margin:0 auto 14px;}
    .detail-name{font-size:18px;font-weight:700;color:#222;text-align:center;margin-bottom:4px;}
    .detail-email{font-size:13px;color:#888;text-align:center;margin-bottom:16px;}
    .detail-meta{background:#f8f9fa;border-radius:8px;padding:12px 14px;margin-bottom:14px;font-size:12px;color:#666;display:flex;flex-direction:column;gap:6px;}
    .detail-meta span{display:flex;align-items:center;gap:7px;}
    .detail-meta i{color:#dc3545;width:14px;}
    .detail-subject{font-weight:700;color:#333;font-size:14px;margin-bottom:8px;}
    .detail-body{color:#555;font-size:13px;line-height:1.7;background:#f8f9fa;border-radius:8px;padding:14px;margin-bottom:16px;white-space:pre-wrap;}
    .detail-actions{display:flex;flex-direction:column;gap:8px;}
    .dbtn{width:100%;padding:10px;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;display:flex;align-items:center;justify-content:center;gap:7px;transition:.2s;}
    .dbtn-reply{background:linear-gradient(135deg,#004080,#0059b3);color:#fff;}
    .dbtn-reply:hover{opacity:.88;}
    .dbtn-del{background:#f8d7da;color:#dc3545;}
    .dbtn-del:hover{background:#dc3545;color:#fff;}
    /* TOOLBAR */
    .toolbar{display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap;align-items:center;}
    .toolbar input{flex:1;min-width:160px;padding:9px 13px;border:2px solid #dee2e6;border-radius:8px;font-size:13px;font-family:inherit;}
    .toolbar input:focus{outline:none;border-color:#dc3545;}
    .btn-refresh{padding:9px 16px;background:linear-gradient(135deg,#dc3545,#c82333);color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;transition:.2s;}
    .btn-refresh:hover{transform:translateY(-1px);}
    /* CONTACT GRID */
    .cgrid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;}
    .ccard{background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.07);transition:.25s;border-top:4px solid #dc3545;cursor:pointer;}
    .ccard:hover{transform:translateY(-5px);box-shadow:0 10px 28px rgba(220,53,69,.18);}
    .ccard.selected{border-top-color:#004080;box-shadow:0 0 0 3px rgba(0,64,128,.25);}
    .ccard-body{padding:14px;}
    .ccard-avatar{width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#dc3545,#c82333);display:flex;align-items:center;justify-content:center;color:white;font-size:16px;font-weight:700;margin-bottom:10px;}
    .ccard-name{font-weight:700;color:#222;font-size:13px;margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .ccard-email{color:#aaa;font-size:11px;margin-bottom:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .ccard-subject{color:#555;font-size:12px;font-weight:600;margin-bottom:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .ccard-preview{color:#bbb;font-size:11px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;margin-bottom:8px;}
    .ccard-meta{font-size:10px;color:#ccc;display:flex;align-items:center;gap:4px;margin-bottom:9px;}
    .ccard-meta i{color:#dc3545;}
    .ccard-actions{display:flex;gap:7px;}
    .cbtn{flex:1;padding:6px;border:none;border-radius:7px;cursor:pointer;font-size:11px;font-weight:600;transition:.2s;display:flex;align-items:center;justify-content:center;gap:4px;}
    .cbtn-view{background:#cce5ff;color:#004085;}
    .cbtn-view:hover{background:#004080;color:#fff;}
    .cbtn-del{background:#f8d7da;color:#721c24;}
    .cbtn-del:hover{background:#dc3545;color:#fff;}
    .empty{text-align:center;padding:60px 20px;color:#ccc;grid-column:1/-1;}
    .empty i{font-size:56px;display:block;margin-bottom:12px;}
    /* TOAST */
    .toast{position:fixed;bottom:22px;right:22px;color:white;padding:11px 18px;border-radius:9px;font-size:13px;font-weight:700;z-index:99999;display:none;}
    .toast.ok{background:#28a745;}
    .toast.err{background:#dc3545;}
    footer{background:#00264d;color:white;text-align:center;padding:12px;font-size:13px;}
    @media(max-width:860px){.layout{grid-template-columns:1fr;}.stats{grid-template-columns:repeat(2,1fr);}}
  </style>
</head>
<body>
<div class="top-bar"><marquee>Contact Messages — View and manage messages from visitors and students</marquee></div>

<!-- HEADER -->
<div class="pg-header">
  <div>
    <h1><i class="fa fa-envelope-open-text"></i> Contact Messages</h1>
    <div class="bc"><a href="../dashboards/admin-dashboard.php"><i class="fa fa-home"></i> Dashboard</a> / Contact Messages</div>
  </div>
  <div class="hdr-btns">
    <a href="../dashboards/admin-dashboard.php" class="btn-hdr ghost"><i class="fa fa-arrow-left"></i> Back</a>
  </div>
</div>

<!-- STATS -->
<div class="stats">
  <div class="stat"><div class="stat-ico ico-red"><i class="fa fa-envelope"></i></div><div><div class="stat-val"><?php echo $total; ?></div><div class="stat-lbl">Total Messages</div></div></div>
  <div class="stat"><div class="stat-ico ico-orange"><i class="fa fa-calendar-day"></i></div><div><div class="stat-val"><?php echo $todayCnt; ?></div><div class="stat-lbl">Today</div></div></div>
  <div class="stat"><div class="stat-ico ico-blue"><i class="fa fa-users"></i></div><div><div class="stat-val" id="sShowing">—</div><div class="stat-lbl">Showing</div></div></div>
  <div class="stat"><div class="stat-ico ico-green"><i class="fa fa-check-circle"></i></div><div><div class="stat-val" id="sSelected">—</div><div class="stat-lbl">Selected</div></div></div>
</div>

<!-- LAYOUT -->
<div class="layout">

  <!-- LEFT PANEL: MESSAGE DETAIL -->
  <div class="panel">
    <h3><i class="fa fa-envelope-open"></i> Message Detail</h3>
    <div id="detailPane">
      <div class="detail-empty">
        <i class="fa fa-envelope"></i>
        <p>Click a message card to view details</p>
      </div>
    </div>
  </div>

  <!-- RIGHT: CONTACT GRID -->
  <div class="content">
    <div class="toolbar">
      <input type="text" id="searchInput" placeholder="Search by name, email or subject..." oninput="filterCards()">
      <button class="btn-refresh" onclick="location.reload()"><i class="fa fa-sync"></i> Refresh</button>
    </div>
    <div class="cgrid" id="cgrid">
      <?php if ($error): ?>
        <div class="empty"><i class="fa fa-exclamation-triangle"></i><p style="color:#dc3545"><?php echo htmlspecialchars($error); ?></p></div>
      <?php elseif (empty($messages)): ?>
        <div class="empty"><i class="fa fa-inbox"></i><p>No messages yet</p></div>
      <?php else: ?>
        <?php foreach ($messages as $i => $m):
          $name    = htmlspecialchars($m['name'] ?? 'Unknown');
          $email   = htmlspecialchars($m['email'] ?? '');
          $subject = htmlspecialchars($m['subject'] ?? 'No Subject');
          $body    = htmlspecialchars($m['message'] ?? '');
          $phone   = htmlspecialchars($m['phone'] ?? '');
          $dt      = isset($m['created_at']) ? date('M d, Y H:i', strtotime($m['created_at'])) : '';
          $initial = strtoupper(substr($m['name'] ?? 'U', 0, 1));
          $id      = (int)($m['id'] ?? 0);
        ?>
        <div class="ccard" id="card-<?php echo $i; ?>" onclick="viewMsg(<?php echo $i; ?>)">
          <div class="ccard-body">
            <div class="ccard-avatar"><?php echo $initial; ?></div>
            <div class="ccard-name"><?php echo $name; ?></div>
            <div class="ccard-email"><?php echo $email; ?></div>
            <div class="ccard-subject"><?php echo $subject; ?></div>
            <div class="ccard-preview"><?php echo $body; ?></div>
            <div class="ccard-meta"><i class="fa fa-clock"></i> <?php echo $dt; ?></div>
            <div class="ccard-actions">
              <button class="cbtn cbtn-view" onclick="event.stopPropagation();viewMsg(<?php echo $i; ?>)"><i class="fa fa-eye"></i> View</button>
              <button class="cbtn cbtn-del"  onclick="event.stopPropagation();delMsg(<?php echo $id; ?>,<?php echo $i; ?>)"><i class="fa fa-trash"></i> Delete</button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

</div><!-- /layout -->

<div class="toast" id="toast"></div>
<footer>© 2025 SCTI — Admin Panel</footer>

<script>
var msgs = <?php echo json_encode(array_values($messages)); ?>;

document.getElementById('sShowing').textContent = msgs.length;
document.getElementById('sSelected').textContent = '—';

/* ── VIEW ── */
function viewMsg(i) {
  var m = msgs[i];
  if (!m) return;

  // highlight selected card
  document.querySelectorAll('.ccard').forEach(function(c){ c.classList.remove('selected'); });
  var card = document.getElementById('card-' + i);
  if (card) card.classList.add('selected');
  document.getElementById('sSelected').textContent = 1;

  var name    = esc(m.name || 'Unknown');
  var email   = esc(m.email || '');
  var subject = esc(m.subject || 'No Subject');
  var body    = esc(m.message || '');
  var phone   = esc(m.phone || '');
  var dt      = m.created_at ? new Date(m.created_at).toLocaleString('en-US',{year:'numeric',month:'short',day:'numeric',hour:'2-digit',minute:'2-digit'}) : '';
  var initial = (m.name || 'U').charAt(0).toUpperCase();
  var id      = parseInt(m.id) || 0;

  document.getElementById('detailPane').innerHTML =
    '<div class="detail-avatar">' + initial + '</div>'
  + '<div class="detail-name">' + name + '</div>'
  + '<div class="detail-email">' + email + '</div>'
  + '<div class="detail-meta">'
  +   (phone ? '<span><i class="fa fa-phone"></i> ' + phone + '</span>' : '')
  +   '<span><i class="fa fa-clock"></i> ' + dt + '</span>'
  + '</div>'
  + '<div class="detail-subject">' + subject + '</div>'
  + '<div class="detail-body">' + body + '</div>'
  + '<div class="detail-actions">'
  +   '<a href="mailto:' + (m.email||'') + '?subject=Re: ' + encodeURIComponent(m.subject||'') + '" class="dbtn dbtn-reply"><i class="fa fa-reply"></i> Reply via Email</a>'
  +   '<button class="dbtn dbtn-del" onclick="delMsg(' + id + ',' + i + ')"><i class="fa fa-trash"></i> Delete Message</button>'
  + '</div>';
}

/* ── DELETE ── */
function delMsg(id, idx) {
  if (!confirm('Delete this message? This cannot be undone.')) return;
  fetch('contact-delete.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({id:id})})
    .then(function(r){ return r.json(); })
    .then(function(d){ if (!d.success) { toast(d.message||'Delete failed','err'); return; } })
    .catch(function(){});

  // Remove from DOM and array
  var card = document.getElementById('card-' + idx);
  if (card) card.remove();
  msgs[idx] = null;

  // Reset detail pane
  document.getElementById('detailPane').innerHTML =
    '<div class="detail-empty"><i class="fa fa-envelope"></i><p>Message deleted</p></div>';
  document.getElementById('sSelected').textContent = '—';

  // Recount showing
  var visible = document.querySelectorAll('.ccard').length;
  document.getElementById('sShowing').textContent = visible;
  toast('Message deleted', 'ok');
}

/* ── SEARCH ── */
function filterCards() {
  var q = document.getElementById('searchInput').value.toLowerCase();
  var cards = document.querySelectorAll('.ccard');
  var shown = 0;
  cards.forEach(function(card, i) {
    var m = msgs[i];
    if (!m) { card.style.display='none'; return; }
    var match = !q
      || (m.name||'').toLowerCase().indexOf(q) > -1
      || (m.email||'').toLowerCase().indexOf(q) > -1
      || (m.subject||'').toLowerCase().indexOf(q) > -1
      || (m.message||'').toLowerCase().indexOf(q) > -1;
    card.style.display = match ? '' : 'none';
    if (match) shown++;
  });
  document.getElementById('sShowing').textContent = shown;
}

/* ── HELPERS ── */
function toast(msg, type) {
  var t = document.getElementById('toast');
  t.textContent = msg;
  t.className = 'toast ' + (type||'ok');
  t.style.display = 'block';
  setTimeout(function(){ t.style.display='none'; }, 3000);
}
function esc(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
</script>
</body>
</html>
