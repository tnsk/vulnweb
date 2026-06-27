<?php
require_once __DIR__ . '/core/bootstrap.php';
require_login();

render_header('Dashboard');
echo '<div class="wrap">';
render_flash();

$user   = current_user();
$solved = Scoreboard::solvedIds();
$all    = Challenges::all();
$total  = count($all);
$done   = count($solved);
$pct    = $total ? round($done / $total * 100) : 0;
$cats   = count(sidebar_groups());

echo '<div class="page-h"><div>';
echo '<h1>Dashboard</h1>';
echo '<div class="chips"><span class="chip">operator <span style="color:var(--accent)">' . e($user['username']) . '</span></span>'
   . '<span class="chip">active severity</span><span class="lvl lvl-' . e(security_level()) . '">' . e(security_level()) . '</span></div>';
echo '</div>';
echo '<div class="kpis">'
   . '<div class="kpi"><div class="n">' . $done . '<span style="color:var(--ink-3)">/' . $total . '</span></div><div class="l">solved</div></div>'
   . '<div class="kpi"><div class="n">' . $cats . '<span style="color:var(--ink-3)">/10</span></div><div class="l">owasp cat.</div></div>'
   . '</div>';
echo '</div>';

echo '<div class="panel" style="display:flex;align-items:center;gap:1rem">'
   . '<span class="p-label" style="margin:0">Progress</span>'
   . '<div class="meter"><i style="width:' . $pct . '%"></i></div>'
   . '<span class="mono" style="color:var(--ink-2);font-size:12px">' . $pct . '%</span></div>';

// Lab kartları — OWASP koduna göre gruplu
foreach (sidebar_groups() as $code => $items) {
    echo '<div class="cat" style="font-family:var(--mono);font-size:11px;letter-spacing:.04em;color:var(--accent);margin:1.4rem 0 .2rem">'
       . e($code) . ' · ' . e(mb_strtoupper(owasp_name($code))) . '</div>';
    echo '<div class="card-grid">';
    foreach ($items as $c) {
        $is = in_array($c['id'], $solved, true);
        $diff = (int) ($c['difficulty'] ?? 1);
        $sq = '';
        for ($i = 1; $i <= 6; $i++) {
            $sq .= '<i class="' . ($i <= $diff ? 'on' : '') . '"></i>';
        }
        echo '<a class="card" href="/' . e($c['path']) . 'index.php">';
        echo '<h4>' . e($c['name']);
        if (!empty($c['dangerZone'])) {
            echo ' <span class="t-dz">DZ</span>';
        }
        if ($is) {
            echo ' <span class="t-ok" style="margin-left:auto"></span>';
        }
        echo '</h4>';
        echo '<div class="meta"><span class="chip chip-cwe">' . e($c['cwe'] ?? '') . '</span>'
           . '<span class="diff">' . $sq . '</span></div>';
        echo '<p>' . e($c['objective'] ?? '') . '</p>';
        echo '</a>';
    }
    echo '</div>';
}

echo '</div>';
render_footer();
