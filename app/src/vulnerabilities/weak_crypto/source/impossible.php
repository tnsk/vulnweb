<?php
/**
 * Weak Crypto — IMPOSSIBLE (güvenli referans)
 * token = base64(rol) . "." . HMAC-SHA256(base64(rol), serverSecret)
 * serverSecret oturuma özel rastgele (random_bytes) ve sunucuda kalır → forge edilemez.
 * Doğrulama hash_equals ile sabit-zamanlı.
 */
if (empty($_SESSION['wc_secret'])) {
    $_SESSION['wc_secret'] = bin2hex(random_bytes(32));
}
$secret = $_SESSION['wc_secret'];
$data   = base64_encode('guest');
$valid  = $data . '.' . hash_hmac('sha256', $data, $secret);
$token  = $_REQUEST['token'] ?? $valid;

echo '<p>Token formatı: <code>base64(rol).HMAC-SHA256(rol, serverSecret)</code> — anahtar sunucuda, rastgele.</p>';
echo '<form method="post"><input type="text" name="token" size="70" value="' . e($valid) . '"> <button type="submit">Uygula</button></form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $parts = explode('.', (string) $_REQUEST['token'], 2);
    $ok = count($parts) === 2
        && hash_equals(hash_hmac('sha256', $parts[0], $secret), $parts[1]);
    if ($ok) {
        $role = (string) base64_decode($parts[0], true);
        echo '<div class="result ok">Doğrulandı, rol: ' . e($role) . ' (forge edilemez).</div>';
    } else {
        echo '<div class="result">Geçersiz token (HMAC tutmadı).</div>';
    }
}
