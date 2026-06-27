<?php
/**
 * LFI/RFI — HIGH
 * KASITLI BOZUK SAVUNMA: 'pages/' ön-eki zorunlu kılınır ama traversal serbest.
 * Bypass:  ?page=pages/../../../../etc/passwd
 */
$page = $_GET['page'] ?? 'pages/home.php';

echo '<form method="get">page: <input type="text" name="page" value="' . e($page) . '" size="50"> <button type="submit">Yükle</button></form>';

ids_log('lfi', $page);

// BOZUK: sadece ön-ek kontrolü; '../' engellenmiyor
if (strncmp($page, 'pages/', 6) !== 0) {
    echo '<div class="result">ERROR: yalnızca pages/ altındaki dosyalar.</div>';
    return;
}

echo '<div class="result">';
ob_start();
include $page;   // pages/../../../etc/passwd ön-eki geçer
$included = ob_get_clean();
echo $included === '' ? '(boş)' : $included;
echo '</div>';

if (preg_match('/root:.*:0:0:/', $included)) {
    mark_solved('lfi');
    echo '<div class="result">✅ Ön-ek kontrolü traversal ile aşıldı — challenge çözüldü!</div>';
}
