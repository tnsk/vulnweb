<?php
/**
 * File Upload — MEDIUM
 * KASITLI BOZUK SAVUNMA: istemcinin gönderdiği Content-Type'a (MIME) güvenir.
 * Bypass: isteği Burp/curl ile gönder, Content-Type: image/jpeg yap, ad shell.php kalsın.
 */
$uploadDir = APP_ROOT . '/hackable/uploads/';
$webBase   = '/hackable/uploads/';

echo '<form method="post" enctype="multipart/form-data">';
echo '  Dosya seç (sadece resim): <input type="file" name="uploaded"> <button type="submit">Yükle</button>';
echo '</form>';

if (!empty($_FILES['uploaded']['name'])) {
    $name = basename($_FILES['uploaded']['name']);
    $type = $_FILES['uploaded']['type'] ?? '';      // BOZUK: istemci MIME'i
    ids_log('file_upload', "$name ($type)");

    if (!in_array($type, ['image/jpeg', 'image/png', 'image/gif'], true)) {
        echo '<div class="result detected">Sadece resim dosyaları (MIME kontrolü).</div>';
        return;
    }
    $target = $uploadDir . $name;
    if (move_uploaded_file($_FILES['uploaded']['tmp_name'], $target)) {
        $url = $webBase . rawurlencode($name);
        echo '<div class="result">Yüklendi: <a href="' . e($url) . '">' . e($url) . '</a></div>';
        if (preg_match('/\.(php|phtml|pht|php5|phar)$/i', $name)) {
            mark_solved('file_upload');
            echo '<div class="result">✅ MIME spoofing ile webshell yüklendi — çözüldü! <code>' . e($url) . '?c=id</code></div>';
        }
    }
}
