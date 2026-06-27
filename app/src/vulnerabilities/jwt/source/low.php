<?php
/**
 * JWT — LOW
 * KASITLI ZAFİYET: imza DOĞRULANMAZ; payload'a körü körüne güvenilir (alg:none kabul).
 * Sömürü: header {"alg":"none"} + payload {"role":"root"} + boş imza ile forge et.
 */
$sample = jwt_make(['user' => 'guest', 'role' => 'user'], 'irrelevant');
$jwt = $_REQUEST['jwt'] ?? $sample;

echo '<p>Oturum JWT\'si (imza doğrulanmıyor!). Rolünü <code>root</code> yap.</p>';
echo '<p style="color:var(--muted);font-size:.85rem">Örnek token: <code style="word-break:break-all">' . e($sample) . '</code></p>';
echo '<form method="post"><input name="jwt" size="70" value="' . e($jwt) . '"> <button type="submit">Doğrula</button></form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ids_log('jwt', $jwt);
    // ----- ZAFİYETLİ: imza yok sayılır -----
    $payload = jwt_payload($jwt);
    $role = (string) ($payload['role'] ?? '');
    // ---------------------------------------
    echo '<div class="result">Token rolü: <strong>' . e($role) . '</strong> (imza kontrol edilmedi)</div>';
    if ($role === 'root') {
        mark_solved('jwt');
        echo '<div class="result">✅ İmzasız JWT forge edildi — challenge çözüldü!</div>';
    }
}
