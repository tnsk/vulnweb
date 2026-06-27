<?php
/**
 * Authentication / Brute-Force — LOW
 * KASITLI ZAFİYET: rate-limit/lockout YOK, md5 parola, kullanıcı enumeration
 * (var-yok / yanlış-parola farklı mesaj). Hedef: admin DIŞINDA bir hesaba gir.
 * Zayıf parolalar: gordonb/abc123, 1337/charley, pablo/letmein
 */
$u = $_POST['u'] ?? '';
$p = $_POST['p'] ?? '';

echo '<p>Bu giriş formu sınırsız deneme + md5 + kullanıcı enumeration içerir. '
   . 'Hedef: <strong>admin dışında</strong> bir hesaba gir.</p>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: SQLi lab\'ından hash\'leri çek ve crack et, '
   . 'ya da yaygın parola dene (abc123, letmein, charley, password).</p>';
echo '<form method="post">kullanıcı: <input name="u" value="' . e($u) . '"> '
   . 'parola: <input name="p" value=""> <button type="submit">Giriş</button></form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ids_log('auth_brute', "$u:$p");
    $stmt = DB::pdo()->prepare('SELECT id, password_md5, role FROM users WHERE username = ?');
    $stmt->execute([$u]);
    $row = $stmt->fetch();
    if (!$row) {
        echo '<div class="result detected">Böyle bir kullanıcı yok.</div>';          // enumeration!
    } elseif (hash_equals($row['password_md5'], md5($p))) {
        echo '<div class="result">Giriş başarılı: ' . e($u) . ' (rol: ' . e($row['role']) . ')</div>';
        if ($u !== 'admin') {
            mark_solved('auth_brute');
            echo '<div class="result">✅ Zayıf hesap kırıldı (lockout yok) — challenge çözüldü!</div>';
        }
    } else {
        echo '<div class="result detected">Parola yanlış (kullanıcı mevcut).</div>';   // enumeration!
    }
    // KASITLI: deneme sayacı / gecikme / lockout YOK
}
