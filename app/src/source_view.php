<?php
require_once __DIR__ . '/core/bootstrap.php';
require_login();

$id    = (string) ($_GET['id'] ?? '');
$level = (string) ($_GET['level'] ?? 'low');
$diff  = isset($_GET['diff']);

$c = Challenges::get($id);
if (!$c) {
    render_header('Kaynak');
    echo '<div class="wrap"><p class="err">Bilinmeyen lab.</p></div>';
    render_footer();
    exit;
}

// Güvenli dosya çözümleme — KASITLI zafiyet DEĞİL (path traversal'a izin vermez).
$srcDir = APP_ROOT . '/' . $c['path'] . 'source/';
function read_src(string $dir, string $level): string
{
    $level = preg_replace('/[^a-z]/', '', $level);                 // sadece harf
    $allowed = ['low', 'medium', 'high', 'impossible'];
    if (!in_array($level, $allowed, true)) {
        return '(geçersiz seviye)';
    }
    $file = $dir . $level . '.php';
    return is_file($file) ? (string) file_get_contents($file) : '(kaynak bulunamadı)';
}

render_header('Kaynak: ' . ($c['name'] ?? $id));
echo '<div class="wrap srcview">';
echo '<p><a href="/' . e($c['path']) . 'index.php">&larr; lab\'a dön</a></p>';

if ($diff) {
    if (!in_array($level, SecurityLevel::LEVELS, true)) {
        $level = security_level();
    }
    echo '<h1>Yan-yana diff: <span class="lvl lvl-' . e($level) . '">' . e($level) . '</span> ↔ '
       . '<span class="lvl lvl-impossible">impossible</span></h1>';
    echo '<div class="diffwrap">';
    echo '<div><h3>' . e($level) . ' (zafiyetli)</h3><pre>' . e(read_src($srcDir, $level)) . '</pre></div>';
    echo '<div><h3>impossible (güvenli)</h3><pre>' . e(read_src($srcDir, 'impossible')) . '</pre></div>';
    echo '</div>';
} else {
    echo '<h1>Kaynak: ' . e($c['name'] ?? $id) . ' — <span class="lvl lvl-' . e($level) . '">' . e($level) . '</span></h1>';
    echo '<pre>' . e(read_src($srcDir, $level)) . '</pre>';
}

echo '</div>';
render_footer();
