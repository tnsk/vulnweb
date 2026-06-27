<?php
/**
 * Auth / Brute-Force — MEDIUM
 * KASITLI BOZUK SAVUNMA: generic hata (enumeration kapalı) + sabit usleep
 * AMA gerçek rate-limit/lockout YOK → brute hâlâ çalışır.
 */
$u = $_POST['u'] ?? '';
$p = $_POST['p'] ?? '';

echo '<p>Generic hata mesajı + küçük gecikme var; ama lockout yok.</p>';
echo '<form method="post">kullanıcı: <input name="u" value="' . e($u) . '"> '
   . 'parola: <input name="p" value=""> <button type="submit">Giriş</button></form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ids_log('auth_brute', "$u:$p");
    usleep(200000);   // paralelleştirilebilir; gerçek koruma değil
    $stmt = DB::pdo()->prepare('SELECT id, password_md5, role FROM users WHERE username = ?');
    $stmt->execute([$u]);
    $row = $stmt->fetch();
    if ($row && hash_equals($row['password_md5'], md5($p))) {
        echo '<div class="result">Giriş başarılı: ' . e($u) . ' (rol: ' . e($row['role']) . ')</div>';
        if ($u !== 'admin') {
            mark_solved('auth_brute');
            echo '<div class="result">✅ Generic hataya rağmen brute başarılı — çözüldü!</div>';
        }
    } else {
        echo '<div class="result detected">Kullanıcı adı veya parola hatalı.</div>';   // generic
    }
}
