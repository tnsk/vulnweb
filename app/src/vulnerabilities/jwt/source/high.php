<?php
/**
 * JWT — HIGH
 * KASITLI BOZUK SAVUNMA: güçlü secret + HS256 doğrulama AMA kütüphane alg:none'ı da kabul eder.
 * Sömürü: header'da {"alg":"none"} kullanarak imzasız {"role":"root"} gönder.
 */
$secret = bin2hex(random_bytes(32));   // güçlü secret (forge edilemez)
$sample = jwt_make(['user' => 'guest', 'role' => 'user'], $secret);
$jwt = $_REQUEST['jwt'] ?? $sample;

echo '<p>Güçlü secret + HS256 — ama <code>alg</code> header\'ına güveniliyor.</p>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: <code>alg:none</code> dene (algoritma karışıklığı).</p>';
echo '<form method="post"><input name="jwt" size="70" value="' . e($jwt) . '"> <button type="submit">Doğrula</button></form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ids_log('jwt', $jwt);
    $alg = (string) (jwt_header($jwt)['alg'] ?? '');
    $accepted = false;
    if (strtolower($alg) === 'none') {
        $accepted = true;                     // BOZUK: imzasız kabul
    } elseif (jwt_verify_hs256($jwt, $secret)) {
        $accepted = true;
    }
    if (!$accepted) {
        echo '<div class="result detected">Token reddedildi.</div>';
    } else {
        $role = (string) (jwt_payload($jwt)['role'] ?? '');
        echo '<div class="result">Kabul edildi (alg=' . e($alg) . '), rol: <strong>' . e($role) . '</strong></div>';
        if ($role === 'root') {
            mark_solved('jwt');
            echo '<div class="result">✅ alg:none ile aşıldı — çözüldü!</div>';
        }
    }
}
