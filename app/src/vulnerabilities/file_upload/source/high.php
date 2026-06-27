<?php
/**
 * File Upload — HIGH
 * KASITLI BOZUK SAVUNMA: uzantı blocklist'i ('php','php3','php4') ama '.phtml/.pht' atlanmış,
 * ayrıca getimagesize ile resim doğrular -> polyglot (GIF89a + PHP) ile aşılır.
 * Bypass: shell.phtml (GIF89a;<?php system($_GET['c']); ?>)
 */
$uploadDir = APP_ROOT . '/hackable/uploads/';
$webBase   = '/hackable/uploads/';

echo '<form method="post" enctype="multipart/form-data">';
echo '  Dosya seç: <input type="file" name="uploaded"> <button type="submit">Yükle</button>';
echo '</form>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: uzantı blocklist\'i eksik; '
   . 'GIF89a sihirli baytıyla başlayan bir <code>.phtml</code> polyglot dene.</p>';

if (!empty($_FILES['uploaded']['name'])) {
    $name = basename($_FILES['uploaded']['name']);
    ids_log('file_upload', $name);
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

    // BOZUK: eksik blocklist (.phtml/.pht yok)
    $blocked = ['php', 'php3', 'php4', 'phps'];
    if (in_array($ext, $blocked, true)) {
        echo '<div class="result detected">Bu uzantı yasak.</div>';
        return;
    }
    // BOZUK: getimagesize polyglot ile kandırılabilir
    if (@getimagesize($_FILES['uploaded']['tmp_name']) === false) {
        echo '<div class="result detected">Geçerli bir resim değil (getimagesize).</div>';
        return;
    }
    $target = $uploadDir . $name;
    if (move_uploaded_file($_FILES['uploaded']['tmp_name'], $target)) {
        $url = $webBase . rawurlencode($name);
        echo '<div class="result">Yüklendi: <a href="' . e($url) . '">' . e($url) . '</a></div>';
        if (preg_match('/\.(phtml|pht|php5|phar)$/i', $name)) {
            mark_solved('file_upload');
            echo '<div class="result">✅ Polyglot + alternatif uzantı ile aşıldı — çözüldü! <code>' . e($url) . '?c=id</code></div>';
        }
    }
}
