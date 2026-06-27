<?php
/**
 * JWT — MEDIUM
 * KASITLI BOZUK SAVUNMA: HS256 doğrulanır AMA secret ZAYIF ('secret').
 * Sömürü: 'secret' ile imzalanmış {"role":"root"} token üret (hashcat ile de kırılır).
 */
$secret = 'secret';   // KASITLI zayıf secret
$sample = jwt_make(['user' => 'guest', 'role' => 'user'], $secret);
$jwt = $_REQUEST['jwt'] ?? $sample;

echo '<p>HS256 doğrulanır — ama secret zayıf.</p>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: secret çok yaygın bir kelime. Geçerli örnek: '
   . '<code style="word-break:break-all">' . e($sample) . '</code></p>';
echo '<form method="post"><input name="jwt" size="70" value="' . e($jwt) . '"> <button type="submit">Doğrula</button></form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ids_log('jwt', $jwt);
    if (!jwt_verify_hs256($jwt, $secret)) {
        echo '<div class="result detected">İmza geçersiz.</div>';
    } else {
        $role = (string) (jwt_payload($jwt)['role'] ?? '');
        echo '<div class="result">Doğrulandı, rol: <strong>' . e($role) . '</strong></div>';
        if ($role === 'root') {
            mark_solved('jwt');
            echo '<div class="result">✅ Zayıf secret ile forge edildi — çözüldü!</div>';
        }
    }
}
