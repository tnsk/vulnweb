<?php
require_once __DIR__ . '/core/bootstrap.php';
require_login();

$msg = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'reset') {
    DB::runLabSql();
    foreach (glob(APP_ROOT . '/hackable/uploads/*') ?: [] as $f) {
        if (is_file($f) && !in_array(basename($f), ['.gitkeep', '.gitignore'], true)) {
            @unlink($f);
        }
    }
    $msg = 'Database rebuilt and re-seeded · uploads cleared.';
}

render_header('Reset');
echo '<div class="wrap">';
render_flash();
echo '<div class="page-h"><div><h1>Setup / Reset</h1>'
   . '<div class="chips"><span class="chip">deterministic state</span></div></div></div>';
if ($msg) {
    echo '<div class="toast solved">▸ ' . e($msg) . '</div>';
}

echo '<p style="color:var(--ink-2);max-width:64ch">Bu işlem tüm lab tablolarını düşürüp yeniden oluşturur, '
   . 'sahte seed verisini yükler; çözüm durumun ve yüklenen dosyalar sıfırlanır.</p>';
echo '<form method="post" style="margin:1rem 0"><input type="hidden" name="action" value="reset">'
   . '<button type="submit">Create / Reset Database</button></form>';

echo '<div class="panel"><span class="p-label">CLI equivalents</span>'
   . '<div class="mono" style="color:var(--ink-2);font-size:12px;line-height:1.9">'
   . 'docker compose exec app php bin/reset.php<br>'
   . 'docker compose down -v &amp;&amp; docker compose up -d &nbsp;<span style="color:var(--ink-3)"># hard reset</span></div></div>';

echo '</div>';
render_footer();
