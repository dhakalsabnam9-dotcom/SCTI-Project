<?php
/**
 * Notice Widget — include in student/teacher dashboards
 * Usage: include 'notice-widget.php'; then call renderNoticeWidget('student') or ('teacher')
 */
function renderNoticeWidget($audience = 'all') {
    require_once dirname(__FILE__).'/../includes/config.php';
    try {
        $db = getDBConnection();
        $stmt = $db->prepare(
            "SELECT * FROM notices WHERE status='active' AND (audience=? OR audience='all' OR audience='emergency')
             ORDER BY priority='urgent' DESC, audience='emergency' DESC, created_at DESC LIMIT 6"
        );
        $stmt->execute([$audience]);
        $notices = $stmt->fetchAll();
    } catch(Exception $e) {
        $notices = [];
    }

    $audColors = [
        'all'       => ['bg'=>'#ede9fe','color'=>'#5b21b6','icon'=>'fa-globe'],
        'student'   => ['bg'=>'#cce5ff','color'=>'#004085','icon'=>'fa-user-graduate'],
        'teacher'   => ['bg'=>'#d4edda','color'=>'#155724','icon'=>'fa-chalkboard-teacher'],
        'emergency' => ['bg'=>'#f8d7da','color'=>'#721c24','icon'=>'fa-triangle-exclamation'],
    ];
    $priColors = [
        'urgent' => ['bg'=>'#f8d7da','color'=>'#721c24'],
        'high'   => ['bg'=>'#fff3cd','color'=>'#856404'],
        'normal' => ['bg'=>'#e2e3e5','color'=>'#383d41'],
    ];
    $accentColor = ($audience === 'teacher') ? '#28a745' : '#004080';
    ?>
    <div class="nw-wrap">
      <div class="nw-header">
        <span class="nw-title"><i class="fa fa-bullhorn"></i> Notices</span>
        <a href="../pages/notice-board.php" class="nw-viewall">View All <i class="fa fa-arrow-right"></i></a>
      </div>
      <?php if (empty($notices)): ?>
      <div class="nw-empty"><i class="fa fa-check-circle"></i> No new notices</div>
      <?php else: ?>
      <div class="nw-list">
        <?php foreach ($notices as $n):
            $aud = $n['audience'] ?? 'all';
            $pri = $n['priority'] ?? 'normal';
            $ac  = $audColors[$aud] ?? $audColors['all'];
            $pc  = $priColors[$pri] ?? $priColors['normal'];
            $isEmergency = ($aud === 'emergency' || $pri === 'urgent');
        ?>
        <div class="nw-item <?=$isEmergency?'nw-emergency':''?>">
          <div class="nw-item-left">
            <span class="nw-aud-dot" style="background:<?=$ac['color']?>" title="<?=ucfirst($aud)?>"></span>
          </div>
          <div class="nw-item-body">
            <div class="nw-item-title"><?=htmlspecialchars($n['title'])?></div>
            <div class="nw-item-meta">
              <span class="nw-badge" style="background:<?=$ac['bg']?>;color:<?=$ac['color']?>">
                <i class="fa <?=$ac['icon']?>"></i> <?=ucfirst($aud)?>
              </span>
              <?php if ($pri !== 'normal'): ?>
              <span class="nw-badge" style="background:<?=$pc['bg']?>;color:<?=$pc['color']?>">
                <?=ucfirst($pri)?>
              </span>
              <?php endif; ?>
              <span class="nw-date"><i class="fa fa-calendar"></i> <?=date('M d', strtotime($n['notice_date'] ?? $n['created_at']))?></span>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
    <style>
    .nw-wrap{background:white;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.08);overflow:hidden}
    .nw-header{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:2px solid #f0f0f0}
    .nw-title{font-size:16px;font-weight:700;color:<?=$accentColor?>;display:flex;align-items:center;gap:8px}
    .nw-viewall{font-size:12px;color:<?=$accentColor?>;text-decoration:none;font-weight:600;display:flex;align-items:center;gap:4px}
    .nw-viewall:hover{opacity:.75}
    .nw-empty{padding:24px;text-align:center;color:#aaa;font-size:13px;display:flex;align-items:center;justify-content:center;gap:8px}
    .nw-list{display:flex;flex-direction:column}
    .nw-item{display:flex;align-items:flex-start;gap:10px;padding:12px 20px;border-bottom:1px solid #f5f5f5;transition:.2s}
    .nw-item:last-child{border-bottom:none}
    .nw-item:hover{background:#f8f9fa}
    .nw-item.nw-emergency{background:#fff5f5;border-left:3px solid #dc3545}
    .nw-item-left{padding-top:5px;flex-shrink:0}
    .nw-aud-dot{width:9px;height:9px;border-radius:50%;display:block}
    .nw-item-body{flex:1;min-width:0}
    .nw-item-title{font-size:13px;font-weight:600;color:#333;margin-bottom:5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .nw-item-meta{display:flex;align-items:center;gap:6px;flex-wrap:wrap}
    .nw-badge{padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;display:inline-flex;align-items:center;gap:3px}
    .nw-date{font-size:10px;color:#aaa;display:flex;align-items:center;gap:3px}
    </style>
    <?php
}
?>
