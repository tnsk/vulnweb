<?php
require_once __DIR__ . '/core/bootstrap.php';
require_login();

render_header('Logs');
echo '<div class="wrap">';
render_flash();
echo '<div class="page-h"><div><h1>Exploit Logs</h1>'
   . '<div class="chips"><span class="chip">blue-team · IDS signals</span></div></div></div>';
echo '<p style="color:var(--ink-2);max-width:64ch">Basit kural-tabanlı IDS, gönderilen payload\'larda saldırı imzası '
   . 'ararsa <span class="detected">DETECTED</span> olarak işaretler. Bir lab\'da saldır, sonra burada izini incele.</p>';

$rows = Logger::recent(150);
echo '<table style="margin-top:.8rem"><thead><tr><th>Time</th><th>IP</th><th>Lab</th><th>Payload</th><th>IDS</th></tr></thead><tbody>';
foreach ($rows as $r) {
    echo '<tr>';
    echo '<td class="mono" style="color:var(--ink-3)">' . e($r['ts']) . '</td>';
    echo '<td class="mono" style="color:var(--ink-2)">' . e($r['ip']) . '</td>';
    echo '<td>' . e($r['challenge_id']) . '</td>';
    echo '<td><code>' . e(mb_substr((string) $r['payload'], 0, 160)) . '</code></td>';
    echo '<td>' . ($r['detected'] ? '<span class="detected mono">⚠ DETECTED</span>' : '<span style="color:var(--ink-4)">—</span>') . '</td>';
    echo '</tr>';
}
if (!$rows) {
    echo '<tr><td colspan="5" style="color:var(--ink-3)">Henüz log yok. Bir lab\'da payload dene.</td></tr>';
}
echo '</tbody></table>';

echo '</div>';
render_footer();
