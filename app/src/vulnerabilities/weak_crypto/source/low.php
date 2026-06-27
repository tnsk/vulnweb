<?php
/**
 * Weak Crypto / Sensitive Data — LOW
 * KASITLI ZAFİYET: yetki token'ı yalnızca base64'lenmiş roldür ("şifreleme" değil).
 * Sömürü: base64("root") = cm9vdA==  ile rolü yükselt.
 */
$token = $_REQUEST['token'] ?? base64_encode('guest');
$role  = (string) base64_decode($token, true);

echo '<p>Yetki token\'in: <code>' . e($token) . '</code> → rol: <strong>' . e($role) . '</strong></p>';
echo '<p style="color:var(--muted);font-size:.85rem">Hedef: rolü <code>root</code> yap. İpucu: token sadece base64\'lü rol.</p>';
echo '<form method="post"><input type="text" name="token" size="40" value="' . e($token) . '"> <button type="submit">Uygula</button></form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ids_log('weak_crypto', $token);
    if ($role === 'root') {
        mark_solved('weak_crypto');
        echo '<div class="result">✅ ROOT yetkisine yükseltildi (base64 ≠ şifreleme) — çözüldü!</div>';
    }
}
