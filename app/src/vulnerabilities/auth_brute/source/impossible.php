<?php
/**
 * Auth / Brute-Force — IMPOSSIBLE (güvenli referans)
 * - Sunucu-taraflı rate-limit + progressive lockout (oturum/IP bazlı)
 * - Generic hata mesajı (enumeration yok), sabit-zamanlı karşılaştırma
 * - Üretimde: password_hash(ARGON2ID)/password_verify + MFA (burada şema md5 olduğundan desen gösterilir)
 */
$u = $_POST['u'] ?? '';
$p = $_POST['p'] ?? '';

$_SESSION['ab_fails'] = $_SESSION['ab_fails'] ?? 0;
$_SESSION['ab_until'] = $_SESSION['ab_until'] ?? 0;

echo '<p>Güvenli giriş: sunucu-taraflı lockout + generic hata.</p>';
echo '<form method="post">kullanıcı: <input name="u" value=""> '
   . 'parola: <input name="p" value=""> <button type="submit">Giriş</button></form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $now = time();
    if ($now < $_SESSION['ab_until']) {
        echo '<div class="result">Çok fazla başarısız deneme. '
           . (int) ($_SESSION['ab_until'] - $now) . ' sn bekleyin.</div>';
    } else {
        $stmt = DB::pdo()->prepare('SELECT id, password_md5, role FROM users WHERE username = ?');
        $stmt->execute([$u]);
        $row = $stmt->fetch();
        if ($row && hash_equals($row['password_md5'], md5($p))) {
            $_SESSION['ab_fails'] = 0;
            echo '<div class="result ok">Giriş başarılı: ' . e($u) . '</div>';
        } else {
            $_SESSION['ab_fails']++;
            if ($_SESSION['ab_fails'] >= 5) {
                $_SESSION['ab_until'] = $now + 30;   // progressive lockout
                $_SESSION['ab_fails'] = 0;
            }
            echo '<div class="result">Kullanıcı adı veya parola hatalı.</div>';   // generic
        }
    }
}
