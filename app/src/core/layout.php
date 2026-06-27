<?php
/** layout.php — "Security Console" sayfa iskeleti, üst bar, severity segmented control,
 *  kategori-kodlu sidebar ve zafiyet sayfası sarmalayıcısı. */
declare(strict_types=1);

/** Aktif sayfanın yolu (severity bar'ın geri dönüşü için). */
function current_path(): string
{
    return $_SERVER['REQUEST_URI'] ?? '/index.php';
}

function nav_active(string $needle): string
{
    return str_contains($_SERVER['SCRIPT_NAME'] ?? '', $needle) ? ' class="active"' : '';
}

function render_header(string $title = 'VulnWeb'): void
{
    $level = security_level();
    $user = current_user();
    ?>
<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title><?= e($title) ?> — VulnWeb</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600;700&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<div class="caution"><span class="dot"></span> <b>INTENTIONALLY VULNERABLE</b> — localhost only · do not expose to any network · no real data</div>
<header class="topbar">
  <a class="brand" href="/index.php"><span class="glyph">V</span> VulnWeb <span class="sub">sec-lab</span></a>
  <nav class="topnav">
    <a href="/index.php"<?= nav_active('index') ?>>Dashboard</a>
    <a href="/scoreboard.php"<?= nav_active('scoreboard') ?>>Scoreboard</a>
    <a href="/logs.php"<?= nav_active('logs') ?>>Logs</a>
    <a href="/setup.php"<?= nav_active('setup') ?>>Reset</a>
  </nav>
  <span class="spacer"></span>
  <?php if ($user): ?>
    <span class="who"><b><?= e($user['username']) ?></b> · <span class="role"><?= e($user['role']) ?></span></span>
    <a class="btn-ghost" href="/logout.php">Sign out</a>
  <?php endif; ?>
</header>
<?php render_sevbar($level); ?>
<?php
}

/** Üst severity segmented control. */
function render_sevbar(string $level): void
{
    $desc = SecurityLevel::label($level);
    $back = e(current_path());
    echo '<form class="sevbar" method="post" action="/security.php">';
    echo '<input type="hidden" name="back" value="' . $back . '">';
    echo '<span class="lab">Severity</span><div class="seg">';
    foreach (SecurityLevel::LEVELS as $lvl) {
        $on = $lvl === $level ? ' on s-' . $lvl : '';
        echo '<button class="seg-btn' . $on . '" name="security" value="' . e($lvl) . '">' . strtoupper(e($lvl)) . '</button>';
    }
    echo '</div>';
    echo '<span class="desc"><b>' . e($desc) . '</b></span>';
    echo '</form>';
}

function render_footer(): void
{
    echo '<footer class="foot">VulnWeb · OWASP Top 10:2025 training lab · localhost only · MIT</footer>';
    echo '</body></html>';
}

/** Flash: bir challenge çözüldüğünde toast. */
function render_flash(): void
{
    $flash = Scoreboard::takeFlash();
    if ($flash) {
        echo '<div class="toast solved">▸ Solved: <strong>' . e($flash['id']) . '</strong> &nbsp; flag <code>' . e($flash['flag']) . '</code></div>';
    }
}

/** OWASP kodu (A01..A10) -> kanonik kategori adı. */
function owasp_name(string $code): string
{
    static $m = [
        'A01' => 'Broken Access Control',
        'A02' => 'Security Misconfiguration',
        'A03' => 'Software Supply Chain Failures',
        'A04' => 'Cryptographic Failures',
        'A05' => 'Injection',
        'A06' => 'Insecure Design',
        'A07' => 'Authentication Failures',
        'A08' => 'Software or Data Integrity Failures',
        'A09' => 'Security Logging & Alerting',
        'A10' => 'Mishandling of Exceptional Conditions',
    ];
    return $m[$code] ?? $code;
}

/** Lab'ları OWASP koduna göre gruplar (A01..A10), koda göre sıralı. */
function sidebar_groups(): array
{
    $groups = [];
    foreach (Challenges::all() as $c) {
        $code = strtok((string) ($c['category'] ?? 'A00'), ':');
        $groups[$code][] = $c;
    }
    uksort($groups, 'strcmp');
    return $groups;   // anahtar = "A01".."A10"
}

/** Sol menü: OWASP kategorisine göre gruplanmış, kodlu. */
function render_sidebar(?string $activeId = null): void
{
    $solved = Scoreboard::solvedIds();
    echo '<aside class="sidebar"><div class="s-head">Vulnerability Labs</div>';
    foreach (sidebar_groups() as $code => $items) {
        echo '<div class="cat">' . e($code) . ' · ' . e(mb_strtoupper(owasp_name($code))) . '</div><ul>';
        foreach ($items as $c) {
            $active = $c['id'] === $activeId ? ' class="active"' : '';
            $tags = '';
            if (!empty($c['dangerZone'])) {
                $tags .= '<span class="t-dz" title="Danger Zone (RCE/file/internal)">DZ</span>';
            }
            if (in_array($c['id'], $solved, true)) {
                $tags .= '<span class="t-ok" title="solved"></span>';
            }
            echo '<li' . $active . '><a href="/' . e($c['path']) . 'index.php">'
               . '<span class="nm">' . e($c['name']) . '</span>'
               . '<span class="tags">' . $tags . '</span></a></li>';
        }
        echo '</ul>';
    }
    echo '</aside>';
}

/** index.php tek satırda çağırır: aktif seviyeye göre source/<level>.php çalıştır + sarmala. */
function vuln_dispatch(string $__vuln_id): void
{
    $__lvl = security_level();
    if (!in_array($__lvl, SecurityLevel::LEVELS, true)) {
        $__lvl = 'low';
    }
    $__c = Challenges::get($__vuln_id);
    $__dir = APP_ROOT . '/' . ($__c['path'] ?? ('vulnerabilities/' . $__vuln_id . '/'));
    ob_start();
    require $__dir . 'source/' . $__lvl . '.php';
    $__body = ob_get_clean();
    render_vuln_page($__vuln_id, $__body);
}

/** Bir zafiyet sayfasını sarmalar. */
function render_vuln_page(string $id, string $bodyHtml): void
{
    $c = Challenges::get($id);
    $name = $c['name'] ?? $id;
    render_header($name);

    echo '<div class="shell">';
    render_sidebar($id);
    echo '<main class="content">';
    render_flash();

    // Başlık + chip'ler
    $diff = (int) ($c['difficulty'] ?? 1);
    $squares = '';
    for ($i = 1; $i <= 6; $i++) {
        $squares .= '<i class="' . ($i <= $diff ? 'on' : '') . '"></i>';
    }
    $solvedPill = Scoreboard::isSolved($id)
        ? '<span class="solved-pill">✓ SOLVED</span>' : '';
    echo '<div class="page-h"><div>';
    echo '<h1>' . e($name) . '</h1>';
    echo '<div class="chips">';
    echo '<span class="chip chip-owasp">' . e($c['category'] ?? '') . '</span>';
    if (!empty($c['cwe'])) {
        echo '<span class="chip chip-cwe">' . e($c['cwe']) . '</span>';
    }
    echo '<span class="diff" title="difficulty">' . $squares . '</span>';
    echo '</div></div>' . $solvedPill . '</div>';

    if (!empty($c['dangerZone'])) {
        echo '<div class="dz-note"><span class="k">DZ</span><span><strong>Danger Zone.</strong> '
           . 'Bu lab gerçek RCE / dosya / iç-ağ işlemleri içerir. Yalnızca izole localhost ortamında çalıştır.</span></div>';
    }

    if ($c) {
        echo '<div class="panel objective"><span class="p-label">Objective</span>' . e($c['objective'] ?? '') . '</div>';
        if (!empty($c['hints'])) {
            echo '<details class="hints"><summary>Hints (' . count($c['hints']) . ')</summary><ol>';
            foreach ($c['hints'] as $h) {
                echo '<li>' . e($h) . '</li>';
            }
            echo '</ol></details>';
        }
    }

    echo '<div class="lab">' . $bodyHtml . '</div>';

    echo '<div class="vuln-actions">';
    echo '<a class="btn" href="/source_view.php?id=' . urlencode($id) . '&level=' . urlencode(security_level()) . '">View source</a>';
    echo '<a class="btn" href="/source_view.php?id=' . urlencode($id) . '&level=impossible">Secure (impossible)</a>';
    echo '<a class="btn" href="/source_view.php?id=' . urlencode($id) . '&diff=1">Side-by-side diff</a>';
    if (!empty($c['mitigationUrl'])) {
        echo '<a class="btn" target="_blank" rel="noopener" href="' . e($c['mitigationUrl']) . '">Mitigation ↗</a>';
    }
    echo '</div>';

    echo '</main></div>';   // .content + .shell
    render_footer();
}
