<?php
/**
 * Fail-Open — IMPOSSIBLE (güvenli referans)
 * - Deny-by-default (fail-closed): yalnızca AÇIKÇA doğrulanmış admin token erişir.
 * - HMAC ile bütünlük (kullanıcı forge edemez); HER istisna → reddet.
 * - Allow-list rol kontrolü.
 */
if (empty($_SESSION['fo_secret'])) {
    $_SESSION['fo_secret'] = bin2hex(random_bytes(32));
}
$perm = $_GET['perm'] ?? '';
$granted = false;

try {
    $parts = explode('.', $perm, 2);
    if (count($parts) === 2
        && hash_equals(hash_hmac('sha256', $parts[0], $_SESSION['fo_secret']), $parts[1])) {
        $data = json_decode(base64_decode($parts[0], true) ?: '', true, 512, JSON_THROW_ON_ERROR);
        $granted = (($data['role'] ?? '') === 'admin');   // allow-list
    }
} catch (\Throwable $e) {
    $granted = false;   // ----- GÜVENLİ: fail-closed -----
}

echo '<p>Güvenli yönetici kapısı: deny-by-default + HMAC doğrulama.</p>';
echo '<form method="get">perm: <input name="perm" value="" size="40"> <button type="submit">Eriş</button></form>';

echo $granted
    ? '<div class="result ok">🔓 Yönetici alanı (yalnızca geçerli imzalı admin token).</div>'
    : '<div class="result">Erişim reddedildi (varsayılan: reddet).</div>';
