<?php
/**
 * SSRF — MEDIUM
 * KASITLI BOZUK SAVUNMA: '127.0.0.1' ve 'localhost' string olarak bloklanır.
 * Bypass: iç DNS adı kullan —  http://adminer:8080  (ya da decimal IP 2130706433).
 */
$url = $_REQUEST['url'] ?? '';

echo '<p>"Savunma": 127.0.0.1 / localhost bloklanır.</p>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: iç servisin DNS adıyla eriş — <code>http://adminer:8080</code></p>';
echo '<form method="post"><input type="text" name="url" size="60" value="' . e($url) . '"> <button type="submit">Getir</button></form>';

if ($url !== '') {
    ids_log('ssrf', $url);
    if (stripos($url, '127.0.0.1') !== false || stripos($url, 'localhost') !== false) {
        echo '<div class="result detected">Reddedildi: loopback adresi.</div>';
    } else {
        $body = @file_get_contents($url);
        if ($body === false) {
            echo '<div class="result detected">Getirilemedi.</div>';
        } else {
            echo '<div class="result">' . e(substr($body, 0, 1500)) . '</div>';
            ssrf_detect($body);
        }
    }
}
