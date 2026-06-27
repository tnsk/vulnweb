<?php
/**
 * Command Injection — IMPOSSIBLE (güvenli referans)
 * - Sıkı IPv4 allowlist doğrulaması (filter_var + regex)
 * - Argümanı escapeshellarg ile geç (shell metakarakterleri etkisiz)
 */
$ip = $_REQUEST['ip'] ?? '';

echo '<form method="post">IP: <input type="text" name="ip" value="" placeholder="127.0.0.1"> <button type="submit">Ping</button></form>';

if ($ip !== '') {
    // ----- GÜVENLİ: katı doğrulama + argüman escaping -----
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        echo '<div class="result">Geçersiz IPv4 adresi.</div>';
    } else {
        $cmd = 'ping -c 4 ' . escapeshellarg($ip);
        $out = (string) shell_exec($cmd . ' 2>&1');
        echo '<div class="result"><strong>$ ' . e($cmd) . "</strong>\n\n" . e($out) . '</div>';
    }
    // ------------------------------------------------------
}
