<?php
require_once __DIR__ . '/core/bootstrap.php';
require_login();

$solved = Scoreboard::solvedIds();
$all = Challenges::all();
$done = count($solved);
$total = count($all);
$pct = $total ? round($done / $total * 100) : 0;

$flags = [];
$pdo = DB::pdo();
$stmt = $pdo->prepare('SELECT challenge_id, flag FROM scores WHERE user_id = ? AND solved = 1');
$stmt->execute([$_SESSION['uid'] ?? 0]);
foreach ($stmt->fetchAll() as $r) {
    $flags[$r['challenge_id']] = $r['flag'];
}

render_header('Scoreboard');
echo '<div class="wrap">';
render_flash();
echo '<div class="page-h"><div><h1>Scoreboard</h1>'
   . '<div class="chips"><span class="chip">' . $done . ' / ' . $total . ' solved</span></div></div>'
   . '<div class="kpi"><div class="n">' . $pct . '<span style="color:var(--ink-3)">%</span></div><div class="l">complete</div></div></div>';

echo '<div class="panel" style="display:flex;align-items:center;gap:1rem"><span class="p-label" style="margin:0">Progress</span>'
   . '<div class="meter"><i style="width:' . $pct . '%"></i></div></div>';

echo '<table style="margin-top:1rem"><thead><tr><th></th><th>Lab</th><th>Category</th><th>CWE</th><th>Diff</th><th>Flag</th></tr></thead><tbody>';
foreach ($all as $c) {
    $is = in_array($c['id'], $solved, true);
    echo '<tr>';
    echo '<td>' . ($is ? '<span class="t-ok"></span>' : '<span style="color:var(--ink-4)">○</span>') . '</td>';
    echo '<td><a href="/' . e($c['path']) . 'index.php">' . e($c['name']) . '</a>'
       . (!empty($c['dangerZone']) ? ' <span class="t-dz">DZ</span>' : '') . '</td>';
    echo '<td><span class="chip chip-owasp">' . e(strtok($c['category'], ':')) . '</span></td>';
    echo '<td class="mono" style="color:var(--ink-2)">' . e($c['cwe'] ?? '') . '</td>';
    echo '<td class="stars">' . str_repeat('▪', (int) ($c['difficulty'] ?? 1)) . '</td>';
    echo '<td>' . ($is ? '<code>' . e($flags[$c['id']] ?? '') . '</code>' : '<span style="color:var(--ink-4)">—</span>') . '</td>';
    echo '</tr>';
}
echo '</tbody></table>';

echo '</div>';
render_footer();
