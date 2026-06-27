<?php
/**
 * SSRF — IMPOSSIBLE (güvenli referans)
 * - Şema allowlist'i (yalnızca http/https)
 * - Host IP'ye çözülür; private/loopback/link-local reddedilir
 * - cURL ile redirect KAPALI (CURLOPT_FOLLOWLOCATION=false); çözülen IP pin'lenebilir
 */
$url = $_REQUEST['url'] ?? '';

echo '<p>Güvenli getirici: yalnızca http/https + public IP + redirect kapalı.</p>';
echo '<form method="post"><input type="text" name="url" size="60" value=""> <button type="submit">Getir</button></form>';

if ($url !== '') {
    $parts = parse_url($url);
    $scheme = strtolower($parts['scheme'] ?? '');
    $host = $parts['host'] ?? '';

    if (!in_array($scheme, ['http', 'https'], true) || $host === '') {
        echo '<div class="result">Reddedildi: yalnızca http/https.</div>';
        return;
    }
    $ip = gethostbyname($host);
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
        echo '<div class="result">Reddedildi: özel/iç ağ adresi (' . e($ip) . ').</div>';
        return;
    }
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,   // redirect ile iç ağa kaçışı engelle
        CURLOPT_TIMEOUT        => 5,
    ]);
    $body = curl_exec($ch);
    curl_close($ch);
    echo '<div class="result">' . e(substr((string) $body, 0, 1500)) . '</div>';
}
