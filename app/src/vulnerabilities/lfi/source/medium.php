<?php
/**
 * LFI/RFI — MEDIUM
 * KASITLI BOZUK SAVUNMA: '../', 'http://', 'https://' non-recursive str_replace.
 * Bypass (iç içe):  ?page=....//....//....//....//etc/passwd
 * Wrapper:          ?page=php://filter/convert.base64-encode/resource=index.php
 */
$raw = $_GET['page'] ?? 'pages/home.php';
$page = str_replace(['http://', 'https://', '../', '..\\'], '', $raw);

echo '<form method="get">page: <input type="text" name="page" value="' . e($raw) . '" size="50"> <button type="submit">Yükle</button></form>';

ids_log('lfi', $raw);
echo '<div class="result">';
ob_start();
include $page;   // non-recursive filtre aşılabilir
$included = ob_get_clean();
echo $included === '' ? '(boş)' : $included;
echo '</div>';

if (preg_match('/root:.*:0:0:/', $included)) {
    mark_solved('lfi');
    echo '<div class="result">✅ Filtre aşıldı (LFI) — challenge çözüldü!</div>';
}
