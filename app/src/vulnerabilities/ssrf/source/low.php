<?php
/**
 * SSRF — LOW
 * KASITLI ZAFİYET: kullanıcı URL'i sunucu tarafından getirilir.
 * Sömürü:  file:///etc/passwd  ·  http://adminer:8080 (yalnızca iç ağdan erişilir)
 *          http://169.254.169.254/latest/meta-data/ (cloud metadata)
 */
$url = $_REQUEST['url'] ?? '';

echo '<p>Bir URL gir; sunucu onu senin için getirir (URL önizleyici):</p>';
echo '<p style="color:var(--muted);font-size:.85rem">Dene: <code>file:///etc/passwd</code> · '
   . '<code>http://adminer:8080</code> (yalnızca container ağından erişilebilen iç servis)</p>';
echo '<form method="post"><input type="text" name="url" size="60" value="' . e($url) . '"> <button type="submit">Getir</button></form>';

if ($url !== '') {
    ids_log('ssrf', $url);
    // ----- ZAFİYETLİ: herhangi bir URL getirilir -----
    $body = @file_get_contents($url);
    // -------------------------------------------------
    if ($body === false) {
        echo '<div class="result detected">Getirilemedi.</div>';
    } else {
        echo '<div class="result">' . e(substr($body, 0, 1500)) . '</div>';
        ssrf_detect($body);
    }
}
