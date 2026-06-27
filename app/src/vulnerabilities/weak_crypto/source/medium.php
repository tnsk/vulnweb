<?php
/**
 * Weak Crypto — MEDIUM
 * KASITLI BOZUK SAVUNMA: token = base64(rol).md5(base64(rol)) — ANAHTARSIZ bütünlük.
 * Saldırgan checksum'ı kendisi hesaplayabilir.
 * Forge: data=base64("root"); token = data + "." + md5(data)
 */
$data  = base64_encode('guest');
$token = $_REQUEST['token'] ?? ($data . '.' . md5($data));

echo '<p>Token formatı: <code>base64(rol).md5(base64(rol))</code></p>';
echo '<p style="color:var(--muted);font-size:.85rem">Geçerli örnek: <code>' . e($token) . '</code></p>';
echo '<form method="post"><input type="text" name="token" size="60" value="' . e($token) . '"> <button type="submit">Uygula</button></form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ids_log('weak_crypto', $_REQUEST['token']);
    $parts = explode('.', (string) $_REQUEST['token'], 2);
    if (count($parts) === 2 && hash_equals(md5($parts[0]), $parts[1])) {
        $role = (string) base64_decode($parts[0], true);
        echo '<div class="result">Doğrulandı, rol: <strong>' . e($role) . '</strong></div>';
        if ($role === 'root') {
            mark_solved('weak_crypto');
            echo '<div class="result">✅ Anahtarsız checksum forge edildi — çözüldü!</div>';
        }
    } else {
        echo '<div class="result detected">Geçersiz token (checksum tutmadı).</div>';
    }
}
