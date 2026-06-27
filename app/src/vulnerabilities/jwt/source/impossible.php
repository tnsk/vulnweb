<?php
/**
 * JWT — IMPOSSIBLE (güvenli referans)
 * - Beklenen algoritma sunucuda SABİT (HS256); header'daki alg'e güvenilmez.
 * - 'none' reddedilir; secret ≥256-bit rastgele.
 * - hash_equals ile sabit-zamanlı doğrulama (üretimde exp/aud/iss de doğrulanır).
 */
if (empty($_SESSION['jwt_secret'])) {
    $_SESSION['jwt_secret'] = bin2hex(random_bytes(32));
}
$secret = $_SESSION['jwt_secret'];
$sample = jwt_make(['user' => current_user()['username'] ?? 'guest', 'role' => 'user'], $secret);
$jwt = $_REQUEST['jwt'] ?? $sample;

echo '<p>Güvenli: algoritma sunucuda sabit (HS256), none reddedilir, secret rastgele.</p>';
echo '<form method="post"><input name="jwt" size="70" value="' . e($sample) . '"> <button type="submit">Doğrula</button></form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alg = (string) (jwt_header($jwt)['alg'] ?? '');
    // Header'daki alg'e GÜVENME: yalnızca beklenen HS256'yı kabul et
    if ($alg !== 'HS256' || !jwt_verify_hs256($jwt, $secret)) {
        echo '<div class="result">Token reddedildi (geçersiz imza/algoritma).</div>';
    } else {
        $role = (string) (jwt_payload($jwt)['role'] ?? '');
        echo '<div class="result ok">Doğrulandı, rol: ' . e($role) . ' (forge edilemez).</div>';
    }
}
