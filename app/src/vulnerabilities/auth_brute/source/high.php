<?php
/**
 * Auth / Brute-Force — HIGH
 * KASITLI BOZUK SAVUNMA: deneme sayacı İSTEMCİ TARAFINDA (cookie) tutulur → cookie'yi
 * temizleyince/sıfırlayınca lockout aşılır. (Sunucu durumu yok.)
 * Bypass: 'ab_tries' cookie'sini gönderme.
 */
$u = $_POST['u'] ?? '';
$p = $_POST['p'] ?? '';
$tries = (int) ($_COOKIE['ab_tries'] ?? 0);

echo '<p>Deneme limiti var (5) — ama sayaç istemci cookie\'sinde.</p>';
echo '<p style="color:var(--muted);font-size:.85rem">Bypass: <code>ab_tries</code> cookie\'sini göndermeden dene.</p>';
echo '<form method="post">kullanıcı: <input name="u" value="' . e($u) . '"> '
   . 'parola: <input name="p" value=""> <button type="submit">Giriş</button></form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ids_log('auth_brute', "$u:$p (tries=$tries)");
    if ($tries >= 5) {
        echo '<div class="result detected">Çok fazla deneme (cookie). Kilitlendi.</div>';
    } else {
        setcookie('ab_tries', (string) ($tries + 1), time() + 3600);   // istemci-taraflı sayaç
        $stmt = DB::pdo()->prepare('SELECT id, password_md5, role FROM users WHERE username = ?');
        $stmt->execute([$u]);
        $row = $stmt->fetch();
        if ($row && hash_equals($row['password_md5'], md5($p))) {
            echo '<div class="result">Giriş başarılı: ' . e($u) . ' (rol: ' . e($row['role']) . ')</div>';
            if ($u !== 'admin') {
                mark_solved('auth_brute');
                echo '<div class="result">✅ İstemci-taraflı lockout aşıldı — çözüldü!</div>';
            }
        } else {
            echo '<div class="result detected">Kullanıcı adı veya parola hatalı.</div>';
        }
    }
}
