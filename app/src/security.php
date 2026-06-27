<?php
require_once __DIR__ . '/core/bootstrap.php';
require_login();

$msg = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (SecurityLevel::set((string) ($_POST['security'] ?? ''))) {
        // Üst bardaki segmented control'den geldiyse, gelinen sayfaya geri dön.
        $back = (string) ($_POST['back'] ?? '');
        if ($back !== '' && $back[0] === '/' && !str_starts_with($back, '//')) {
            header('Location: ' . $back);
            exit;
        }
        $msg = 'Severity set to ' . security_level();
    }
}

render_header('Severity');
echo '<div class="wrap">';
render_flash();
echo '<div class="page-h"><div><h1>Severity Level</h1>'
   . '<div class="chips"><span class="chip">active</span><span class="lvl lvl-' . e(security_level()) . '">' . e(security_level()) . '</span></div></div></div>';
if ($msg) {
    echo '<div class="toast solved">▸ ' . e($msg) . '</div>';
}

echo '<p style="color:var(--ink-2);max-width:62ch">Severity, üstteki segmented control\'den her sayfada değiştirilebilir. '
   . 'Her zafiyet, seçilen seviyeye göre <code>source/&lt;level&gt;.php</code> dosyasını yükler — '
   . 'aynı açığın savunması seviye yükseldikçe güçlenir.</p>';

$rows = [
    ['low', 'Savunma yok — naif, savunmasız kod.', 'En çok açık'],
    ['medium', 'Naif / bypass\'lanabilir savunma (klasik tuzak).', 'Filtre bypass'],
    ['high', 'Daha güçlü ama yine bypass\'lanabilir savunma.', 'Zor ama mümkün'],
    ['impossible', 'Gerçek güvenli implementasyon — diff hedefi.', 'Güvenli referans'],
];
echo '<table style="margin-top:1rem"><thead><tr><th>Level</th><th>Açıklama</th><th>Karakter</th></tr></thead><tbody>';
foreach ($rows as [$lvl, $d, $tag]) {
    $cur = $lvl === security_level() ? ' style="background:var(--panel)"' : '';
    echo '<tr' . $cur . '><td><span class="lvl lvl-' . e($lvl) . '">' . e($lvl) . '</span></td>'
       . '<td style="color:var(--ink-2)">' . e($d) . '</td><td style="color:var(--ink-3)">' . e($tag) . '</td></tr>';
}
echo '</tbody></table>';

echo '</div>';
render_footer();
