<?php
/**
 * CSRF — IMPOSSIBLE (güvenli referans)
 * - Per-request synchronizer token (random_bytes), $_SESSION'da
 * - POST + hash_equals ile doğrulama
 * - Üretimde: SameSite=Strict, HttpOnly, Secure cookie + hassas işlemde mevcut parola
 */
$uid = (int) ($_SESSION['uid'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::require();                 // geçersizse durur
    $name = trim((string) ($_POST['name'] ?? ''));
    if ($name !== '') {
        $pdo = DB::pdo();
        $pdo->prepare('UPDATE users SET first_name = ? WHERE id = ?')->execute([$name, $uid]);
        // Token'ı döndür (one-time benzeri)
        unset($_SESSION['csrf_token']);
        echo '<div class="result ok">Görünen ad güvenli şekilde güncellendi.</div>';
    }
}

$cur = current_user();
echo '<p>Mevcut görünen ad: <strong>' . e($cur['first_name'] ?? '') . '</strong></p>';
echo '<form method="post">';
echo '  Yeni görünen ad: <input type="text" name="name" value=""> ';
echo Csrf::field();
echo '  <button type="submit">Değiştir</button>';
echo '</form>';
