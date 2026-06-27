<?php
/**
 * SSRF — HIGH
 * KASITLI BOZUK SAVUNMA: host resolve edilip private/loopback IP'ler reddedilir
 * AMA şema kısıtlanmaz → file:// bypass eder.
 * Bypass:  file:///etc/passwd
 */
$url = $_REQUEST['url'] ?? '';

echo '<p>"Savunma": host\'un IP\'si çözülür, özel/loopback aralıklar reddedilir.</p>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: ya HTTP değil... şema kontrolü var mı?  <code>file:///etc/passwd</code></p>';
echo '<form method="post"><input type="text" name="url" size="60" value="' . e($url) . '"> <button type="submit">Getir</button></form>';

if ($url !== '') {
    ids_log('ssrf', $url);
    $host = parse_url($url, PHP_URL_HOST);
    $blocked = false;
    if ($host) {
        $ip = gethostbyname($host);
        // private/loopback/link-local reddet
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            $blocked = true;
        }
    }
    // BOZUK: host yoksa (file://) kontrol atlanır; şema allowlist'i yok
    if ($blocked) {
        echo '<div class="result detected">Reddedildi: özel/iç IP (' . e($host) . ').</div>';
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
