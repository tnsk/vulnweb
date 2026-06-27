<?php
/**
 * File Upload — IMPOSSIBLE (güvenli referans)
 * - Uzantı ALLOWLIST + sunucu-taraflı MIME (finfo)
 * - Rastgele dosya adı (orijinal ada güvenme)
 * - Yeniden encode/boyut kontrolü (burada finfo ile yetiniyoruz)
 * - Üretimde: docroot DIŞINDA sakla + upload dizininde PHP exec kapalı
 */
$uploadDir = APP_ROOT . '/hackable/uploads/';
$webBase   = '/hackable/uploads/';

echo '<form method="post" enctype="multipart/form-data">';
echo '  Resim seç (jpg/png/gif): <input type="file" name="uploaded"> <button type="submit">Yükle</button>';
echo '</form>';

if (!empty($_FILES['uploaded']['name'])) {
    $tmp  = $_FILES['uploaded']['tmp_name'];
    $ext  = strtolower(pathinfo($_FILES['uploaded']['name'], PATHINFO_EXTENSION));
    $allowExt  = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($tmp);

    if (!isset($allowExt[$ext]) || $allowExt[$ext] !== $mime) {
        echo '<div class="result">Yalnızca gerçek jpg/png/gif kabul edilir (uzantı+MIME eşleşmeli).</div>';
        return;
    }
    // Rastgele ad, izin verilen uzantı
    $safeName = bin2hex(random_bytes(16)) . '.' . $ext;
    if (move_uploaded_file($tmp, $uploadDir . $safeName)) {
        $url = $webBase . $safeName;
        echo '<div class="result ok">Güvenli şekilde yüklendi: <a href="' . e($url) . '">' . e($url) . '</a></div>';
    }
}
