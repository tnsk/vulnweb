<?php
/**
 * Weak Crypto — HIGH
 * KASITLI BOZUK SAVUNMA: token = base64(rol).md5(SECRET.base64(rol))
 * ama SECRET kodda HARDCODED (kaynağı görüntüle ile okunur) → forge edilebilir.
 * Forge: data=base64("root"); token = data + "." + md5(SECRET + data)
 */
$SECRET = 'vulnweb-2026';   // KASITLI: hardcoded anahtar (kaynakta görünür)
$data   = base64_encode('guest');
$token  = $_REQUEST['token'] ?? ($data . '.' . md5($SECRET . $data));

echo '<p>Token formatı: <code>base64(rol).md5(SECRET + base64(rol))</code></p>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: "Kaynağı Görüntüle" ile SECRET değerini bul.</p>';
echo '<form method="post"><input type="text" name="token" size="60" value="' . e($token) . '"> <button type="submit">Uygula</button></form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ids_log('weak_crypto', $_REQUEST['token']);
    $parts = explode('.', (string) $_REQUEST['token'], 2);
    if (count($parts) === 2 && hash_equals(md5($SECRET . $parts[0]), $parts[1])) {
        $role = (string) base64_decode($parts[0], true);
        echo '<div class="result">Doğrulandı, rol: <strong>' . e($role) . '</strong></div>';
        if ($role === 'root') {
            mark_solved('weak_crypto');
            echo '<div class="result">✅ Hardcoded anahtar ile forge edildi — çözüldü!</div>';
        }
    } else {
        echo '<div class="result detected">Geçersiz token (imza tutmadı).</div>';
    }
}
