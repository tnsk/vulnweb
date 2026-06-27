<?php
/**
 * CSRF — HIGH
 * KASITLI BOZUK SAVUNMA: token var ama TAHMİN EDİLEBİLİR (host adının md5'i) — sabit.
 * Bypass: saldırgan token = md5(host) değerini hesaplayıp isteğe ekler.
 */
$uid  = (int) ($_SESSION['uid'] ?? 0);
$host = $_SERVER['HTTP_HOST'] ?? '127.0.0.1';
$predictable = md5($host);             // BOZUK: tahmin edilebilir, sabit token
$name  = $_GET['name'] ?? null;
$token = $_GET['token'] ?? '';

if ($name !== null && $name !== '') {
    ids_log('csrf', "$name token=$token");
    if (hash_equals($predictable, (string) $token)) {
        $pdo = DB::pdo();
        $pdo->prepare('UPDATE users SET first_name = ? WHERE id = ?')->execute([$name, $uid]);
        mark_solved('csrf');
        echo '<div class="result">✅ Tahmin edilebilir token ile aşıldı — "' . e($name) . '" — çözüldü!</div>';
    } else {
        echo '<div class="result detected">Reddedildi: token geçersiz.</div>';
    }
}

$cur = current_user();
echo '<p>Mevcut görünen ad: <strong>' . e($cur['first_name'] ?? '') . '</strong></p>';
echo '<form method="get">';
echo '  Yeni görünen ad: <input type="text" name="name" value=""> ';
echo '  <input type="hidden" name="token" value="' . e($predictable) . '"> ';
echo '  <button type="submit">Değiştir</button>';
echo '</form>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: token = <code>md5(host)</code>. Sabit ve hesaplanabilir.</p>';
